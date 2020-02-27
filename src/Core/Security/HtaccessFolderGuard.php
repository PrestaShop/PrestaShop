<?php
/**
 * 2007-2020 Friends of PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 */

namespace PrestaShop\PrestaShop\Core\Security;

use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShop\PrestaShop\Core\Exception\IOException;

/**
 * Class HtaccessFolderGuard protects a designated folder by inserting an htaccess file in it
 * which prevents access from an external call.
 */
class HtaccessFolderGuard implements FolderGuardInterface
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
        if (!is_dir($folderPath)) {
            throw new FileNotFoundException(sprintf('Cannot protect nonexistent folder %s', $folderPath));
        }

        $htaccessPath = $folderPath . DIRECTORY_SEPARATOR . '.htaccess';
        if (!file_exists($htaccessPath)) {
            if (!is_writable($folderPath)) {
                throw new IOException('Could not write into module folder', 0, null, $folderPath);
            }

            file_put_contents($htaccessPath, $this->htaccessContent);
        }
    }
}
