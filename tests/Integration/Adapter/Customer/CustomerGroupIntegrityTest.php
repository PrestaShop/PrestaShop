<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Integration\Adapter\Customer;

use Configuration;
use Context;
use Currency;
use Db;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\AddCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerGroupNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetCustomerForViewing;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * customer_group has no foreign key, so an unknown group id used to be accepted on write and only
 * failed later, when the customer view loaded the group and read a name off null.
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/42130
 */
class CustomerGroupIntegrityTest extends KernelTestCase
{
    private const MISSING_GROUP_ID = 999999;
    private const EMAIL = 'group.integrity.probe@example.com';

    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->commandBus = self::getContainer()->get('prestashop.core.command_bus');
        $this->queryBus = self::getContainer()->get('prestashop.core.query_bus');

        // The view handler formats amounts, which needs a currency in context - the CLI kernel
        // does not set one.
        $context = Context::getContext();
        if (null === $context->currency || !$context->currency->id) {
            $context->currency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
        }

        $this->removeProbeCustomer();
    }

    protected function tearDown(): void
    {
        $this->removeProbeCustomer();
    }

    public function testItRefusesToCreateACustomerInAGroupThatDoesNotExist(): void
    {
        $this->assertFalse(
            $this->groupExists(self::MISSING_GROUP_ID),
            sprintf('Group %d must not exist for this test to mean anything', self::MISSING_GROUP_ID)
        );

        $this->expectException(CustomerGroupNotFoundException::class);

        $this->commandBus->handle($this->buildCommand([self::MISSING_GROUP_ID], self::MISSING_GROUP_ID));
    }

    /**
     * Rows written before this check existed still have to render.
     */
    public function testTheCustomerViewSurvivesAGroupThatWasRemovedAfterwards(): void
    {
        $validGroupId = (int) Db::getInstance()->getValue(
            'SELECT `id_group` FROM `' . _DB_PREFIX_ . 'group` ORDER BY `id_group`'
        );
        $this->assertGreaterThan(0, $validGroupId, 'The fixtures should provide at least one group');

        /** @var CustomerId $customerId */
        $customerId = $this->commandBus->handle($this->buildCommand([$validGroupId], $validGroupId));

        // Simulate the row surviving a group removal, which is what the database allows today.
        Db::getInstance()->execute(
            'UPDATE `' . _DB_PREFIX_ . 'customer_group` SET `id_group` = ' . self::MISSING_GROUP_ID
            . ' WHERE `id_customer` = ' . $customerId->getValue()
        );

        $viewable = $this->queryBus->handle(new GetCustomerForViewing($customerId->getValue()));

        $this->assertNotNull($viewable, 'Viewing a customer with a dangling group must not fail');
    }

    /**
     * @param int[] $groupIds
     */
    private function buildCommand(array $groupIds, int $defaultGroupId): AddCustomerCommand
    {
        $command = new AddCustomerCommand(
            'Group',
            'Integrity',
            self::EMAIL,
            'Str0ng!Passw0rd',
            $defaultGroupId,
            $groupIds,
            1
        );

        return $command;
    }

    private function groupExists(int $groupId): bool
    {
        return (bool) Db::getInstance()->getValue(
            'SELECT `id_group` FROM `' . _DB_PREFIX_ . 'group` WHERE `id_group` = ' . $groupId
        );
    }

    private function removeProbeCustomer(): void
    {
        $id = (int) Db::getInstance()->getValue(
            'SELECT `id_customer` FROM `' . _DB_PREFIX_ . 'customer` WHERE `email` = "' . pSQL(self::EMAIL) . '"'
        );

        if ($id) {
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'customer_group` WHERE `id_customer` = ' . $id);
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'customer` WHERE `id_customer` = ' . $id);
        }
    }
}
