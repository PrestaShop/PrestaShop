<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Product;

use Configuration;
use Context;
use Db;
use Product;
use SpecificPrice;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tools;

/**
 * Loading a product in full replaces $price with the final price for display, ecotax included. Writing
 * that back into the catalogue price made a plain new Product($id, true) followed by save() raise the
 * price by the ecotax on every call, compounding.
 */
class SaveKeepsStoredPriceTest extends KernelTestCase
{
    private const STORED_PRICE = 10.0;
    private const ECOTAX = 2.0;
    private const UNIT_PRICE = 5.0;

    private int $idProduct = 0;

    /** @var string|false */
    private $previousEcotaxSetting;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        Context::getContext()->container = self::getContainer();

        $this->previousEcotaxSetting = Configuration::get('PS_USE_ECOTAX');
        Configuration::updateValue('PS_USE_ECOTAX', 1);

        $lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $product = new Product();
        $product->id_category_default = 2;
        $product->name = [$lang => 'save keeps stored price'];
        $product->link_rewrite = [$lang => Tools::str2url('save keeps stored price')];
        $product->price = self::STORED_PRICE;
        $product->ecotax = self::ECOTAX;
        $product->unit_price = self::UNIT_PRICE;
        $product->id_tax_rules_group = 0;
        $product->add();
        $this->idProduct = (int) $product->id;
    }

    protected function tearDown(): void
    {
        if ($this->idProduct) {
            (new Product($this->idProduct))->delete();
            $this->idProduct = 0;
        }
        Configuration::updateValue('PS_USE_ECOTAX', $this->previousEcotaxSetting);
        Product::resetStaticCache();

        parent::tearDown();
    }

    public function testSavingAFullyLoadedProductDoesNotRaiseItsPrice(): void
    {
        foreach ([1, 2, 3] as $pass) {
            Product::resetStaticCache();
            (new Product($this->idProduct, true, (int) Configuration::get('PS_LANG_DEFAULT')))->save();

            $this->assertSame(
                self::STORED_PRICE,
                $this->storedPrice(),
                sprintf('the catalogue price moved on save number %d', $pass)
            );
        }
    }

    public function testTheUnitPriceRatioStaysDerivedFromTheStoredPrice(): void
    {
        Product::resetStaticCache();
        (new Product($this->idProduct, true, (int) Configuration::get('PS_LANG_DEFAULT')))->save();

        // Ecotax is on, so the ratio is (price + ecotax) / unit_price.
        $this->assertSame(
            (self::STORED_PRICE + self::ECOTAX) / self::UNIT_PRICE,
            (float) $this->storedColumn('unit_price_ratio')
        );
    }

    public function testTheSavedObjectAgreesWithTheRowItWrote(): void
    {
        Product::resetStaticCache();
        $product = new Product($this->idProduct, true, (int) Configuration::get('PS_LANG_DEFAULT'));
        $product->save();

        $this->assertSame(
            (float) $this->storedColumn('unit_price_ratio'),
            (float) $product->unit_price_ratio,
            'the object must not report a different ratio from the one it stored'
        );
    }

    /**
     * The constructor's price also has reductions applied, so a specific price used to be baked in the
     * same way - downwards, and compounding.
     */
    public function testASpecificPriceIsNotBakedIntoTheStoredPrice(): void
    {
        $specificPrice = new SpecificPrice();
        $specificPrice->id_product = $this->idProduct;
        $specificPrice->id_shop = 0;
        $specificPrice->id_shop_group = 0;
        $specificPrice->id_currency = 0;
        $specificPrice->id_country = 0;
        $specificPrice->id_group = 0;
        $specificPrice->id_customer = 0;
        $specificPrice->id_product_attribute = 0;
        $specificPrice->price = -1.0;
        $specificPrice->from_quantity = 1;
        $specificPrice->reduction = 0.5;
        $specificPrice->reduction_type = 'percentage';
        $specificPrice->reduction_tax = 0;
        $specificPrice->from = '0000-00-00 00:00:00';
        $specificPrice->to = '0000-00-00 00:00:00';
        $specificPrice->add();

        try {
            foreach ([1, 2, 3] as $pass) {
                Product::resetStaticCache();
                SpecificPrice::flushCache();
                (new Product($this->idProduct, true, (int) Configuration::get('PS_LANG_DEFAULT')))->save();

                $this->assertSame(
                    self::STORED_PRICE,
                    $this->storedPrice(),
                    sprintf('the catalogue price moved on save number %d', $pass)
                );
            }
        } finally {
            $specificPrice->delete();
            SpecificPrice::flushCache();
        }
    }

    public function testAPriceTheCallerAssignedIsStillSaved(): void
    {
        Product::resetStaticCache();
        $product = new Product($this->idProduct, true, (int) Configuration::get('PS_LANG_DEFAULT'));
        $product->price = 42.0;
        $product->save();

        $this->assertSame(42.0, $this->storedPrice());
    }

    /**
     * A product loaded without the full flag never gets a computed price, so nothing is restored.
     */
    public function testAPartiallyLoadedProductIsUnaffected(): void
    {
        Product::resetStaticCache();
        $product = new Product($this->idProduct);
        $product->save();

        $this->assertSame(self::STORED_PRICE, $this->storedPrice());
    }

    private function storedPrice(): float
    {
        return (float) $this->storedColumn('price');
    }

    private function storedColumn(string $column): string
    {
        return (string) Db::getInstance()->getValue(
            'SELECT `' . $column . '` FROM ' . _DB_PREFIX_ . 'product WHERE id_product = ' . $this->idProduct,
            false
        );
    }
}
