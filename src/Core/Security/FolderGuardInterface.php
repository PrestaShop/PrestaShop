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
