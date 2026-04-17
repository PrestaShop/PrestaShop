<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
