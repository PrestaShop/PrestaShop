<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Configuration;
use Context;
use Db;
use PHPUnit\Framework\TestCase;
use Product;
use StockAvailable;
use Tests\Integration\Utility\ContextMockerTrait;

class ProductTest extends TestCase
{
    use ContextMockerTrait;

    protected function setUp(): void
    {
        parent::setUp();
        self::mockContext();
    }

    public function testSaveActiveRecordStyle(): void
    {
        $product = new Product(null, false, 1);
        $product->name = 'A Product';
        $product->price = 42.42;
        $product->link_rewrite = 'a-product';
        $this->assertTrue($product->save());
    }

    /**
     * The color swatches shown on product miniatures must point to the default combination
     * of their color group, not an arbitrary sibling (issue #35481): product 1 ("Hummingbird
     * printed t-shirt") has the default combination "S, White", so the white swatch must link
     * to it and not, for example, to "M, White".
     */
    public function testGetAttributesColorListPointsToDefaultCombination(): void
    {
        $idShop = (int) Context::getContext()->shop->id;

        $defaultIdProductAttribute = (int) Db::getInstance()->getValue(
            'SELECT `id_product_attribute` FROM `' . _DB_PREFIX_ . 'product_attribute_shop`
             WHERE `id_product` = 1 AND `id_shop` = ' . $idShop . ' AND `default_on` = 1'
        );
        $this->assertGreaterThan(0, $defaultIdProductAttribute);

        $colorList = Product::getAttributesColorList([1]);
        $this->assertArrayHasKey(1, $colorList);

        $idProductAttributes = array_map(
            static fn (array $color): int => (int) $color['id_product_attribute'],
            $colorList[1]
        );

        $this->assertContains(
            $defaultIdProductAttribute,
            $idProductAttributes,
            'The color swatch list must point to the default combination of its color group.'
        );
    }

    /**
     * When unavailable attributes are hidden, a swatch must not point to an out-of-stock
     * combination: the product page cannot select it and falls back to another color.
     * Product 1 with "S, White" (default) out of stock: the white swatch points to an
     * in-stock white combination.
     */
    public function testGetAttributesColorListSkipsOutOfStockDefaultWhenUnavailableAttributesAreHidden(): void
    {
        $db = Db::getInstance();
        $idShop = (int) Context::getContext()->shop->id;
        $defaultIdProductAttribute = (int) $db->getValue(
            'SELECT `id_product_attribute` FROM `' . _DB_PREFIX_ . 'product_attribute_shop`
             WHERE `id_product` = 1 AND `id_shop` = ' . $idShop . ' AND `default_on` = 1'
        );
        $this->assertGreaterThan(0, $defaultIdProductAttribute);
        $whiteIdAttribute = (int) $db->getValue(
            'SELECT `id_attribute` FROM `' . _DB_PREFIX_ . 'product_attribute_combination` pac
             JOIN `' . _DB_PREFIX_ . 'attribute` a USING (`id_attribute`)
             JOIN `' . _DB_PREFIX_ . 'attribute_group` ag USING (`id_attribute_group`)
             WHERE pac.`id_product_attribute` = ' . $defaultIdProductAttribute . ' AND ag.`is_color_group` = 1'
        );
        $this->assertGreaterThan(0, $whiteIdAttribute);

        $previousDisplay = Configuration::get('PS_DISP_UNAVAILABLE_ATTR');
        $previousQuantity = StockAvailable::getQuantityAvailableByProduct(1, $defaultIdProductAttribute);
        Configuration::updateValue('PS_DISP_UNAVAILABLE_ATTR', 0);
        StockAvailable::setQuantity(1, $defaultIdProductAttribute, 0);
        try {
            $swatches = array_column(Product::getAttributesColorList([1])[1] ?? [], 'id_product_attribute', 'id_attribute');
        } finally {
            StockAvailable::setQuantity(1, $defaultIdProductAttribute, $previousQuantity);
            Configuration::updateValue('PS_DISP_UNAVAILABLE_ATTR', $previousDisplay);
        }

        $this->assertArrayHasKey($whiteIdAttribute, $swatches);
        $this->assertNotSame($defaultIdProductAttribute, (int) $swatches[$whiteIdAttribute]);
        $this->assertGreaterThan(0, StockAvailable::getQuantityAvailableByProduct(1, (int) $swatches[$whiteIdAttribute]));
        $this->assertSame(1, (int) $db->getValue(
            'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'product_attribute_combination`
             WHERE `id_product_attribute` = ' . (int) $swatches[$whiteIdAttribute] . ' AND `id_attribute` = ' . $whiteIdAttribute
        ), 'The white swatch must point to a white combination.');
    }
}
