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

namespace Tests\Unit\PrestaShopBundle\Install;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Install\InstallationState;

class InstallationStateTest extends TestCase
{
    /**
     * @var string
     */
    private $directory;

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
