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

namespace PrestaShop\PrestaShop\Adapter\Security;

use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Security\BackOfficeAccessPrerequisitesHandlerInterface;
use PrestaShop\PrestaShop\Core\Security\Exception\UnableToRenameAdminDirectoryException;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

final class BackOfficeAccessPrerequisitesHandler implements BackOfficeAccessPrerequisitesHandlerInterface
{
    /**
     * @var string
     */
    private $adminDir;

    /**
     * @var Tools
     */
    private $tools;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param ConfigurationInterface $configuration
     * @param Tools $tools
     * @param Filesystem $filesystem
     */
    public function __construct(ConfigurationInterface $configuration, Tools $tools, Filesystem $filesystem)
    {
        $this->adminDir = $configuration->get('_PS_ADMIN_DIR_');
        $this->tools = $tools;
        $this->filesystem = $filesystem;
    }

    /**
     * {@inheritdoc}
     */
    public function isAdminDirectoryRenamed()
    {
        return basename($this->adminDir) != 'admin'
            || !$this->filesystem->exists($this->adminDir . '/../admin/')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function renameAdminDirectory()
    {
        $newName = sprintf(
            'admin%03d%s/',
            mt_rand(0, 999),
            strtolower($this->tools->generatePassword(6))
        );

        try {
            $this->filesystem->rename($this->adminDir.'/../admin/', $this->adminDir.'/../'.$newName, true);
        } catch (IOException $e) {
            throw new UnableToRenameAdminDirectoryException(
                $newName,
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }

        return $newName;
    }

    /**
     * {@inheritdoc}
     */
    public function installDirectoryExists()
    {
        return $this->filesystem->exists($this->adminDir . '/../install');
    }
}
