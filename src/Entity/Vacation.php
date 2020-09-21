<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=VacationRepository::class)
 */
class Vacation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user_list","add_vac", "vac_list"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user_list","add_vac", "vac_list"})
     * @Assert\NotBlank
     */
    private $dateStart;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user_list","add_vac", "vac_list"})
     * @Assert\NotBlank
     */
    private $dateEnd;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user_list","add_vac", "vac_list"})
     * @Assert\NotBlank
     * @Assert\Choice({"En attente", "Valide", "Refus"})
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="vacations")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank
     */
    private $user;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateStart(): ?string
    {
        return $this->dateStart;
    }

    public function setDateStart(string $dateStart): self
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getDateEnd(): ?string
    {
        return $this->dateEnd;
    }

    public function setDateEnd(string $dateEnd): self
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
