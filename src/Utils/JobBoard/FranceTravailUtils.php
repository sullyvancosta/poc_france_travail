<?php

declare(strict_types=1);

namespace App\Utils\JobBoard;

use App\DTO\JobBoard\FranceTravail\LoginResponseDTO;
use App\DTO\JobBoard\FranceTravail\JobOffers\GetJobOffersDTO;
use App\Exception\JobBoard\FranceTravail\FranceTravailGetJobOffersInvalidParametersException;
use App\Exception\JobBoard\FranceTravail\FranceTravailLoginException;
use JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FranceTravailUtils
{

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly string $authLoginURL,
        private readonly string $authClientId,
        private readonly string $authClientSecret,
        private readonly string $baseApiURL,
    )
    {}

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws JsonException
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws FranceTravailLoginException
     * @throws ExceptionInterface
     * @throws FranceTravailGetJobOffersInvalidParametersException
     */
    public function getJobOffers(
        array $inseeCodes = [],
        array $departments = []
    ): GetJobOffersDTO
    {
        if (count($inseeCodes) > 0 && count($departments) > 0) {
            throw new FranceTravailGetJobOffersInvalidParametersException();
        }

        $authToken = $this->loginToApi();
        $getJobOffersQuery = [];
        if (count($inseeCodes) > 0) {
            $getJobOffersQuery['commune'] = implode(',', $inseeCodes);
        } else if (count($departments) > 0) {
            $getJobOffersQuery['departement'] = implode(',', $departments);
        }

        $offerResponse = $this->httpClient->request(
            Request::METHOD_GET,
            sprintf('%s/offresdemploi/v2/offres/search', $this->baseApiURL),
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $authToken,
                ],
                'query' => $getJobOffersQuery,
            ]
        );

        $offerResponseArray = $offerResponse->toArray();
        $offerResponseJson = json_encode($offerResponseArray, JSON_THROW_ON_ERROR);

        return $this->serializer->deserialize($offerResponseJson, GetJobOffersDTO::class, 'json');
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws JsonException
     * @throws ExceptionInterface
     * @throws FranceTravailLoginException
     */
    protected function loginToApi(): string
    {
        $loginResponse = $this->httpClient->request(
            Request::METHOD_POST,
            $this->authLoginURL,
            [
                'body' => [
                    'grant_type'    => 'client_credentials',
                    'client_id'     => $this->authClientId,
                    'client_secret' => $this->authClientSecret,
                    'scope'         => 'api_offresdemploiv2 o2dsoffre',
                ],
            ]
        );

        $loginResponseData = $loginResponse->toArray();
        $loginResponseDataJson = json_encode($loginResponseData, JSON_THROW_ON_ERROR);

        $loginResponseDTO = $this->serializer->deserialize($loginResponseDataJson, LoginResponseDTO::class, 'json');
        $loginViolations = $this->validator->validate($loginResponseDTO);

        if (0 !== $loginViolations->count()) {
            throw new FranceTravailLoginException();
        }

        return $loginResponseDTO->access_token;
    }
}
