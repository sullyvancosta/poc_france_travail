<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\JobOffer;
use App\Repository\JobOfferRepository;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(name: 'app:generate-job-offers-statistics')]
class GenerateJobOffersStatistics
{

    public function __construct(
        private readonly EntityManagerInterface $em
    )
    {}

    /**
     * @throws DBALException
     */
    public function __invoke(OutputInterface $output): int
    {
        /** @var JobOfferRepository $jobOfferRepo */
        $jobOfferRepo = $this->em->getRepository(JobOffer::class);
        $dataForStatistics = $jobOfferRepo->getStatisticsForReporting();

        $statistics = [];
        foreach ($dataForStatistics  as $dataForStatistic) {
            $companyName = $dataForStatistic['company'];
            if (false === isset($statistics[$companyName])) {
                $statistics[$companyName] = [
                    'country' => $dataForStatistic['country'],
                    'job_offers_statistics' => [],
                ];
            }

            $contractType = $dataForStatistic['contract_type'];
            $offersCount = $dataForStatistic['offers_count'];
            $statistics[$companyName]['job_offers_statistics'][$contractType] = $offersCount;
        }

        $fileName = sprintf('%s_job_offers_statistics.json', time());
        $filesystem = new Filesystem();
        $filesystem->dumpFile($fileName, (string) json_encode($statistics));

        $output->writeln(sprintf('File "%s" available at root project with data', $fileName));

        return Command::SUCCESS;
    }
}
