<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Import\Engine;

use Configuration as LegacyConfiguration;
use Db;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;
use Shop as LegacyShop;
use Tests\Resources\DatabaseDump;
use Tests\Resources\Resetter\ProductResetter;
use Tests\Resources\Resetter\ShopResetter;

/**
 * None of the columns the import resolves BY has a unique constraint, so every
 * one of them can match several entities. The policy (PLAN.md decision 22) is
 * that an ambiguous LINK warns and uses the lowest id, while an ambiguous
 * IDENTITY fails the row — the identity half is covered by
 * ProductImporterAssociationsTest, and this covers the link half for every
 * remaining resolvable column: brand, supplier, sibling category, feature,
 * feature value and shop.
 *
 * Each pair of homonyms is created here rather than assumed, and each assertion
 * checks BOTH halves of the policy: the warning carries the match count, and
 * the LOWEST id is the one actually written.
 */
class ProductImporterAmbiguityTest extends AbstractProductImportEngineTestCase
{
    private const FIELDS = ['name', 'reference', 'manufacturer', 'supplier', 'category', 'features', 'shop'];

    private static int $lowestManufacturerId;
    private static int $lowestSupplierId;
    private static int $lowestCategoryId;
    private static int $lowestFeatureId;
    private static int $lowestFeatureValueId;
    private static int $lowestShopId;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        DatabaseDump::restoreTables([
            'shop', 'shop_group', 'configuration',
            'manufacturer', 'manufacturer_shop', 'manufacturer_lang',
            'supplier', 'supplier_shop', 'supplier_lang',
            'category', 'category_shop', 'category_lang',
            'feature', 'feature_shop', 'feature_lang', 'feature_value', 'feature_value_lang',
        ]);
        ProductResetter::resetProducts();
        LegacyConfiguration::resetStaticCache();
        LegacyShop::resetStaticCache();
        self::bootKernel();

        $db = Db::getInstance();
        $homeCategoryId = (int) LegacyConfiguration::get('PS_HOME_CATEGORY');

        self::$lowestManufacturerId = self::seedTwins(
            static fn (): int => self::insertManufacturer($db, 'Ambiguous Brand')
        );
        self::$lowestSupplierId = self::seedTwins(
            static fn (): int => self::insertSupplier($db, 'Ambiguous Supplier')
        );
        self::$lowestCategoryId = self::seedTwins(
            static fn (): int => self::insertCategory($db, 'Ambiguous Category', $homeCategoryId)
        );
        self::$lowestFeatureId = self::seedTwins(
            static fn (): int => self::insertFeature($db, 'Ambiguous Feature')
        );
        // both values belong to the FIRST feature: the importer resolves the
        // value inside the feature it just picked
        self::$lowestFeatureValueId = self::seedTwins(
            static fn (): int => self::insertFeatureValue($db, self::$lowestFeatureId, 'Ambiguous Value')
        );
        self::$lowestShopId = self::seedTwins(
            static fn (): int => self::insertShop($db, 'Ambiguous Shop')
        );

        LegacyShop::resetStaticCache();
    }

    public static function tearDownAfterClass(): void
    {
        ShopResetter::resetShops();
        DatabaseDump::restoreTables([
            'manufacturer', 'manufacturer_shop', 'manufacturer_lang',
            'supplier', 'supplier_shop', 'supplier_lang',
            'category', 'category_shop', 'category_lang',
            'feature', 'feature_shop', 'feature_lang', 'feature_value', 'feature_value_lang',
        ]);
        parent::tearDownAfterClass();
    }

    /**
     * Every resolvable column reports its ambiguity with the match count, and
     * writes the lowest id — one import, six independent lookup paths.
     */
    public function testAmbiguousLinksWarnWithTheirCountAndUseTheLowestId(): void
    {
        [, $messages] = $this->runImport('product_ambiguous_links.csv', self::FIELDS);

        $this->assertNoErrors($messages);
        $productId = $this->getProductIdByReference('AMB-LINK-1');
        $this->assertNotNull($productId);

        // every message must name the count AND the id that was used, so the
        // merchant can tell which homonym won without opening the database
        $this->assertWarningMatches($messages, 'manufacturer', 'Brand "Ambiguous Brand" matches 2 brands; the first one (id ' . self::$lowestManufacturerId . ') was used.');
        $this->assertWarningMatches($messages, 'supplier', 'Supplier "Ambiguous Supplier" matches 2 suppliers; the first one (id ' . self::$lowestSupplierId . ') was used.');
        $this->assertWarningMatches($messages, 'category', 'Category "Ambiguous Category" matches 2 sibling categories; the first one (id ' . self::$lowestCategoryId . ') was used.');
        $this->assertWarningMatches($messages, 'features', 'Feature "Ambiguous Feature" matches 2 features; the first one (id ' . self::$lowestFeatureId . ') was used.');
        $this->assertWarningMatches($messages, 'features', 'Feature value "Ambiguous Value" matches 2 values of the same feature; the first one (id ' . self::$lowestFeatureValueId . ') was used.');
        $this->assertWarningMatches($messages, 'shop', 'Shop "Ambiguous Shop" matches 2 shops; the first one (id ' . self::$lowestShopId . ') was used.');

        // and the writes really did land on the lowest id of each pair
        $this->assertSame(
            self::$lowestManufacturerId,
            (int) $this->fetchOne('SELECT id_manufacturer FROM {p}product WHERE id_product = :id', ['id' => $productId])
        );
        $this->assertContains(
            self::$lowestCategoryId,
            array_map(
                static fn (array $row): int => (int) $row['id_category'],
                $this->fetchAll('SELECT id_category FROM {p}category_product WHERE id_product = :id', ['id' => $productId])
            )
        );
        $this->assertSame(
            self::$lowestSupplierId,
            (int) $this->fetchOne('SELECT id_supplier FROM {p}product_supplier WHERE id_product = :id', ['id' => $productId])
        );
        $featureRow = $this->fetchRow('SELECT id_feature, id_feature_value FROM {p}feature_product WHERE id_product = :id', ['id' => $productId]);
        $this->assertNotFalse($featureRow);
        $this->assertSame(self::$lowestFeatureId, (int) $featureRow['id_feature']);
        $this->assertSame(self::$lowestFeatureValueId, (int) $featureRow['id_feature_value']);
        $this->assertContains(
            self::$lowestShopId,
            array_map(
                static fn (array $row): int => (int) $row['id_shop'],
                $this->fetchAll('SELECT id_shop FROM {p}product_shop WHERE id_product = :id', ['id' => $productId])
            )
        );
    }

    /**
     * The resolver caches are quiet: the ambiguity is reported on the FIRST
     * resolution only, so a file repeating the same ambiguous name does not
     * repeat the warning for every row.
     */
    public function testTheAmbiguityIsReportedOncePerBatchNotOncePerRow(): void
    {
        // three rows, all naming the same ambiguous brand
        [, $messages] = $this->runImport('product_ambiguous_repeated.csv', ['name', 'reference', 'manufacturer'], [], null, 5);

        $brandWarnings = array_filter(
            $this->messagesOfSeverity($messages, ImportMessage::SEVERITY_WARNING),
            static fn (ImportMessage $message): bool => 'manufacturer' === $message->field
        );
        $this->assertCount(1, $brandWarnings, 'A repeated ambiguous name must be reported once, not once per row');
    }

    /**
     * @param list<ImportMessage> $messages
     */
    private function assertWarningMatches(array $messages, string $field, string $expected): void
    {
        $texts = array_map(
            static fn (ImportMessage $message): string => $message->message,
            array_filter(
                $this->messagesOfSeverity($messages, ImportMessage::SEVERITY_WARNING),
                static fn (ImportMessage $message): bool => $field === $message->field
            )
        );

        $this->assertContains($expected, array_values($texts), sprintf('Missing the "%s" ambiguity warning. Got: %s', $field, implode(' | ', $texts)));
    }

    /**
     * Creates the same entity twice and returns the LOWEST of the two ids —
     * which is the one every resolver must pick.
     *
     * @param callable(): int $insert
     */
    private static function seedTwins(callable $insert): int
    {
        $firstId = $insert();
        $insert();

        return $firstId;
    }

    private static function insertManufacturer(Db $db, string $name): int
    {
        $db->insert('manufacturer', ['name' => pSQL($name), 'active' => 1, 'date_add' => date('Y-m-d H:i:s'), 'date_upd' => date('Y-m-d H:i:s')]);
        $manufacturerId = (int) $db->Insert_ID();
        $db->insert('manufacturer_shop', ['id_manufacturer' => $manufacturerId, 'id_shop' => self::DEFAULT_SHOP_ID]);
        $db->insert('manufacturer_lang', ['id_manufacturer' => $manufacturerId, 'id_lang' => 1, 'description' => '', 'short_description' => '', 'meta_title' => '', 'meta_description' => '']);

        return $manufacturerId;
    }

    private static function insertSupplier(Db $db, string $name): int
    {
        $db->insert('supplier', ['name' => pSQL($name), 'active' => 1, 'date_add' => date('Y-m-d H:i:s'), 'date_upd' => date('Y-m-d H:i:s')]);
        $supplierId = (int) $db->Insert_ID();
        $db->insert('supplier_shop', ['id_supplier' => $supplierId, 'id_shop' => self::DEFAULT_SHOP_ID]);
        $db->insert('supplier_lang', ['id_supplier' => $supplierId, 'id_lang' => 1, 'description' => '', 'meta_title' => '', 'meta_description' => '']);

        return $supplierId;
    }

    private static function insertCategory(Db $db, string $name, int $parentCategoryId): int
    {
        $db->insert('category', [
            'id_parent' => $parentCategoryId, 'id_shop_default' => self::DEFAULT_SHOP_ID, 'level_depth' => 2,
            'nleft' => 0, 'nright' => 0, 'active' => 1, 'date_add' => date('Y-m-d H:i:s'), 'date_upd' => date('Y-m-d H:i:s'),
        ]);
        $categoryId = (int) $db->Insert_ID();
        $db->insert('category_shop', ['id_category' => $categoryId, 'id_shop' => self::DEFAULT_SHOP_ID, 'position' => 0]);
        $db->insert('category_lang', [
            'id_category' => $categoryId, 'id_shop' => self::DEFAULT_SHOP_ID, 'id_lang' => 1,
            'name' => pSQL($name), 'description' => '', 'additional_description' => '',
            'link_rewrite' => 'ambiguous-category', 'meta_title' => '', 'meta_description' => '',
        ]);

        return $categoryId;
    }

    private static function insertFeature(Db $db, string $name): int
    {
        $db->insert('feature', ['position' => 0]);
        $featureId = (int) $db->Insert_ID();
        $db->insert('feature_shop', ['id_feature' => $featureId, 'id_shop' => self::DEFAULT_SHOP_ID]);
        $db->insert('feature_lang', ['id_feature' => $featureId, 'id_lang' => 1, 'name' => pSQL($name)]);

        return $featureId;
    }

    private static function insertFeatureValue(Db $db, int $featureId, string $value): int
    {
        $db->insert('feature_value', ['id_feature' => $featureId, 'custom' => 0, 'position' => 0]);
        $featureValueId = (int) $db->Insert_ID();
        $db->insert('feature_value_lang', ['id_feature_value' => $featureValueId, 'id_lang' => 1, 'value' => pSQL($value)]);

        return $featureValueId;
    }

    private static function insertShop(Db $db, string $name): int
    {
        $db->insert('shop', [
            'id_shop_group' => 1, 'name' => pSQL($name), 'color' => '', 'id_category' => 2,
            'theme_name' => 'classic', 'active' => 1, 'deleted' => 0,
        ]);

        return (int) $db->Insert_ID();
    }
}
