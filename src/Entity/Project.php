<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 */
class Project {
  /**
   * @ORM\Id()
   * @ORM\GeneratedValue()
   * @ORM\Column(type="integer")
   */
  private $id;

  /**
   * @ORM\Column(type="string", length=100)
   * @Assert\NotBlank()
   */
  private $name;

  /**
   * @ORM\Column(type="integer")
   * @Assert\Type(
   *      type="integer",
   *     message="The value {{ value }} is not a valid {{ type }}."
   * )
   */
  private $difficultyLevel;

  public function getId(): ?int {
    return $this->id;
  }

  public function getName(): ?string {
    return $this->name;
  }

  public function setName(string $name): self {
    $this->name = $name;

    return $this;
  }

  public function getDifficultyLevel(): ?int {
    return $this->difficultyLevel;
  }

  public function setDifficultyLevel(int $difficultyLevel): self {
    $this->difficultyLevel = $difficultyLevel;

    return $this;
  }
}
