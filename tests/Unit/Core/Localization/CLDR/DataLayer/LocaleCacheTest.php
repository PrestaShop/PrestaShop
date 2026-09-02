<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Localization\CLDR\DataLayer;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Localization\CLDR\DataLayer\LocaleCache as CldrLocaleCacheDataLayer;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleData as CldrLocaleData;
use Symfony\Component\Cache\Adapter\AdapterInterface as CacheAdapterInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

class LocaleCacheTest extends TestCase
{
    /**
     * The tested data layer
     *
     * @var CldrLocaleCacheDataLayer
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
        $this->layer = new CldrLocaleCacheDataLayer($cacheAdapter);
    }

    /**
     * Given a valid CLDR LocaleCache data layer object
     * When asking it to write data and then read the same data
     * Then the said data should be retrieved unchanged
     */
    public function testReadWrite()
    {
        $data = new CldrLocaleData();

        $this->layer->write('fooBar', $data);

        // Get value back from cache
        $cachedData = $this->layer->read('fooBar');

        $this->assertInstanceOf(
            CldrLocaleData::class,
            $cachedData
        );

        // Same test with unknown cache key
        $cachedData = $this->layer->read('unknown');

        $this->assertNull($cachedData);
    }
}
