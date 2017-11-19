<?php
/**
 * 2007-2017 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\TestCase;

class File
{
    /**
     * Recursivly copy a directory
     *
     * @var $src the source path (eg. /home/dir/to/copy)
     * @var $dst the destination path (eg. /home/)
     */
    public static function recurseCopy($src, $dst)
    {
        $dirp = opendir($src);
        @mkdir($dst);
        $file = readdir($dirp);
        while ($file !== false) {
            if ($file != '.' && $file != '..') {
                if (is_dir($src.'/'.$file)) {
                    File::recurseCopy($src.'/'.$file, $dst.'/'.$file);
                } else {
                    copy($src.'/'.$file, $dst.'/'.$file);
                }
            }
            $file = readdir($dirp);
        }
        closedir($dirp);
    }

    /**
     * Recursivly delete a directory
     *
     * @var $dir the directory to delete path (eg. /home/dir/to/delete)
     */
    public static function recurseDelete($dir)
    {
        $dirp = opendir($dir);
        $file = readdir($dirp);
        while ($file !== false) {
            if ($file != '.' && $file != '..') {
                if (is_dir($dir.'/'.$file)) {
                    File::recurseDelete($dir.'/'.$file);
                } else {
                    unlink($dir.'/'.$file);
                }
            }
            $file = readdir($dirp);
        }
        closedir($dirp);
        rmdir($dir);
    }
}
