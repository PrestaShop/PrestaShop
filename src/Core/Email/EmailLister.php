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

namespace PrestaShop\PrestaShop\Core\Email;

use PrestaShop\PrestaShop\Core\Foundation\Filesystem\Exception;
use PrestaShop\PrestaShop\Core\Foundation\Filesystem\FileSystem;

class EmailLister
{
    /**
     * @var FileSystem
     */
    private $filesystem;

    public function __construct(FileSystem $fs)
    {
        $this->filesystem = $fs;
    }

    /**
     * Return the list of available mails.
     *
     * @param string $dir
     *
     * @return array|null
     */
    public function getAvailableMails($dir)
    {
        try {
            $mail_directory = $this->filesystem->listFilesRecursively($dir);
        } catch (Exception $e) {
            return null;
        }

        $mail_list = [];
        foreach ($mail_directory as $mail) {
            $ext = $mail->getExtension();
            if (strtolower($ext) !== 'html') {
                continue;
            }

            $name = $mail->getBasename('.' . $ext);

            // Do not include hidden files (.html, .name.html, ...)
            if (substr($name, 0, 1) === '.') {
                continue;
            }

            $mail_list[$name] = $name;
        }

        return array_values($mail_list);
    }

    /**
     * Give in input getAvailableMails(), will output a human readable and proper string name.
     *
     * @return string
     */
    public function getCleanedMailName($mail_name)
    {
        $tmp = explode('.', $mail_name);
        $mail_name = $tmp[0];

        return ucfirst(trim(str_replace(['_', '-'], ' ', $mail_name)));
    }
}
