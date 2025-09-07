<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\JobOfferRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JobOfferRepository::class)]
class JobOffer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(length: 255)]
    public ?string $franceTravailId = null;

    #[ORM\Column(length: 255)]
    public ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    public ?string $content = null;

    #[ORM\Column]
    public DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    public ?DateTime $updatedAt = null;

    #[ORM\Column(length: 255)]
    public ?string $contractType = null;

    #[ORM\Column(length: 255)]
    public ?string $applyUrl = null;

    #[ORM\Column(length: 255)]
    public ?string $company = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    public ?City $city = null;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }
}
