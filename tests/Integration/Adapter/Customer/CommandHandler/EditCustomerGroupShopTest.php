<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Customer\CommandHandler;

use Customer;
use Db;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\EditCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerConstraintException;
use Shop;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

/**
 * In the All shops context the customer form lists the groups of every shop, so a group belonging to
 * another shop can be picked. Storing it leaves the customer with a group its own shop cannot
 * resolve, which is what this refuses.
 */
class EditCustomerGroupShopTest extends KernelTestCase
{
    private const FOREIGN_SHOP_ID = 999;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
    }

    protected function tearDown(): void
    {
        DatabaseDump::restoreTables(['group', 'group_lang', 'group_shop', 'customer', 'customer_group']);

        parent::tearDown();
    }

    public function testAGroupFromAnotherShopIsRefused(): void
    {
        $customer = $this->getCustomer();
        $foreignGroupId = $this->createGroupInShop(self::FOREIGN_SHOP_ID);

        $this->expectException(CustomerConstraintException::class);

        $this->handle($customer, array_merge($customer->getGroups(), [$foreignGroupId]));
    }

    public function testTheCustomerOwnGroupsAreStillAccepted(): void
    {
        $customer = $this->getCustomer();

        $this->handle($customer, $customer->getGroups());

        $this->addToAssertionCount(1);
    }

    /**
     * @param int[] $groupIds
     */
    private function handle(Customer $customer, array $groupIds): void
    {
        $command = (new EditCustomerCommand((int) $customer->id))
            ->setGroupIds($groupIds)
            ->setDefaultGroupId((int) $customer->id_default_group);

        self::getContainer()->get('prestashop.core.command_bus')->handle($command);
    }

    private function getCustomer(): Customer
    {
        $customerId = (int) Db::getInstance()->getValue(
            'SELECT id_customer FROM ' . _DB_PREFIX_ . 'customer WHERE deleted = 0 AND is_guest = 0 ORDER BY id_customer ASC'
        );
        $this->assertGreaterThan(0, $customerId, 'the fixture has no registered customer');

        $customer = new Customer($customerId);
        // The guard is about the shops that share customers with the one the account lives in.
        $this->assertNotContains(self::FOREIGN_SHOP_ID, Shop::getSharedShops((int) $customer->id_shop, Shop::SHARE_CUSTOMER) ?: []);

        return $customer;
    }

    private function createGroupInShop(int $shopId): int
    {
        $prefix = _DB_PREFIX_;
        Db::getInstance()->execute("INSERT INTO {$prefix}group (reduction, price_display_method, show_prices, date_add, date_upd) VALUES (0, 0, 1, NOW(), NOW())");
        $groupId = (int) Db::getInstance()->Insert_ID();
        Db::getInstance()->execute("INSERT INTO {$prefix}group_lang (id_group, id_lang, name) VALUES ($groupId, 1, 'Group of another shop')");
        Db::getInstance()->execute("INSERT INTO {$prefix}group_shop (id_group, id_shop) VALUES ($groupId, $shopId)");

        return $groupId;
    }
}
