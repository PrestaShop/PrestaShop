<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Employee\TwoFactor;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Employee\TwoFactor\EmployeeBackupCodeGenerator;
use Symfony\Component\PasswordHasher\Hasher\NativePasswordHasher;

class EmployeeBackupCodeGeneratorTest extends TestCase
{
    public function testCodesRetainTheirFormatAndAreStoredAsVerifiableHashes(): void
    {
        $hasher = new NativePasswordHasher(4, 10240, 4);
        $set = (new EmployeeBackupCodeGenerator($hasher))->generateBackupCodeSet();
        self::assertCount(8, $set['plainBackupCodes']);
        self::assertCount(8, array_unique($set['plainBackupCodes']));
        foreach ($set['plainBackupCodes'] as $index => $code) {
            self::assertMatchesRegularExpression('/^[ABCDEFGHJKLMNPQRSTUVWXYZ23456789]{10}$/', $code);
            self::assertNotSame($code, $set['hashedBackupCodes'][$index]);
            self::assertTrue($hasher->verify($set['hashedBackupCodes'][$index], $code));
        }
    }
}
