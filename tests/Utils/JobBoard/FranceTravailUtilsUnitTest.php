<?php

declare(strict_types=1);

namespace App\Tests\Utils\JobBoard;

use App\Exception\JobBoard\FranceTravail\FranceTravailLoginException;
use App\Utils\JobBoard\FranceTravailUtils;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionMethod;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FranceTravailUtilsUnitTest extends TestCase
{
    private HttpClientInterface&MockObject $httpClient;
    private SerializerInterface&MockObject $serializer;
    private ValidatorInterface&MockObject $validator;
    private string $authLoginURL = 'auth_login_url';
    private string $authClientId = 'auth_client_id';
    private string $authClientSecret = 'auth_client_secret';
    private string $baseApiURL = 'base_api_url';

    private FranceTravailUtils $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);

        $this->sut = new FranceTravailUtils(
            $this->httpClient,
            $this->serializer,
            $this->validator,
            $this->authLoginURL,
            $this->authClientId,
            $this->authClientSecret,
            $this->baseApiURL,
        );
    }

    /**
     * @throws ReflectionException
     */
    public function testLoginToApiWithWrongCredentialsThrowCorrectException(): void
    {
        $this->expectException(FranceTravailLoginException::class);

        $exceptionToThrow = new ClientException(new MockResponse());
        $this->httpClient
            ->method('request')
            ->with(Request::METHOD_POST, $this->authLoginURL, self::anything())
            ->willThrowException($exceptionToThrow);

        $method = new ReflectionMethod(FranceTravailUtils::class, 'loginToApi');
        $method->invoke($this->sut);
    }
}
