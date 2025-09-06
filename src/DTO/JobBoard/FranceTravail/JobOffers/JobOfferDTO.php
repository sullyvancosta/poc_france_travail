<?php

declare(strict_types=1);

namespace App\DTO\JobBoard\FranceTravail\JobOffers;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

class JobOfferDTO
{

    #[NotBlank]
    public string $id = '';

    #[NotBlank]
    public string $intitule = '';

    #[NotBlank]
    public string $description = '';

    #[NotBlank]
    public string $dateCreation = '';

    #[NotBlank]
    public string $dateActualisation = '';

    #[NotBlank]
    public string $typeContrat = '';

    #[Valid]
    public JobOfferOrigineOffre $origineOffre;

    #[Valid]
    public JobOfferLieuTravail $lieuTravail;
}
