<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Import\Engine;

use Db;
use Tests\Resources\DatabaseDump;
use Tests\Resources\Resetter\ProductResetter;

/**
 * The supplier column describes ONE supplier and only the fields the file
 * carries, but the CQRS commands behind it replace whole collections:
 * SetSuppliersCommand deletes the suppliers missing from its list, and
 * UpdateProductSuppliersCommand overwrites the reference and price of the
 * association it is given.
 *
 * So a maintenance file that re-points products at a supplier without restating
 * everything else must not destroy what it does not mention — which is what
 * these tests pin. Legacy behaved this way for free, because it read the values
 * off a loaded Product whose fillInfo() skipped empty cells.
 */
class ProductImporterSupplierTest extends AbstractProductImportEngineTestCase
{
    private const CREATE_FIELDS = ['name', 'reference', 'supplier', 'supplier_reference', 'wholesale_price'];
    private const REIMPORT_FIELDS = ['reference', 'supplier'];

    private static int $firstSupplierId;
    private static int $secondSupplierId;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        DatabaseDump::restoreTables(['supplier', 'supplier_shop', 'supplier_lang', 'product_supplier']);
        ProductResetter::resetProducts();
        self::bootKernel();

        $db = Db::getInstance();
        self::$firstSupplierId = self::insertSupplier($db, 'Import Supplier One');
        self::$secondSupplierId = self::insertSupplier($db, 'Import Supplier Two');
    }

    public static function tearDownAfterClass(): void
    {
        DatabaseDump::restoreTables([
            'supplier', 'supplier_shop', 'supplier_lang', 'product_supplier',
            'currency', 'currency_lang', 'currency_shop',
        ]);
        parent::tearDownAfterClass();
    }

    /**
     * Re-importing with the supplier column mapped but NOT wholesale_price or
     * supplier_reference must leave both values alone. Sending the raw cells
     * would silently reset the price to 0 and blank the reference on every
     * touched product.
     */
    public function testReimportingWithoutThePriceColumnsKeepsTheCurrentValues(): void
    {
        [, $messages] = $this->runImport('product_supplier_full.csv', self::CREATE_FIELDS);
        $this->assertNoErrors($messages);

        $productId = $this->getProductIdByReference('SUP-MERGE-1');
        $this->assertNotNull($productId);
        $this->assertSame('SUPREF-ORIGINAL', (string) $this->fetchOne(
            'SELECT product_supplier_reference FROM {p}product_supplier WHERE id_product = :id AND id_supplier = :supplier',
            ['id' => $productId, 'supplier' => self::$firstSupplierId]
        ));
        $this->assertSame('12.500000', (string) $this->fetchOne(
            'SELECT product_supplier_price_te FROM {p}product_supplier WHERE id_product = :id AND id_supplier = :supplier',
            ['id' => $productId, 'supplier' => self::$firstSupplierId]
        ));

        [, $messages] = $this->runImport('product_supplier_reimport.csv', self::REIMPORT_FIELDS, ['matchRef' => true]);
        $this->assertNoErrors($messages);

        $this->assertSame('SUPREF-ORIGINAL', (string) $this->fetchOne(
            'SELECT product_supplier_reference FROM {p}product_supplier WHERE id_product = :id AND id_supplier = :supplier',
            ['id' => $productId, 'supplier' => self::$firstSupplierId]
        ), 'An unmapped supplier_reference column must not blank the existing reference');
        $this->assertSame('12.500000', (string) $this->fetchOne(
            'SELECT product_supplier_price_te FROM {p}product_supplier WHERE id_product = :id AND id_supplier = :supplier',
            ['id' => $productId, 'supplier' => self::$firstSupplierId]
        ), 'An unmapped wholesale_price column must not reset the supplier price to 0');
    }

    /**
     * The file has no currency column at all, so a supplier price recorded in a
     * non-default currency must keep it. Resetting it to the shop default would
     * leave the NUMBER untouched while changing its meaning — 100 USD silently
     * read as 100 EUR. Legacy reset it on every save; this is a deliberate
     * divergence, for the same reason as the price and reference above.
     */
    public function testReimportingKeepsTheCurrencyTheSupplierPriceWasRecordedIn(): void
    {
        ProductResetter::resetProducts();

        [, $messages] = $this->runImport('product_supplier_full.csv', self::CREATE_FIELDS);
        $this->assertNoErrors($messages);
        $productId = $this->getProductIdByReference('SUP-MERGE-1');
        $this->assertNotNull($productId);

        // a REAL second currency: the updater rejects one that does not exist,
        // so the association cannot be pinned to a synthetic id
        $otherCurrencyId = $this->createSecondCurrency();
        $this->connection->executeStatement(
            str_replace('{p}', $this->dbPrefix, 'UPDATE {p}product_supplier SET id_currency = :currency WHERE id_product = :id AND id_supplier = :supplier'),
            ['currency' => $otherCurrencyId, 'id' => $productId, 'supplier' => self::$firstSupplierId]
        );

        [, $messages] = $this->runImport('product_supplier_reimport.csv', self::REIMPORT_FIELDS, ['matchRef' => true]);
        $this->assertNoErrors($messages);

        $this->assertSame(
            $otherCurrencyId,
            (int) $this->fetchOne(
                'SELECT id_currency FROM {p}product_supplier WHERE id_product = :id AND id_supplier = :supplier',
                ['id' => $productId, 'supplier' => self::$firstSupplierId]
            ),
            'Re-importing must not silently move the supplier price to the shop default currency'
        );
    }

    /**
     * The file names one supplier; a product may legitimately have several
     * (added in the back office, or by an earlier file). Importing must ADD to
     * the association, never replace it.
     */
    public function testReimportingKeepsTheSuppliersTheFileDoesNotMention(): void
    {
        ProductResetter::resetProducts();

        [, $messages] = $this->runImport('product_supplier_full.csv', self::CREATE_FIELDS);
        $this->assertNoErrors($messages);
        $productId = $this->getProductIdByReference('SUP-MERGE-1');
        $this->assertNotNull($productId);

        // a second supplier the import file knows nothing about
        Db::getInstance()->insert('product_supplier', [
            'id_product' => $productId,
            'id_product_attribute' => 0,
            'id_supplier' => self::$secondSupplierId,
            'product_supplier_reference' => 'ADDED-BY-HAND',
            'product_supplier_price_te' => '9.990000',
            'id_currency' => 1,
        ]);

        [, $messages] = $this->runImport('product_supplier_reimport.csv', self::REIMPORT_FIELDS, ['matchRef' => true]);
        $this->assertNoErrors($messages);

        $associatedSupplierIds = array_map(
            static fn (array $row): int => (int) $row['id_supplier'],
            $this->fetchAll('SELECT id_supplier FROM {p}product_supplier WHERE id_product = :id ORDER BY id_supplier ASC', ['id' => $productId])
        );
        $this->assertSame(
            [self::$firstSupplierId, self::$secondSupplierId],
            $associatedSupplierIds,
            'Importing one supplier must not delete the others'
        );
        $this->assertSame('ADDED-BY-HAND', (string) $this->fetchOne(
            'SELECT product_supplier_reference FROM {p}product_supplier WHERE id_product = :id AND id_supplier = :supplier',
            ['id' => $productId, 'supplier' => self::$secondSupplierId]
        ));
    }

    /**
     * The default catalog ships a single currency, so the non-default one this
     * test needs has to be created — and it must really exist, because the
     * supplier updater validates the currency id.
     */
    private function createSecondCurrency(): int
    {
        $db = Db::getInstance();
        $db->insert('currency', [
            'name' => 'Import Test Dollar', 'iso_code' => 'USD', 'numeric_iso_code' => '840',
            'precision' => 2, 'conversion_rate' => '1.100000', 'deleted' => 0,
            'active' => 1, 'unofficial' => 0, 'modified' => 0,
        ]);
        $currencyId = (int) $db->Insert_ID();
        $db->insert('currency_lang', [
            'id_currency' => $currencyId, 'id_lang' => 1,
            'name' => 'Import Test Dollar', 'symbol' => '$', 'pattern' => '¤#,##0.00',
        ]);
        $db->insert('currency_shop', [
            'id_currency' => $currencyId, 'id_shop' => self::DEFAULT_SHOP_ID, 'conversion_rate' => '1.100000',
        ]);

        return $currencyId;
    }

    private static function insertSupplier(Db $db, string $name): int
    {
        $db->insert('supplier', ['name' => pSQL($name), 'active' => 1, 'date_add' => date('Y-m-d H:i:s'), 'date_upd' => date('Y-m-d H:i:s')]);
        $supplierId = (int) $db->Insert_ID();
        $db->insert('supplier_shop', ['id_supplier' => $supplierId, 'id_shop' => self::DEFAULT_SHOP_ID]);
        $db->insert('supplier_lang', ['id_supplier' => $supplierId, 'id_lang' => 1, 'description' => '', 'meta_title' => '', 'meta_description' => '']);

        return $supplierId;
    }
}
