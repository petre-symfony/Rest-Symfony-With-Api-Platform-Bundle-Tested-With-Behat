<?php

use Behat\Behat\Context\Context;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * This context class contains the definitions of the steps used by the demo 
 * feature file. Learn how to get started with Behat and BDD on Behat's website.
 * 
 * @see http://behat.org/en/latest/quick_start.html
 */
class FeatureContext implements Context {
  /**
   * @var KernelInterface
   */
  private $kernel;

  /**
   * @var Response|null
   */
  private $response;

  public function __construct(KernelInterface $kernel) {
    $this->kernel = $kernel;
  }

  /**
   * @BeforeScenario
   */
  public function clearData(){
    $purger = new ORMPurger(
      $this
        ->getContainer()
        ->get('doctrine.orm.entity_manager')
    );
    $purger->purge();
  }

  public function getContainer(){
    return $this->kernel->getContainer();
  }

}
