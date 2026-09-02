<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Localization\DataLayer;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Localization\Currency\CurrencyData;
use PrestaShop\PrestaShop\Core\Localization\Currency\DataLayer\CurrencyCache as CurrencyCacheDataLayer;
use PrestaShop\PrestaShop\Core\Localization\Currency\LocalizedCurrencyId;
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

    protected function setUp(): void
    {
        // Let's use a real cache adapter (easier to setup, and a php array is always available in any environment)
        $cacheAdapter = new ArrayAdapter();

        /* @var CacheAdapterInterface $cacheAdapter */
        $this->layer = new CurrencyCacheDataLayer($cacheAdapter);
    }

    /**
     * Given a valid CurrencyCache data layer object
     * When asking it to write data and then read the same data
     * Then the said data should be retrieved unchanged
     */
    public function testReadWrite()
    {
        $data = new CurrencyData();

        /* @noinspection PhpUnhandledExceptionInspection */
        $this->layer->write(new LocalizedCurrencyId('foo', 'bar'), $data);
        /** @noinspection end */

        // Get value back from cache
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @phpstan-ignore-next-line */
        $cachedData = $this->layer->read(new LocalizedCurrencyId('foo', 'bar'));
        /* @noinspection end */

        $this->assertInstanceOf(
            CurrencyData::class,
            $cachedData
        );

        // Same test with unknown cache key
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @phpstan-ignore-next-line */
        $cachedData = $this->layer->read(new LocalizedCurrencyId('unknown', 'unknown'));
        /* @noinspection end */

        $this->assertNull($cachedData);
    }
}
