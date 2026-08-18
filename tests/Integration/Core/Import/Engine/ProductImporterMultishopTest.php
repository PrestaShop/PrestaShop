<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Import\Engine;

use Configuration as LegacyConfiguration;
use Db;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\ImportEntityExistenceChecker;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;
use Shop as LegacyShop;
use Tests\Resources\DatabaseDump;
use Tests\Resources\Resetter\ProductResetter;
use Tests\Resources\Resetter\ShopResetter;

/**
 * Multistore behavior of the run's frozen ShopConstraint: writes land on the
 * constraint's shops, a match_ref reference living outside the scope fails
 * the row instead of creating a duplicate product, and a feature reused from
 * another shop gets its feature_shop association ensured (never duplicated).
 */
class ProductImporterMultishopTest extends AbstractProductImportEngineTestCase
{
    private const FIELDS = ['name', 'reference', 'price_tex', 'features'];
    private const DELETED_SHOP_NAME = 'deleted_shop_import';

    private static int $secondShopId;

    private static int $deletedShopId;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        DatabaseDump::restoreTables(['shop', 'shop_group', 'configuration']);
        ProductResetter::resetProducts();
        DatabaseDump::restoreTables(['feature', 'feature_lang', 'feature_shop', 'feature_value', 'feature_value_lang']);
        LegacyConfiguration::resetStaticCache();
        LegacyShop::resetStaticCache();
        self::bootKernel();

        self::getContainer()->get('prestashop.adapter.legacy.configuration')->set('PS_MULTISHOP_FEATURE_ACTIVE', 1);

        $db = Db::getInstance();
        $db->insert('shop_group', [
            'name' => 'test_group_import', 'color' => '', 'share_customer' => 0,
            'share_order' => 0, 'share_stock' => 0, 'active' => 1, 'deleted' => 0,
        ]);
        $secondGroupId = (int) $db->Insert_ID();
        $db->insert('shop', [
            'id_shop_group' => $secondGroupId, 'name' => 'test_shop_import', 'color' => '',
            'id_category' => 2, 'theme_name' => 'classic', 'active' => 1, 'deleted' => 0,
        ]);
        self::$secondShopId = (int) $db->Insert_ID();

        // soft-deleted shop: the row still exists, so the import must treat it as
        // absent instead of assigning (and thereby resurrecting) it
        $db->insert('shop', [
            'id_shop_group' => $secondGroupId, 'name' => self::DELETED_SHOP_NAME, 'color' => '',
            'id_category' => 2, 'theme_name' => 'classic', 'active' => 1, 'deleted' => 1,
        ]);
        self::$deletedShopId = (int) $db->Insert_ID();

        LegacyShop::resetStaticCache();
    }

    public static function tearDownAfterClass(): void
    {
        ProductResetter::resetProducts();
        DatabaseDump::restoreTables(['feature', 'feature_lang', 'feature_value', 'feature_value_lang', 'configuration']);
        // restores shop, shop_group and every *_shop table
        ShopResetter::resetShops();
        LegacyShop::resetStaticCache();
        LegacyConfiguration::resetStaticCache();
        parent::tearDownAfterClass();
    }

    public function testShopConstraintScopesWritesLookupsAndFeatureAssociations(): void
    {
        self::bootKernel();

        // 1. a run scoped to shop 2 creates the product ON shop 2 only,
        //    and the auto-created feature is associated to shop 2 only
        $context = $this->buildContext('product_multishop.csv', self::FIELDS, [], 1, ';', ',', ShopConstraint::shop(self::$secondShopId));
        $messages = (new ImportEngineTestRunner())->run($this->getEntityImporter(), $context);
        $this->assertNoErrors($messages);

        $productId = $this->getProductIdByReference('MS-1');
        $this->assertNotNull($productId);
        $this->assertNotFalse($this->fetchOne('SELECT 1 FROM {p}product_shop WHERE id_product = :id AND id_shop = :shop', ['id' => $productId, 'shop' => self::$secondShopId]));
        $this->assertFalse($this->fetchOne('SELECT 1 FROM {p}product_shop WHERE id_product = :id AND id_shop = 1', ['id' => $productId]), 'A run scoped to shop 2 must not associate the product with shop 1');

        $featureId = $this->getMultishopFeatureId();
        $this->assertSame([self::$secondShopId], $this->getFeatureShopIds($featureId));

        // 2. match_ref on shop 1: the reference exists in the catalog but on
        //    none of the run's shops -> row ERROR, never a duplicate product
        [, $messages] = $this->runImport('product_multishop.csv', self::FIELDS, ['matchRef' => true]);
        $scopeErrors = array_values(array_filter(
            $this->messagesOfSeverity($messages, ImportMessage::SEVERITY_ERROR),
            static fn (ImportMessage $message): bool => 'reference' === $message->field
        ));
        $this->assertCount(1, $scopeErrors);
        $this->assertStringContainsString("outside the run's shop scope", $scopeErrors[0]->message);
        $this->assertSame(1, (int) $this->fetchOne("SELECT COUNT(*) FROM {p}product WHERE reference = 'MS-1'"), 'The out-of-scope reference must not create a duplicate product');

        // 3. a shop 1 run reusing the feature BY NAME must not duplicate it,
        //    but must ensure its feature_shop association covers shop 1
        //    (feature reads INNER JOIN feature_shop: without the association
        //    the imported values would be invisible on shop 1).
        //    Fresh kernel first: each production batch request gets fresh
        //    services, while this test would otherwise reuse the resolver
        //    whose run-lifetime caches still hold run 1's feature
        self::bootKernel();
        $GLOBALS['kernel'] = self::$kernel;
        [, $messages] = $this->runImport('product_multishop_shop1.csv', self::FIELDS);
        $this->assertNoErrors($messages);

        $this->assertSame(1, (int) $this->fetchOne("SELECT COUNT(DISTINCT fl.id_feature) FROM {p}feature_lang fl WHERE fl.name = 'Multishop Feature' AND fl.id_lang = 1"), 'The feature must be reused, not duplicated per shop');
        $this->assertSame([1, self::$secondShopId], $this->getFeatureShopIds($featureId));
    }

    /**
     * A soft-deleted shop row still exists, so both the name lookup and the
     * generic id probe must treat it as absent — assigning it would resurrect it
     * on the imported product.
     */
    public function testSoftDeletedShopsAreTreatedAsAbsent(): void
    {
        self::bootKernel();
        $GLOBALS['kernel'] = self::$kernel;

        // the id probe (ImportEntityExistenceChecker::SOFT_DELETE_TABLES)
        $existenceChecker = self::getContainer()->get(ImportEntityExistenceChecker::class);
        $this->assertFalse($existenceChecker->exists('shop', self::$deletedShopId), 'A soft-deleted shop must not be reported as existing');
        $this->assertTrue($existenceChecker->exists('shop', self::$secondShopId));

        // the name lookup (ShopRepository::getShopIdsByName)
        [$context, $messages] = $this->runImport('product_shop_deleted.csv', ['name', 'reference', 'shop']);
        $this->assertNoErrors($messages);

        $shopWarnings = array_values(array_filter(
            $this->messagesOfSeverity($messages, ImportMessage::SEVERITY_WARNING),
            static fn (ImportMessage $message): bool => 'shop' === $message->field
        ));
        $this->assertCount(1, $shopWarnings);
        $this->assertStringContainsString(self::DELETED_SHOP_NAME, $shopWarnings[0]->message);

        // the entry was dropped, so the product falls back to the run's shop
        $productId = $this->getProductIdByReference('DEL-SHP-1');
        $this->assertNotNull($productId);
        $this->assertFalse(
            $this->fetchOne('SELECT 1 FROM {p}product_shop WHERE id_product = :id AND id_shop = :shop', ['id' => $productId, 'shop' => self::$deletedShopId]),
            'The product must never be associated with a soft-deleted shop'
        );
        $this->assertNotFalse($this->fetchOne('SELECT 1 FROM {p}product_shop WHERE id_product = :id AND id_shop = :shop', ['id' => $productId, 'shop' => $context->getShopId()]));
    }

    private function getMultishopFeatureId(): int
    {
        $featureId = $this->fetchOne("SELECT fl.id_feature FROM {p}feature_lang fl WHERE fl.name = 'Multishop Feature' AND fl.id_lang = 1");
        $this->assertNotFalse($featureId);

        return (int) $featureId;
    }

    /**
     * @return list<int>
     */
    private function getFeatureShopIds(int $featureId): array
    {
        $rows = $this->fetchAll('SELECT id_shop FROM {p}feature_shop WHERE id_feature = :id ORDER BY id_shop', ['id' => $featureId]);

        return array_map(static fn (array $row): int => (int) $row['id_shop'], $rows);
    }
}
