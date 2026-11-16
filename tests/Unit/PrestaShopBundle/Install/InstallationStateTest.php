<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Install;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Install\InstallationState;

class InstallationStateTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir() . '/ps_installation_state_' . uniqid();
        mkdir($this->directory);
    }

    protected function tearDown(): void
    {
        foreach (['parameters.php', '.install.prestashop'] as $file) {
            if (file_exists($this->directory . '/' . $file)) {
                unlink($this->directory . '/' . $file);
            }
        }
        rmdir($this->directory);
    }

    public function testShopIsNotInstalledWithoutSettingsFile(): void
    {
        self::assertFalse($this->buildState()->isInstalled());
    }

    public function testShopIsNotInstalledWhileAnInstallationIsRunning(): void
    {
        touch($this->directory . '/parameters.php');
        touch($this->directory . '/.install.prestashop');

        self::assertFalse($this->buildState()->isInstalled());
    }

    public function testShopIsInstalledOnceSettingsFileExistsAndNoInstallationIsRunning(): void
    {
        touch($this->directory . '/parameters.php');

        self::assertTrue($this->buildState()->isInstalled());
    }

    private function buildState(): InstallationState
    {
        return new InstallationState(
            $this->directory . '/parameters.php',
            $this->directory . '/.install.prestashop'
        );
    }
}
