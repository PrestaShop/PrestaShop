<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Db;
use ReflectionProperty;
use SpecificPrice;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SpecificPriceCacheTest extends KernelTestCase
{
    private const TEST_CUSTOMER_ID = 999420;
    private const PRODUCT_WITH_PRICE_ID = 999421;
    private const PRODUCT_WITHOUT_PRICE_ID = 999422;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->cleanUpTestData();
    }

    protected function tearDown(): void
    {
        $this->cleanUpTestData();
        parent::tearDown();
    }

    public function testCacheCouldHaveSpecificPricePopulatesCacheForPresentAndMissingProducts(): void
    {
        Db::getInstance()->insert('specific_price', [
            'id_specific_price_rule' => 0,
            'id_cart' => 0,
            'id_product' => self::PRODUCT_WITH_PRICE_ID,
            'id_shop' => 0,
            'id_shop_group' => 0,
            'id_currency' => 0,
            'id_country' => 0,
            'id_group' => 0,
            'id_customer' => self::TEST_CUSTOMER_ID,
            'id_product_attribute' => 0,
            'price' => -1,
            'from_quantity' => 1,
            'reduction' => 0.150000,
            'reduction_tax' => 1,
            'reduction_type' => 'percentage',
            'from' => '0000-00-00 00:00:00',
            'to' => '0000-00-00 00:00:00',
        ]);

        SpecificPrice::flushCache();
        SpecificPrice::cacheCouldHaveSpecificPrice([
            self::PRODUCT_WITH_PRICE_ID,
            self::PRODUCT_WITHOUT_PRICE_ID,
        ]);

        $this->assertFalse($this->getHasGlobalProductRules());
        $cache = $this->getCouldHaveSpecificPriceCache();
        $this->assertArrayHasKey(self::PRODUCT_WITH_PRICE_ID, $cache);
        $this->assertArrayHasKey(self::PRODUCT_WITHOUT_PRICE_ID, $cache);
        $this->assertTrue($cache[self::PRODUCT_WITH_PRICE_ID]);
        $this->assertFalse($cache[self::PRODUCT_WITHOUT_PRICE_ID]);

        $specificPrice = SpecificPrice::getSpecificPrice(
            self::PRODUCT_WITH_PRICE_ID,
            1,
            1,
            1,
            1,
            1,
            null,
            self::TEST_CUSTOMER_ID
        );
        $this->assertNotEmpty($specificPrice);
        $this->assertSame([], SpecificPrice::getSpecificPrice(self::PRODUCT_WITHOUT_PRICE_ID, 1, 1, 1, 1, 1));
    }

    public function testCacheCouldHaveSpecificPriceDetectsGlobalRuleAndFlushCacheResetsIt(): void
    {
        Db::getInstance()->insert('specific_price', [
            'id_specific_price_rule' => 0,
            'id_cart' => 0,
            'id_product' => 0,
            'id_shop' => 0,
            'id_shop_group' => 0,
            'id_currency' => 0,
            'id_country' => 0,
            'id_group' => 0,
            'id_customer' => self::TEST_CUSTOMER_ID,
            'id_product_attribute' => 0,
            'price' => -1,
            'from_quantity' => 1,
            'reduction' => 0.100000,
            'reduction_tax' => 1,
            'reduction_type' => 'percentage',
            'from' => '0000-00-00 00:00:00',
            'to' => '0000-00-00 00:00:00',
        ]);

        SpecificPrice::flushCache();
        SpecificPrice::cacheCouldHaveSpecificPrice([
            self::PRODUCT_WITHOUT_PRICE_ID,
        ]);

        $this->assertTrue($this->getHasGlobalProductRules());
        $this->assertSame([], $this->getCouldHaveSpecificPriceCache());

        Db::getInstance()->delete(
            'specific_price',
            'id_customer = ' . self::TEST_CUSTOMER_ID . ' AND id_product = 0'
        );
        SpecificPrice::flushCache();
        $this->assertNull($this->getHasGlobalProductRules());

        SpecificPrice::cacheCouldHaveSpecificPrice([
            self::PRODUCT_WITHOUT_PRICE_ID,
        ]);
        $this->assertFalse($this->getHasGlobalProductRules());
        $cache = $this->getCouldHaveSpecificPriceCache();
        $this->assertFalse($cache[self::PRODUCT_WITHOUT_PRICE_ID]);
    }

    public function testCacheCouldHaveSpecificPriceHandlesEmptyInput(): void
    {
        SpecificPrice::flushCache();
        SpecificPrice::cacheCouldHaveSpecificPrice([]);

        $this->assertNull($this->getHasGlobalProductRules());
        $this->assertSame([], $this->getCouldHaveSpecificPriceCache());
    }

    private function cleanUpTestData(): void
    {
        Db::getInstance()->delete(
            'specific_price',
            'id_customer = ' . self::TEST_CUSTOMER_ID
        );
        SpecificPrice::flushCache();
    }

    private function getHasGlobalProductRules(): ?bool
    {
        $prop = new ReflectionProperty(SpecificPrice::class, '_hasGlobalProductRules');
        $value = $prop->getValue();

        return is_bool($value) ? $value : null;
    }

    /**
     * @return array<int, bool>
     */
    private function getCouldHaveSpecificPriceCache(): array
    {
        $prop = new ReflectionProperty(SpecificPrice::class, '_couldHaveSpecificPriceCache');
        $value = $prop->getValue();

        return is_array($value) ? $value : [];
    }
}
