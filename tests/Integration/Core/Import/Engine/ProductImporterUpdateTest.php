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
}
