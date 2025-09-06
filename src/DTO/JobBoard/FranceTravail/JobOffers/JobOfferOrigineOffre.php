<?php

declare(strict_types=1);

namespace App\DTO\JobBoard\FranceTravail\JobOffers;

use Symfony\Component\Validator\Constraints\NotBlank;

class JobOfferOrigineOffre
{

    #[NotBlank]
    public string $origine;

    #[NotBlank]
    public string $urlOrigine;
}
