<?php

declare(strict_types=1);

namespace App\DTO\JobBoard\FranceTravail\JobOffers;

use Symfony\Component\Validator\Constraints\Valid;

class GetJobOffersDTO
{

    /**
     * @var JobOfferDTO[]
     */
    #[Valid]
    public array $resultats = [];
}
