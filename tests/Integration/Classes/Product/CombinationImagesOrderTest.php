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
 * The help text of the combination image selector used to promise that the default image would be
 * the first one selected. `ps_product_attribute_image` holds only the two identifiers that form its
 * primary key, so a selection order is never stored, and `Product::getCombinationImages()` orders by
 * the position of the image within the product.
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/38415
 */
class CombinationImagesOrderTest extends TestCase
{
    private int $productId;

    private int $combinationId;

    private int $firstPositionImageId;

    private int $secondPositionImageId;

    /**
     * @var array<int, array<string, mixed>>
     */
    private array $backup = [];

    protected function setUp(): void
    {
        parent::setUp();

        $product = Db::getInstance()->getRow(
            'SELECT i.id_product, MIN(i.position) AS first_position
             FROM ' . _DB_PREFIX_ . 'image i
             INNER JOIN ' . _DB_PREFIX_ . 'product_attribute pa ON pa.id_product = i.id_product
             GROUP BY i.id_product HAVING COUNT(DISTINCT i.id_image) >= 2'
        );

        if (!$product) {
            $this->markTestSkipped('No product with two images and a combination to exercise the ordering with.');
        }

        $this->productId = (int) $product['id_product'];
        $this->combinationId = (int) Db::getInstance()->getValue(
            'SELECT id_product_attribute FROM ' . _DB_PREFIX_ . 'product_attribute WHERE id_product = ' . $this->productId
        );

        $images = Db::getInstance()->executeS(
            'SELECT id_image FROM ' . _DB_PREFIX_ . 'image WHERE id_product = ' . $this->productId . ' ORDER BY position'
        );
        $this->firstPositionImageId = (int) $images[0]['id_image'];
        $this->secondPositionImageId = (int) $images[1]['id_image'];

        $this->backup = Db::getInstance()->executeS(
            'SELECT * FROM ' . _DB_PREFIX_ . 'product_attribute_image WHERE id_product_attribute = ' . $this->combinationId
        ) ?: [];

        Db::getInstance()->delete('product_attribute_image', 'id_product_attribute = ' . $this->combinationId);
        // Inserted back to front on purpose: if the order followed the selection, the image sitting
        // second in the product would come out first.
        Db::getInstance()->insert('product_attribute_image', [
            'id_product_attribute' => $this->combinationId,
            'id_image' => $this->secondPositionImageId,
        ]);
        Db::getInstance()->insert('product_attribute_image', [
            'id_product_attribute' => $this->combinationId,
            'id_image' => $this->firstPositionImageId,
        ]);
    }

    protected function tearDown(): void
    {
        Db::getInstance()->delete('product_attribute_image', 'id_product_attribute = ' . $this->combinationId);
        foreach ($this->backup as $row) {
            Db::getInstance()->insert('product_attribute_image', $row, false, true, Db::INSERT_IGNORE);
        }

        parent::tearDown();
    }

    public function testTheDefaultImageFollowsTheProductOrderNotTheSelectionOrder(): void
    {
        $images = (new Product($this->productId))->getCombinationImages((int) Configuration::get('PS_LANG_DEFAULT'));

        $this->assertIsArray($images);
        $this->assertArrayHasKey($this->combinationId, $images);

        $this->assertSame(
            $this->firstPositionImageId,
            (int) $images[$this->combinationId][0]['id_image'],
            'The first image offered for the combination must be the one that comes first in the product.'
        );
    }

    public function testTheSelectionCarriesNoOrderOfItsOwn(): void
    {
        $columns = array_column(
            Db::getInstance()->executeS('SHOW COLUMNS FROM ' . _DB_PREFIX_ . 'product_attribute_image') ?: [],
            'Field'
        );

        // Nothing here could record which image was picked first.
        $this->assertSame(['id_product_attribute', 'id_image'], $columns);
    }
}
