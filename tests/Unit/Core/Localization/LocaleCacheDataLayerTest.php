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

namespace Tests\Unit\Core\Localization;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Localization\CLDR\DataLayer\LocaleCache;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleData;
use PrestaShop\PrestaShop\Core\Localization\DataLayer\LocaleCacheDataLayer;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

class LocaleCacheDataLayerTest extends TestCase
{
    /**
     * The tested data layer
     *
     * @var LocaleCacheDataLayer
     */
    protected $layer;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        // Let's use a real cache adapter (easier to setup, and a php array is always available in any environment)
        $cacheAdapter = new ArrayAdapter();

        /* @var AdapterInterface $cacheAdapter */
        $this->layer = new LocaleCache($cacheAdapter);
    }

    public function testReadWrite()
    {
        $data = new LocaleData();
        $data->foo = ['bar', 'baz'];

        /* @noinspection PhpUnhandledExceptionInspection */
        $this->layer->write('fooBar', $data);
        /** @noinspection end */

        // Get value back from cache
        /** @noinspection PhpUnhandledExceptionInspection */
        $cachedData = $this->layer->read('fooBar');
        /* @noinspection end */

        $this->assertInstanceOf(
            LocaleData::class,
            $cachedData
        );

        $this->assertSame(
            ['bar', 'baz'],
            $cachedData->foo
        );

        // Same test with unknown cache key
        /** @noinspection PhpUnhandledExceptionInspection */
        $cachedData = $this->layer->read('unknown');
        /* @noinspection end */

        $this->assertNull($cachedData);
    }
}
