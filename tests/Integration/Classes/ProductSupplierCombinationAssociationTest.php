<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Db;
use PHPUnit\Framework\TestCase;
use PrestaShopException;
use ProductSupplier;
use Tests\Integration\Utility\ContextMockerTrait;

/**
 * When the combinations import creates a combination it copies the product's supplier associations onto
 * it. It decided whether one already existed by looking at the combination id carried by the row it was
 * handed, but getSupplierCollection() groups by supplier, so that row can be the product level one even
 * when the combination already has its own association. Copying it again then breaks the unique key on
 * (id_product, id_product_attribute, id_supplier) and aborts the whole import.
 */
class ProductSupplierCombinationAssociationTest extends TestCase
{
    use ContextMockerTrait;

    private const PRODUCT_ID = 999123;
    private const COMBINATION_ID = 7;
    private const SUPPLIER_ID = 3;

    protected function setUp(): void
    {
        parent::setUp();
        self::mockContext();

        $this->removeRows();
        // What the product already has: an association at product level, and one for the combination.
        $this->associate(0);
        $this->associate(self::COMBINATION_ID);
    }

    protected function tearDown(): void
    {
        $this->removeRows();

        parent::tearDown();
    }

    /**
     * The row the import is handed does not tell it whether this combination is already associated.
     */
    public function testTheCollectionCanHandBackTheProductLevelRow(): void
    {
        $rows = ProductSupplier::getSupplierCollection(self::PRODUCT_ID);

        $combinationIds = [];
        foreach ($rows as $row) {
            $combinationIds[] = (int) $row->id_product_attribute;
        }

        self::assertCount(1, $combinationIds, 'the collection is grouped by supplier');
        self::assertSame(
            [0],
            $combinationIds,
            'the row handed back is the product level one, so its combination id says nothing about ' . self::COMBINATION_ID
        );
    }

    /**
     * Asking for the combination does tell it.
     */
    public function testAskingForTheCombinationFindsTheExistingAssociation(): void
    {
        self::assertGreaterThan(
            0,
            ProductSupplier::getIdByProductAndSupplier(self::PRODUCT_ID, self::COMBINATION_ID, self::SUPPLIER_ID)
        );
    }

    /**
     * And this is why the difference matters: copying the association a second time is what the import
     * did, and it is rejected the way the report describes.
     */
    public function testCopyingItAgainBreaksTheUniqueKey(): void
    {
        $this->expectException(PrestaShopException::class);
        $this->expectExceptionMessageMatches('/Duplicate entry/');

        $this->associate(self::COMBINATION_ID);
    }

    private function associate(int $combinationId): void
    {
        $productSupplier = new ProductSupplier();
        $productSupplier->id_product = self::PRODUCT_ID;
        $productSupplier->id_product_attribute = $combinationId;
        $productSupplier->id_supplier = self::SUPPLIER_ID;
        $productSupplier->product_supplier_reference = 'REF-' . $combinationId;
        $productSupplier->product_supplier_price_te = 1.0;
        $productSupplier->id_currency = 1;
        $productSupplier->add();
    }

    private function removeRows(): void
    {
        Db::getInstance()->execute(
            'DELETE FROM ' . _DB_PREFIX_ . 'product_supplier WHERE id_product = ' . self::PRODUCT_ID
        );
    }
}
