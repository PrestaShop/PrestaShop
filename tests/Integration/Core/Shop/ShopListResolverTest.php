<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Shop;

use Configuration;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopCollection;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Multistore\MultistoreConfig;
use PrestaShop\PrestaShop\Core\Shop\ShopListResolverInterface;
use Shop;
use ShopGroup;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\Resetter\ShopResetter;

/**
 * ShopListResolver against a real multistore installation: the default shop (1) plus a
 * second shop in the default group, and a third shop alone in a second group. Covers the
 * constraint → shop ids mapping for every ShopConstraint shape, and the deterministic
 * representative-shop rule on both of its branches (default shop in scope / lowest shop
 * id of the scope).
 */
class ShopListResolverTest extends KernelTestCase
{
    private const DEFAULT_SHOP_ID = 1;

    private static int $secondShopId;
    private static int $thirdShopId;
    private static int $defaultGroupId;
    private static int $secondGroupId;

    private static ShopListResolverInterface $shopListResolver;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::bootKernel();

        ShopResetter::resetShops();
        Configuration::updateGlobalValue(MultistoreConfig::FEATURE_STATUS, 1);

        self::$defaultGroupId = (int) Shop::getGroupFromShop(self::DEFAULT_SHOP_ID, true);

        $secondShop = new Shop();
        $secondShop->name = 'Shop List Resolver Shop 2';
        $secondShop->id_shop_group = self::$defaultGroupId;
        $secondShop->id_category = 2;
        $secondShop->save();
        self::$secondShopId = (int) $secondShop->id;

        $secondGroup = new ShopGroup();
        $secondGroup->name = 'Shop List Resolver Group 2';
        $secondGroup->save();
        self::$secondGroupId = (int) $secondGroup->id;

        $thirdShop = new Shop();
        $thirdShop->name = 'Shop List Resolver Shop 3';
        $thirdShop->id_shop_group = self::$secondGroupId;
        $thirdShop->id_category = 2;
        $thirdShop->save();
        self::$thirdShopId = (int) $thirdShop->id;

        Shop::resetStaticCache();
        Shop::resetContext();

        self::$shopListResolver = self::getContainer()->get(ShopListResolverInterface::class);
    }

    public static function tearDownAfterClass(): void
    {
        ShopResetter::resetShops();

        parent::tearDownAfterClass();
    }

    public function testResolveShopIdsCoversEveryConstraintShape(): void
    {
        $this->assertSame([self::DEFAULT_SHOP_ID], self::$shopListResolver->resolveShopIds(ShopConstraint::shop(self::DEFAULT_SHOP_ID)));
        $this->assertSame([self::DEFAULT_SHOP_ID, self::$secondShopId], self::$shopListResolver->resolveShopIds(ShopConstraint::shopGroup(self::$defaultGroupId)));
        $this->assertSame([self::$thirdShopId], self::$shopListResolver->resolveShopIds(ShopConstraint::shopGroup(self::$secondGroupId)));
        $this->assertSame(
            [self::DEFAULT_SHOP_ID, self::$secondShopId, self::$thirdShopId],
            self::$shopListResolver->resolveShopIds(ShopConstraint::allShops())
        );
        $this->assertSame(
            [self::$thirdShopId, self::$secondShopId],
            self::$shopListResolver->resolveShopIds(ShopCollection::shops([self::$thirdShopId, self::$secondShopId]))
        );
    }

    public function testRepresentativeShopIsTheDefaultShopWhenItBelongsToTheScope(): void
    {
        $this->assertSame(self::DEFAULT_SHOP_ID, self::$shopListResolver->resolveRepresentativeShopId(ShopConstraint::allShops()));
        $this->assertSame(self::DEFAULT_SHOP_ID, self::$shopListResolver->resolveRepresentativeShopId(ShopConstraint::shopGroup(self::$defaultGroupId)));
        $this->assertSame(
            self::DEFAULT_SHOP_ID,
            self::$shopListResolver->resolveRepresentativeShopId(ShopCollection::shops([self::$secondShopId, self::DEFAULT_SHOP_ID]))
        );
    }

    public function testRepresentativeShopFallsBackToTheLowestShopIdOfTheScope(): void
    {
        // The second group does not contain the default shop.
        $this->assertSame(self::$thirdShopId, self::$shopListResolver->resolveRepresentativeShopId(ShopConstraint::shopGroup(self::$secondGroupId)));
        $this->assertSame(
            self::$secondShopId,
            self::$shopListResolver->resolveRepresentativeShopId(ShopCollection::shops([self::$thirdShopId, self::$secondShopId]))
        );
    }
}
