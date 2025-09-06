<?php

declare(strict_types=1);

namespace App\Exception\JobBoard\FranceTravail;

use Exception;

class FranceTravailGetJobOffersInvalidParametersException extends Exception
{

    protected $message = 'Filter on geographic criteria is incorrect; enter either a commune, a department, a region, or a country/continent.';
}
