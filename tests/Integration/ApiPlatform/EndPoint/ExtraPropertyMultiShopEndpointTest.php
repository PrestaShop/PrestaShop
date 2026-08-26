<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\ApiPlatform\EndPoint;

use Db;
use Module;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyRegistryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagManager;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\Multistore\MultistoreConfig;
use Product;
use Shop;
use Tests\Resources\Resetter\ProductResetter;
use Tests\Resources\Resetter\ShopResetter;
use Tools;

/**
 * Multishop contract of the extra properties on the Admin API: the shop context request
 * parameters (shopId / shopGroupId / shopIds / allShops — parsed by the API kernel's
 * ShopContextListener) drive both writes (fan-out to every shop in scope) and reads (the
 * scope's representative shop, i.e. the default shop when it belongs to the scope).
 *
 * Uses the extrapropertytest module (COMMON api_flag + LANG api_note on product) plus a
 * SHOP-scoped core definition registered directly through the registry — the module-based
 * suite has no SHOP-scoped property.
 */
final class ExtraPropertyMultiShopEndpointTest extends ApiTestCase
{
    private const MODULE_NAME = 'extrapropertytest';
    private const PRODUCT_READ = 'product_read';
    private const PRODUCT_WRITE = 'product_write';
    private const PRODUCT_ID = 1;
    private const DEFAULT_SHOP_ID = 1;

    private static int $secondShopId;
    private static int $defaultGroupId;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        ProductResetter::resetProducts();
        // The Admin API refuses to run on a multistore installation unless the dedicated
        // feature flag is enabled (see AdminAPIFeatureListener) — every route 404s otherwise.
        self::updateConfiguration(MultistoreConfig::FEATURE_STATUS, 1);
        self::getContainer()->get(FeatureFlagManager::class)->enable(FeatureFlagSettings::FEATURE_FLAG_ADMIN_API_MULTISTORE);

        self::$defaultGroupId = (int) Shop::getGroupFromShop(self::DEFAULT_SHOP_ID, true);
        self::$secondShopId = self::addShop('Extra Property API Shop 2', self::$defaultGroupId);

        // Associate the tested product to both shops so per-shop reads/writes are valid on each.
        $product = new Product(self::PRODUCT_ID);
        $product->id_shop_list = [self::DEFAULT_SHOP_ID, self::$secondShopId];
        $product->save();

        // Install the module registering the COMMON + LANG product properties.
        $sourceModuleDir = dirname(__DIR__, 3) . '/Resources/modules_tests/' . self::MODULE_NAME;
        if (is_dir($sourceModuleDir)) {
            Tools::recurseCopy($sourceModuleDir, _PS_MODULE_DIR_ . self::MODULE_NAME);
        }
        if (Module::isInstalled(self::MODULE_NAME)) {
            Module::getInstanceByName(self::MODULE_NAME)->uninstall();
        }
        $module = Module::getInstanceByName(self::MODULE_NAME);
        self::assertInstanceOf(Module::class, $module);
        self::assertTrue((bool) $module->install());

        // SHOP-scoped core definition, registered directly (no module owns it).
        self::getContainer()->get(ExtraPropertyRegistryInterface::class)->register(self::shopScopedDefinition());
    }

    public static function tearDownAfterClass(): void
    {
        self::getContainer()->get(ExtraPropertyRegistryInterface::class)->unregister(self::shopScopedDefinition(), true);
        if (Module::isInstalled(self::MODULE_NAME)) {
            Module::getInstanceByName(self::MODULE_NAME)->uninstall();
        }
        if (is_dir(_PS_MODULE_DIR_ . self::MODULE_NAME)) {
            Tools::deleteDirectory(_PS_MODULE_DIR_ . self::MODULE_NAME);
        }

        self::getContainer()->get(FeatureFlagManager::class)->disable(FeatureFlagSettings::FEATURE_FLAG_ADMIN_API_MULTISTORE);
        ProductResetter::resetProducts();
        ShopResetter::resetShops();

        parent::tearDownAfterClass();
    }

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['product_extra', 'product_extra_lang', 'product_extra_shop'] as $table) {
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . $table . '`');
        }
    }

    public function testSingleShopWritesStayOnTheirShop(): void
    {
        $patched = $this->partialUpdateItem(
            '/products/' . self::PRODUCT_ID . '?shopId=' . self::$secondShopId,
            ['extraProperties' => ['_core' => ['api_shop_note' => 'only-shop-2']]],
            [self::PRODUCT_WRITE]
        );
        $this->assertSame('only-shop-2', $patched['extraProperties']['_core']['api_shop_note']);

        $secondShopProduct = $this->getItem('/products/' . self::PRODUCT_ID . '?shopId=' . self::$secondShopId, [self::PRODUCT_READ]);
        $this->assertSame('only-shop-2', $secondShopProduct['extraProperties']['_core']['api_shop_note']);

        // The default shop was outside the constraint: it keeps its (default NULL) value.
        $defaultShopProduct = $this->getItem('/products/' . self::PRODUCT_ID . '?shopId=' . self::DEFAULT_SHOP_ID, [self::PRODUCT_READ]);
        $this->assertNull($defaultShopProduct['extraProperties']['_core']['api_shop_note']);
    }

    public function testGroupAndAllShopsWritesFanOutToEveryShopInScope(): void
    {
        // Shop group scope: the LANG value (product is multilang-multishop) lands on every group shop.
        $this->partialUpdateItem(
            '/products/' . self::PRODUCT_ID . '?shopGroupId=' . self::$defaultGroupId,
            ['extraProperties' => [self::MODULE_NAME => ['api_note' => ['en-US' => 'group note']]]],
            [self::PRODUCT_WRITE]
        );

        foreach ([self::DEFAULT_SHOP_ID, self::$secondShopId] as $shopId) {
            $product = $this->getItem('/products/' . self::PRODUCT_ID . '?shopId=' . $shopId, [self::PRODUCT_READ]);
            $this->assertSame('group note', $product['extraProperties'][self::MODULE_NAME]['api_note']['en-US']);
        }

        // All-shops scope: the SHOP value lands on every shop.
        $this->partialUpdateItem(
            '/products/' . self::PRODUCT_ID . '?allShops',
            ['extraProperties' => ['_core' => ['api_shop_note' => 'everywhere']]],
            [self::PRODUCT_WRITE]
        );

        foreach ([self::DEFAULT_SHOP_ID, self::$secondShopId] as $shopId) {
            $product = $this->getItem('/products/' . self::PRODUCT_ID . '?shopId=' . $shopId, [self::PRODUCT_READ]);
            $this->assertSame('everywhere', $product['extraProperties']['_core']['api_shop_note']);
        }
    }

    public function testShopCollectionWritesOnlyTheListedShops(): void
    {
        $this->partialUpdateItem(
            '/products/' . self::PRODUCT_ID . '?shopIds=' . self::$secondShopId,
            ['extraProperties' => ['_core' => ['api_shop_note' => 'collection']]],
            [self::PRODUCT_WRITE]
        );

        $secondShopProduct = $this->getItem('/products/' . self::PRODUCT_ID . '?shopId=' . self::$secondShopId, [self::PRODUCT_READ]);
        $this->assertSame('collection', $secondShopProduct['extraProperties']['_core']['api_shop_note']);

        $defaultShopProduct = $this->getItem('/products/' . self::PRODUCT_ID . '?shopId=' . self::DEFAULT_SHOP_ID, [self::PRODUCT_READ]);
        $this->assertNull($defaultShopProduct['extraProperties']['_core']['api_shop_note']);
    }

    public function testNonSingleShopReadsReturnTheRepresentativeShopValue(): void
    {
        // Diverge the shops deliberately, then read with broad scopes: the value returned
        // is the representative shop's — the default shop, since it belongs to both scopes.
        $this->partialUpdateItem(
            '/products/' . self::PRODUCT_ID . '?shopId=' . self::DEFAULT_SHOP_ID,
            ['extraProperties' => ['_core' => ['api_shop_note' => 'default-shop-value']]],
            [self::PRODUCT_WRITE]
        );
        $this->partialUpdateItem(
            '/products/' . self::PRODUCT_ID . '?shopId=' . self::$secondShopId,
            ['extraProperties' => ['_core' => ['api_shop_note' => 'second-shop-value']]],
            [self::PRODUCT_WRITE]
        );

        $groupRead = $this->getItem('/products/' . self::PRODUCT_ID . '?shopGroupId=' . self::$defaultGroupId, [self::PRODUCT_READ]);
        $this->assertSame('default-shop-value', $groupRead['extraProperties']['_core']['api_shop_note']);

        $allShopsRead = $this->getItem('/products/' . self::PRODUCT_ID . '?allShops', [self::PRODUCT_READ]);
        $this->assertSame('default-shop-value', $allShopsRead['extraProperties']['_core']['api_shop_note']);
    }

    private static function shopScopedDefinition(): ExtraPropertyDefinition
    {
        return new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'api_shop_note',
            type: ExtraPropertyType::STRING,
            scope: ExtraPropertyScope::SHOP,
            associatedApis: ['/products/{productId}'],
        );
    }
}
