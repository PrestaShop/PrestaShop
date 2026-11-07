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
 * setWsCombinations() used to insert a bare product_attribute row for any id it did not recognise -
 * including the empty <id/> that `products?schema=blank` hands back - giving a simple product a
 * combination no shop-scoped query can see.
 */
class WsCombinationAssociationTest extends TestCase
{
    /** @var int[] */
    private array $productIds = [];

    protected function tearDown(): void
    {
        foreach ($this->productIds as $id) {
            Db::getInstance()->delete('product_attribute', 'id_product = ' . $id);
            Db::getInstance()->delete('stock_available', 'id_product = ' . $id);
            (new Product($id))->delete();
        }
        $this->productIds = [];

        parent::tearDown();
    }

    /**
     * @dataProvider associationsThatNameNoExistingCombination
     *
     * @param array<int, array<string, string>> $association
     */
    public function testAnAssociationNamingNoCombinationCreatesNothing(array $association): void
    {
        $product = $this->newProduct('ws combination none');
        $product->setWsCombinations($association);

        $this->assertSame([], $this->combinationIdsOf((int) $product->id));
    }

    /**
     * @return array<string, array{array<int, array<string, string>>}>
     */
    public function associationsThatNameNoExistingCombination(): array
    {
        return [
            'the empty id that schema=blank returns' => [[['id' => '']]],
            'an explicit zero' => [[['id' => '0']]],
            'an id that exists nowhere' => [[['id' => '99999999']]],
            'no entries at all' => [[]],
        ];
    }

    /**
     * The association still does what it is for: an existing combination is moved onto the product.
     */
    public function testAnExistingCombinationIsStillAssociated(): void
    {
        $donor = $this->newProduct('ws combination donor');
        Db::getInstance()->insert('product_attribute', [
            'id_product' => (int) $donor->id,
            'reference' => 'WS-COMBI',
            'minimal_quantity' => 1,
        ]);
        $idCombination = (int) Db::getInstance()->Insert_ID();
        // A real combination also has its per-shop row; without it Shop::addSqlAssociation() hides it.
        Db::getInstance()->insert('product_attribute_shop', [
            'id_product' => (int) $donor->id,
            'id_product_attribute' => $idCombination,
            'id_shop' => 1,
            'minimal_quantity' => 1,
        ]);

        $target = $this->newProduct('ws combination target');
        $target->setWsCombinations([['id' => (string) $idCombination]]);

        $this->assertSame([$idCombination], $this->combinationIdsOf((int) $target->id));
        $this->assertSame([], $this->combinationIdsOf((int) $donor->id));

        Db::getInstance()->delete('product_attribute_shop', 'id_product_attribute = ' . $idCombination);
    }

    private function newProduct(string $name): Product
    {
        $lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $product = new Product();
        $product->id_category_default = 2;
        $product->name = [$lang => $name];
        $product->link_rewrite = [$lang => Tools::str2url($name . '-' . uniqid())];
        $product->price = 10;
        $product->id_tax_rules_group = 0;
        $product->add();
        $this->productIds[] = (int) $product->id;

        return $product;
    }

    /**
     * @return int[]
     */
    private function combinationIdsOf(int $idProduct): array
    {
        $rows = Db::getInstance()->executeS(
            'SELECT id_product_attribute FROM ' . _DB_PREFIX_ . 'product_attribute WHERE id_product = ' . $idProduct
        ) ?: [];

        return array_map(static fn (array $row): int => (int) $row['id_product_attribute'], $rows);
    }
}
