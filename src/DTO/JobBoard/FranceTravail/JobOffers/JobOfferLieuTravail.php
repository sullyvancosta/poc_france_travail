<?php

declare(strict_types=1);

namespace App\DTO\JobBoard\FranceTravail\JobOffers;

use Symfony\Component\Validator\Constraints\NotBlank;

class JobOfferLieuTravail
{

    #[NotBlank]
    public string $libelle = '';

    #[NotBlank]
    public float $latitude = 0.0;

    #[NotBlank]
    public float $longitude = 0.0;

    #[NotBlank]
    public string $codePostal = '';

    #[NotBlank]
    public string $commune = '';
}
