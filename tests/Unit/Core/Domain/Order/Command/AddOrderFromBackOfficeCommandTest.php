<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Order\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeId;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\NoEmployeeId;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\AddOrderFromBackOfficeCommand;

class AddOrderFromBackOfficeCommandTest extends TestCase
{
    /**
     * Denormalization path (Admin API CQRSCreate) omits employeeId/orderMessage;
     * the command must build and expose the documented defaults.
     */
    public function testConstructWithOnlyRequiredNamedArgumentsUsesDefaults(): void
    {
        $command = new AddOrderFromBackOfficeCommand(
            1,
            paymentModuleName: 'ps_checkpayment',
            orderStateId: 2,
        );

        $this->assertSame(1, $command->getCartId()->getValue());
        $this->assertInstanceOf(NoEmployeeId::class, $command->getEmployeeId());
        $this->assertSame(NoEmployeeId::NO_EMPLOYEE_ID_VALUE, $command->getEmployeeId()->getValue());
        $this->assertSame('', $command->getOrderMessage());
        $this->assertSame('ps_checkpayment', $command->getPaymentModuleName());
        $this->assertSame(2, $command->getOrderStateId());
    }

    /**
     * Positional full-args construction (existing core call sites) must be unchanged.
     */
    public function testConstructWithAllPositionalArgumentsBehavesIdentically(): void
    {
        $command = new AddOrderFromBackOfficeCommand(1, 3, 'a message', 'ps_checkpayment', 2);

        $this->assertSame(1, $command->getCartId()->getValue());
        $this->assertInstanceOf(EmployeeId::class, $command->getEmployeeId());
        $this->assertSame(3, $command->getEmployeeId()->getValue());
        $this->assertSame('a message', $command->getOrderMessage());
        $this->assertSame('ps_checkpayment', $command->getPaymentModuleName());
        $this->assertSame(2, $command->getOrderStateId());
    }
}
