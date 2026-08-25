<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Import\Engine;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Tax\TaxComputer;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;
use Tests\Resources\DatabaseDump;
use Tests\Resources\Resetter\ProductResetter;

class ProductImporterCreateTest extends AbstractProductImportEngineTestCase
{
    private const FIXTURE = 'product_create_basic.csv';

    private const FIELDS = [
        'name', 'reference', 'category', 'price_tex', 'price_tin', 'id_tax_rules_group', 'wholesale_price',
        'active', 'visibility', 'condition', 'online_only', 'show_price', 'available_for_order', 'on_sale',
        'available_date', 'ean13', 'isbn', 'upc', 'mpn', 'width', 'height', 'depth', 'weight',
        'additional_shipping_cost', 'unity', 'unit_price', 'ecotax', 'quantity', 'location', 'out_of_stock',
        'minimal_quantity', 'low_stock_threshold', 'manufacturer', 'supplier', 'supplier_reference', 'tags',
        'meta_title', 'meta_description', 'link_rewrite', 'available_now', 'available_later',
        'delivery_in_stock', 'delivery_out_stock', 'description', 'description_short',
        'reduction_price', 'reduction_from', 'reduction_to', 'date_add',
    ];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::resetImportTables();
    }

    public static function tearDownAfterClass(): void
    {
        self::resetImportTables();
        parent::tearDownAfterClass();
    }

    private static function resetImportTables(): void
    {
        ProductResetter::resetProducts();
        DatabaseDump::restoreTables([
            'category', 'category_lang', 'category_shop', 'category_group',
            'manufacturer', 'manufacturer_lang', 'manufacturer_shop',
            'tag',
        ]);
    }

    public function testCreateProductsFromFullyMappedFile(): void
    {
        [$context, $messages] = $this->runImport(self::FIXTURE, self::FIELDS);

        $this->assertNoErrors($messages);
        $this->assertSame([], $context->getSkippedRows());

        $this->assertFullyMappedProduct();
        $this->assertPriceTaxIncludedWasDetaxed();
    }

    private function assertFullyMappedProduct(): void
    {
        $productId = $this->getProductIdByReference('IMP-CHAIR-001');
        $this->assertNotNull($productId, 'Product IMP-CHAIR-001 was not created');

        $product = $this->fetchRow('SELECT * FROM {p}product WHERE id_product = :id', ['id' => $productId]);
        $productShop = $this->fetchRow('SELECT * FROM {p}product_shop WHERE id_product = :id AND id_shop = 1', ['id' => $productId]);
        $productLang = $this->fetchRow('SELECT * FROM {p}product_lang WHERE id_product = :id AND id_lang = 1 AND id_shop = 1', ['id' => $productId]);

        // basic info + SEO + labels (localized)
        $this->assertSame('Imported Chair', $productLang['name']);
        $this->assertSame('Long chair description', strip_tags($productLang['description']));
        $this->assertSame('Short chair desc', strip_tags($productLang['description_short']));
        $this->assertSame('Meta chair', $productLang['meta_title']);
        $this->assertSame('Meta desc chair', $productLang['meta_description']);
        $this->assertSame('imported-chair', $productLang['link_rewrite']);
        $this->assertSame('In stock now', $productLang['available_now']);
        $this->assertSame('Back soon', $productLang['available_later']);
        $this->assertSame('2 days', $productLang['delivery_in_stock']);
        $this->assertSame('7 days', $productLang['delivery_out_stock']);

        // options / flags
        $this->assertSame('1', (string) $productShop['active']);
        $this->assertSame('both', $productShop['visibility']);
        $this->assertSame('new', $productShop['condition']);
        $this->assertSame('0', (string) $productShop['online_only']);
        $this->assertSame('1', (string) $productShop['show_price']);
        $this->assertSame('1', (string) $productShop['available_for_order']);
        $this->assertSame('0', (string) $productShop['on_sale']);
        $this->assertStringStartsWith('2026-09-01', (string) $productShop['available_date']);

        // prices
        $this->assertSame('99.900000', (string) $productShop['price']);
        $this->assertSame('1', (string) $productShop['id_tax_rules_group']);
        $this->assertSame('40.000000', (string) $productShop['wholesale_price']);
        $this->assertSame('piece', $product['unity']);
        $this->assertSame('9.990000', (string) $productShop['unit_price']);
        // ecotax is zeroed because PS_USE_ECOTAX is disabled in the fixtures
        $this->assertSame('0.000000', (string) $productShop['ecotax']);

        // details / references / dimensions
        $this->assertSame('1234567890128', $product['ean13']);
        $this->assertSame('123456789012', $product['upc']);
        $this->assertSame('MPN-CHAIR1', $product['mpn']);
        $this->assertSame('30.000000', (string) $product['width']);
        $this->assertSame('45.000000', (string) $product['height']);
        $this->assertSame('50.000000', (string) $product['depth']);
        $this->assertSame('7.500000', (string) $product['weight']);
        $this->assertSame('4.990000', (string) $product['additional_shipping_cost']);
        $this->assertSame('2', (string) $product['minimal_quantity']);
        $this->assertSame('5', (string) $product['low_stock_threshold']);
        $this->assertSame('1', (string) $product['low_stock_alert']);

        // stock
        $stock = $this->fetchRow('SELECT * FROM {p}stock_available WHERE id_product = :id AND id_product_attribute = 0', ['id' => $productId]);
        $this->assertSame('25', (string) $stock['quantity']);
        $this->assertSame('A-01', $stock['location']);
        $this->assertSame('1', (string) $stock['out_of_stock']);

        // categories: path auto-created under Home, first entry is the default
        $importedFurnitureId = $this->fetchOne("SELECT cl.id_category FROM {p}category_lang cl INNER JOIN {p}category c ON c.id_category = cl.id_category WHERE cl.name = 'Imported Furniture' AND cl.id_lang = 1 AND c.id_parent = 2");
        $this->assertNotFalse($importedFurnitureId, 'Category "Imported Furniture" was not auto-created under Home');
        $livingRoomId = $this->fetchOne("SELECT cl.id_category FROM {p}category_lang cl INNER JOIN {p}category c ON c.id_category = cl.id_category WHERE cl.name = 'Living Room' AND cl.id_lang = 1 AND c.id_parent = :parent", ['parent' => $importedFurnitureId]);
        $this->assertNotFalse($livingRoomId, 'Category "Living Room" was not auto-created under "Imported Furniture"');
        $this->assertSame((string) $livingRoomId, (string) $productShop['id_category_default']);
        $associatedCategoryIds = array_column($this->fetchAll('SELECT id_category FROM {p}category_product WHERE id_product = :id', ['id' => $productId]), 'id_category');
        $this->assertContains((string) $livingRoomId, array_map('strval', $associatedCategoryIds));

        // manufacturer auto-created and associated
        $manufacturerId = $this->fetchOne("SELECT id_manufacturer FROM {p}manufacturer WHERE name = 'Imported Brand'");
        $this->assertNotFalse($manufacturerId, 'Manufacturer "Imported Brand" was not auto-created');
        $this->assertSame((string) $manufacturerId, (string) $product['id_manufacturer']);

        // supplier association (existing supplier, never auto-created)
        $supplierRow = $this->fetchRow('SELECT * FROM {p}product_supplier WHERE id_product = :id', ['id' => $productId]);
        $this->assertNotFalse($supplierRow, 'Product supplier row missing');
        $this->assertSame('1', (string) $supplierRow['id_supplier']);
        $this->assertSame('FSUP-REF-1', $supplierRow['product_supplier_reference']);
        $this->assertSame('1', (string) $product['id_supplier'], 'Default supplier not set');

        // tags
        $tags = array_column($this->fetchAll('SELECT t.name FROM {p}product_tag pt INNER JOIN {p}tag t ON t.id_tag = pt.id_tag WHERE pt.id_product = :id', ['id' => $productId]), 'name');
        sort($tags);
        $this->assertSame(['chair', 'imported'], $tags);

        // specific price (basic reduction)
        $specificPrice = $this->fetchRow('SELECT * FROM {p}specific_price WHERE id_product = :id', ['id' => $productId]);
        $this->assertNotFalse($specificPrice, 'Specific price row missing');
        $this->assertSame('amount', $specificPrice['reduction_type']);
        $this->assertSame('10.000000', (string) $specificPrice['reduction']);
        $this->assertStringStartsWith('2026-08-01', (string) $specificPrice['from']);
        $this->assertStringStartsWith('2026-12-31', (string) $specificPrice['to']);

        // date_add through the fallback writer; date_upd stays "now"
        $this->assertSame('2020-05-04 10:30:00', (string) $product['date_add']);
        $this->assertStringNotContainsString('2020-05-04', (string) $product['date_upd']);
    }

    private function assertPriceTaxIncludedWasDetaxed(): void
    {
        $productId = $this->getProductIdByReference('IMP-LAMP-002');
        $this->assertNotNull($productId, 'Product IMP-LAMP-002 was not created');

        $productShop = $this->fetchRow('SELECT price, visibility, `condition`, online_only, on_sale FROM {p}product_shop WHERE id_product = :id AND id_shop = 1', ['id' => $productId]);

        // expected price derives from the same rate the importer used
        // (tax rules group 1 for the shop address country, legacy Shop::getAddress resolution)
        $shopCountryId = (int) $this->fetchOne("SELECT value FROM {p}configuration WHERE name = 'PS_SHOP_COUNTRY_ID'");
        if ($shopCountryId <= 0) {
            $shopCountryId = (int) $this->fetchOne("SELECT value FROM {p}configuration WHERE name = 'PS_COUNTRY_DEFAULT'");
        }
        $rate = self::getContainer()->get(TaxComputer::class)->getTaxRate(new TaxRulesGroupId(1), new CountryId($shopCountryId));
        $divisor = $rate->dividedBy(new DecimalNumber('100'), 6)->plus(new DecimalNumber('1'));
        $expectedPrice = (new DecimalNumber('120.00'))->dividedBy($divisor, 6);
        $this->assertEqualsWithDelta((float) (string) $expectedPrice, (float) $productShop['price'], 0.000001);

        $this->assertSame('catalog', $productShop['visibility']);
        $this->assertSame('used', $productShop['condition']);
        $this->assertSame('1', (string) $productShop['online_only']);
        $this->assertSame('1', (string) $productShop['on_sale']);
    }
}
