<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\JobOffer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JobOffer>
 */
class JobOfferRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobOffer::class);
    }

    /**
     * @return array<int, array{company: string, country: string, contract_type: string, offers_count: int}>
     * @throws DBALException
     */
    public function getStatisticsForReporting(): array
    {
        $sql = <<<sql
            select jo.company, 'FRANCE' as country, jo.contract_type, count(1) as offers_count
            from job_offer jo
            group by company, contract_type
            order by company
sql;
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $result = $stmt->executeQuery();

        return $result->fetchAllAssociative();
    }
}
