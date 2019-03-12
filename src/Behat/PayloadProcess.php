<?php

namespace App\Behat;


use Doctrine\ORM\EntityManagerInterface;


class PayloadProcess {

	/**
	 * @var array
	 */
	private $chunks;
	/**
	 * @var EntityManagerInterface
	 */
	private $em;

	private static $allowedClasses = ["Programmer", "Project", "User", "Battle"];

	public function __construct(array $chunks, EntityManagerInterface $em) {
		$this->chunks = $chunks;
		$this->em = $em;
	}

	public function process(){
		$className = ucwords(substr($this->chunks[0], 0, -1));
		if(!in_array($className, self::$allowedClasses)){
			throw new \Exception(
					'Allowed classes are: '. implode(',', self::$allowedClasses)
			);
		}
		$fieldValue = $this->chunks[1];
		$repository = $this->em->getRepository("App:$className");

		if( $fieldValue !== 'last'){
			switch ($className)	{
				case 'Programmer':
					$result = $repository->findOneBy(['nickname' => $fieldValue]);
					break;
				case 'Project':
					$result = $repository->findOneBy(['name' => $fieldValue]);
					break;
				case 'User':
					$result = $repository->findOneBy(['email' => $fieldValue]);
					break;
				default:
					throw new \Exception("Payloads like %battle.whatever.id% unless %battle.last.id% are not implemented");
			}
		} else {
			$result = $repository->findLastId();

		}

		if (!$result){
			if($fieldValue === 'last') {
				throw new \Exception('No '. strtolower($className) . ' records in database');
			} else {
				switch ($className){
					case 'Programmer':
						throw new \Exception(
			"No " . strtolower($className) . " found by nickname $fieldValue"
						);
						break;
					case 'Project':
						throw new \Exception(
								"No " . strtolower($className) . " found by name $fieldValue"
						);
						break;
					case 'User':
						throw new \Exception(
								"No " . strtolower($className) . " found by email $fieldValue"
						);
						break;
					default:
				}
			}
		}

		return $result->getId();
	}


}