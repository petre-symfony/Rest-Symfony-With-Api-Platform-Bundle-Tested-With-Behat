<?php

namespace App\Services;


use App\Entity\Battle;
use Doctrine\ORM\EntityManagerInterface;

class BattleManager {


	public function finishBattleCreation(Battle $battle){
		$programmer = $battle->getProgrammer();
		$project = $battle->getProject();

		if ($programmer->getPowerLevel() < $project->getDifficultyLevel()){
			//not enough energy
			$battle->setBattleLostByProgrammer(
				'You don\'t have the skills to even start this project. Read the documentation (i.e. power up) and try again!'
			);
		} else {
			if (rand(0, 2) != 2) {
				$battle->setBattleWonByProgrammer(
					'You battled heroically, asked great questions, worked pragmatically and finished on time. You\'re a hero!'
				);
			} else {
				$battle->setBattleLostByProgrammer(
					'Requirements kept changing, too many meetings, project failed :('
				);
			}
			$programmer->setPowerLevel($programmer->getPowerLevel() - $project->getDifficultyLevel());

		}

		$battle->setFoughtAt();
		return $battle;
	}
}