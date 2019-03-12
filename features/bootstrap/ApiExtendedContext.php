<?php

use Behat\Behat\Context\Context;
use Imbo\BehatApiExtension\Context\ApiContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Context\Environment\InitializedContextEnvironment;
use App\Behat\PayloadProcess;
use Behat\Gherkin\Node\PyStringNode;
use http\Exception\InvalidArgumentException;
use GuzzleHttp\Psr7;
use Assert\AssertionFailedException as AssertionFailure;
use Imbo\BehatApiExtension\Exception\AssertionFailedException;
use Assert\Assertion;


class ApiExtendedContext extends ApiContext implements Context {
  /* the same values as in config/api_platform.yml */
  private static $formats = [
    'application/ld+json'      => 'jsonld',
    'application/vnd.api+json' => 'jsonapi',
    'application/hal+json'     => 'jsonhal',
    'application/json'         => 'json',
    'text/html'                => 'html'
  ];

	/** @var FeatureContext $featureContext */
	private $featureContext;

	/** @BeforeScenario */
	public function gatherContexts(BeforeScenarioScope $scope){
		$env = $scope->getEnvironment();

		if ($env instanceof InitializedContextEnvironment) {
			$this->featureContext = $env->getContext(FeatureContext::class);
		}
	}

  /**
   * Set the request body to a string
   * and process any syntaxes like
   * %whatever_entity_class|weavarryan@google.com|id%
   * or %whatever_entity_class|last|id%
   *
   * @param resource|string|PyStringNode $string The content to set as the request body
   * @throws InvalidArgumentException If form_params or multipart is used in the request options
   *                                  an exception will be thrown as these can't be combined.
   * @return self
   *
   * @Given I have the request body:
   */
  public function setRequestBody($string) {
    if (!empty($this->requestOptions['multipart']) || !empty($this->requestOptions['form_params'])) {
      throw new InvalidArgumentException(
        'It\'s not allowed to set a request body when using multipart/form-data or form parameters.'
      );
    }
    $decodedstring = $this->processReplacements($string);
    $this->request = $this->request->withBody(Psr7\stream_for($decodedstring));
    return $this;
  }


  /**
   * Request a path in some determined format
   *
   * @param string $path The path to request
   * @param string $method The HTTP method to use
   * @return self
   *
   * @When I request for :path
   * @When I request for :path using HTTP :method
   * @When I request for :path using HTTP :method in :format format
   */
  public function requestFormattedPath($path, $method = null, $format="application/hal+json") {
    $formattedPath = $path . '.' . self::$formats[$format];
    $this->setRequestPath($formattedPath);
    if (null === $method) {
      $this->setRequestMethod('GET', false);
    } else {
      $this->setRequestMethod($method);
    }
    return $this->sendRequest();
  }

  /**
   * Search a response header value against a string
   *
   * @param string $header The name of the header
   * @param string $value The value to compare with
   * @throws AssertionFailedException
   * @return void
   *
   * @Then the :header response header contains :value
   */
  public function assertResponseHeaderIs($header, $value) {
    $this->requireResponse();
    try {
      Assertion::contains(
        $actual = $this->response->getHeaderLine($header),
        $value,
        sprintf(
          'Expected the "%s" response header to contain "%s", got "%s".',
          $header,
          $value,
          $actual
        )
      );
    } catch (AssertionFailure $e) {
      throw new AssertionFailedException($e->getMessage());
    }
  }

  private function processReplacements($payload){
    while (false !== $startPos = strpos($payload, '%')) {
      $endPos = strpos($payload, '%', $startPos + 1);
      if (!$endPos) {
        throw new \Exception('Cannot find finishing % - expression look unbalanced!');
      }
      $expression = substr($payload, $startPos + 1, $endPos - $startPos - 1);
      $expressionChunks = explode('|', $expression);
      $payloadProcessClass = new PayloadProcess($expressionChunks, $this->featureContext->getEntityManager());
      $evaluated = $payloadProcessClass->process();
      // replace the expression with the final value
      $payload = str_replace('%'.$expression.'%', $evaluated, $payload);
    }
    return $payload;
  }
}