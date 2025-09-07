<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class BaseIntegrationTestCase extends KernelTestCase
{

    protected ContainerInterface $container;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->container = self::$kernel->getContainer();
    }

    /**
     * @template T of object
     * @param class-string<T> $className
     * @return T
     */
    protected function getService(string $className): object
    {
        return $this->container->get($className);
    }

    protected function setService(string $id, ?object $service): void
    {
        $this->container->set($id, $service);
    }
}
