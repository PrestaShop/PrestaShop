<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Requirement;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Requirement\PhpVersionRequirement;

class PhpVersionRequirementTest extends TestCase
{
    /**
     * @dataProvider provideVersionIds
     */
    public function testItAcceptsOnlyTheSupportedRange(int $versionId, bool $expected): void
    {
        $this->assertSame($expected, PhpVersionRequirement::isVersionSupported($versionId));
    }

    public function provideVersionIds(): iterable
    {
        yield 'below the minimum' => [PhpVersionRequirement::MINIMUM_VERSION_ID - 1, false];
        yield 'the minimum itself' => [PhpVersionRequirement::MINIMUM_VERSION_ID, true];
        yield 'inside the range' => [PhpVersionRequirement::MINIMUM_VERSION_ID + 1, true];
        yield 'the maximum itself' => [PhpVersionRequirement::MAXIMUM_VERSION_ID, true];
        yield 'above the maximum' => [PhpVersionRequirement::MAXIMUM_VERSION_ID + 1, false];
    }

    /**
     * The installer checks the PHP version before the autoloader is reachable, so it cannot use this
     * class and repeats the range in install-dev/install_version.php. Parsed rather than required:
     * that file defines constants, and another test may already have loaded it.
     *
     * @dataProvider provideConstantPairs
     */
    public function testItStaysInStepWithTheInstaller(string $installerConstant, $expected): void
    {
        $installVersion = file_get_contents(__DIR__ . '/../../../../install-dev/install_version.php');
        $this->assertIsString($installVersion);

        $matched = preg_match(
            "/define\('" . preg_quote($installerConstant, '/') . "', '?([^')]+)'?\)/",
            $installVersion,
            $matches
        );

        $this->assertSame(1, $matched, sprintf('%s is not defined by the installer', $installerConstant));
        $this->assertSame((string) $expected, $matches[1]);
    }

    public function provideConstantPairs(): iterable
    {
        yield ['_PS_INSTALL_MINIMUM_PHP_VERSION_ID_', PhpVersionRequirement::MINIMUM_VERSION_ID];
        yield ['_PS_INSTALL_MAXIMUM_PHP_VERSION_ID_', PhpVersionRequirement::MAXIMUM_VERSION_ID];
        yield ['_PS_INSTALL_MINIMUM_PHP_VERSION_', PhpVersionRequirement::MINIMUM_VERSION];
        yield ['_PS_INSTALL_MAXIMUM_PHP_VERSION_', PhpVersionRequirement::MAXIMUM_VERSION];
    }
}
