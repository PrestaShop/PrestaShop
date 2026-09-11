<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Debug;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Debug\DebugMode;

/**
 * isDebugModeEnabled() used to compare the text of the define() it found in the defines files against the
 * literal 'false'. config/defines_custom.inc.php is a supported override and the official Docker image
 * guards its define with `if ((bool) getenv('PS_DEV_MODE'))`, so with the variable unset an inactive define
 * was still reported as enabled - and SwitchDebugModeHandler then had nothing to do when asked to enable
 * debug mode.
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/38771
 */
class DebugModeTest extends TestCase
{
    private function debugMode(?bool $runtime, string $parsed): DebugMode
    {
        return new class($runtime, $parsed) extends DebugMode {
            /** @var bool|null */
            private $runtime;
            /** @var string */
            private $parsed;

            public function __construct(?bool $runtime, string $parsed)
            {
                $this->runtime = $runtime;
                $this->parsed = $parsed;
            }

            protected function getRuntimeDebugMode(): ?bool
            {
                return $this->runtime;
            }

            public function getCurrentDebugMode()
            {
                return $this->parsed;
            }
        };
    }

    public function testItReportsTheConstantInEffectRatherThanTheFileText(): void
    {
        // exactly what the Docker image's defines_custom.inc.php parses to, with PS_DEV_MODE unset
        $debugMode = $this->debugMode(false, "(bool) getenv('PS_DEV_MODE')");

        $this->assertFalse($debugMode->isDebugModeEnabled());
    }

    public function testItReportsEnabledWhenTheConstantIsTrue(): void
    {
        $debugMode = $this->debugMode(true, "(bool) getenv('PS_DEV_MODE')");

        $this->assertTrue($debugMode->isDebugModeEnabled());
    }

    /**
     * Outside a booted shop the constant is missing and the previous behaviour has to stand.
     */
    public function testItFallsBackToTheParsedValueWhenTheConstantIsNotDefined(): void
    {
        $this->assertFalse($this->debugMode(null, 'false')->isDebugModeEnabled());
        $this->assertTrue($this->debugMode(null, 'true')->isDebugModeEnabled());
        $this->assertTrue($this->debugMode(null, "isset(\$_COOKIE['debug'])")->isDebugModeEnabled());
    }

    /**
     * A literal false in the file must not be read as enabled just because the constant says so - the
     * constant wins, and that is the point.
     */
    public function testTheConstantWinsOverTheFileText(): void
    {
        $this->assertTrue($this->debugMode(true, 'false')->isDebugModeEnabled());
        $this->assertFalse($this->debugMode(false, 'true')->isDebugModeEnabled());
    }
}
