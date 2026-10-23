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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Customer;
use Employee;
use PHPUnit\Framework\TestCase;

/**
 * A password reset token is random: it cannot be rebuilt from the time of the request and data
 * the requester may know, such as the customer id and secure key.
 */
class ResetPasswordTokenTest extends TestCase
{
    public function testCustomerTokenIsRandomAndNotDerivedFromTheSecureKey(): void
    {
        $customer = new Customer();
        $customer->id = 42;
        $customer->secure_key = md5('known to the attacker');

        $now = time();
        $customer->stampResetPasswordToken();
        $firstToken = $customer->reset_password_token;
        $customer->stampResetPasswordToken();

        $this->assertRegExp('/^[0-9a-f]{40}$/', $firstToken);
        $this->assertNotSame($firstToken, $customer->reset_password_token);
        for ($time = $now - 2; $time <= $now + 2; ++$time) {
            $this->assertNotSame(sha1($time . $customer->id . '-' . $customer->secure_key), $firstToken);
        }
    }

    public function testEmployeeTokenIsRandom(): void
    {
        $employee = new Employee();
        $employee->id = 42;

        $employee->stampResetPasswordToken();
        $firstToken = $employee->reset_password_token;
        $employee->stampResetPasswordToken();

        $this->assertRegExp('/^[0-9a-f]{40}$/', $firstToken);
        $this->assertNotSame($firstToken, $employee->reset_password_token);
    }
}
