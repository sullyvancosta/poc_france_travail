<?php

declare(strict_types=1);

namespace App\DTO\JobBoard\FranceTravail\JobOffers;

use Symfony\Component\Validator\Constraints\NotBlank;

class JobOfferEntreprise
{

    #[NotBlank]
    public string $nom = '';

    public bool $entrepriseAdaptee = false;
}
