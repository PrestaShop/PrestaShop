<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\DependencyInjection;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\DependencyInjection\CacheAdapterFactory;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;

class CacheAdapterFactoryTest extends TestCase
{
    /**
     * @var CacheAdapterFactory
     */
    private $cacheAdapterFactory;

    public function setUp(): void
    {
        $this->cacheAdapterFactory = new CacheAdapterFactory();
    }

    /**
     * @dataProvider getAdapterClassesForDriver
     */
    public function testReturnValue(string $driver, string $expectedClass): void
    {
        if (
            $driver === 'apcu' && !ApcuAdapter::isSupported()
            || $driver === 'memcached' && !MemcachedAdapter::isSupported()
        ) {
            $this->markTestSkipped('apcu is not supported');
        }
        $this->assertTrue($this->cacheAdapterFactory->getCacheAdapter($driver) instanceof $expectedClass);
    }

    public function getAdapterClassesForDriver(): array
    {
        return [
            ['apcu', ApcuAdapter::class],
            ['memcached', MemcachedAdapter::class],
            ['array', ArrayAdapter::class],
            ['other', ArrayAdapter::class],
        ];
    }
}
