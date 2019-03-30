<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Action\CreateBattle;

/**
 * @ORM\Table(name="battle_battle")
 * @ORM\Entity(repositoryClass="App\Repository\BattleRepository")
 * @ApiResource(
 *   collectionOperations={
 *     "get",
 *     "post"={
 *       "method"="POST",
 *       "controller"=CreateBattle::class
 *	   }
 *	 }
 *
 * )
 */
class Battle {
  /**
   * @ORM\Id()
   * @ORM\GeneratedValue()
   * @ORM\Column(type="integer")
   */
  private $id;

  /**
   * @ORM\ManyToOne(targetEntity="App\Entity\Programmer")
   * @ORM\JoinColumn(nullable=false)
   * @Assert\NotNull()
   */
  private $programmer;

  /**
   * @ORM\ManyToOne(targetEntity="App\Entity\Project")
   * @ORM\JoinColumn(nullable=false)
   * @Assert\NotNull()
   */
  private $project;

  /**
   * @ORM\Column(type="datetime")
   * @Assert\NotNull()
   */
  private $foughtAt;

  /**
   * @ORM\Column(type="boolean")
   * @Assert\NotNull()
   */
  private $didProgrammerWin;

  /**
   * @ORM\Column(type="text")
   * @Assert\NotBlank()
   */
  private $notes;

  public function setBattleLostByProgrammer($notes){
    $this->didProgrammerWin = false;
    $this->notes = $notes;
  }

  public function getId(): ?int {
    return $this->id;
  }

  public function getProgrammer(): ?Programmer {
    return $this->programmer;
  }

	public function setProgrammer(?Programmer $programmer): self {
		$this->programmer=$programmer;

		return $this;
	}

  public function getProject(): ?Project {
    return $this->project;
  }

	public function setProject(?Project $project): self {
		$this->project=$project;

		return $this;
	}

  public function getFoughtAt(): ?\DateTimeInterface {
    return $this->foughtAt;
  }

  public function setFoughtAt(){
  	$this->foughtAt = new \DateTime();

  	return $this;
	}

  public function getDidProgrammerWin(): ?bool {
    return $this->didProgrammerWin;
  }

  public function getNotes(): ?string {
    return $this->notes;
  }
}