<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes;

use ConfigurationTest;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Requirement\PhpVersionRequirement;

class ConfigurationTestPhpVersionTest extends TestCase
{
    /**
     * The System information page runs this test and prints an error when it fails, so it has to answer
     * for the release it ships with rather than for a version bound left behind by an older branch.
     */
    public function testItAnswersForTheVersionRangeTheReleaseSupports(): void
    {
        $this->assertSame(PhpVersionRequirement::isVersionSupported(), ConfigurationTest::test_phpversion());
    }

    /**
     * Driven with explicit ids rather than with the running interpreter: on a supported one every wrong
     * bound returns true, including the 7.1.3 this replaced, so that assertion could not fail.
     *
     * @dataProvider provideVersionIds
     */
    public function testItRejectsVersionsOutsideTheRange(int $versionId, bool $expected): void
    {
        $this->assertSame($expected, ConfigurationTest::test_phpversion($versionId));
    }

    public function provideVersionIds(): iterable
    {
        yield 'PHP 7.4, below the range' => [70433, false];
        yield 'PHP 8.0, below the range' => [80030, false];
        yield 'PHP 8.1, the minimum' => [80100, true];
        yield 'PHP 8.5, the maximum' => [80599, true];
        yield 'PHP 8.6, above the range' => [80600, false];
    }

    /**
     * ConfigurationTest::run() hands the test its configuration value, `false` for this one, and that
     * must not be read as a version id.
     */
    public function testItFallsBackToTheRunningVersion(): void
    {
        $this->assertSame(PhpVersionRequirement::isVersionSupported(), ConfigurationTest::run('phpversion', false) === 'ok');
    }

    /**
     * new_phpversion has always delegated to phpversion, so leaving it in the optional list reported the
     * same failure twice on the page.
     */
    public function testTheOptionalListNoLongerRepeatsThePhpVersionTest(): void
    {
        $this->assertArrayNotHasKey('new_phpversion', ConfigurationTest::getDefaultTestsOp());
        $this->assertArrayHasKey('phpversion', ConfigurationTest::getDefaultTests());
    }
}
