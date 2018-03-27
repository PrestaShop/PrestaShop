<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Core\Localization\DataLayer;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Localization\Currency\CurrencyData as CurrencyData;
use PrestaShop\PrestaShop\Core\Localization\Currency\DataLayer\CurrencyCache as CurrencyCacheDataLayer;
use Symfony\Component\Cache\Adapter\AdapterInterface as CacheAdapterInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

class CurrencyCacheTest extends TestCase
{
    /**
     * The tested data layer
     *
     * @var CurrencyCacheDataLayer
     */
    protected $layer;

    protected function setUp()
    {
        // Let's use a real cache adapter (easier to setup, and a php array is always available in any environment)
        $cacheAdapter = new ArrayAdapter();

        /** @var CacheAdapterInterface $cacheAdapter */
        $this->layer = new CurrencyCacheDataLayer($cacheAdapter);
    }

    /**
     * Given a valid CurrencyCache data layer object
     * When asking it to write data and then read the same data
     * Then the said data should be retrieved unchanged
     */
    public function testReadWrite()
    {
        $data      = new CurrencyData();
        $data->foo = ['bar', 'baz'];

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->layer->write('fooBar', $data);
        /** @noinspection end */

        // Get value back from cache
        /** @noinspection PhpUnhandledExceptionInspection */
        $cachedData = $this->layer->read('fooBar');
        /** @noinspection end */

        $this->assertInstanceOf(
            CurrencyData::class,
            $cachedData
        );

        $this->assertSame(
            ['bar', 'baz'],
            $cachedData->foo
        );

        // Same test with unknown cache key
        /** @noinspection PhpUnhandledExceptionInspection */
        $cachedData = $this->layer->read('unknown');
        /** @noinspection end */

        $this->assertNull($cachedData);
    }
}
