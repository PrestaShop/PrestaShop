<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Employee\TwoFactor;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Employee\TwoFactor\TrustedDeviceVersionUpdater;

final class TrustedDeviceVersionUpdaterTest extends TestCase
{
    /**
     * @param array<string, mixed> $expression
     */
    #[DataProvider('unexpectedExpressions')]
    public function testArbitraryExpressionsInSensitiveFieldsAreRejected(array $expression): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new TrustedDeviceVersionUpdater())->getLegacyUpdateFields(['passwd' => $expression], false, false);
    }

    public static function unexpectedExpressions(): iterable
    {
        yield 'arbitrary SQL' => [['type' => 'sql', 'value' => 'passwd']];
        yield 'malformed array' => [[]];
    }

    public function testUnrelatedPartialWriteCannotReplayTheCounter(): void
    {
        self::assertSame(
            ['lastname' => 'Lovelace'],
            (new TrustedDeviceVersionUpdater())->getLegacyUpdateFields(['two_factor_trusted_version' => 1, 'lastname' => 'Lovelace'], false, false)
        );
    }
}
