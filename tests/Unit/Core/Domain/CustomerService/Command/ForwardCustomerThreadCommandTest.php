<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\CustomerService\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\ForwardCustomerThreadCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Exception\CustomerServiceException;

class ForwardCustomerThreadCommandTest extends TestCase
{
    public function testConstructorMatchesToAnotherEmployeeFactory(): void
    {
        $viaFactory = ForwardCustomerThreadCommand::toAnotherEmployee(12, 34, 'please handle');
        $viaConstructor = new ForwardCustomerThreadCommand(12, 'please handle', 34);

        $this->assertEquals($viaFactory->getCustomerThreadId(), $viaConstructor->getCustomerThreadId());
        $this->assertEquals($viaFactory->getEmployeeId(), $viaConstructor->getEmployeeId());
        $this->assertSame($viaFactory->getEmail(), $viaConstructor->getEmail());
        $this->assertSame($viaFactory->getComment(), $viaConstructor->getComment());
        $this->assertTrue($viaConstructor->forwardToEmployee());
    }

    public function testConstructorMatchesToSomeoneElseFactory(): void
    {
        $viaFactory = ForwardCustomerThreadCommand::toSomeoneElse(12, 'someone@example.com', 'fyi');
        $viaConstructor = new ForwardCustomerThreadCommand(12, 'fyi', null, 'someone@example.com');

        $this->assertEquals($viaFactory->getCustomerThreadId(), $viaConstructor->getCustomerThreadId());
        $this->assertSame($viaFactory->getEmployeeId(), $viaConstructor->getEmployeeId());
        $this->assertEquals($viaFactory->getEmail(), $viaConstructor->getEmail());
        $this->assertSame($viaFactory->getComment(), $viaConstructor->getComment());
        $this->assertFalse($viaConstructor->forwardToEmployee());
    }

    public function testConstructorRejectsBothEmployeeAndEmail(): void
    {
        $this->expectException(CustomerServiceException::class);
        $this->expectExceptionCode(CustomerServiceException::INVALID_FORWARD_TARGET);

        new ForwardCustomerThreadCommand(12, 'comment', 34, 'someone@example.com');
    }

    public function testConstructorRejectsNeitherEmployeeNorEmail(): void
    {
        $this->expectException(CustomerServiceException::class);
        $this->expectExceptionCode(CustomerServiceException::INVALID_FORWARD_TARGET);

        new ForwardCustomerThreadCommand(12, 'comment');
    }
}
