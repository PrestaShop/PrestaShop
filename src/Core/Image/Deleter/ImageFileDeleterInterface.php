<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Image\Deleter;

/**
 * Interface ImageFileDeleterInterface describes an image file deleter.
 */
interface ImageFileDeleterInterface
{
    /**
     * Recursively deletes all images in the given path and removes empty folders.
     *
     * @param string $path images directory
     * @param bool $recursively if true deletes images from subdirectories
     * @param bool $deleteSubdirectories if true deletes the subdirectories as well
     * @param string $format image format
     *
     * @return bool
     */
    public function deleteFromPath($path, $recursively = false, $deleteSubdirectories = false, $format = 'jpg');

    /**
     * Delete all images from given path.
     *
     * @param string $path
     * @param string $format
     */
    public function deleteAllImages($path, $format = 'jpg');
}
