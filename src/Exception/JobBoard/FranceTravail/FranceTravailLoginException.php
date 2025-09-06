<?php

declare(strict_types=1);

namespace App\Exception\JobBoard\FranceTravail;

use Exception;

class FranceTravailLoginException extends Exception
{
    protected $message = 'Unable to login to France Travail API';
}
