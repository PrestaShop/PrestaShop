<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Localization\CLDR\DataLayer;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Localization\CLDR\CurrencyData as CldrCurrencyData;
use PrestaShop\PrestaShop\Core\Localization\CLDR\DataLayer\CurrencyCache as CldrCurrencyCacheDataLayer;
use Symfony\Component\Cache\Adapter\AdapterInterface as CacheAdapterInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

class CurrencyCacheTest extends TestCase
{
    /**
     * The tested data layer
     *
     * @var CldrCurrencyCacheDataLayer
     */
    protected $layer;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        // Let's use a real cache adapter (easier to setup, and a php array is always available in any environment)
        $cacheAdapter = new ArrayAdapter();

        /* @var CacheAdapterInterface $cacheAdapter */
        $this->layer = new CldrCurrencyCacheDataLayer($cacheAdapter);
    }

    public function testReadWrite()
    {
        $data = new CldrCurrencyData();

        $this->layer->write('fooBar', $data);

        $cachedData = $this->layer->read('fooBar');

        $this->assertInstanceOf(
            CldrCurrencyData::class,
            $cachedData
        );

        self::assertEquals($data, $cachedData);

        // Same test with unknown cache key
        $cachedData = $this->layer->read('unknown');

        $this->assertNull($cachedData);
    }
}
