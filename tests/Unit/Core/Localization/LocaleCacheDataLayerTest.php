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
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleData;
use PrestaShop\PrestaShop\Core\Localization\DataLayer\LocaleCacheDataLayer;
use Psr\Cache\CacheItemInterface;
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
     * @inheritDoc
     */
    protected function setUp()
    {
        // Let's use a real cache adapter (easier to setup, and a php array is always available in any environment)
        $cacheAdapter = new ArrayAdapter();

        // Prefill some cache values (for read test)
        $item = $cacheAdapter->getItem('knownKey');
        $item->set('known key result');
        $cacheAdapter->save($item);

        /** @var AdapterInterface $cacheAdapter */
        $this->layer = new LocaleCacheDataLayer($cacheAdapter);
    }

    /**
     * @dataProvider provideCacheReadResults
     *
     * @param $key
     *  Identifier of data to read
     *
     * @param $expectedResult
     *  Result of read
     *
     * @throws \PrestaShop\PrestaShop\Core\Data\Layer\DataLayerException
     */
    public function testRead($key, $expectedResult)
    {
        $cachedData = $this->layer->read($key);

        $this->assertSame(
            $expectedResult,
            $cachedData
        );
    }

    public function provideCacheReadResults()
    {
        return [
            "Known key"   => [
                'key'   => 'knownKey',
                'value' => 'known key result',
            ],
            "Unknown key" => [
                'key'   => 'unknownKey',
                'value' => null,
            ],
        ];
    }

    public function testWrite()
    {
        $data      = new LocaleData();
        $data->foo = ['bar', 'baz'];

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->layer->write('someKey', $data);
        /** @noinspection end */

        // Get value back from cache
        /** @noinspection PhpUnhandledExceptionInspection */
        $cachedData = $this->layer->read('someKey');
        /** @noinspection end */

        $this->assertInstanceOf(
            LocaleData::class,
            $cachedData
        );

        $this->assertSame(
            ['bar', 'baz'],
            $cachedData->foo
        );
    }

    protected function mockCacheItem($key, $isHit, $value = null)
    {
        $cacheItem = $this->createMock(CacheItemInterface::class);
        $cacheItem->method('getKey')->willReturn($key);
        $cacheItem->method('isHit')->willReturn($isHit);
        $cacheItem->method('get')->willReturn($value);
        $cacheItem->method('set')->willReturn(true);

        return $cacheItem;
    }
}
