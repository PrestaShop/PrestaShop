<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Security;

use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShop\PrestaShop\Core\Exception\IOException;

/**
 * Interface used to protect a folder (via htaccess file, index.php redirection file, ...)
 */
interface FolderGuardInterface
{
    /**
     * @param string $folderPath
     *
     * @throws IOException
     * @throws FileNotFoundException
     */
    public function protectFolder($folderPath);
}
