<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Image;

use Db;
use Image;
use PrestaShopDatabaseException;
use Product;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * The `id_product_cover` unique key on the image table is not scoped to a shop, so anything deciding the
 * value of that column has to ask the question globally. Image::getCover() answers it per shop, which is
 * the mismatch behind #23777.
 */
class CoverScopeTest extends KernelTestCase
{
    private ?int $productId = null;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        global $kernel;
        $kernel = self::$kernel;
    }

    protected function tearDown(): void
    {
        if (null !== $this->productId) {
            Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'image_shop WHERE id_product = ' . $this->productId);
            Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'image WHERE id_product = ' . $this->productId);
            (new Product($this->productId))->delete();
            $this->productId = null;
        }
        parent::tearDown();
    }

    private function makeProductWithCover(): Image
    {
        $product = new Product();
        $product->name = ['1' => 'Cover scope product'];
        $product->link_rewrite = ['1' => 'cover-scope-product'];
        $product->price = 10.0;
        $product->add();
        $this->productId = (int) $product->id;

        $cover = new Image();
        $cover->id_product = $this->productId;
        $cover->cover = true;
        $cover->add();
        $cover->associateTo([1]);

        return $cover;
    }

    public function testGlobalCoverIsVisibleWhereTheShopScopedOneIsNot(): void
    {
        $this->makeProductWithCover();

        self::assertNotEmpty(
            Image::getGlobalCover($this->productId),
            'The product carries a cover in the image table'
        );

        // A shop that has no image_shop row for this product. The cover still exists globally, so anything
        // writing the global cover column must not conclude from this that it may claim it.
        $unknownShopId = 1 + (int) Db::getInstance()->getValue('SELECT MAX(id_shop) FROM ' . _DB_PREFIX_ . 'shop');
        self::assertEmpty(
            Image::getCover($this->productId, $unknownShopId),
            'Image::getCover() is scoped to a shop and reports no cover there'
        );
    }

    public function testASecondGlobalCoverIsRejectedByTheDatabase(): void
    {
        $this->makeProductWithCover();

        $second = new Image();
        $second->id_product = $this->productId;
        $second->cover = true;

        $this->expectException(PrestaShopDatabaseException::class);
        $second->add();
    }
}
