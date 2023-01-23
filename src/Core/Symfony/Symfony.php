<?php

namespace PrestaShop\PrestaShop\Core\Symfony;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class Symfony
{
    /**
     * @var KernelInterface
     */
    private static $kernel;

    public static function getContainer(): ContainerInterface
    {
        return self::$kernel->getContainer();
    }

    public function get(string $fqcn): object
    {
        return self::$kernel->getContainer()->get($fqcn);
    }

    public static function setKernelInstance(KernelInterface $kernel): void
    {
        self::$kernel = $kernel;
    }
}
