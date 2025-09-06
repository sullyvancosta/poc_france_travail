<?php

declare(strict_types=1);

namespace App\DTO\JobBoard\FranceTravail;

use Symfony\Component\Validator\Constraints\NotBlank;

class LoginResponseDTO
{
    #[NotBlank]
    public string $access_token = '';
}
