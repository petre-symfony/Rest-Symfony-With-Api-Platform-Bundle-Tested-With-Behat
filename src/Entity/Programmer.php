<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProgrammerRepository")
 * @ApiResource(
 *   attributes={
 *     "normalization_context" = {"groups"={"programmer.read"}},
 *     "denormalization_context" = {"groups"={"programmer.write"}}
 *   }
 * )
 */
class Programmer {
  /**
   * @ORM\Id()
   * @ORM\GeneratedValue()
   * @ORM\Column(type="integer")
   * @Groups({
   *   "programmer.read"
   * })
   */
  private $id;

  /**
   * @ORM\Column(type="string", length=100, unique=true)
   * @Assert\NotBlank()
   * @Groups({
   *   "programmer.read",
   *   "programmer.write"
   * })
   */
  private $nickname;

  /**
   * @ORM\Column(type="integer")
   * @Assert\Range(
   *      min = 1,
   *      max = 5,
   *      minMessage = "You must enter at least {{ min}}",
   *      maxMessage = "You must enter at most {{ max }}"
   * )
   * @Groups({
   *   "programmer.read",
   *   "programmer.write"
   * })
   */
  private $avatarNumber;

  /**
   * @ORM\Column(type="string", length=255, nullable=true)
   * @Groups({
   *   "programmer.read",
   *   "programmer.write"
   * })
   */
  private $tagLine;

  /**
   * @ORM\Column(type="integer")
   * @Assert\Type(
   *      type="integer",
   *     message="The value {{ value }} is not a valid {{ type }}."
   * )
   * @Groups({
   *   "programmer.read",
   *   "programmer.write"
   * })
   */
  private $powerLevel = 0;

  /**
   * @ORM\ManyToOne(targetEntity="App\Entity\User")
   * @ORM\JoinColumn(nullable=false)
   * @Assert\NotNull()
   * @Groups({
   *   "programmer.write"
   * })
   */
  private $user;

  public function __construct($nickname = null, $avatarNumber = null) {
    $this->nickname = $nickname;
    $this->avatarNumber = $avatarNumber;
  }

  public function getId(): ?int {
  return $this->id;
  }

  public function getNickname(): ?string {
    return $this->nickname;
  }

  public function setNickname(string $nickname): self {
    $this->nickname = $nickname;

    return $this;
  }

  public function getAvatarNumber(): ?int {
    return $this->avatarNumber;
  }

  public function setAvatarNumber(int $avatarNumber): self {
    $this->avatarNumber = $avatarNumber;

    return $this;
  }

  public function getTagLine(): ?string {
    return $this->tagLine;
  }

  public function setTagLine(?string $tagLine): self {
    $this->tagLine = $tagLine;

    return $this;
  }

  public function getPowerLevel(): ?int {
    return $this->powerLevel;
  }

  public function setPowerLevel(int $powerLevel): self {
    $this->powerLevel = $powerLevel;

    return $this;
  }

  public function getUser(): ?User {
    return $this->user;
  }

  public function setUser(?User $user): self {
    $this->user = $user;

    return $this;
  }
}