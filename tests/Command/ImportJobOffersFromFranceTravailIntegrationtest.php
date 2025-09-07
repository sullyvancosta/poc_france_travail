<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Tests\BaseIntegrationTestCase;
use App\Utils\JobBoard\FranceTravailUtils;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ImportJobOffersFromFranceTravailIntegrationtest extends BaseIntegrationTestCase
{

    private FranceTravailUtils&MockObject $franceTravailUtils;

    protected function setUp(): void
    {
        parent::setUp();
        $this->franceTravailUtils = $this->createMock(FranceTravailUtils::class);
        $this->setService(FranceTravailUtils::class, $this->franceTravailUtils);
    }

    public function todoTestGetJobOffersReturnsEmptyData(): void
    {
        // TODO
        $application = new Application(self::$kernel);

        $command = $application->find('app:create-user');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
        $commandTester->assertCommandIsSuccessful();
    }
}
