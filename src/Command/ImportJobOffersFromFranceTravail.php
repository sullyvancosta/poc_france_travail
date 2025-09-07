<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\City;
use App\Entity\JobOffer;
use App\Exception\JobBoard\FranceTravail\FranceTravailGetJobOffersInvalidParametersException;
use App\Exception\JobBoard\FranceTravail\FranceTravailLoginException;
use App\Repository\CityRepository;
use App\Utils\JobBoard\FranceTravailUtils;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

#[AsCommand(name: 'app:import-job-offers-france-travail')]
class ImportJobOffersFromFranceTravail
{
    private const int BATCH_SIZE = 150;
    private const int MAX_IMPORT_COUNT = 1000;

    private SymfonyStyle $io;
    private CityRepository $cityRepo;

    public function __construct(
        private readonly FranceTravailUtils $franceTravailUtils,
        private readonly ValidatorInterface $validator,
        private readonly EntityManagerInterface $em,
    )
    {
        /** @var CityRepository $cityRepo */
        $cityRepo = $this->em->getRepository(City::class);
        $this->cityRepo = $cityRepo;
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws JsonException
     * @throws FranceTravailLoginException
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws ExceptionInterface
     * @throws FranceTravailGetJobOffersInvalidParametersException
     */
    public function __invoke(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->io->info('Process - Import France Travail job offers - started');

        $cities = $this->cityRepo->findBy(['useForFranceTravail' => true]);
        foreach ($cities as $city) {
            $this->io->info(sprintf('Import Job offers for city %s', (string) $city->name));
            $this->importByCity($city);
        }
        $this->io->info('Process - Import France Travail job offers - ended');

        return Command::SUCCESS;
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws JsonException
     * @throws FranceTravailLoginException
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws ExceptionInterface
     * @throws FranceTravailGetJobOffersInvalidParametersException
     */
    protected function importByCity(City $city): void
    {
        $inseeCodes = [];
        $departments = [];
        if (true === $city->franceTravailUseDepartment) {
            $departments[] = $city->department;
        } else {
            $inseeCodes[] = $city->inseeCode;
        }

        $offset = 0;
        $limit = self::BATCH_SIZE - 1;
        $jobOfferRepo = $this->em->getRepository(JobOffer::class);
        do {
            $jobOffers = $this->franceTravailUtils->getJobOffers($inseeCodes, $departments, "$offset-$limit");

            foreach ($jobOffers->resultats as $jobOfferFromFranceTravail) {
                // Data validation
                $jobOfferViolations = $this->validator->validate($jobOfferFromFranceTravail);
                if (0 !== $jobOfferViolations->count()) {
                    $explainedViolations = [];
                    foreach ($jobOfferViolations as $jobOfferViolation) {
                        $propertyPath = $jobOfferViolation->getPropertyPath();
                        if (false === isset($explainedViolations[$propertyPath])) {
                            $explainedViolations[$propertyPath] = [];
                        }
                        $explainedViolations[$propertyPath][] = $jobOfferViolation->getMessage();
                    }

                    $this->io->warning(sprintf(
                        'JobOffer (FranceTravailId) #%s does not respect our DTO conditions (%s)',
                        $jobOfferFromFranceTravail->id,
                        (string) json_encode($explainedViolations)
                    ));
                    continue;
                }

                $city = $this->cityRepo->findOneBy(['inseeCode' => $jobOfferFromFranceTravail->lieuTravail->commune]);
                if (null === $city) {
                    $this->io->warning(sprintf(
                        'City not found while processing JobOffer (FranceTravailId) #%s (%s)',
                        $jobOfferFromFranceTravail->id,
                        $jobOfferFromFranceTravail->lieuTravail->commune
                    ));
                    continue;
                }

                // Upsert JobOffer entity
                $jobOfferEntity = $jobOfferRepo->findOneBy(['franceTravailId' => $jobOfferFromFranceTravail->id]);
                if (null === $jobOfferEntity) {
                    $this->io->writeln(sprintf(
                        'JobOffer (FranceTravailId) #%s does not exists in our DB - Creating new one',
                        $jobOfferFromFranceTravail->id
                    ));
                    $jobOfferEntity = new JobOffer();
                    $jobOfferEntity->franceTravailId = $jobOfferFromFranceTravail->id;
                    $this->em->persist($jobOfferEntity);
                } else {
                    $this->io->writeln(sprintf(
                        'JobOffer (FranceTravailId) #%s exists in our DB - Update existing',
                        $jobOfferFromFranceTravail->id
                    ));
                    $jobOfferEntity->updatedAt = new DateTime();
                }

                $jobOfferEntity->title = $jobOfferFromFranceTravail->intitule;
                $jobOfferEntity->content = $jobOfferFromFranceTravail->description;
                $jobOfferEntity->contractType = $jobOfferFromFranceTravail->typeContrat;
                $jobOfferEntity->applyUrl = $jobOfferFromFranceTravail->origineOffre->urlOrigine;
                $jobOfferEntity->company = $jobOfferFromFranceTravail->entreprise->nom;
                $jobOfferEntity->city = $city;
            }

            $this->em->flush();
            $offset += self::BATCH_SIZE;
            $limit += self::BATCH_SIZE - 1;
        } while ($offset < self::MAX_IMPORT_COUNT);
    }
}
