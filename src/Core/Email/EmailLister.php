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
        if (!is_dir($dir)) {
            return null;
        }

        $mail_directory = $this->filesystem->listEntriesRecursively($dir);
        $mail_list = [];

        // Remove unwanted .html / .txt / .tpl / .php / . / ..
        foreach ($mail_directory as $mail) {
            if (strpos($mail->getFilename(), '.') !== false) {
                $tmp = explode('.', $mail->getFilename());

                // Check for filename existence (left part) and if extension is html (right part)
                if (!isset($tmp[0]) || (isset($tmp[1]) && $tmp[1] !== 'html')) {
                    continue;
                }

                $mail_name_no_ext = $tmp[0];
                if (!in_array($mail_name_no_ext, $mail_list)) {
                    $mail_list[] = $mail_name_no_ext;
                }
            }
        }

        return $mail_list;
    }

    /**
     * Give in input getAvailableMails(), will output a human readable and proper string name.
     *
     * @return string
     */
    public function getCleanedMailName($mail_name)
    {
        if (strpos($mail_name, '.') !== false) {
            $tmp = explode('.', $mail_name);

            if (!isset($tmp[0])) {
                return $mail_name;
            }

            $mail_name = $tmp[0];
        }

        return ucfirst(str_replace(['_', '-'], ' ', $mail_name));
    }
}
