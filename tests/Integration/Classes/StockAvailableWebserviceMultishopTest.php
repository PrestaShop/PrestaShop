<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Combination;
use Configuration as LegacyConfiguration;
use Context;
use Db;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShopBundle\Entity\Shop;
use PrestaShopBundle\Entity\ShopGroup as ShopGroupEntity;
use Product;
use Shop as LegacyShop;
use StockAvailable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;
use WebserviceRequest;

/**
 * Updating a combination's stock_available via the legacy Webservice (PUT) on a multishop install
 * with shared stock must keep the combination's stock readable for the whole group. Before the
 * fix the Webservice overwrote the shared row's id_shop (0 -> a concrete shop), which detached it
 * from the group's shared-stock lookups so the quantity read 0 everywhere and the combination
 * looked like it had vanished from the product.
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/38049
 *
 * @group webservice
 */
class StockAvailableWebserviceMultishopTest extends KernelTestCase
{
    private const ID_GROUP = 1;
    private const WS_KEY = 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA'; // 32 chars

    private static int $idShop2;
    private static int $idProduct;
    private static int $idProductAttribute;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$idShop2 = self::initMultistore();
        self::createProductWithCombination();
        self::createWebserviceKey();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreAllTables();
        LegacyShop::resetStaticCache();
        LegacyConfiguration::resetStaticCache();
    }

    public function testUpdatingSharedCombinationStockViaWebserviceKeepsItVisibleInTheGroup(): void
    {
        $idStockAvailable = (int) Db::getInstance()->getValue(
            'SELECT id_stock_available FROM ' . _DB_PREFIX_ . 'stock_available
             WHERE id_product = ' . self::$idProduct . '
             AND id_product_attribute = ' . self::$idProductAttribute
        );
        $this->assertGreaterThan(0, $idStockAvailable, 'precondition: combination stock row exists');

        // Precondition: the combination's shared stock is readable from both shops of the group.
        $this->assertSame(5, $this->availableInShop(1), 'precondition: stock readable in shop 1');
        $this->assertSame(5, $this->availableInShop(self::$idShop2), 'precondition: stock readable in shop 2');

        // A bog-standard stock update sent through the Webservice, carrying the id_shop /
        // id_shop_group exactly as the reporter does in #38049.
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
<prestashop xmlns:xlink="http://www.w3.org/1999/xlink">
    <stock_available>
        <id>' . $idStockAvailable . '</id>
        <id_product>' . self::$idProduct . '</id_product>
        <id_product_attribute>' . self::$idProductAttribute . '</id_product_attribute>
        <id_shop>1</id_shop>
        <id_shop_group>' . self::ID_GROUP . '</id_shop_group>
        <quantity>50</quantity>
        <depends_on_stock>0</depends_on_stock>
        <out_of_stock>0</out_of_stock>
    </stock_available>
</prestashop>';

        $result = $this->callWebservice('PUT', ['id_shop' => 1, 'id_shop_group' => self::ID_GROUP], $xml);

        // Discriminating check (avoid a meaningless pass): the PUT must really have run.
        $this->assertStringNotContainsStringIgnoringCase(
            '<error',
            $result,
            "The Webservice PUT failed instead of running:\n" . $result
        );

        // The combination row itself must never be dropped by a stock update.
        $this->assertSame(
            1,
            $this->countCombinations(),
            'The combination must survive a stock_available update via Webservice'
        );

        // Core of #38049: after the update the combination's stock must stay readable for the
        // whole group (the new quantity). Before the fix the Webservice overwrote the shared
        // row's id_shop (0 -> 1), so the shared-stock lookup no longer matched it and the
        // combination's stock read 0 everywhere — i.e. the combination "disappeared".
        $this->assertSame(50, $this->availableInShop(1), 'stock must stay readable in shop 1 after the update');
        $this->assertSame(50, $this->availableInShop(self::$idShop2), 'stock must stay readable in shop 2 after the update');
    }

    private function availableInShop(int $idShop): int
    {
        return (int) StockAvailable::getQuantityAvailableByProduct(self::$idProduct, self::$idProductAttribute, $idShop);
    }

    private function countCombinations(): int
    {
        return (int) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'product_attribute
             WHERE id_product = ' . self::$idProduct
        );
    }

    private function callWebservice(string $method, array $params, string $xml): string
    {
        $previousMethod = $_SERVER['REQUEST_METHOD'] ?? null;
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';

        WebserviceRequest::$ws_current_classname = 'WebserviceRequest';
        $request = WebserviceRequest::getInstance();
        $result = $request->fetch(self::WS_KEY, $method, 'stock_availables', $params, false, $xml);
        WebserviceRequest::resetStaticCache();

        if (null === $previousMethod) {
            unset($_SERVER['REQUEST_METHOD']);
        } else {
            $_SERVER['REQUEST_METHOD'] = $previousMethod;
        }

        return is_array($result) && isset($result['content']) ? (string) $result['content'] : '';
    }

    private static function createProductWithCombination(): void
    {
        LegacyConfiguration::updateGlobalValue('PS_WEBSERVICE', 1);

        // Share stock across the group (this is the default "multistore" setup that the bug
        // report uses): stock is then stored once per group (id_shop = 0, id_shop_group = group).
        Db::getInstance()->update('shop_group', ['share_stock' => 1], 'id_shop_group = ' . self::ID_GROUP);
        LegacyShop::resetStaticCache();
        LegacyConfiguration::resetStaticCache();

        // Shop::getGroup() caches the ShopGroup on the Shop INSTANCE, and the context shop may
        // already have been built by an earlier test in the same process, back when share_stock
        // was still 0. resetStaticCache() clears statics but cannot clear that instance property,
        // so StockAvailable::addSqlShopRestriction() would keep using the per-shop restriction
        // (id_shop = X AND id_shop_group = 0) and read 0 from the shared-stock rows. Rebuild the
        // context shop so it picks the updated group up.
        $context = Context::getContext();
        if (null !== $context->shop && $context->shop->id) {
            $context->shop = new LegacyShop((int) $context->shop->id);
        }

        LegacyShop::setContext(LegacyShop::CONTEXT_ALL);

        $product = new Product();
        $product->name = ['1' => 'WS stock test product'];
        $product->link_rewrite = ['1' => 'ws-stock-test-product'];
        $product->price = 10;
        $product->id_category_default = 2;
        $product->add();
        self::$idProduct = (int) $product->id;

        $combination = new Combination();
        $combination->id_product = self::$idProduct;
        $combination->minimal_quantity = 1;
        $combination->add();
        self::$idProductAttribute = (int) $combination->id;

        // Stock for the combination, in the shared group context.
        LegacyShop::setContext(LegacyShop::CONTEXT_GROUP, self::ID_GROUP);
        StockAvailable::setQuantity(self::$idProduct, self::$idProductAttribute, 5, null, false);
        LegacyShop::setContext(LegacyShop::CONTEXT_ALL);
    }

    private static function createWebserviceKey(): void
    {
        Db::getInstance()->insert('webservice_account', [
            'key' => self::WS_KEY,
            'description' => 'test #38049',
            'class_name' => 'WebserviceRequest',
            'is_module' => 0,
            'module_name' => '',
            'active' => 1,
        ]);
        $idAccount = (int) Db::getInstance()->Insert_ID();

        Db::getInstance()->insert('webservice_account_shop', [
            'id_webservice_account' => $idAccount,
            'id_shop' => 1,
        ]);
        Db::getInstance()->insert('webservice_account_shop', [
            'id_webservice_account' => $idAccount,
            'id_shop' => self::$idShop2,
        ]);

        foreach (['GET', 'PUT', 'POST', 'DELETE', 'HEAD'] as $method) {
            Db::getInstance()->insert('webservice_permission', [
                'resource' => 'stock_availables',
                'method' => $method,
                'id_webservice_account' => $idAccount,
            ]);
        }
    }

    private static function initMultistore(): int
    {
        DatabaseDump::restoreAllTables();
        LegacyConfiguration::resetStaticCache();
        LegacyShop::resetStaticCache();
        self::bootKernel();
        $container = self::$kernel->getContainer();
        $configuration = $container->get('prestashop.adapter.legacy.configuration');
        $entityManager = $container->get('doctrine.orm.entity_manager');

        $configuration->set('PS_MULTISHOP_FEATURE_ACTIVE', 1);

        $shopGroup = $entityManager->find(ShopGroupEntity::class, self::ID_GROUP);
        $shop = new Shop();
        $shop->setActive(true);
        $shop->setIdCategory(2);
        $shop->setName('test_shop_2');
        $shop->setShopGroup($shopGroup);
        $shop->setColor('red');
        $shop->setThemeName(Theme::getDefaultTheme());
        $shop->setDeleted(false);
        $entityManager->persist($shop);
        $entityManager->flush();

        LegacyShop::resetStaticCache();

        return (int) $shop->getId();
    }
}
