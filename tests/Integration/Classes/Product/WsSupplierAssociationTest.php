<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Product;

use Configuration;
use Db;
use PHPUnit\Framework\TestCase;
use Product;
use Tools;

/**
 * id_supplier, supplier_reference and wholesale_price are columns of the product row, so the webservice
 * stored them and answered success while product_supplier - the association the back office reads - was
 * never written.
 */
class WsSupplierAssociationTest extends TestCase
{
    private int $idSupplier = 0;

    private int $otherSupplier = 0;

    /** @var int[] */
    private array $productIds = [];

    protected function setUp(): void
    {
        parent::setUp();

        $suppliers = Db::getInstance()->executeS(
            'SELECT id_supplier FROM ' . _DB_PREFIX_ . 'supplier ORDER BY id_supplier ASC LIMIT 2'
        );
        if (count($suppliers) < 2) {
            $this->markTestSkipped('Two suppliers are needed to prove the others are left alone.');
        }
        $this->idSupplier = (int) $suppliers[0]['id_supplier'];
        $this->otherSupplier = (int) $suppliers[1]['id_supplier'];
    }

    protected function tearDown(): void
    {
        foreach ($this->productIds as $id) {
            Db::getInstance()->delete('product_supplier', 'id_product = ' . $id);
            Db::getInstance()->delete('category_product', 'id_product = ' . $id);
            (new Product($id))->delete();
        }
        $this->productIds = [];

        parent::tearDown();
    }

    public function testCreatingAProductThroughTheWebserviceAssociatesItsSupplier(): void
    {
        $product = $this->webserviceProduct('ws supplier created');
        $product->addWs();
        $this->productIds[] = (int) $product->id;

        $rows = $this->associationsOf((int) $product->id);
        $this->assertCount(1, $rows);
        $this->assertSame($this->idSupplier, (int) $rows[0]['id_supplier']);
        $this->assertSame('WS-REF', $rows[0]['product_supplier_reference']);
        $this->assertSame(4.5, (float) $rows[0]['product_supplier_price_te']);
    }

    public function testUpdatingThroughTheWebserviceAssociatesItToo(): void
    {
        $product = $this->webserviceProduct('ws supplier updated');
        $product->add();
        $this->productIds[] = (int) $product->id;
        $this->assertCount(0, $this->associationsOf((int) $product->id), 'a plain add() leaves it unassociated');

        $reloaded = new Product((int) $product->id);
        $reloaded->id_supplier = $this->idSupplier;
        $reloaded->supplier_reference = 'WS-REF';
        $reloaded->wholesale_price = 4.5;
        $reloaded->updateWs();

        $rows = $this->associationsOf((int) $product->id);
        $this->assertCount(1, $rows);
        $this->assertSame($this->idSupplier, (int) $rows[0]['id_supplier']);
    }

    /**
     * Repeating the call neither stacks rows up nor rewrites the one that is there.
     */
    public function testRepeatingTheUpdateLeavesTheAssociationAlone(): void
    {
        $product = $this->webserviceProduct('ws supplier repeated');
        $product->addWs();
        $this->productIds[] = (int) $product->id;

        $again = new Product((int) $product->id);
        $again->supplier_reference = 'WS-REF-SECOND';
        $again->updateWs();

        $rows = $this->associationsOf((int) $product->id);
        $this->assertCount(1, $rows);
        $this->assertSame('WS-REF', $rows[0]['product_supplier_reference']);
    }

    /**
     * The reference and the cost price on the product row can drift from the association - the back
     * office syncs the row FROM the association, and "Cost price" is a product field of its own. A
     * webservice request hydrates the entity from the database, so a call that says nothing about
     * suppliers arrives carrying those drifted values; it must not write them back.
     */
    public function testAWebserviceUpdateDoesNotOverwriteWhatTheBackOfficeSet(): void
    {
        $product = $this->webserviceProduct('ws supplier drifted');
        $product->add();
        $this->productIds[] = (int) $product->id;
        $product->addSupplierReference($this->idSupplier, 0, 'BO-REF', 9.99);

        Db::getInstance()->update(
            'product',
            ['supplier_reference' => '', 'wholesale_price' => 4.5],
            'id_product = ' . (int) $product->id
        );

        $reloaded = new Product((int) $product->id);
        $reloaded->updateWs();

        $rows = $this->associationsOf((int) $product->id);
        $this->assertCount(1, $rows);
        $this->assertSame('BO-REF', $rows[0]['product_supplier_reference']);
        $this->assertSame(9.99, (float) $rows[0]['product_supplier_price_te']);
    }

    /**
     * A supplier a merchant attached elsewhere must survive a webservice update that says nothing about it.
     */
    public function testAnotherSuppliersAssociationIsLeftAlone(): void
    {
        $product = $this->webserviceProduct('ws supplier preserved');
        $product->addWs();
        $this->productIds[] = (int) $product->id;
        $product->addSupplierReference($this->otherSupplier, 0, 'SET-IN-THE-BACK-OFFICE', 9.0);
        $this->assertCount(2, $this->associationsOf((int) $product->id));

        $again = new Product((int) $product->id);
        $again->id_supplier = $this->idSupplier;
        $again->supplier_reference = 'WS-REF';
        $again->updateWs();

        $rows = $this->associationsOf((int) $product->id);
        $this->assertCount(2, $rows, 'the other supplier is still there');
        $kept = array_values(array_filter($rows, fn ($r) => (int) $r['id_supplier'] === $this->otherSupplier));
        $this->assertSame('SET-IN-THE-BACK-OFFICE', $kept[0]['product_supplier_reference']);
    }

    /**
     * A payload carrying no supplier must not invent one.
     */
    public function testAProductWithNoSupplierGetsNoAssociation(): void
    {
        $product = $this->webserviceProduct('ws supplier absent');
        $product->id_supplier = 0;
        $product->supplier_reference = '';
        $product->addWs();
        $this->productIds[] = (int) $product->id;

        $this->assertCount(0, $this->associationsOf((int) $product->id));
    }

    private function webserviceProduct(string $name): Product
    {
        $lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $product = new Product();
        $product->id_category_default = 2;
        $product->name = [$lang => $name];
        $product->link_rewrite = [$lang => Tools::str2url($name)];
        $product->price = 10;
        $product->id_tax_rules_group = 1;
        $product->id_supplier = $this->idSupplier;
        $product->supplier_reference = 'WS-REF';
        $product->wholesale_price = 4.5;

        return $product;
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function associationsOf(int $idProduct): array
    {
        return Db::getInstance()->executeS(
            'SELECT id_supplier, product_supplier_reference, product_supplier_price_te
             FROM ' . _DB_PREFIX_ . 'product_supplier WHERE id_product = ' . $idProduct
        ) ?: [];
    }
}
