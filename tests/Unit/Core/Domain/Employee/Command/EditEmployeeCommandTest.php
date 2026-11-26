<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Employee\Command;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\EditEmployeeCommand;

class EditEmployeeCommandTest extends TestCase
{
    public function testOmittedTwoFactorSettingsAreUnchanged(): void
    {
        $command = (new EditEmployeeCommand(1))->setLastName('Lovelace');

        self::assertNull($command->getTwoFactorEnabled());
        self::assertNull($command->getTwoFactorTotpEnabled());
        self::assertNull($command->getTwoFactorEmailEnabled());
        self::assertNull($command->getTwoFactorRequired());
    }

    #[DataProvider('explicitSettings')]
    public function testSettingsCanBeExplicitlyEnabledOrDisabled(bool $enabled): void
    {
        $command = (new EditEmployeeCommand(1))
            ->setTwoFactorEnabled($enabled)
            ->setTwoFactorTotpEnabled($enabled)
            ->setTwoFactorEmailEnabled($enabled)
            ->setTwoFactorRequired($enabled);

        self::assertSame($enabled, $command->getTwoFactorEnabled());
        self::assertSame($enabled, $command->getTwoFactorTotpEnabled());
        self::assertSame($enabled, $command->getTwoFactorEmailEnabled());
        self::assertSame($enabled, $command->getTwoFactorRequired());
    }

    public static function explicitSettings(): iterable
    {
        yield 'enabled' => [true];
        yield 'disabled' => [false];
    }
}
