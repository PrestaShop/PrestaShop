<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Category;
use Configuration;
use Db;
use Language;
use Product;
use Shop;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * `indexation` is a shop-scoped field, so it must live in the *_shop tables and survive the
 * separate writes those tables already receive. Category is the interesting case: it had no
 * shop-scoped ObjectModel field before, and Category::addPosition() writes category_shop
 * itself.
 */
class EntityIndexationTest extends KernelTestCase
{
    private int $shopId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shopId = (int) Configuration::get('PS_SHOP_DEFAULT');
        Shop::setContext(Shop::CONTEXT_SHOP, $this->shopId);
    }

    public function testCategoryIndexationDefaultsToIndexable(): void
    {
        $category = $this->createCategory();
        $reloaded = new Category((int) $category->id, null, $this->shopId);

        $this->assertTrue((bool) $reloaded->indexation, 'A new category must be indexable by default.');
        $this->deleteCategory($category);
    }

    public function testCategoryIndexationIsStoredPerShopAndSurvivesPositionWrites(): void
    {
        $category = $this->createCategory();

        $category->indexation = false;
        $category->save();

        // The value must land in category_shop, not in the category table.
        $stored = Db::getInstance()->getValue(
            'SELECT `indexation` FROM `' . _DB_PREFIX_ . 'category_shop` WHERE `id_category` = '
            . (int) $category->id . ' AND `id_shop` = ' . $this->shopId
        );
        $this->assertSame('0', (string) $stored, 'indexation must be written to category_shop.');

        // addPosition() writes the same row; it must not reset the flag.
        $category->addPosition(7, $this->shopId);
        $afterPosition = Db::getInstance()->getRow(
            'SELECT `indexation`, `position` FROM `' . _DB_PREFIX_ . 'category_shop` WHERE `id_category` = '
            . (int) $category->id . ' AND `id_shop` = ' . $this->shopId
        );
        $this->assertSame('0', (string) $afterPosition['indexation'], 'addPosition() must not clear indexation.');
        $this->assertSame('7', (string) $afterPosition['position']);

        $reloaded = new Category((int) $category->id, null, $this->shopId);
        $this->assertFalse((bool) $reloaded->indexation);

        $this->deleteCategory($category);
    }

    public function testProductIndexationIsStoredPerShop(): void
    {
        $productId = (int) Db::getInstance()->getValue(
            'SELECT `id_product` FROM `' . _DB_PREFIX_ . 'product` ORDER BY `id_product` ASC'
        );
        $this->assertGreaterThan(0, $productId, 'The fixture shop must contain a product.');

        $product = new Product($productId, false, null, $this->shopId);
        $original = (bool) $product->indexation;
        $this->assertTrue($original, 'An existing product must default to indexable.');

        $product->indexation = false;
        $product->save();

        $stored = Db::getInstance()->getValue(
            'SELECT `indexation` FROM `' . _DB_PREFIX_ . 'product_shop` WHERE `id_product` = '
            . $productId . ' AND `id_shop` = ' . $this->shopId
        );
        $this->assertSame('0', (string) $stored, 'indexation must be written to product_shop.');

        $reloaded = new Product($productId, false, null, $this->shopId);
        $this->assertFalse((bool) $reloaded->indexation);

        $reloaded->indexation = $original;
        $reloaded->save();
    }

    private function createCategory(): Category
    {
        $category = new Category();
        $category->name = $this->everyLanguage('Indexation test 14317');
        $category->link_rewrite = $this->everyLanguage('indexation-test-14317');
        $category->id_parent = (int) Configuration::get('PS_HOME_CATEGORY');
        $category->active = true;
        $category->add();

        return $category;
    }

    private function deleteCategory(Category $category): void
    {
        $category->delete();
    }

    /**
     * @return array<int, string>
     */
    private function everyLanguage(string $value): array
    {
        $values = [];
        foreach (Language::getLanguages(false) as $language) {
            $values[(int) $language['id_lang']] = $value;
        }

        return $values;
    }
}
