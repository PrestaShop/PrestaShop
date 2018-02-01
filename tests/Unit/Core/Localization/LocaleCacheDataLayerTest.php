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
use PrestaShop\PrestaShop\Core\Localization\DataLayer\LocaleCacheDataLayer;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

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
        $cacheAdapter = $this->createMock(AdapterInterface::class);
        $cacheAdapter->method('getItem')
            ->willReturnMap([
                ['knownKey', $this->mockCacheItem('knownKey', true, 'known key result')],
                ['unknownKey', $this->mockCacheItem('unknownKey', false)],
            ]);
        /** @var AdapterInterface $cacheAdapter */
        $this->layer = new LocaleCacheDataLayer($cacheAdapter);
    }

    protected function mockCacheItem($key, $isHit, $value = null)
    {
        $cacheItem = $this->createMock(CacheItemInterface::class);
        $cacheItem->method('getKey')->willReturn($key);
        $cacheItem->method('isHit')->willReturn($isHit);
        $cacheItem->method('get')->willReturn($value);

        return $cacheItem;
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
        $this->assertSame(
            $expectedResult,
            $this->layer->read($key)
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
    }
}
