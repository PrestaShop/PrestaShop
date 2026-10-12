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

namespace PrestaShopBundle\Install;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Tells whether the shop is already installed: its settings file exists and no installation is running any more
 * (the lock file is written when the settings file is generated and removed at the end of the installation).
 */
final class InstallationState
{
    /**
     * @var string
     */
    private $settingsFile;

    /**
     * @var string
     */
    private $lockFile;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(string $settingsFile, string $lockFile, ?Filesystem $filesystem = null)
    {
        $this->settingsFile = $settingsFile;
        $this->lockFile = $lockFile;
        $this->filesystem = $filesystem ?? new Filesystem();
    }

    public function isInstalled(): bool
    {
        return $this->filesystem->exists($this->settingsFile) && !$this->filesystem->exists($this->lockFile);
    }
}
