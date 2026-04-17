<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Customer\ValueObject;

use Generator;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;

class CustomerIdTest extends TestCase
{
    /**
     * @dataProvider getValidValues
     *
     * @param int $value
     */
    public function testCreateWithPositiveValue(int $value): void
    {
        $customerId = new CustomerId($value);

        $this->assertEquals($value, $customerId->getValue());
    }

    /**
     * @dataProvider getInvalidValues
     *
     * @param int $value
     */
    public function testItThrowsExceptionWhenProvidingInvalidValue(int $value): void
    {
        $this->expectException(CustomerConstraintException::class);
        $this->expectExceptionCode(CustomerConstraintException::INVALID_ID);

        new CustomerId($value);
    }

    /**
     * @return Generator
     */
    public function getValidValues(): Generator
    {
        yield [
            1,
        ];

        yield [
            150,
        ];
    }

    /**
     * @return Generator
     */
    public function getInvalidValues(): Generator
    {
        yield [
            0,
        ];

        yield [
            -10,
        ];
    }
}
