<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\DataHandler;

use ErrorException;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\AddCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\EditCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\CustomerFormDataHandler;
use PrestaShop\PrestaShop\Core\Group\Provider\DefaultGroupsProviderInterface;

class CustomerFormDataHandlerTest extends TestCase
{
    /**
     * @dataProvider getCreateGenderData
     */
    public function testCreateSetsGenderId(array $genderData, int $expectedGenderId): void
    {
        $bus = $this->createMock(CommandBusInterface::class);
        $bus
            ->expects($this->once())
            ->method('handle')
            ->with($this->callback(function (AddCustomerCommand $command) use ($expectedGenderId): bool {
                $this->assertSame($expectedGenderId, $command->getGenderId());

                return true;
            }))
            ->willReturn(new CustomerId(42))
        ;

        $customerId = $this->failOnWarning(function () use ($bus, $genderData) {
            return $this->createDataHandler($bus)->create(array_merge($this->getCreateFormData(), $genderData));
        });

        $this->assertSame(42, $customerId);
    }

    public static function getCreateGenderData(): iterable
    {
        yield 'selected social title' => [['gender_id' => '2'], 2];
        yield 'no social title selected' => [['gender_id' => null], 0];
        // The social title field is not part of the form when no social title exists
        yield 'no social title field' => [[], 0];
    }

    /**
     * @dataProvider getUpdateGenderData
     */
    public function testUpdateSetsGenderId(array $genderData, ?int $expectedGenderId): void
    {
        $bus = $this->createMock(CommandBusInterface::class);
        $bus
            ->expects($this->once())
            ->method('handle')
            ->with($this->callback(function (EditCustomerCommand $command) use ($expectedGenderId): bool {
                $this->assertSame(42, $command->getCustomerId()->getValue());
                $this->assertSame($expectedGenderId, $command->getGenderId());

                return true;
            }))
        ;

        $this->failOnWarning(function () use ($bus, $genderData): void {
            $this->createDataHandler($bus)->update(42, array_merge($this->getUpdateFormData(), $genderData));
        });
    }

    public static function getUpdateGenderData(): iterable
    {
        yield 'selected social title' => [['gender_id' => 2], 2];
        yield 'no social title selected' => [['gender_id' => null], null];
        // The social title field is not part of the form when no social title exists
        yield 'no social title field' => [[], null];
    }

    /**
     * PHPUnit only reports warnings, an undefined array key must make the test fail
     * since it results in an error page in debug mode.
     */
    private function failOnWarning(callable $callback)
    {
        set_error_handler(static function (int $severity, string $message): bool {
            throw new ErrorException($message, 0, $severity);
        });

        try {
            return $callback();
        } finally {
            restore_error_handler();
        }
    }

    private function createDataHandler(CommandBusInterface $bus): CustomerFormDataHandler
    {
        return new CustomerFormDataHandler(
            $bus,
            1,
            false,
            $this->createMock(DefaultGroupsProviderInterface::class)
        );
    }

    private function getCreateFormData(): array
    {
        return [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@prestashop.com',
            'password' => 'Str0ng-Passw0rd!',
            'default_group_id' => 3,
            'group_ids' => [1, 2, 3],
            'is_enabled' => true,
            'is_partner_offers_subscribed' => false,
            'birthday' => null,
            'is_guest' => false,
        ];
    }

    private function getUpdateFormData(): array
    {
        return [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@prestashop.com',
            'password' => null,
            'default_group_id' => 3,
            'group_ids' => [1, 2, 3],
            'is_enabled' => true,
            'is_newsletter_subscribed' => false,
            'is_partner_offers_subscribed' => false,
            'birthday' => null,
        ];
    }
}
