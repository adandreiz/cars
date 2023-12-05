<?php

namespace App\Entity;

use App\Repository\CarRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CarRepository::class)]
#[UniqueEntity(
    fields: ['make', 'model', 'buildDate','colour'],
    message: 'A car with the same values already exists'
)]
class Car
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    private ?string $make = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    private ?string $model = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\NotBlank()]
    #[Assert\GreaterThanOrEqual('-4 years')]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]
    private ?\DateTimeInterface $buildDate = null;

    #[ORM\ManyToOne]
    #[Assert\NotBlank()]
    #[ORM\JoinColumn(nullable: false)]
    private ?Colour $colour = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMake(): ?string
    {
        return $this->make;
    }

    public function setMake(string $make): static
    {
        $this->make = $make;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getBuildDate(): ?\DateTimeInterface
    {
        return $this->buildDate;
    }

    public function setBuildDate(\DateTimeInterface $buildDate): static
    {
        $this->buildDate = $buildDate;

        return $this;
    }

    public function getColour(): ?Colour
    {
        return $this->colour;
    }

    public function setColour(?Colour $colour): static
    {
        $this->colour = $colour;

        return $this;
    }
}
