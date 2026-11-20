<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\DataHandler;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\AddCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\DeleteCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\SendCustomerPasswordSetupLinkCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CannotSendPasswordSetupLinkException;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Domain\CustomerB2b\Command\AddCustomerB2bCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerB2b\Exception\CustomerB2bConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CustomerB2b\Exception\PasswordSetupLinkNotSentException;
use PrestaShop\PrestaShop\Core\Domain\CustomerB2b\ValueObject\CustomerB2bId;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\CustomerB2bFormDataHandler;
use PrestaShop\PrestaShop\Core\Group\Provider\DefaultGroup;
use PrestaShop\PrestaShop\Core\Group\Provider\DefaultGroups;
use PrestaShop\PrestaShop\Core\Group\Provider\DefaultGroupsProviderInterface;
use PrestaShopBundle\Entity\Enum\CustomerB2bStatus;
use Psr\Log\LoggerInterface;
use RuntimeException;

class CustomerB2bFormDataHandlerTest extends TestCase
{
    private const SHOP_ID = 4;
    private const CUSTOMERS_GROUP_ID = 3;

    /**
     * @var list<object>
     */
    private array $dispatched = [];

    /**
     * @var array<class-string, callable>
     */
    private array $handlers = [];

    private MockObject&LoggerInterface $logger;

    private CustomerB2bFormDataHandler $handler;

    protected function setUp(): void
    {
        $this->dispatched = [];
        $this->handlers = [
            AddCustomerCommand::class => static fn () => new CustomerId(10),
            AddCustomerB2bCommand::class => static fn () => new CustomerB2bId(20),
            DeleteCustomerCommand::class => static fn () => null,
            SendCustomerPasswordSetupLinkCommand::class => static fn () => null,
        ];

        $commandBus = $this->createMock(CommandBusInterface::class);
        $commandBus->method('handle')->willReturnCallback(function (object $command) {
            $this->dispatched[] = $command;

            return ($this->handlers[$command::class])($command);
        });

        $shopContext = $this->createMock(ShopContext::class);
        $shopContext->method('getId')->willReturn(self::SHOP_ID);

        $defaultGroupsProvider = $this->createMock(DefaultGroupsProviderInterface::class);
        $defaultGroupsProvider->method('getGroups')->willReturn(new DefaultGroups(
            new DefaultGroup(1, 'Visitor'),
            new DefaultGroup(2, 'Guest'),
            new DefaultGroup(self::CUSTOMERS_GROUP_ID, 'Customer'),
        ));

        $this->logger = $this->createMock(LoggerInterface::class);
        $this->handler = new CustomerB2bFormDataHandler($commandBus, $shopContext, $defaultGroupsProvider, $this->logger);
    }

    public function testItCreatesCustomerThenProfile(): void
    {
        $this->logger->expects($this->never())->method($this->anything());

        $id = $this->handler->create($this->data(['send_password_setup_link' => false]));

        $this->assertSame(20, $id);
        $this->assertCount(2, $this->dispatched);

        /** @var AddCustomerCommand $addCustomer */
        $addCustomer = $this->dispatched[0];
        $this->assertInstanceOf(AddCustomerCommand::class, $addCustomer);
        $this->assertSame('new', $addCustomer->getFirstName()->getValue());
        $this->assertSame('customer', $addCustomer->getLastName()->getValue());
        $this->assertSame('new@prestashop.com', $addCustomer->getEmail()->getValue());
        $this->assertSame(2, $addCustomer->getGenderId());
        $this->assertSame(16, \strlen($addCustomer->getPassword()->getValue()));
        $this->assertSame(self::CUSTOMERS_GROUP_ID, $addCustomer->getDefaultGroupId());
        $this->assertSame([self::CUSTOMERS_GROUP_ID], $addCustomer->getGroupIds());
        $this->assertSame(self::SHOP_ID, $addCustomer->getShopId());
        $this->assertTrue($addCustomer->isEnabled());
        $this->assertFalse($addCustomer->isPartnerOffersSubscribed());
        $this->assertFalse($addCustomer->isNewsletterSubscribed());
        $this->assertFalse($addCustomer->isGuest());

        /** @var AddCustomerB2bCommand $addProfile */
        $addProfile = $this->dispatched[1];
        $this->assertInstanceOf(AddCustomerB2bCommand::class, $addProfile);
        $this->assertSame(10, $addProfile->customerId->getValue());
        $this->assertSame(5, $addProfile->businessEntityId->getValue());
        $this->assertSame(6, $addProfile->roleId->getValue());
        $this->assertSame(CustomerB2bStatus::ACTIVE, $addProfile->status);
        $this->assertSame('EXT-1', $addProfile->externalRef?->getValue());
    }

    public function testItUsesExistingCustomer(): void
    {
        $id = $this->handler->create($this->data([
            'use_existing_customer' => true,
            'existing_customer' => [7 => ['id_customer' => '42']],
        ]));

        $this->assertSame(20, $id);
        $this->assertCount(1, $this->dispatched);
        $this->assertInstanceOf(AddCustomerB2bCommand::class, $this->dispatched[0]);
        $this->assertSame(42, $this->dispatched[0]->customerId->getValue());
    }

    public function testItSendsNoLinkToExistingCustomer(): void
    {
        $this->handler->create($this->data([
            'use_existing_customer' => true,
            'existing_customer' => [['id_customer' => 42]],
            'send_password_setup_link' => true,
        ]));

        $this->assertCount(1, $this->dispatched);
        $this->assertInstanceOf(AddCustomerB2bCommand::class, $this->dispatched[0]);
    }

    public function testItDeletesNewCustomerWhenProfileFails(): void
    {
        $original = new RuntimeException('profile failed');
        $this->handlers[AddCustomerB2bCommand::class] = static function () use ($original): never {
            throw $original;
        };
        $this->logger->expects($this->never())->method($this->anything());

        try {
            $this->handler->create($this->data());
            $this->fail('A RuntimeException should have been thrown.');
        } catch (RuntimeException $e) {
            $this->assertSame($original, $e);
        }

        $this->assertCount(3, $this->dispatched);
        /** @var DeleteCustomerCommand $delete */
        $delete = $this->dispatched[2];
        $this->assertInstanceOf(DeleteCustomerCommand::class, $delete);
        $this->assertSame(10, $delete->getCustomerId()->getValue());
        $this->assertTrue($delete->getDeleteMethod()->isAllowedToRegisterAfterDelete());
    }

    public function testItDeletesNewCustomerWhenProfileDataIsInvalid(): void
    {
        try {
            $this->handler->create($this->data(['external_ref' => str_repeat('a', 256)]));
            $this->fail('A CustomerB2bConstraintException should have been thrown.');
        } catch (CustomerB2bConstraintException) {
        }

        $this->assertCount(2, $this->dispatched);
        $this->assertInstanceOf(DeleteCustomerCommand::class, $this->dispatched[1]);
    }

    public function testItKeepsExistingCustomerWhenProfileFails(): void
    {
        $this->handlers[AddCustomerB2bCommand::class] = static function (): never {
            throw new RuntimeException('creation failed');
        };

        try {
            $this->handler->create($this->data(['use_existing_customer' => true, 'existing_customer' => [['id_customer' => 42]]]));
            $this->fail('A RuntimeException should have been thrown.');
        } catch (RuntimeException) {
        }

        $this->assertCount(1, $this->dispatched);
    }

    public function testItLogsFailedCustomerDeletion(): void
    {
        $original = new RuntimeException('creation failed');
        $this->handlers[AddCustomerB2bCommand::class] = static function () use ($original): never {
            throw $original;
        };
        $this->handlers[DeleteCustomerCommand::class] = static function (): never {
            throw new RuntimeException('delete failed');
        };
        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with($this->anything(), $this->callback(
                static fn (array $context): bool => 'Customer' === $context['object_type']
                    && 10 === $context['object_id']
                    && $context['exception'] instanceof RuntimeException
                    && 'delete failed' === $context['exception']->getMessage(),
            ));

        $this->expectExceptionObject($original);

        $this->handler->create($this->data());
    }

    public function testItSendsLinkToNewCustomerWhenRequested(): void
    {
        $this->handler->create($this->data(['send_password_setup_link' => true]));

        $this->assertCount(3, $this->dispatched);
        $this->assertInstanceOf(SendCustomerPasswordSetupLinkCommand::class, $this->dispatched[2]);
        $this->assertSame(10, $this->dispatched[2]->customerId->getValue());
    }

    public function testItReportsCreatedIdWhenMailFails(): void
    {
        $mailFailure = new CannotSendPasswordSetupLinkException('unlucky');
        $this->handlers[SendCustomerPasswordSetupLinkCommand::class] = static function () use ($mailFailure): never {
            throw $mailFailure;
        };
        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with($this->anything(), $this->callback(
                static fn (array $context): bool => 'CustomerB2b' === $context['object_type']
                    && 20 === $context['object_id']
                    && $mailFailure === $context['exception'],
            ));
        $this->logger->expects($this->never())->method('warning');

        try {
            $this->handler->create($this->data(['send_password_setup_link' => true]));
            $this->fail('A PasswordSetupLinkNotSentException should have been thrown.');
        } catch (PasswordSetupLinkNotSentException $e) {
            $this->assertSame(20, $e->id->getValue());
            $this->assertSame($mailFailure, $e->getPrevious());
        }
    }

    /**
     * @param array<string, mixed> $overrides
     *
     * @return array<string, mixed>
     */
    private function data(array $overrides = []): array
    {
        return $overrides + [
            'use_existing_customer' => false,
            'customer' => [
                'gender_id' => 2,
                'first_name' => 'new',
                'last_name' => 'customer',
                'email' => 'new@prestashop.com',
            ],
            'send_password_setup_link' => false,
            'existing_customer' => [['id_customer' => 99]],
            'business_entity_id' => 5,
            'role_id' => 6,
            'external_ref' => 'EXT-1',
            'status' => CustomerB2bStatus::ACTIVE,
        ];
    }
}
