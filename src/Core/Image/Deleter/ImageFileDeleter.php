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

namespace PrestaShop\PrestaShop\Core\Image\Deleter;

/**
 * Class ImageFileDeleter is responsible for deleting image files.
 */
final class ImageFileDeleter implements ImageFileDeleterInterface
{
    /**
     * {@inheritdoc}
     */
    public function deleteFromPath($path, $recursively = false, $deleteSubdirectories = false, $format = 'jpg')
    {
        if (!$path || !$format || !is_dir($path)) {
            return false;
        }

        foreach (scandir($path, SCANDIR_SORT_NONE) as $file) {
            $pattern = '/^[0-9]+(\-(.*))?\.' . $format . '$/';

            if ($recursively && is_dir($path . $file) && (preg_match('/^[0-9]$/', $file))) {
                // Recursion
                $this->deleteFromPath($path . $file . '/', $recursively, $deleteSubdirectories, $format);
            }

            // Delete the file by regex pattern
            $this->deleteByPattern($pattern, $path, $file);

            // Delete fileType file if it exists in the same directory.
            if (file_exists($path . 'fileType')) {
                unlink($path . 'fileType');
            }
        }

        // Can we remove the image folder?
        if ($deleteSubdirectories && is_numeric(basename($path))) {
            $removeFolder = true;
            foreach (scandir($path, SCANDIR_SORT_NONE) as $file) {
                if (($file != '.' && $file != '..' && $file != 'index.php')) {
                    $removeFolder = false;
                    break;
                }
            }

            if ($removeFolder) {
                // we're only removing index.php if it's a folder we want to delete
                if (file_exists($path . 'index.php')) {
                    unlink($path . 'index.php');
                }
                rmdir($path);
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAllImages($path, $format = 'jpg')
    {
        foreach (scandir($path, SCANDIR_SORT_NONE) as $file) {
            $this->deleteByPattern(
                '/(.*)\.' . $format . '$/',
                $path,
                $file
            );
        }
    }

    /**
     * Delete images by given regex pattern from given path.
     *
     * @param string $pattern regex pattern
     * @param string $path file directory path
     * @param string $filename
     */
    private function deleteByPattern($pattern, $path, $filename)
    {
        if (preg_match($pattern, $filename)) {
            unlink($path . $filename);
        }
    }
}
