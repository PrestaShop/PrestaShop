<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Domain\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email;

/**
 * Class EmailTest
 */
class EmailTest extends TestCase
{
    /**
     * @dataProvider getValidEmailValues
     */
    public function testItCreatesEmailWithValidValues($validEmail)
    {
        $email = new Email($validEmail);

        $this->assertEquals($validEmail, $email->getValue());
    }

    /**
     * @dataProvider getInvalidEmailValues
     */
    public function testItThrowsExceptionWhenCreatingEmailWithInvalidValue($invalidEmail)
    {
        $this->expectException(DomainConstraintException::class);
        $this->expectExceptionCode(DomainConstraintException::INVALID_EMAIL);

        new Email($invalidEmail);
    }

    /**
     * @dataProvider getEmailCompareValues
     */
    public function testEmailComparesValuesCorrectly($firstEmail, $secondEmail, $expectedCompareResult)
    {
        $this->assertEquals($expectedCompareResult, (new Email($firstEmail))->isEqualTo(new Email($secondEmail)));
    }

    public function getValidEmailValues()
    {
        yield ['demo.demo@prestashop.com'];
        yield ['12312321@123.com'];
        yield ['abc_123o@a.eu'];
    }

    public function getInvalidEmailValues()
    {
        yield [''];
        yield [123];
        yield [sprintf('very_long_email_%s@demo.com', str_repeat('A', 231))];
    }

    public function getEmailCompareValues()
    {
        yield ['demo@demo.com', 'demo@demo.com', true];
        yield ['demo@demo.com', 'no_the_same@demo.com', false];
    }
}
