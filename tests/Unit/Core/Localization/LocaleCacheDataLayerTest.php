<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Localization;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Localization\CLDR\DataLayer\LocaleCache;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleData;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

class LocaleCacheDataLayerTest extends TestCase
{
    /**
     * The tested data layer
     *
     * @var LocaleCache
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
        $data->setLocaleCode('fr');

        $this->layer->write('fooBar', $data);

        // Get value back from cache
        $cachedData = $this->layer->read('fooBar');

        $this->assertInstanceOf(
            LocaleData::class,
            $cachedData
        );

        $this->assertSame(
            'fr',
            $cachedData->getLocaleCode()
        );

        // Same test with unknown cache key
        $cachedData = $this->layer->read('unknown');

        $this->assertNull($cachedData);
    }
}
