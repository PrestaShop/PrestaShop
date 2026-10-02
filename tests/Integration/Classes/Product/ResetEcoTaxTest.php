<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Product;

use Db;
use PHPUnit\Framework\TestCase;
use Product;
use Tests\Resources\DatabaseDump;

/**
 * Switching "Enable ecotax" off runs Product::resetEcoTax(). Product::priceCalculation() prefers a
 * combination's ecotax over the product's, so anything left on product_attribute keeps being charged
 * after the feature is off - and the back office hides the inputs then, so nothing can clear it.
 */
class ResetEcoTaxTest extends TestCase
{
    private const PRODUCT_ECOTAX = 3.0;
    private const COMBINATION_ECOTAX = 7.0;

    private int $productId;
    private int $combinationId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productId = (int) Db::getInstance()->getValue(
            'SELECT id_product FROM ' . _DB_PREFIX_ . 'product_attribute ORDER BY id_product'
        );
        $this->combinationId = (int) Db::getInstance()->getValue(
            'SELECT id_product_attribute FROM ' . _DB_PREFIX_ . 'product_attribute WHERE id_product = '
            . $this->productId . ' ORDER BY id_product_attribute'
        );

        $this->setEcotax('product', 'id_product', $this->productId, self::PRODUCT_ECOTAX);
        $this->setEcotax('product_shop', 'id_product', $this->productId, self::PRODUCT_ECOTAX);
        $this->setEcotax('product_attribute', 'id_product_attribute', $this->combinationId, self::COMBINATION_ECOTAX);
        $this->setEcotax('product_attribute_shop', 'id_product_attribute', $this->combinationId, self::COMBINATION_ECOTAX);
    }

    protected function tearDown(): void
    {
        DatabaseDump::restoreTables(['product', 'product_shop', 'product_attribute', 'product_attribute_shop']);

        parent::tearDown();
    }

    public function testItClearsTheEcotaxOnProductsAndOnTheirCombinations(): void
    {
        // Guards against a fixture that was already zero, which would make every assertion vacuous.
        $this->assertSame(self::PRODUCT_ECOTAX, $this->ecotaxOf('product', 'id_product', $this->productId));
        $this->assertSame(self::COMBINATION_ECOTAX, $this->ecotaxOf('product_attribute', 'id_product_attribute', $this->combinationId));

        $this->assertTrue(Product::resetEcoTax());

        $this->assertSame(0.0, $this->ecotaxOf('product', 'id_product', $this->productId));
        $this->assertSame(0.0, $this->ecotaxOf('product_shop', 'id_product', $this->productId));
        $this->assertSame(0.0, $this->ecotaxOf('product_attribute', 'id_product_attribute', $this->combinationId));
        $this->assertSame(0.0, $this->ecotaxOf('product_attribute_shop', 'id_product_attribute', $this->combinationId));
    }

    /**
     * The symptom the reset exists to prevent: the combination stays priced with an ecotax the
     * merchant has switched off and can no longer edit.
     */
    public function testTheCombinationIsNoLongerPricedWithTheEcotaxAfterTheReset(): void
    {
        $withEcotax = $this->combinationPrice();

        Product::resetEcoTax();
        $afterReset = $this->combinationPrice();

        $this->assertEqualsWithDelta(self::COMBINATION_ECOTAX, $withEcotax - $afterReset, 0.01);
    }

    private function combinationPrice(): float
    {
        Product::flushPriceCache();
        $specificPrice = null;

        return (float) Product::getPriceStatic(
            $this->productId,
            false,
            $this->combinationId,
            6,
            null,
            false,
            false,
            1,
            false,
            null,
            null,
            null,
            $specificPrice,
            true
        );
    }

    private function setEcotax(string $table, string $key, int $id, float $value): void
    {
        Db::getInstance()->execute(
            'UPDATE ' . _DB_PREFIX_ . $table . ' SET ecotax = ' . $value . ' WHERE ' . $key . ' = ' . $id
        );
    }

    private function ecotaxOf(string $table, string $key, int $id): float
    {
        return (float) Db::getInstance()->getValue(
            'SELECT ecotax FROM ' . _DB_PREFIX_ . $table . ' WHERE ' . $key . ' = ' . $id
        );
    }
}
