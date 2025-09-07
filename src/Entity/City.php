<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CityRepository::class)]
class City
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(length: 255)]
    public ?string $name = null;

    #[ORM\Column(length: 10)]
    public ?string $postalCode = null;

    #[ORM\Column(length: 10)]
    public ?string $inseeCode = null;

    #[ORM\Column(length: 10)]
    public ?string $department = null;

    #[ORM\Column]
    public bool $franceTravailUseDepartment = false;

    #[ORM\Column]
    public bool $useForFranceTravail = false;
}
