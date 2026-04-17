<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Domain\Customer\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\FirstName;

class FirstNameTest extends TestCase
{
    public function testExceptionIsThrownWhenSuppliedFirstNameIsTooLong()
    {
        $this->expectException(CustomerConstraintException::class);
        $this->expectExceptionCode(CustomerConstraintException::INVALID_FIRST_NAME);

        $veryLongFirstName = str_repeat('A', 256);

        new FirstName($veryLongFirstName);
    }

    /**
     * @dataProvider getInvalidFirstNames
     */
    public function testItThrowsExceptionWhenInvalidFirstNameIsSupplied($invalidFirstName)
    {
        $this->expectException(CustomerConstraintException::class);
        $this->expectExceptionCode(CustomerConstraintException::INVALID_FIRST_NAME);

        new FirstName($invalidFirstName);
    }

    /**
     * @dataProvider getValidFirstNames
     */
    public function testItCreatesFirstNameWithValid($validFirstName)
    {
        $firstName = new FirstName($validFirstName);

        $this->assertEquals($validFirstName, $firstName->getValue());
    }

    public function getInvalidFirstNames()
    {
        yield ['First123Name'];
        yield ['My !@# name'];
        yield ['26589'];
        yield ['My+first+name'];
        yield ['@My@first%name'];
    }

    public function getValidFirstNames()
    {
        yield ['Demo Demo'];
        yield ['MyNameIsPrettyLong'];
        yield ['ABC'];
        yield [''];
    }
}
