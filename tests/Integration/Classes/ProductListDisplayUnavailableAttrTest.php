<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Combination;
use Configuration;
use Db;
use PHPUnit\Framework\TestCase;
use Product;
use ProductAttribute;
use Tests\Integration\Utility\ContextMockerTrait;

/**
 * A product listing must honour "Display unavailable attributes"
 * (PS_DISP_UNAVAILABLE_ATTR): when it is off and the default combination is out
 * of stock, the list must surface an available combination — just like the
 * product page does — instead of the out-of-stock default. See issue #41558.
 */
class ProductListDisplayUnavailableAttrTest extends TestCase
{
    use ContextMockerTrait;

    private int $productId;
    private int $defaultCombinationId;
    private int $availableCombinationId;
    /** @var mixed */
    private $originalSetting;

    protected function setUp(): void
    {
        parent::setUp();
        self::mockContext();

        $this->originalSetting = Configuration::get('PS_DISP_UNAVAILABLE_ATTR');
        $idLang = (int) Configuration::get('PS_LANG_DEFAULT');
        $idShop = (int) Configuration::get('PS_SHOP_DEFAULT');

        $product = new Product(null, false, $idLang);
        $product->name = 'Test combo product 41558';
        $product->link_rewrite = 'test-combo-product-41558-' . uniqid();
        $product->price = 10.0;
        $product->active = true;
        $product->out_of_stock = 0; // deny ordering when out of stock
        $product->save();
        $this->productId = (int) $product->id;

        // Default combination is out of stock; the alternative is in stock.
        $this->defaultCombinationId = $this->addCombination($idShop, $this->attributeId('Size', 'S'), 0);
        $this->availableCombinationId = $this->addCombination($idShop, $this->attributeId('Size', 'M'), 10);

        $product->setDefaultAttribute($this->defaultCombinationId);
    }

    protected function tearDown(): void
    {
        Configuration::updateValue('PS_DISP_UNAVAILABLE_ATTR', $this->originalSetting);
        if (!empty($this->productId)) {
            (new Product($this->productId))->delete();
        }
        parent::tearDown();
    }

    public function testListSurfacesAvailableCombinationWhenUnavailableAttributesAreHidden(): void
    {
        Configuration::updateValue('PS_DISP_UNAVAILABLE_ATTR', 0);

        self::assertSame($this->availableCombinationId, $this->resolveListCombination());
    }

    public function testListKeepsDefaultCombinationWhenUnavailableAttributesAreShown(): void
    {
        Configuration::updateValue('PS_DISP_UNAVAILABLE_ATTR', 1);

        self::assertSame($this->defaultCombinationId, $this->resolveListCombination());
    }

    /**
     * Feeds Product::getProductProperties() a listing-style row (no explicit
     * id_product_attribute) and returns the combination it resolves.
     */
    private function resolveListCombination(): int
    {
        $idShop = (int) Configuration::get('PS_SHOP_DEFAULT');
        $row = Db::getInstance()->getRow('
            SELECT p.*, product_shop.*, p.id_product
            FROM ' . _DB_PREFIX_ . 'product p
            INNER JOIN ' . _DB_PREFIX_ . 'product_shop product_shop
                ON product_shop.id_product = p.id_product AND product_shop.id_shop = ' . $idShop . '
            WHERE p.id_product = ' . $this->productId);
        unset($row['id_product_attribute']);

        $result = Product::getProductProperties((int) Configuration::get('PS_LANG_DEFAULT'), $row);

        return (int) $result['id_product_attribute'];
    }

    private function addCombination(int $idShop, int $attributeId, int $quantity): int
    {
        $combination = new Combination();
        $combination->id_product = $this->productId;
        $combination->add();
        $combination->setAttributes([$attributeId]);
        $id = (int) $combination->id;

        $db = Db::getInstance();
        $db->execute('DELETE FROM ' . _DB_PREFIX_ . 'stock_available WHERE id_product = ' . $this->productId . ' AND id_product_attribute = ' . $id);
        $db->execute('
            INSERT INTO ' . _DB_PREFIX_ . 'stock_available
                (id_product, id_product_attribute, id_shop, id_shop_group, quantity, physical_quantity, reserved_quantity, depends_on_stock, out_of_stock, location)
            VALUES (' . $this->productId . ', ' . $id . ', ' . $idShop . ', 0, ' . $quantity . ', ' . $quantity . ', 0, 0, 0, "")');

        return $id;
    }

    private function attributeId(string $group, string $name): int
    {
        foreach (ProductAttribute::getAttributes((int) Configuration::get('PS_LANG_DEFAULT')) as $attribute) {
            if ($attribute['attribute_group'] === $group && $attribute['name'] === $name) {
                return (int) $attribute['id_attribute'];
            }
        }

        self::markTestSkipped(sprintf('Demo attribute %s:%s not available.', $group, $name));
    }
}
