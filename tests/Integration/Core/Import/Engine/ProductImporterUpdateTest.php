<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Import\Engine;

use Tests\Resources\Resetter\ProductResetter;

class ProductImporterUpdateTest extends AbstractProductImportEngineTestCase
{
    private const FORCE_IDS_FIELDS = ['id', 'name', 'reference', 'price_tex'];
    private const MATCH_REF_FIELDS = ['reference', 'name', 'price_tex'];
    private const SPECIFIC_PRICE_FIELDS = ['reference', 'name', 'price_tex', 'reduction_price', 'reduction_from', 'reduction_to'];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        ProductResetter::resetProducts();
    }

    public static function tearDownAfterClass(): void
    {
        ProductResetter::resetProducts();
        parent::tearDownAfterClass();
    }

    public function testForceIdsCreatesWithForcedIdAndUpdatesExisting(): void
    {
        [, $messages] = $this->runImport('product_forceids.csv', self::FORCE_IDS_FIELDS, ['forceIds' => true]);
        $this->assertNoErrors($messages);

        // unknown id 9911 -> created with EXACTLY that id, through the fallback writer
        $forced = $this->fetchRow('SELECT * FROM {p}product WHERE id_product = 9911');
        $this->assertNotFalse($forced, 'Product was not created with the forced id 9911');
        $this->assertSame('FORCED-9911', $forced['reference']);
        $this->assertNotFalse($this->fetchRow('SELECT 1 FROM {p}product_shop WHERE id_product = 9911 AND id_shop = 1'));
        $this->assertNotFalse($this->fetchRow('SELECT 1 FROM {p}product_lang WHERE id_product = 9911 AND id_lang = 1 AND id_shop = 1'));
        $this->assertNotFalse($this->fetchRow('SELECT 1 FROM {p}stock_available WHERE id_product = 9911 AND id_product_attribute = 0'));
        $this->assertNotFalse($this->fetchRow('SELECT 1 FROM {p}category_product WHERE id_product = 9911'));
        $this->assertSame('Forced Id Product', $this->fetchOne('SELECT name FROM {p}product_lang WHERE id_product = 9911 AND id_lang = 1 AND id_shop = 1'));

        // existing id 1 -> updated in place
        $this->assertSame('Renamed By Id', $this->fetchOne('SELECT name FROM {p}product_lang WHERE id_product = 1 AND id_lang = 1 AND id_shop = 1'));
        $this->assertSame('42.000000', (string) $this->fetchOne('SELECT price FROM {p}product_shop WHERE id_product = 1 AND id_shop = 1'));
    }

    public function testForceIdsOffIgnoresTheIdColumn(): void
    {
        [, $messages] = $this->runImport('product_forceids.csv', self::FORCE_IDS_FIELDS, ['forceIds' => false]);
        $this->assertNoErrors($messages);

        // the id 9955 does not exist anywhere in the file; row 1 (id 9911) must
        // have been created with an auto-increment id, NOT 9911 (already created
        // by the previous test - so here we assert the second run created new
        // products instead of updating by id)
        $forcedProducts = $this->fetchAll("SELECT id_product FROM {p}product WHERE reference = 'FORCED-9911' ORDER BY id_product");
        $this->assertCount(2, $forcedProducts, 'force IDs off must create a NEW product, ignoring the id column');
        $this->assertNotSame('9911', (string) $forcedProducts[1]['id_product']);

        // row 2 has id 1 but no reference: with force IDs off it must NOT update
        // product 1 but create a new product
        $this->assertSame('Renamed By Id', $this->fetchOne('SELECT name FROM {p}product_lang WHERE id_product = 1 AND id_lang = 1 AND id_shop = 1'), 'Product 1 name must be unchanged from the previous run');
    }

    public function testMatchRefUpdatesByReferenceAndCreatesUnknownReferences(): void
    {
        [, $messages] = $this->runImport('product_match_ref.csv', self::MATCH_REF_FIELDS, ['matchRef' => true]);
        $this->assertNoErrors($messages);

        // demo_1 is a fixture product: updated, not duplicated
        $demoProducts = $this->fetchAll("SELECT p.id_product FROM {p}product p WHERE p.reference = 'demo_1'");
        $this->assertCount(1, $demoProducts);
        $demoProductId = (int) $demoProducts[0]['id_product'];
        $this->assertSame('Updated By Reference', $this->fetchOne('SELECT name FROM {p}product_lang WHERE id_product = :id AND id_lang = 1 AND id_shop = 1', ['id' => $demoProductId]));
        $this->assertSame('55.500000', (string) $this->fetchOne('SELECT price FROM {p}product_shop WHERE id_product = :id AND id_shop = 1', ['id' => $demoProductId]));

        // unknown reference -> created
        $this->assertNotNull($this->getProductIdByReference('NEW-REF-777'));
    }

    /**
     * Re-importing a row that carries a reduction used to fail the WHOLE row:
     * AddSpecificPriceCommand hit NOT_UNIQUE_PER_PRODUCT, the row was reported as
     * an error and its accessories were then dropped by the association phase.
     */
    public function testSpecificPriceReimportUpdatesInsteadOfFailingTheRow(): void
    {
        [, $messages] = $this->runImport('product_specific_price_reimport.csv', self::SPECIFIC_PRICE_FIELDS, ['matchRef' => true]);
        $this->assertNoErrors($messages);

        $productId = $this->getProductIdByReference('SPEC-REIMPORT-1');
        $this->assertNotNull($productId);
        $this->assertSame('10.000000', (string) $this->fetchOne('SELECT reduction FROM {p}specific_price WHERE id_product = :id', ['id' => $productId]));

        // exact same file again: the identical rule already exists
        [, $messages] = $this->runImport('product_specific_price_reimport.csv', self::SPECIFIC_PRICE_FIELDS, ['matchRef' => true]);
        $this->assertNoErrors($messages);
        $this->assertCount(
            1,
            $this->fetchAll('SELECT id_specific_price FROM {p}specific_price WHERE id_product = :id', ['id' => $productId]),
            'Re-importing an identical reduction must not create a second rule'
        );

        // same dates, new value: the existing rule is edited, not duplicated
        [, $messages] = $this->runImport('product_specific_price_reimport_updated.csv', self::SPECIFIC_PRICE_FIELDS, ['matchRef' => true]);
        $this->assertNoErrors($messages);

        $specificPrices = $this->fetchAll('SELECT * FROM {p}specific_price WHERE id_product = :id', ['id' => $productId]);
        $this->assertCount(1, $specificPrices, 'Changing the reduction value must edit the existing rule');
        $this->assertSame('7.500000', (string) $specificPrices[0]['reduction']);
        $this->assertSame('amount', (string) $specificPrices[0]['reduction_type']);
    }

    /**
     * A maintenance file that only re-prices existing products has no reason to
     * carry a name column. Required-ness is decided PER ROW — a name is
     * mandatory only when the row CREATES a product — not per mapped column, so
     * this must import cleanly instead of being rejected up front.
     *
     * Kept last on purpose: the force-IDs tests above share state, so inserting
     * a resetting test between them breaks them.
     */
    public function testUpdateOnlyFileWithoutANameColumnImportsCleanly(): void
    {
        ProductResetter::resetProducts();

        [, $messages] = $this->runImport('product_price_only_update.csv', ['reference', 'price_tex'], ['matchRef' => true]);
        $this->assertNoErrors($messages);

        $productId = $this->getProductIdByReference('demo_1');
        $this->assertNotNull($productId);
        $this->assertSame('77.250000', (string) $this->fetchOne('SELECT price FROM {p}product_shop WHERE id_product = :id AND id_shop = 1', ['id' => $productId]));
    }
}
