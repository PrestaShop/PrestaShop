<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Domain\Customer\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Password;

class PasswordTest extends TestCase
{
    /**
     * @dataProvider getTooShortOrTooLongPasswords
     */
    public function testItThrowsExceptionWhenCreatingTooShortOrTooLongPassword($password)
    {
        $this->expectException(CustomerConstraintException::class);
        $this->expectExceptionCode(CustomerConstraintException::INVALID_PASSWORD);

        new Password($password);
    }

    /**
     * @dataProvider getValidPasswords
     */
    public function testItCreatesNewPassword($passwordValue)
    {
        $password = new Password($passwordValue);

        $this->assertEquals($passwordValue, $password->getValue());
    }

    public function getTooShortOrTooLongPasswords()
    {
        yield [''];
        yield ['p'];
        yield ['ps'];
        yield ['pwd'];
        yield [123];
        yield ['pwds'];
        yield ['very_long_and_super_secret_password_which_is_one_char_longer_than_allowed'];
    }

    public function getValidPasswords()
    {
        yield ['short'];
        yield [12345];
        yield ['a_BIT_longer_password_1593!@'];
    }
}
