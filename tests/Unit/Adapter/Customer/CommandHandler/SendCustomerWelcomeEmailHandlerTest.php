<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Customer\CommandHandler;

use Customer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Customer\CommandHandler\SendCustomerWelcomeEmailHandler;
use PrestaShop\PrestaShop\Adapter\Customer\CustomerWelcomeMailer;
use PrestaShop\PrestaShop\Adapter\Customer\Repository\CustomerRepository;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\SendCustomerWelcomeEmailCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\CustomerAccountOrigin;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CannotSendCustomerWelcomeEmailException;
use PrestaShopException;

class SendCustomerWelcomeEmailHandlerTest extends TestCase
{
    private const CUSTOMER_ID = 42;

    public static function getFailures(): iterable
    {
        yield 'a false result' => [null];
        yield 'an exception' => [new PrestaShopException('Connection lost')];
    }

    #[DataProvider('getFailures')]
    public function testItReportsTokenSaveFailure(?PrestaShopException $failure): void
    {
        $customer = $this->mockCustomer();
        $customer->expects($this->once())->method('stampResetPasswordToken');
        $update = $customer->expects($this->once())->method('update');
        if (null === $failure) {
            $update->willReturn(false);
        } else {
            $update->willThrowException($failure);
        }

        $mailer = $this->createMock(CustomerWelcomeMailer::class);
        $mailer->expects($this->never())->method('send');

        $this->assertSendingFails($customer, $mailer, $failure);
    }

    #[DataProvider('getFailures')]
    public function testItReportsMailFailure(?PrestaShopException $failure): void
    {
        $customer = $this->mockCustomer();
        $customer->method('update')->willReturn(true);

        $mailer = $this->createMock(CustomerWelcomeMailer::class);
        $send = $mailer
            ->expects($this->once())
            ->method('send')
            ->with($customer, CustomerAccountOrigin::BACK_OFFICE);

        if (null === $failure) {
            $send->willReturn(false);
        } else {
            $send->willThrowException($failure);
        }

        $this->assertSendingFails($customer, $mailer, $failure);
    }

    private function mockCustomer(): Customer&MockObject
    {
        $customer = $this->createMock(Customer::class);
        $customer->id = self::CUSTOMER_ID;
        $customer->active = true;
        $customer->deleted = false;
        $customer->method('isGuest')->willReturn(false);
        $customer->method('hasRecentResetPasswordToken')->willReturn(false);

        return $customer;
    }

    private function assertSendingFails(Customer $customer, CustomerWelcomeMailer $mailer, ?PrestaShopException $failure): void
    {
        $repository = $this->createMock(CustomerRepository::class);
        $repository->method('get')->willReturn($customer);

        $handler = new SendCustomerWelcomeEmailHandler($repository, $mailer);

        try {
            $handler->handle(new SendCustomerWelcomeEmailCommand(self::CUSTOMER_ID, CustomerAccountOrigin::BACK_OFFICE));
            $this->fail('A CannotSendCustomerWelcomeEmailException should have been thrown.');
        } catch (CannotSendCustomerWelcomeEmailException $e) {
            $this->assertSame($failure, $e->getPrevious());
        }
    }
}
