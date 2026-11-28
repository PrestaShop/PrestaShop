<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Db;
use Image;
use ImageType;
use Shop as LegacyShop;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

class ImageDuplicateProductImagesTest extends KernelTestCase
{
    private const TABLES_TO_RESTORE = ['shop', 'shop_group', 'product_shop', 'image', 'image_lang', 'image_shop'];
    private const SOURCE_PRODUCT_ID = 999501;
    private const TARGET_PRODUCT_ID = 999502;

    /** @var string[] */
    private array $createdFiles = [];

    protected function setUp(): void
    {
        parent::setUp();
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        LegacyShop::resetStaticCache();
        self::bootKernel();
    }

    protected function tearDown(): void
    {
        foreach ($this->createdFiles as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
        $this->createdFiles = [];

        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        LegacyShop::resetStaticCache();
        parent::tearDown();
    }

    public function testDuplicateProductImagesCopiesShopsFromSourceImageAndAllSupportedFormats(): void
    {
        $db = Db::getInstance();

        $db->insert('shop_group', [
            'name' => 'test_dup_img_group',
            'color' => '',
            'share_customer' => 0,
            'share_order' => 0,
            'share_stock' => 0,
            'active' => 1,
            'deleted' => 0,
        ]);
        $secondGroupId = (int) $db->Insert_ID();
        $db->insert('shop', [
            'id_shop_group' => $secondGroupId,
            'name' => 'test_dup_img_shop',
            'color' => '',
            'id_category' => 2,
            'theme_name' => 'classic',
            'active' => 1,
            'deleted' => 0,
        ]);
        $secondShopId = (int) $db->Insert_ID();
        LegacyShop::resetStaticCache();

        $now = date('Y-m-d H:i:s');
        foreach ([
            [self::SOURCE_PRODUCT_ID, 1],
            [self::SOURCE_PRODUCT_ID, $secondShopId],
            [self::TARGET_PRODUCT_ID, 1],
            [self::TARGET_PRODUCT_ID, $secondShopId],
        ] as [$productId, $shopId]) {
            $db->insert('product_shop', [
                'id_product' => $productId,
                'id_shop' => $shopId,
                'id_category_default' => 2,
                'id_tax_rules_group' => 0,
                'date_add' => $now,
                'date_upd' => $now,
            ]);
        }

        $imageOld = new Image();
        $imageOld->id_product = self::SOURCE_PRODUCT_ID;
        $imageOld->position = 1;
        $imageOld->cover = true;
        $this->assertTrue((bool) $imageOld->add());
        $this->assertTrue((bool) $imageOld->associateTo($secondShopId));

        $imageTypes = ImageType::getImagesTypes('products');
        $this->assertNotEmpty($imageTypes);
        $typeName = $imageTypes[0]['name'];

        $oldBasePath = $imageOld->getPathForCreation();
        $this->createDummyFile($oldBasePath . '.jpg');
        foreach (['jpg', 'webp', 'avif'] as $format) {
            $this->createDummyFile($oldBasePath . '-' . $typeName . '.' . $format);
        }

        $this->assertTrue((bool) Image::duplicateProductImages(self::SOURCE_PRODUCT_ID, self::TARGET_PRODUCT_ID, []));

        $newRows = $db->executeS(
            'SELECT `id_image` FROM `' . _DB_PREFIX_ . 'image` WHERE `id_product` = ' . self::TARGET_PRODUCT_ID
        );
        $this->assertCount(1, $newRows);

        $imageNew = new Image((int) $newRows[0]['id_image']);
        $newBasePath = _PS_PRODUCT_IMG_DIR_ . $imageNew->getExistingImgPath();
        $this->createdFiles[] = $newBasePath . '.jpg';
        foreach (['jpg', 'webp', 'avif'] as $format) {
            $this->createdFiles[] = $newBasePath . '-' . $typeName . '.' . $format;
        }

        $this->assertFileExists($newBasePath . '.jpg');
        $this->assertFileExists($newBasePath . '-' . $typeName . '.jpg');
        $this->assertFileExists($newBasePath . '-' . $typeName . '.webp');
        $this->assertFileExists($newBasePath . '-' . $typeName . '.avif');

        $newShops = $imageNew->getAssociatedShops();
        sort($newShops);
        $expectedShops = [1, $secondShopId];
        sort($expectedShops);
        $this->assertSame($expectedShops, $newShops);
    }

    private function createDummyFile(string $path): void
    {
        file_put_contents($path, 'dummy');
        $this->createdFiles[] = $path;
    }
}
