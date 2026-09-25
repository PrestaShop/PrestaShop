<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Product;

use Category;
use Configuration;
use Db;
use PHPUnit\Framework\TestCase;
use Product;
use Tools;

/**
 * The webservice runs a resource's field setters before it saves the object and before it applies the
 * associations, so on a creation position_in_category is asked for while the category link it refers to
 * does not exist yet. Each test replays that order rather than calling the setter on a ready product.
 *
 * @see \WebserviceRequestCore::saveEntityFromXml()
 */
class WsPositionInCategoryTest extends TestCase
{
    private const SIZE = 5;

    private Category $category;

    private Category $otherCategory;

    /** @var Product[] */
    private array $products = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = $this->createCategory('ws position');
        $this->otherCategory = $this->createCategory('ws position elsewhere');

        for ($i = 0; $i < self::SIZE; ++$i) {
            $product = $this->createProduct('ws position product ' . $i);
            $product->add();
            $product->addToCategories([$this->category->id]);
            $this->products[] = $product;
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->products as $product) {
            if ($product->id) {
                Db::getInstance()->delete('category_product', 'id_product = ' . (int) $product->id);
                $product->delete();
            }
        }
        $this->category->delete();
        $this->otherCategory->delete();
        $this->products = [];

        parent::tearDown();
    }

    public function testACreatedProductLandsAtTheRequestedPosition(): void
    {
        $product = $this->saveThroughWebservice($this->createProduct('ws position created'), 3);

        $this->assertSame('3', (string) $product->getWsPositionInCategory());
        $this->assertSame((int) $product->id, $this->productAt(3));
    }

    /**
     * Nothing may be reordered on behalf of a product that is not in the category yet. The category is
     * given a gap first, because renumbering a contiguous one writes back what was already there.
     */
    public function testAskingBeforeTheAssociationExistsLeavesTheCategoryAlone(): void
    {
        $this->products[1]->setWsCategories([['id' => (string) $this->otherCategory->id]]);
        $before = $this->positions();
        $this->assertSame([1, 3, 4, 5], array_keys($before));

        $product = $this->createProduct('ws position pending');
        $product->setWsPositionInCategory('3');

        $this->assertSame($before, $this->positions());
    }

    /**
     * A product joining the category adds a slot to it, so the position after the last one is free.
     */
    public function testACreatedProductMayTakeTheSlotAfterTheLastOne(): void
    {
        $product = $this->saveThroughWebservice($this->createProduct('ws position appended'), self::SIZE + 1);

        $this->assertSame((string) (self::SIZE + 1), (string) $product->getWsPositionInCategory());
    }

    /**
     * Dropping a category from a product's associations vacates a position and renumbers nothing, so the
     * position stored against a product stops indexing the ordered list.
     */
    public function testAVacatedPositionDoesNotDisplaceAnotherProduct(): void
    {
        $this->products[1]->setWsCategories([['id' => (string) $this->otherCategory->id]]);

        $moved = $this->products[4];
        $moved->setWsPositionInCategory('2');

        $this->assertSame((int) $moved->id, $this->productAt(2));
    }

    public function testAProductAlreadyPlacedStillMoves(): void
    {
        $moved = $this->products[3];
        $moved->setWsPositionInCategory('2');

        $this->assertSame((int) $moved->id, $this->productAt(2));
    }

    /**
     * Replays WebserviceRequestCore::saveEntityFromXml(): field setters, then the save, then the
     * associations.
     */
    private function saveThroughWebservice(Product $product, int $position): Product
    {
        $product->setWsPositionInCategory((string) $position);
        $product->add();
        $product->setWsCategories([['id' => (string) $this->category->id]]);
        $this->products[] = $product;

        return $product;
    }

    private function createCategory(string $name): Category
    {
        $category = new Category();
        $category->name = [1 => $name];
        $category->link_rewrite = [1 => Tools::str2url($name)];
        $category->id_parent = (int) Configuration::get('PS_HOME_CATEGORY');
        $category->active = true;
        $category->add();

        return $category;
    }

    private function createProduct(string $name): Product
    {
        $product = new Product();
        $product->id_category_default = (int) $this->category->id;
        $product->name = [1 => $name];
        $product->link_rewrite = [1 => Tools::str2url($name)];
        $product->price = 10;
        $product->id_tax_rules_group = 1;

        return $product;
    }

    private function productAt(int $position): int
    {
        return (int) Db::getInstance()->getValue(
            'SELECT id_product FROM ' . _DB_PREFIX_ . 'category_product
             WHERE id_category = ' . (int) $this->category->id . ' AND position = ' . $position
        );
    }

    /**
     * @return array<int, int> id_product keyed by position
     */
    private function positions(): array
    {
        $rows = Db::getInstance()->executeS(
            'SELECT id_product, position FROM ' . _DB_PREFIX_ . 'category_product
             WHERE id_category = ' . (int) $this->category->id . ' ORDER BY position'
        );

        $positions = [];
        foreach ($rows as $row) {
            $positions[(int) $row['position']] = (int) $row['id_product'];
        }

        return $positions;
    }
}
