<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Context;
use Db;
use PHPUnit\Framework\TestCase;
use Product;
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
}
