<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

#[Assert\Cascade]
class CarDTO
{
    #[Assert\NotNull]
    public string $make;

    #[Assert\NotNull]
    public string $model;

    #[Assert\NotNull]
    #[Assert\Date]
    #[Assert\GreaterThanOrEqual('-4 years')]
    public string $buildDate;

    #[Assert\NotNull]
    public int $colourId;
}