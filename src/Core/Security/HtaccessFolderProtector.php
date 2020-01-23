<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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

use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShop\PrestaShop\Core\Exception\IOException;

/**
 * Class HtaccessFolderProtector protects a designated folder by inserting an htaccess file in it
 * which prevents access from an external call.
 */
class HtaccessFolderProtector implements FolderProtectorInterface
{
    /**
     * @var string
     */
    private $htaccessContent;

    /**
     * @param string $htaccessTemplatePath
     *
     * @throws FileNotFoundException
     */
    public function __construct($htaccessTemplatePath)
    {
        if (!file_exists($htaccessTemplatePath)) {
            throw new FileNotFoundException(sprintf('Could not find file %s', $htaccessTemplatePath));
        }
        $this->htaccessContent = file_get_contents($htaccessTemplatePath);
    }

    /**
     * {@inheritdoc}
     */
    public function protectFolder($folderPath)
    {
        if (!file_exists($folderPath) || !is_dir($folderPath)) {
            throw new FileNotFoundException(sprintf('Cannot protect nonexistent folder %s', $folderPath));
        }

        $htaccessPath = $folderPath . DIRECTORY_SEPARATOR . '.htaccess';
        if (!file_exists($htaccessPath)) {
            if (!@file_put_contents($htaccessPath, $this->htaccessContent)) {
                throw new IOException('Could not write htaccess file', 0, null, $htaccessPath);
            }
        }
    }
}
