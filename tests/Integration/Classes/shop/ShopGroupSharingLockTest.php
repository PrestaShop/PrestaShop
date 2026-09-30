<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\shop;

use Configuration;
use Customer;
use PHPUnit\Framework\TestCase;
use Shop;
use ShopGroup;

class ShopGroupSharingLockTest extends TestCase
{
    /** @var int[] */
    private $shopIds = [];

    /** @var int|null */
    private $groupId;

    /** @var int|null */
    private $customerId;

    protected function tearDown(): void
    {
        if ($this->customerId) {
            (new Customer($this->customerId))->delete();
        }
        foreach ($this->shopIds as $id) {
            $shop = new Shop($id);
            $shop->deleted = true;
            $shop->update();
        }
        if ($this->groupId) {
            $group = new ShopGroup($this->groupId);
            $group->deleted = true;
            $group->update();
        }
        Shop::cacheShops(true);
    }

    public function testAGroupHoldingOneShopStaysEditableEvenWithCustomers(): void
    {
        $this->givenAGroupWithOneShopAndACustomer();

        // The data really is there - this is what the old condition looked at on its own.
        $this->assertTrue(
            ShopGroup::hasDependency($this->groupId, 'customer'),
            'the fixture is meant to have a customer, otherwise the test proves nothing'
        );

        // ...but with a single shop in the group there is no second shop to expose it to.
        $this->assertFalse(
            ShopGroup::isSharingLocked($this->groupId, 'customer'),
            'a group holding one shop must keep the sharing flags editable'
        );
    }

    public function testASecondShopInTheGroupLocksTheFlags(): void
    {
        $this->givenAGroupWithOneShopAndACustomer();
        $this->shopIds[] = $this->createShop('Probe16804 shop B');
        Shop::cacheShops(true);

        $this->assertCount(2, Shop::getShops(false, $this->groupId, true));
        $this->assertTrue(
            ShopGroup::isSharingLocked($this->groupId, 'customer'),
            'once the group holds a second shop the customer data may not be re-scoped'
        );
    }

    public function testAnEmptyGroupIsNeverLocked(): void
    {
        $this->groupId = $this->createGroup();
        $this->shopIds[] = $this->createShop('Probe16804 shop A');
        Shop::cacheShops(true);

        $this->assertFalse(ShopGroup::isSharingLocked($this->groupId, 'customer'));
        $this->assertFalse(ShopGroup::isSharingLocked($this->groupId, 'order'));
    }

    private function givenAGroupWithOneShopAndACustomer(): void
    {
        $this->groupId = $this->createGroup();
        $shopId = $this->createShop('Probe16804 shop A');
        $this->shopIds[] = $shopId;
        Shop::cacheShops(true);

        $customer = new Customer();
        $customer->firstname = 'Probe';
        $customer->lastname = 'Sharing';
        $customer->email = 'probe16804@example.com';
        // Customer::$definition validates this with isHashedPassword, which insists on a
        // 32 or 60 character string, so a plain literal is rejected.
        $customer->passwd = password_hash('probe16804', PASSWORD_BCRYPT);
        $customer->id_shop = $shopId;
        $customer->id_shop_group = $this->groupId;
        $customer->add();
        $this->customerId = (int) $customer->id;
    }

    private function createGroup(): int
    {
        $group = new ShopGroup();
        $group->name = 'Probe16804 group';
        $group->active = true;
        $group->add();

        return (int) $group->id;
    }

    private function createShop(string $name): int
    {
        $shop = new Shop();
        $shop->name = $name;
        $shop->id_shop_group = $this->groupId;
        $shop->id_category = (int) Configuration::get('PS_HOME_CATEGORY');
        $shop->theme_name = _THEME_NAME_;
        $shop->active = true;
        $shop->add();

        return (int) $shop->id;
    }
}
