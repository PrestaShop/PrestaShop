<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Security;

use PrestaShop\PrestaShop\Core\Install\InstallationOptions;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class responsible for checking if back office access prerequisites are met.
 */
final class BackOfficeAccessPrerequisitesChecker implements BackOfficeAccessPrerequisitesCheckerInterface
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $rootDir;

    /**
     * @param Filesystem $filesystem
     * @param string $rootDirectory root directory of PrestaShop
     */
    public function __construct(Filesystem $filesystem, string $rootDirectory)
    {
        $this->rootDir = $rootDirectory;
        $this->filesystem = $filesystem;
    }

    /**
     * {@inheritdoc}
     */
    public function defaultAdminDirectoryExists()
    {
        $defaultAdminDir = InstallationOptions::DEFAULT_ADMIN_DIR;

        return $this->filesystem->exists($this->rootDir . "/{$defaultAdminDir}");
    }

    /**
     * {@inheritdoc}
     */
    public function installDirectoryExists()
    {
        $installDir = InstallationOptions::INSTALL_DIR;

        return $this->filesystem->exists($this->rootDir . "/{$installDir}");
    }
}
