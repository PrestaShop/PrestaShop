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

/**
 * A product created through the webservice stored `id_category_default` but no `category_product` row,
 * so it belonged to no category and the front office could not reach it until it was saved again from
 * the back office.
 */
class WsDefaultCategoryTest extends TestCase
{
    /**
     * @var int[]
     */
    private array $createdProductIds = [];

    protected function tearDown(): void
    {
        foreach ($this->createdProductIds as $productId) {
            (new Product($productId))->delete();
        }
        $this->createdProductIds = [];

        parent::tearDown();
    }

    public function testTheDefaultCategoryIsAssociatedOnCreation(): void
    {
        $homeCategoryId = (int) Configuration::get('PS_HOME_CATEGORY');

        $product = $this->createThroughWebservice($homeCategoryId);

        $this->assertSame(
            [$homeCategoryId],
            $this->associatedCategoryIds((int) $product->id),
            'The default category must be associated so the product is reachable'
        );
    }

    /**
     * A payload naming its own categories is applied after this and replaces them, so the default one
     * must not survive as an extra association.
     */
    public function testCategoriesGivenInThePayloadReplaceTheDefaultOne(): void
    {
        $homeCategoryId = (int) Configuration::get('PS_HOME_CATEGORY');
        $rootCategoryId = (int) Configuration::get('PS_ROOT_CATEGORY');

        $product = $this->createThroughWebservice($homeCategoryId);
        $product->setWsCategories([$rootCategoryId]);

        $this->assertSame([$rootCategoryId], $this->associatedCategoryIds((int) $product->id));
    }

    public function testAnUnknownDefaultCategoryAssociatesNothing(): void
    {
        $product = $this->createThroughWebservice(999999);

        $this->assertSame([], $this->associatedCategoryIds((int) $product->id));
    }

    private function createThroughWebservice(int $defaultCategoryId): Product
    {
        $product = new Product();
        $product->name = [(int) Configuration::get('PS_LANG_DEFAULT') => 'Webservice category probe'];
        $product->link_rewrite = [(int) Configuration::get('PS_LANG_DEFAULT') => 'webservice-category-probe'];
        $product->price = 10;
        $product->active = true;
        $product->id_category_default = $defaultCategoryId;
        $product->addWs();

        $this->createdProductIds[] = (int) $product->id;

        return $product;
    }

    /**
     * @return int[]
     */
    private function associatedCategoryIds(int $productId): array
    {
        $rows = Db::getInstance()->executeS(
            'SELECT id_category FROM ' . _DB_PREFIX_ . 'category_product WHERE id_product = ' . $productId . ' ORDER BY id_category'
        ) ?: [];

        return array_map(static fn (array $row): int => (int) $row['id_category'], $rows);
    }
}
