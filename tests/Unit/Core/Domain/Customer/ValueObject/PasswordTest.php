<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
