<?php

use Behat\Behat\Context\Context;
use Imbo\BehatApiExtension\Context\ApiContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Context\Environment\InitializedContextEnvironment;

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

}