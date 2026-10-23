<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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

        $this->assertMatchesRegularExpression('/^[0-9a-f]{40}$/', $firstToken);
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

        $this->assertMatchesRegularExpression('/^[0-9a-f]{40}$/', $firstToken);
        $this->assertNotSame($firstToken, $employee->reset_password_token);
    }
}
