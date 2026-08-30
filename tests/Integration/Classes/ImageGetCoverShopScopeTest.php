<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Configuration as LegacyConfiguration;
use Db;
use Image;
use Shop as LegacyShop;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

class ImageGetCoverShopScopeTest extends KernelTestCase
{
    private const TABLES_TO_RESTORE = ['shop', 'shop_group', 'image_shop', 'configuration'];

    private static int $secondShopId;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        LegacyConfiguration::resetStaticCache();
        LegacyShop::resetStaticCache();
        self::bootKernel();

        $configuration = self::$kernel->getContainer()->get('prestashop.adapter.legacy.configuration');
        $configuration->set('PS_MULTISHOP_FEATURE_ACTIVE', 1);

        $db = Db::getInstance();
        // Second shop group + shop.
        $db->insert('shop_group', [
            'name' => 'test_group_img', 'color' => '', 'share_customer' => 0,
            'share_order' => 0, 'share_stock' => 0, 'active' => 1, 'deleted' => 0,
        ]);
        $secondGroupId = (int) $db->Insert_ID();
        $db->insert('shop', [
            'id_shop_group' => $secondGroupId, 'name' => 'test_shop_img', 'color' => '',
            'id_category' => 2, 'theme_name' => 'classic', 'active' => 1, 'deleted' => 0,
        ]);
        self::$secondShopId = (int) $db->Insert_ID();
        LegacyShop::resetStaticCache();

        // Product 1 ships images 1 (cover in shop 1) and 2. Make image 2 the cover in the new shop.
        $db->insert('image_shop', ['id_product' => 1, 'id_image' => 1, 'id_shop' => self::$secondShopId, 'cover' => 0]);
        $db->insert('image_shop', ['id_product' => 1, 'id_image' => 2, 'id_shop' => self::$secondShopId, 'cover' => 1]);
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        LegacyShop::resetStaticCache();
        LegacyConfiguration::resetStaticCache();
    }

    /**
     * The product cover is stored per shop in image_shop. Image::getCover() queried it without an
     * id_shop filter, returning an arbitrary shop's cover. Each shop must get its own cover.
     */
    public function testGetCoverIsShopScoped(): void
    {
        self::bootKernel();

        $coverShop1 = Image::getCover(1, 1);
        $coverShop2 = Image::getCover(1, self::$secondShopId);

        self::assertSame(1, (int) $coverShop1['id_image'], 'shop 1 cover is image 1');
        self::assertSame(2, (int) $coverShop2['id_image'], 'second shop cover is image 2');
    }
}
