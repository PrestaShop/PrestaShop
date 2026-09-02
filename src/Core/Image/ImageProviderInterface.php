<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Image;

/**
 * Interface ThumbnailProviderInterface.
 */
interface ImageProviderInterface
{
    /**
     * Get thumbnail image path.
     *
     * @param int $imageId
     *
     * @return string Path to thumbnail
     */
    public function getPath($imageId);
}
