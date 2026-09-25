<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Product;

use Configuration;
use Context;
use Currency;
use Db;
use Product;
use Shop;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Product::getProductProperties() memoises the presented row, and that row carries per-shop values.
 * The key left the shop out, so presenting the same product for a second shop in one request returned
 * the first shop's row.
 */
class ProductPropertiesCachePerShopTest extends KernelTestCase
{
    private const PRODUCT_ID = 6;
    private const FIRST_SHOP_PRICE = 11.90;
    private const SECOND_SHOP_PRICE = 99.90;

    private int $secondShop = 0;

    private string $previousPrice = '';

    private string $previousCategory = '';

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $context = Context::getContext();
        $context->container = self::getContainer();
        $context->currency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
        $context->currentLocale = self::getContainer()
            ->get('prestashop.core.localization.locale.repository')
            ->getLocale($context->language->getLocale());

        $this->previousPrice = (string) Db::getInstance()->getValue(
            'SELECT price FROM ' . _DB_PREFIX_ . 'product_shop WHERE id_product = ' . self::PRODUCT_ID . ' AND id_shop = 1',
            false
        );
        $this->previousCategory = (string) Db::getInstance()->getValue(
            'SELECT id_category_default FROM ' . _DB_PREFIX_ . 'product_shop WHERE id_product = ' . self::PRODUCT_ID . ' AND id_shop = 1',
            false
        );

        Db::getInstance()->execute(
            'INSERT INTO ' . _DB_PREFIX_ . 'configuration (name, value, date_add, date_upd)
             VALUES ("PS_MULTISHOP_FEATURE_ACTIVE", 1, NOW(), NOW())
             ON DUPLICATE KEY UPDATE value = 1'
        );
        Shop::resetStaticCache();

        $shop = new Shop();
        $shop->active = true;
        $shop->id_shop_group = 1;
        $shop->id_category = 2;
        $shop->theme_name = _THEME_NAME_;
        $shop->name = 'product properties cache test shop';
        $shop->color = 'red';
        $shop->add();
        $this->secondShop = (int) $shop->id;
        Shop::resetStaticCache();

        Db::getInstance()->execute(
            'INSERT IGNORE INTO ' . _DB_PREFIX_ . 'product_shop
                (id_product, id_shop, id_category_default, price, active, visibility, id_tax_rules_group, indexed, date_add, date_upd)
             SELECT id_product, ' . $this->secondShop . ', id_category_default, price, active, visibility, id_tax_rules_group, indexed, date_add, date_upd
             FROM ' . _DB_PREFIX_ . 'product_shop WHERE id_product = ' . self::PRODUCT_ID . ' AND id_shop = 1'
        );
        $this->setShopPrice(1, self::FIRST_SHOP_PRICE);
        $this->setShopPrice($this->secondShop, self::SECOND_SHOP_PRICE);
    }

    protected function tearDown(): void
    {
        Shop::setContext(Shop::CONTEXT_ALL);
        Db::getInstance()->delete('stock_available', 'id_shop = ' . $this->secondShop);
        Db::getInstance()->delete('product_shop', 'id_shop = ' . $this->secondShop);
        Db::getInstance()->delete('shop_url', 'id_shop = ' . $this->secondShop);
        Db::getInstance()->delete('shop', 'id_shop = ' . $this->secondShop);
        Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'configuration WHERE id_shop = ' . $this->secondShop);
        Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'configuration WHERE name = "PS_MULTISHOP_FEATURE_ACTIVE"');
        Db::getInstance()->update(
            'product_shop',
            ['price' => $this->previousPrice, 'id_category_default' => $this->previousCategory],
            'id_product = ' . self::PRODUCT_ID . ' AND id_shop = 1'
        );
        Shop::resetStaticCache();
        Product::resetStaticCache();

        parent::tearDown();
    }

    public function testEachShopGetsItsOwnPresentedRow(): void
    {
        $this->assertSame(self::FIRST_SHOP_PRICE, $this->presentedPrice(1));
        $this->assertSame(self::SECOND_SHOP_PRICE, $this->presentedPrice($this->secondShop));
    }

    /**
     * And in the other order, so the assertion cannot pass just because the first shop happened to be
     * asked first.
     */
    public function testTheOrderOfTheShopsDoesNotMatter(): void
    {
        $this->assertSame(self::SECOND_SHOP_PRICE, $this->presentedPrice($this->secondShop));
        $this->assertSame(self::FIRST_SHOP_PRICE, $this->presentedPrice(1));
    }

    /**
     * A column that comes straight out of product_shop and is never recomputed, so it isolates the
     * static array from the separate cache getPriceStatic() keeps.
     */
    public function testAPlainPerShopColumnIsNotSharedEither(): void
    {
        Db::getInstance()->update('product_shop', ['id_category_default' => 3], 'id_product = ' . self::PRODUCT_ID . ' AND id_shop = 1');
        Db::getInstance()->update('product_shop', ['id_category_default' => 4], 'id_product = ' . self::PRODUCT_ID . ' AND id_shop = ' . $this->secondShop);

        $this->assertSame(3, $this->presentedField(1, 'id_category_default'));
        $this->assertSame(4, $this->presentedField($this->secondShop, 'id_category_default'));
    }

    /**
     * Documents which shop the presentation follows, because that is what the key has to match:
     * every per-shop read inside it goes through the context object - priceCalculation() at
     * Product.php:3515, computeUnitPriceRatio() at :5754, the tax-rules-group warmup at :5462 - and not
     * through Shop::getContextShopID(). Moving the shop context alone therefore changes nothing, and
     * the key must not pretend otherwise.
     */
    public function testThePresentationFollowsTheContextObjectNotTheShopContext(): void
    {
        $context = Context::getContext();
        $context->shop = new Shop(1);
        Shop::setContext(Shop::CONTEXT_SHOP, $this->secondShop);
        Product::resetStaticCache();

        $row = $this->rowFor(1);

        $this->assertSame(
            self::FIRST_SHOP_PRICE,
            (float) Product::getProductProperties((int) Configuration::get('PS_LANG_DEFAULT'), $row, $context)['price']
        );
    }

    private function setShopPrice(int $idShop, float $price): void
    {
        Db::getInstance()->update('product_shop', ['price' => $price], 'id_product = ' . self::PRODUCT_ID . ' AND id_shop = ' . $idShop);
    }

    private function presentedPrice(int $idShop): float
    {
        return (float) $this->present($idShop)['price'];
    }

    private function presentedField(int $idShop, string $field): int
    {
        return (int) $this->present($idShop)[$field];
    }

    /**
     * @return array<string, mixed>
     */
    private function present(int $idShop): array
    {
        $context = Context::getContext();
        Shop::setContext(Shop::CONTEXT_SHOP, $idShop);
        $context->shop = new Shop($idShop);

        return Product::getProductProperties(
            (int) Configuration::get('PS_LANG_DEFAULT'),
            $this->rowFor($idShop),
            $context
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function rowFor(int $idShop): array
    {
        return Db::getInstance()->getRow(
            'SELECT p.*, ps.* FROM ' . _DB_PREFIX_ . 'product p
             JOIN ' . _DB_PREFIX_ . 'product_shop ps ON ps.id_product = p.id_product AND ps.id_shop = ' . $idShop . '
             WHERE p.id_product = ' . self::PRODUCT_ID
        );
    }
}
