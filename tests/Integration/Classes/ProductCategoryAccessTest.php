<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Integration\Classes;

use Cache;
use Category;
use Configuration;
use Context;
use Customer;
use Db;
use Group;
use PHPUnit\Framework\TestCase;
use Product;
use Tests\Integration\Utility\ContextMockerTrait;

/**
 * Group-based visibility (checkAccess) must consider every group the context customer belongs to,
 * not only their default group, when no customer id is passed (e.g. Product::getAccessories()).
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/38807
 */
class ProductCategoryAccessTest extends TestCase
{
    use ContextMockerTrait;

    private const TEST_CATEGORY_ID = 999901;
    private const TEST_PRODUCT_ID = 999901;

    /** @var int */
    private $extraGroupId;

    /** @var int */
    private $otherGroupId;

    /** @var int */
    private $defaultGroupId;

    /** @var int */
    private $customerId;

    protected function setUp(): void
    {
        parent::setUp();
        self::mockContext();

        $this->defaultGroupId = (int) Configuration::get('PS_CUSTOMER_GROUP');
        $this->extraGroupId = $this->createGroup('Access test extra group');
        $this->otherGroupId = $this->createGroup('Access test other group');

        // The accessory's category is restricted to the extra group only (not the default group).
        $this->associateCategoryToGroups(self::TEST_CATEGORY_ID, [$this->extraGroupId]);
        Db::getInstance()->insert('category_product', [
            'id_category' => self::TEST_CATEGORY_ID,
            'id_product' => self::TEST_PRODUCT_ID,
            'position' => 0,
        ]);

        // A customer whose default group is the standard one, but who also belongs to the extra group.
        $this->customerId = $this->createCustomer($this->defaultGroupId, [$this->defaultGroupId, $this->extraGroupId]);
    }

    protected function tearDown(): void
    {
        Db::getInstance()->delete('category_product', 'id_category = ' . self::TEST_CATEGORY_ID);
        Db::getInstance()->delete('category_group', 'id_category = ' . self::TEST_CATEGORY_ID);
        Db::getInstance()->delete('customer_group', 'id_customer = ' . (int) $this->customerId);
        Db::getInstance()->delete('customer', 'id_customer = ' . (int) $this->customerId);
        Db::getInstance()->delete('group', 'id_group IN (' . (int) $this->extraGroupId . ', ' . (int) $this->otherGroupId . ')');
        Db::getInstance()->delete('group_lang', 'id_group IN (' . (int) $this->extraGroupId . ', ' . (int) $this->otherGroupId . ')');
        Cache::clean('Product::checkAccess_*');
        Cache::clean('Category::checkAccess_*');
        parent::tearDown();
    }

    public function testAccessoryVisibleForNonDefaultGroupOfLoggedInCustomer(): void
    {
        $this->loginContextCustomer($this->customerId);

        // No customer id is passed, mirroring Product::getAccessories().
        $this->assertTrue(
            Product::checkAccessStatic(self::TEST_PRODUCT_ID, false),
            'A product whose category is allowed for a non-default group of the logged-in customer must be accessible'
        );

        $category = new Category();
        $category->id = self::TEST_CATEGORY_ID;
        $this->assertTrue(
            $category->checkAccess(0),
            'A category allowed for a non-default group of the logged-in customer must be accessible'
        );
    }

    public function testAccessDeniedForGroupTheCustomerDoesNotBelongTo(): void
    {
        // Restrict the category to a group the customer is NOT a member of.
        $this->associateCategoryToGroups(self::TEST_CATEGORY_ID, [$this->otherGroupId]);
        $this->loginContextCustomer($this->customerId);

        $this->assertFalse(
            Product::checkAccessStatic(self::TEST_PRODUCT_ID, false),
            'A product restricted to a group the customer does not belong to must not be accessible'
        );
    }

    public function testGuestKeepsUnidentifiedGroupAccess(): void
    {
        // Guest context (no logged-in customer).
        Context::getContext()->customer = new Customer();

        $this->assertFalse(
            Product::checkAccessStatic(self::TEST_PRODUCT_ID, false),
            'A guest must not access a product restricted to a non-guest group'
        );

        $this->associateCategoryToGroups(self::TEST_CATEGORY_ID, [(int) Configuration::get('PS_UNIDENTIFIED_GROUP')]);
        Cache::clean('Product::checkAccess_*');
        $this->assertTrue(
            Product::checkAccessStatic(self::TEST_PRODUCT_ID, false),
            'A guest must access a product allowed for the unidentified group'
        );
    }

    private function loginContextCustomer(int $customerId): void
    {
        $customer = new Customer($customerId);
        Context::getContext()->customer = $customer;
        Cache::clean('Product::checkAccess_*');
        Cache::clean('Category::checkAccess_*');
    }

    private function createGroup(string $name): int
    {
        $group = new Group();
        $group->name = [(int) Configuration::get('PS_LANG_DEFAULT') => $name];
        $group->price_display_method = 0;
        $group->show_prices = true;
        $group->reduction = 0;
        $group->add();

        return (int) $group->id;
    }

    private function associateCategoryToGroups(int $categoryId, array $groupIds): void
    {
        Db::getInstance()->delete('category_group', 'id_category = ' . (int) $categoryId);
        foreach ($groupIds as $groupId) {
            Db::getInstance()->insert('category_group', [
                'id_category' => (int) $categoryId,
                'id_group' => (int) $groupId,
            ]);
        }
    }

    private function createCustomer(int $defaultGroupId, array $groupIds): int
    {
        $customer = new Customer();
        $customer->firstname = 'Access';
        $customer->lastname = 'Test';
        $customer->email = 'access-test-' . uniqid() . '@example.com';
        $customer->passwd = md5(uniqid('', true));
        $customer->id_default_group = $defaultGroupId;
        $customer->add();

        Db::getInstance()->delete('customer_group', 'id_customer = ' . (int) $customer->id);
        foreach (array_unique($groupIds) as $groupId) {
            Db::getInstance()->insert('customer_group', [
                'id_customer' => (int) $customer->id,
                'id_group' => (int) $groupId,
            ]);
        }

        return (int) $customer->id;
    }
}
