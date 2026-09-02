<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Domain\CustomerService\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Exception\CustomerServiceException;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerThreadStatus;

class CustomerThreadStatusTest extends TestCase
{
    /**
     * @dataProvider getValidCustomerThreadStatuses
     */
    public function testCanBeCreatedWithValidStatus($customerThreadStatus)
    {
        $status = new CustomerThreadStatus($customerThreadStatus);

        $this->assertEquals($customerThreadStatus, $status->getValue());
    }

    /**
     * @dataProvider getInvalidCustomerThreadStatuses
     */
    public function testThrowsExceptionWhenCreatingWithInvalidStatus($invalidCustomerThreadStatus)
    {
        $this->expectException(CustomerServiceException::class);

        new CustomerThreadStatus($invalidCustomerThreadStatus);
    }

    public function getValidCustomerThreadStatuses()
    {
        yield [CustomerThreadStatus::PENDING_1];
        yield [CustomerThreadStatus::PENDING_2];
        yield [CustomerThreadStatus::OPEN];
        yield [CustomerThreadStatus::CLOSED];
    }

    public function getInvalidCustomerThreadStatuses()
    {
        yield [1];
        yield ['opened'];
    }
}
