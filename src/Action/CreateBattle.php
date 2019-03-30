<?php

namespace App\Action;


use App\Entity\Battle;
use App\Services\BattleManager;
use Doctrine\ORM\EntityManagerInterface;

class CreateBattle {

	public function __invoke(Battle $data): Battle {
		$battleManager = new BattleManager();

		$data = $battleManager->finishBattleCreation($data);

		return $data;
	}

}