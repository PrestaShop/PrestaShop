<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Cart;
use Configuration;
use Context;
use Country;
use Currency;
use Customer;
use Db;
use Language;
use Product;
use ProductAssembler;
use ReflectionProperty;
use Shop;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

class ProductCombinationUnitPriceCacheTest extends KernelTestCase
{
    private const TABLES_TO_RESTORE = ['product', 'product_shop', 'product_attribute', 'product_attribute_shop'];

    protected function setUp(): void
    {
        parent::setUp();
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        Product::resetStaticCache();
        self::bootKernel();
    }

    protected function tearDown(): void
    {
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        Product::resetStaticCache();
        parent::tearDown();
    }

    public function testUnitPriceRatioMatchesWithPrefetchedCombinationData(): void
    {
        $db = Db::getInstance();

        $db->update(
            'product',
            ['price' => 20.000000, 'unit_price' => 10.000000],
            'id_product = 1'
        );
        $db->update(
            'product_shop',
            ['price' => 20.000000, 'unit_price' => 10.000000],
            'id_product = 1 AND id_shop = 1'
        );
        $db->update(
            'product_attribute',
            ['price' => 5.000000, 'unit_price_impact' => 2.500000],
            'id_product_attribute = 1'
        );
        $db->update(
            'product_attribute_shop',
            ['price' => 5.000000, 'unit_price_impact' => 2.500000],
            'id_product_attribute = 1 AND id_shop = 1'
        );

        $context = Context::getContext();
        $context->shop = new Shop(1);
        $context->language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $context->currency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
        $context->country = new Country((int) Configuration::get('PS_COUNTRY_DEFAULT'));
        if (!$context->cart) {
            $context->cart = new Cart();
        }
        $context->customer = new Customer();

        Product::flushPriceCache();
        Product::cacheCombinationUnitPriceData([1], 1);

        $prop = new ReflectionProperty(Product::class, 'combinationUnitPriceCache');
        $cache = $prop->getValue();
        $this->assertArrayHasKey('1-1', $cache);
        $this->assertEqualsWithDelta(5.0, $cache['1-1']['price'], 0.0001);
        $this->assertEqualsWithDelta(2.5, $cache['1-1']['unit_price_impact'], 0.0001);

        $assembled = (new ProductAssembler($context))->assembleProducts([
            ['id_product' => 1, 'id_product_attribute' => 1],
        ]);

        // baseProductPrice (20 + 5) / baseUnitPrice (10 + 2.5) = 25 / 12.5 = 2.0
        $this->assertCount(1, $assembled);
        $this->assertEqualsWithDelta(2.0, (float) $assembled[0]['unit_price_ratio'], 0.0001);

        Product::flushPriceCache();
        $this->assertSame([], $prop->getValue());
    }
}
