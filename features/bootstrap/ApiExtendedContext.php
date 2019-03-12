<?php

use Behat\Behat\Context\Context;
use Imbo\BehatApiExtension\Context\ApiContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Context\Environment\InitializedContextEnvironment;
use App\Behat\PayloadProcess;
use Behat\Gherkin\Node\PyStringNode;
use http\Exception\InvalidArgumentException;
use GuzzleHttp\Psr7;


class ApiExtendedContext extends ApiContext implements Context {
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