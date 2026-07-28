<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Integration\Adapter\Customer;

use Configuration;
use Db;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\AddCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerConstraintException;
use PrestaShop\PrestaShop\Core\Security\PasswordPolicyConfiguration;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * The back office customer form applies PS_SECURITY_PASSWORD_POLICY_MINIMUM_LENGTH through a Symfony
 * constraint. Entry points that dispatch the command directly only met the Password value object's
 * own bounds, so the same shop accepted a password through the API that it refused in the form.
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/42129
 */
class CustomerPasswordPolicyTest extends KernelTestCase
{
    private const EMAIL = 'password.policy.probe@example.com';

    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @var string
     */
    private $originalMinLength;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->commandBus = self::getContainer()->get('prestashop.core.command_bus');

        $this->originalMinLength = (string) Configuration::get(
            PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_LENGTH
        );
        Configuration::updateValue(PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_LENGTH, 8);

        $this->removeProbeCustomer();
    }

    protected function tearDown(): void
    {
        Configuration::updateValue(
            PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_LENGTH,
            $this->originalMinLength
        );

        $this->removeProbeCustomer();
    }

    public function testItRefusesAPasswordShorterThanTheConfiguredMinimum(): void
    {
        $this->expectException(CustomerConstraintException::class);

        // Six characters: above the value object's own floor, below the configured minimum of 8.
        $this->commandBus->handle($this->buildCommand('abcdef'));
    }

    public function testItAcceptsAPasswordThatMeetsTheConfiguredMinimum(): void
    {
        $customerId = $this->commandBus->handle($this->buildCommand('Str0ng!Passw0rd'));

        $this->assertGreaterThan(0, $customerId->getValue());
    }

    private function buildCommand(string $password): AddCustomerCommand
    {
        $groupId = (int) Db::getInstance()->getValue(
            'SELECT `id_group` FROM `' . _DB_PREFIX_ . 'group` ORDER BY `id_group`'
        );

        return new AddCustomerCommand(
            'Password',
            'Policy',
            self::EMAIL,
            $password,
            $groupId,
            [$groupId],
            1
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
