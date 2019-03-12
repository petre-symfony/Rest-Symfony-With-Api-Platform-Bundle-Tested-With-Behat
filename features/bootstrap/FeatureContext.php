<?php

use Behat\Behat\Context\Context;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use App\Entity\User;
use App\Entity\Programmer;
use Doctrine\ORM\EntityManagerInterface;
use Behat\Gherkin\Node\TableNode;
use Symfony\Component\PropertyAccess\PropertyAccess;

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


  /**
   * @Given the user :email exists
   */
  public function givenTheUserExists($email) {
    $this->createUser($email);
  }


  /**
   * @Given the following programmers exists:
   */
  public function theFollowingProgrammersExists(TableNode $table) {
    foreach ($table->getHash() as $row){
      $this->createProgrammer($row, null);
    }
  }


  public function getContainer(){
    return $this->kernel->getContainer();
  }

  public function getEntityManager(){
    return $this->getContainer()->get('doctrine.orm.entity_manager');
  }

  private function createUser($email){
    $user = new User();
    $user->setEmail($email);
    $user->setRoles(["ROLE_USER"]);
    /** @var EntityManagerInterface $em */
    $em = $this->getContainer()->get('doctrine.orm.entity_manager');
    $em->persist($user);
    $em->flush();
    return $user;
  }

  private function createProgrammer(array $data, User $owner=null){
    $em = $this->getEntityManager();
    $user = $owner ? $owner : $em->getRepository('App:User')->findAny();
    $data['powerLevel'] = isset($data['powerLevel']) ? $data['powerLevel'] : rand(1, 10);
    $data['avatarNumber'] = isset($data['avatarNumber']) ? $data['avatarNumber'] : rand(1, 5);
    $data = array_merge(
      [
        'user'  => $user
      ],
      $data
    );
    $accessor = PropertyAccess::createPropertyAccessor();
    $programmer = new Programmer();
    foreach ($data as $key => $val) {
      $accessor->setValue($programmer, $key, $val);
    }
    $em->persist($programmer);
    $em->flush();
    return $programmer;
  }
}
