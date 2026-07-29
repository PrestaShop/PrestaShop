<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Product\Combination;

use Combination;
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
 * A combination image selection belongs to the shop it was made in. Saving the combination in one
 * shop must not discard the images another shop had associated with it.
 */
class CombinationImagesShopScopeTest extends KernelTestCase
{
    private const TABLES_TO_RESTORE = ['shop', 'shop_group', 'image_shop', 'product_attribute_image', 'configuration'];

    private const COMBINATION_ID = 1;
    private const SHOP_1_IMAGE_ID = 1;
    private const SHOP_2_IMAGE_ID = 2;

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

        // Each shop owns exactly one of the two images: image 1 stays in shop 1, image 2 moves to
        // the second shop. This is the ordinary multistore setup - an image belongs to the shops it
        // was associated with, and the combination selection can only ever name a visible image.
        $db->delete('image_shop', 'id_image = ' . self::SHOP_2_IMAGE_ID);
        $db->insert('image_shop', [
            'id_product' => 1, 'id_image' => self::SHOP_2_IMAGE_ID,
            'id_shop' => self::$secondShopId, 'cover' => 1,
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        // Each case builds its own associations, so start from none for this combination.
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
     * Reproduces the reported journey: pick an image for the combination in one shop, switch the
     * shop context, pick that shop's own image, and the first shop's choice must still be there.
     */
    public function testSavingInOneShopKeepsAnotherShopsSelection(): void
    {
        self::bootKernel();
        $commandBus = self::getContainer()->get('prestashop.core.command_bus');

        $commandBus->handle(new SetCombinationImagesCommand(
            self::COMBINATION_ID,
            [self::SHOP_1_IMAGE_ID],
            ShopConstraint::shop(1)
        ));
        $commandBus->handle(new SetCombinationImagesCommand(
            self::COMBINATION_ID,
            [self::SHOP_2_IMAGE_ID],
            ShopConstraint::shop(self::$secondShopId)
        ));

        $associatedImageIds = $this->getAssociatedImageIds();

        self::assertContains(
            self::SHOP_1_IMAGE_ID,
            $associatedImageIds,
            'saving the combination in the second shop discarded the first shop selection'
        );
        self::assertContains(self::SHOP_2_IMAGE_ID, $associatedImageIds, 'the second shop selection was stored');
    }

    /**
     * The mirror of the above: a shop must not be able to drop an image it cannot even see, so
     * removing every image in one shop only clears the ones belonging to that shop.
     */
    public function testClearingInOneShopKeepsAnotherShopsSelection(): void
    {
        self::bootKernel();
        $commandBus = self::getContainer()->get('prestashop.core.command_bus');
        $this->associateOneImagePerShop($commandBus);

        $commandBus->handle(new RemoveAllCombinationImagesCommand(
            self::COMBINATION_ID,
            ShopConstraint::shop(self::$secondShopId)
        ));

        $associatedImageIds = $this->getAssociatedImageIds();

        self::assertContains(
            self::SHOP_1_IMAGE_ID,
            $associatedImageIds,
            'clearing the images in the second shop discarded the first shop selection'
        );
        self::assertNotContains(
            self::SHOP_2_IMAGE_ID,
            $associatedImageIds,
            'the second shop images were not cleared'
        );
    }

    /**
     * The form and the combination list read through this repository, so each shop must be shown
     * its own associations only - otherwise a shop lists, and then re-saves, another shop images.
     */
    public function testEachShopReadsOnlyItsOwnAssociations(): void
    {
        self::bootKernel();
        $this->associateOneImagePerShop(self::getContainer()->get('prestashop.core.command_bus'));
        $repository = self::getContainer()->get(ProductImageRepository::class);
        $combinationId = new CombinationId(self::COMBINATION_ID);

        $shop1ImageIds = $this->flattenImageIds(
            $repository->getImageIdsForCombinations([$combinationId], ShopConstraint::shop(1))
        );
        $shop2ImageIds = $this->flattenImageIds(
            $repository->getImageIdsForCombinations([$combinationId], ShopConstraint::shop(self::$secondShopId))
        );

        self::assertSame([self::SHOP_1_IMAGE_ID], $shop1ImageIds, 'the first shop sees only its own image');
        self::assertSame([self::SHOP_2_IMAGE_ID], $shop2ImageIds, 'the second shop sees only its own image');
    }

    /**
     * Scoping the delete must not make a row unreachable. An association naming an image of the
     * first shop is the first shop's to remove, whichever shop happened to write it.
     */
    public function testAnAssociationWrittenFromAnotherShopStaysRemovable(): void
    {
        self::bootKernel();
        $commandBus = self::getContainer()->get('prestashop.core.command_bus');

        // The webservice and the import can write an association naming an image the writing shop
        // does not hold; the form cannot, because its choices are already shop scoped.
        $commandBus->handle(new SetCombinationImagesCommand(
            self::COMBINATION_ID,
            [self::SHOP_1_IMAGE_ID],
            ShopConstraint::shop(self::$secondShopId)
        ));
        self::assertContains(self::SHOP_1_IMAGE_ID, $this->getAssociatedImageIds());

        $commandBus->handle(new RemoveAllCombinationImagesCommand(
            self::COMBINATION_ID,
            ShopConstraint::shop(1)
        ));

        self::assertNotContains(
            self::SHOP_1_IMAGE_ID,
            $this->getAssociatedImageIds(),
            'the shop owning the image cannot remove an association another shop wrote'
        );
    }

    /**
     * The webservice and the import go through the legacy ObjectModel rather than the command bus,
     * and they discarded other shops' selections the same way.
     */
    public function testLegacySetImagesKeepsAnotherShopsSelection(): void
    {
        self::bootKernel();
        $this->associateOneImagePerShop(self::getContainer()->get('prestashop.core.command_bus'));

        $previousContext = LegacyShop::getContext();
        LegacyShop::setContext(LegacyShop::CONTEXT_SHOP, self::$secondShopId);

        try {
            $combination = new Combination(self::COMBINATION_ID);
            $combination->setImages([self::SHOP_2_IMAGE_ID]);
        } finally {
            LegacyShop::setContext($previousContext, 1);
        }

        self::assertContains(
            self::SHOP_1_IMAGE_ID,
            $this->getAssociatedImageIds(),
            'the legacy setter discarded the first shop selection'
        );
    }

    private function associateOneImagePerShop(object $commandBus): void
    {
        $commandBus->handle(new SetCombinationImagesCommand(
            self::COMBINATION_ID,
            [self::SHOP_1_IMAGE_ID],
            ShopConstraint::shop(1)
        ));
        $commandBus->handle(new SetCombinationImagesCommand(
            self::COMBINATION_ID,
            [self::SHOP_2_IMAGE_ID],
            ShopConstraint::shop(self::$secondShopId)
        ));
    }

    /**
     * @param array<int, ImageId[]> $imageIdsByCombinationId
     *
     * @return int[]
     */
    private function flattenImageIds(array $imageIdsByCombinationId): array
    {
        $imageIds = array_map(
            static fn (ImageId $imageId): int => $imageId->getValue(),
            $imageIdsByCombinationId[self::COMBINATION_ID] ?? []
        );
        sort($imageIds);

        return $imageIds;
    }

    /**
     * @return int[]
     */
    private function getAssociatedImageIds(): array
    {
        $rows = Db::getInstance()->executeS(
            'SELECT id_image FROM ' . _DB_PREFIX_ . 'product_attribute_image
             WHERE id_product_attribute = ' . self::COMBINATION_ID
        );

        return array_map('intval', array_column($rows ?: [], 'id_image'));
    }
}
