<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Customer\CommandHandler;

use Configuration as LegacyConfiguration;
use Customer;
use Db;
use Group;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\EditCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerGroupShopAssociationException;
use PrestaShopBundle\Entity\Shop;
use PrestaShopBundle\Entity\ShopGroup;
use Shop as LegacyShop;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

/**
 * @see https://github.com/PrestaShop/PrestaShop/issues/23192
 */
class EditCustomerGroupShopAssociationTest extends KernelTestCase
{
    private const ID_DEFAULT_GROUP = 3; // Customer group, present on shop 1 by default

    private static int $idShop2;
    private static int $shopTwoOnlyGroupId;
    private static int $customerInShopOneId;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::initMultistore();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreAllTables();
        LegacyShop::resetStaticCache();
        LegacyConfiguration::resetStaticCache();
    }

    public function testAssigningGroupFromAnotherShopThrows(): void
    {
        $commandBus = self::getContainer()->get('prestashop.core.command_bus');

        $command = new EditCustomerCommand(self::$customerInShopOneId);
        // The default group (shop 1) stays valid; the extra group exists only on shop 2.
        $command->setGroupIds([self::ID_DEFAULT_GROUP, self::$shopTwoOnlyGroupId]);
        $command->setDefaultGroupId(self::ID_DEFAULT_GROUP);

        $this->expectException(CustomerGroupShopAssociationException::class);

        $commandBus->handle($command);
    }

    public function testAssigningGroupFromTheCustomerShopSucceeds(): void
    {
        $commandBus = self::getContainer()->get('prestashop.core.command_bus');

        $command = new EditCustomerCommand(self::$customerInShopOneId);
        $command->setGroupIds([self::ID_DEFAULT_GROUP]);
        $command->setDefaultGroupId(self::ID_DEFAULT_GROUP);

        $commandBus->handle($command);

        $customer = new Customer(self::$customerInShopOneId);
        $this->assertSame([self::ID_DEFAULT_GROUP], array_map('intval', $customer->getGroups()));
    }

    private static function initMultistore(): void
    {
        DatabaseDump::restoreAllTables();
        LegacyConfiguration::resetStaticCache();
        LegacyShop::resetStaticCache();
        self::bootKernel();
        $container = self::$kernel->getContainer();
        $configuration = $container->get('prestashop.adapter.legacy.configuration');
        $entityManager = $container->get('doctrine.orm.entity_manager');

        // activate multistore
        $configuration->set('PS_MULTISHOP_FEATURE_ACTIVE', 1);

        // add a shop in existing group (shop group 1 does not share customers by default)
        $shopGroup = $entityManager->find(ShopGroup::class, 1);
        $shop = new Shop();
        $shop->setActive(true);
        $shop->setIdCategory(2);
        $shop->setName('test_shop_2');
        $shop->setShopGroup($shopGroup);
        $shop->setColor('red');
        $shop->setThemeName(Theme::getDefaultTheme());
        $shop->setDeleted(false);
        $entityManager->persist($shop);
        $entityManager->flush();
        self::$idShop2 = (int) $shop->getId();

        LegacyShop::resetStaticCache();
        LegacyShop::setContext(LegacyShop::CONTEXT_SHOP, 1);

        // a group associated ONLY with shop 2
        $group = new Group();
        $group->name = [1 => 'Shop 2 only', 2 => 'Shop 2 only'];
        $group->price_display_method = 0;
        $group->show_prices = true;
        $group->add();
        self::$shopTwoOnlyGroupId = (int) $group->id;
        // Group::add() auto-associates the group with the current context shop (1); replace that
        // association so the group exists ONLY on shop 2.
        Db::getInstance()->delete('group_shop', 'id_group = ' . self::$shopTwoOnlyGroupId);
        Db::getInstance()->insert('group_shop', [
            'id_group' => self::$shopTwoOnlyGroupId,
            'id_shop' => self::$idShop2,
        ]);

        // a customer that belongs to shop 1
        $customer = new Customer();
        $customer->firstname = 'John';
        $customer->lastname = 'Doe';
        $customer->email = 'issue23192@example.com';
        $customer->passwd = md5('issue23192');
        $customer->id_shop = 1;
        $customer->id_default_group = self::ID_DEFAULT_GROUP;
        $customer->add();
        $customer->updateGroup([self::ID_DEFAULT_GROUP]);
        self::$customerInShopOneId = (int) $customer->id;
    }
}
