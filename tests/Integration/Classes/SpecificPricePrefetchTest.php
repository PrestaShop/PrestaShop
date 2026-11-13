<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Configuration;
use Db;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use ReflectionProperty;
use SpecificPrice;

class SpecificPricePrefetchTest extends TestCase
{
    private int $productWithSpecificPrice;
    private int $productWithoutSpecificPrice;
    /** @var mixed */
    private $originalFeatureActive;
    private int $globalSpecificPriceId = 0;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalFeatureActive = Configuration::get('PS_SPECIFIC_PRICE_FEATURE_ACTIVE');
        Configuration::updateGlobalValue('PS_SPECIFIC_PRICE_FEATURE_ACTIVE', '1');

        $this->productWithSpecificPrice = (int) Db::getInstance()->getValue(
            'SELECT `id_product` FROM `' . _DB_PREFIX_ . 'specific_price` WHERE `id_product` > 0'
        );
        $this->productWithoutSpecificPrice = (int) Db::getInstance()->getValue(
            'SELECT p.`id_product` FROM `' . _DB_PREFIX_ . 'product` p
            WHERE NOT EXISTS (SELECT 1 FROM `' . _DB_PREFIX_ . 'specific_price` sp WHERE sp.`id_product` = p.`id_product`)'
        );
        self::assertGreaterThan(0, $this->productWithSpecificPrice);
        self::assertGreaterThan(0, $this->productWithoutSpecificPrice);
        self::assertSame(
            0,
            (int) Db::getInstance()->getValue('SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'specific_price` WHERE `id_product` = 0')
        );

        SpecificPrice::flushCache();
    }

    protected function tearDown(): void
    {
        if ($this->globalSpecificPriceId) {
            Db::getInstance()->delete('specific_price', '`id_specific_price` = ' . $this->globalSpecificPriceId);
        }
        Configuration::updateGlobalValue('PS_SPECIFIC_PRICE_FEATURE_ACTIVE', $this->originalFeatureActive);
        SpecificPrice::flushCache();
        parent::tearDown();
    }

    public function testPrefetchMatchesPerProductResolution(): void
    {
        $productIds = [$this->productWithSpecificPrice, $this->productWithoutSpecificPrice];
        $expected = [];
        foreach ($productIds as $productId) {
            $expected[$productId] = $this->couldHaveSpecificPrice($productId);
        }
        self::assertSame(
            [$this->productWithSpecificPrice => true, $this->productWithoutSpecificPrice => false],
            $expected
        );

        SpecificPrice::flushCache();
        SpecificPrice::prefetchCouldHaveSpecificPrice(array_map('strval', $productIds));

        self::assertSame($expected, $this->getCouldHaveSpecificPriceCache());
    }

    public function testPrefetchLoadsNothingWhenSpecificPricesAreNotUsed(): void
    {
        Configuration::updateGlobalValue('PS_SPECIFIC_PRICE_FEATURE_ACTIVE', '0');

        SpecificPrice::prefetchCouldHaveSpecificPrice([$this->productWithSpecificPrice, $this->productWithoutSpecificPrice]);

        self::assertSame([], $this->getCouldHaveSpecificPriceCache());
    }

    public function testPrefetchLoadsNothingWhenARuleAppliesToEveryProduct(): void
    {
        Db::getInstance()->insert('specific_price', [
            'id_product' => 0,
            'id_shop' => 0,
            'price' => -1,
            'from_quantity' => 1,
            'reduction' => 0.1,
            'reduction_tax' => 1,
            'reduction_type' => 'percentage',
            'from' => '1970-01-01 00:00:00',
            'to' => '1970-01-01 00:00:00',
        ]);
        $this->globalSpecificPriceId = (int) Db::getInstance()->Insert_ID();

        SpecificPrice::prefetchCouldHaveSpecificPrice([$this->productWithSpecificPrice, $this->productWithoutSpecificPrice]);

        self::assertSame([], $this->getCouldHaveSpecificPriceCache());
        self::assertTrue($this->couldHaveSpecificPrice($this->productWithoutSpecificPrice));
    }

    private function couldHaveSpecificPrice(int $productId): bool
    {
        $method = new ReflectionMethod(SpecificPrice::class, 'couldHaveSpecificPrice');
        $method->setAccessible(true);

        return $method->invoke(null, $productId);
    }

    /**
     * @return array<int, bool>
     */
    private function getCouldHaveSpecificPriceCache(): array
    {
        $property = new ReflectionProperty(SpecificPrice::class, '_couldHaveSpecificPriceCache');
        $property->setAccessible(true);

        return $property->getValue();
    }
}
