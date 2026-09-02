<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Domain\Customer\ValueObject;

use DateTime;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Birthday;
use stdClass;

class BirthdayTest extends TestCase
{
    public function testBirthdayCanBeCreatedWithValidDate()
    {
        $birthday = new Birthday('2008-07-31');

        $this->assertEquals('2008-07-31', $birthday->getValue());
    }

    /**
     * @dataProvider getInvalidBirthdays
     */
    public function testItThrowsExceptionWhenCreatingBirthdayWithInvalidData($invalidBirthday)
    {
        $this->expectException(CustomerConstraintException::class);
        $this->expectExceptionCode(CustomerConstraintException::INVALID_BIRTHDAY);

        new Birthday($invalidBirthday);
    }

    public function getInvalidBirthdays()
    {
        yield ['2150-25-100'];
        yield [new stdClass()];
        yield ['1900-13-33'];
        yield [(new DateTime('+10 days'))->format('Y-m-d')];
    }
}
