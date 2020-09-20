<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=VacationRepository::class)
 */
class Vacation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"add", "user_list"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"add", "user_list"})
     */
    private $dateStart;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"add", "user_list"})
     */
    private $dateEnd;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="vacations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $employee;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"add", "user_list"})
     */
    private $status;

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

    public function getEmployee(): ?User
    {
        return $this->employee;
    }

    public function setEmployee(?User $employee): self
    {
        $this->employee = $employee;

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
}
