<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Employee\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\InvalidEmployeeIdException;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeId;

class EmployeeIdTest extends TestCase
{
    /**
     * @dataProvider getValidValues
     */
    public function testItCreatesEmployeeWithValidValues($employeId): void
    {
        $employeeId = new EmployeeId($employeId);

        $this->assertEquals($employeId, $employeeId->getValue());
    }

    public function getValidValues(): iterable
    {
        yield [0];
        yield [1];
    }

    /**
     * @dataProvider getInvalidValues
     */
    public function testItExceptionThrownWithInvalidValues($employeId): void
    {
        $this->expectException(InvalidEmployeeIdException::class);
        new EmployeeId($employeId);
    }

    public function getInvalidValues(): iterable
    {
        yield ['-1'];
        yield ['1.1'];
        yield ['a'];
        yield ['+'];
    }
}
