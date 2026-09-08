<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Product\Combination;

use Configuration as LegacyConfiguration;
use Db;
use PrestaShop\PrestaShop\Adapter\Product\Image\Repository\ProductImageRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\RemoveAllCombinationImagesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\SetCombinationImagesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Shop as LegacyShop;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

/**
 * The combination image selection is per shop, and `product_attribute_image` carries the shop it was
 * made for. Saving in one shop must leave every other shop's selection alone, including when the two
 * shops picked the very same image - which a rule derived from `image_shop` alone cannot express.
 */
class CombinationImagesShopScopeTest extends KernelTestCase
{
    private const TABLES_TO_RESTORE = ['shop', 'shop_group', 'image_shop', 'product_attribute_image', 'configuration'];

    private const COMBINATION_ID = 1;
    private const SHARED_IMAGE_ID = 1;
    private const SHOP_2_IMAGE_ID = 2;

    private static int $secondShopId;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        LegacyConfiguration::resetStaticCache();
        LegacyShop::resetStaticCache();
        self::bootKernel();

        self::$kernel->getContainer()->get('prestashop.adapter.legacy.configuration')
            ->set('PS_MULTISHOP_FEATURE_ACTIVE', 1);

        $db = Db::getInstance();
        $db->insert('shop_group', [
            'name' => 'test_group_combination_img', 'color' => '', 'share_customer' => 0,
            'share_order' => 0, 'share_stock' => 0, 'active' => 1, 'deleted' => 0,
        ]);
        $secondGroupId = (int) $db->Insert_ID();
        $db->insert('shop', [
            'id_shop_group' => $secondGroupId, 'name' => 'test_shop_combination_img', 'color' => '',
            'id_category' => 2, 'theme_name' => 'classic', 'active' => 1, 'deleted' => 0,
        ]);
        self::$secondShopId = (int) $db->Insert_ID();
        LegacyShop::resetStaticCache();

        // Image 1 belongs to BOTH shops - the case the shop of the image cannot distinguish.
        // Image 2 belongs to the second shop only.
        $db->insert('image_shop', [
            'id_product' => 1, 'id_image' => self::SHARED_IMAGE_ID,
            'id_shop' => self::$secondShopId, 'cover' => 0,
        ]);
        $db->delete('image_shop', 'id_image = ' . self::SHOP_2_IMAGE_ID);
        $db->insert('image_shop', [
            'id_product' => 1, 'id_image' => self::SHOP_2_IMAGE_ID,
            'id_shop' => self::$secondShopId, 'cover' => 1,
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        Db::getInstance()->delete('product_attribute_image', 'id_product_attribute = ' . self::COMBINATION_ID);
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        LegacyShop::resetStaticCache();
        LegacyConfiguration::resetStaticCache();
    }

    /**
     * The case a rule based on the image's own shops cannot express: both shops can see the image,
     * so only the association itself can say which shop chose it.
     */
    public function testAShopKeepsItsSharedImageWhenAnotherShopChangesItsOwn(): void
    {
        self::bootKernel();
        $commandBus = self::getContainer()->get('prestashop.core.command_bus');

        $commandBus->handle(new SetCombinationImagesCommand(
            self::COMBINATION_ID,
            [self::SHARED_IMAGE_ID],
            ShopConstraint::shop(1)
        ));
        $commandBus->handle(new SetCombinationImagesCommand(
            self::COMBINATION_ID,
            [self::SHOP_2_IMAGE_ID],
            ShopConstraint::shop(self::$secondShopId)
        ));

        self::assertSame(
            [self::SHARED_IMAGE_ID],
            $this->getImageIdsForShop(1),
            'the second shop discarded the first shop selection of the shared image'
        );
        self::assertSame([self::SHOP_2_IMAGE_ID], $this->getImageIdsForShop(self::$secondShopId));
    }

    /**
     * Both shops may select the same image, and each holds it independently.
     */
    public function testTwoShopsCanSelectTheSameImageIndependently(): void
    {
        self::bootKernel();
        $commandBus = self::getContainer()->get('prestashop.core.command_bus');

        $commandBus->handle(new SetCombinationImagesCommand(
            self::COMBINATION_ID,
            [self::SHARED_IMAGE_ID],
            ShopConstraint::shop(1)
        ));
        $commandBus->handle(new SetCombinationImagesCommand(
            self::COMBINATION_ID,
            [self::SHARED_IMAGE_ID],
            ShopConstraint::shop(self::$secondShopId)
        ));

        self::assertSame([self::SHARED_IMAGE_ID], $this->getImageIdsForShop(1));
        self::assertSame([self::SHARED_IMAGE_ID], $this->getImageIdsForShop(self::$secondShopId));

        // Clearing it in the second shop leaves the first shop holding it.
        $commandBus->handle(new RemoveAllCombinationImagesCommand(
            self::COMBINATION_ID,
            ShopConstraint::shop(self::$secondShopId)
        ));

        self::assertSame([self::SHARED_IMAGE_ID], $this->getImageIdsForShop(1), 'the first shop lost the shared image');
        self::assertSame([], $this->getImageIdsForShop(self::$secondShopId));
    }

    /**
     * @return int[]
     */
    private function getImageIdsForShop(int $shopId): array
    {
        $repository = self::getContainer()->get(ProductImageRepository::class);
        $imageIds = $repository->getImageIdsForCombinations(
            [new CombinationId(self::COMBINATION_ID)],
            ShopConstraint::shop($shopId)
        );

        $ids = array_map(
            static fn (ImageId $imageId): int => $imageId->getValue(),
            $imageIds[self::COMBINATION_ID] ?? []
        );
        sort($ids);

        return $ids;
    }
}
