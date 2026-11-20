<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Image\Deleter;

use PrestaShop\PrestaShop\Core\Image\Exception\ImageException;
use PrestaShop\PrestaShop\Core\Image\ImageFormatConfiguration;

/**
 * Removes the "No picture" thumbnails that writers do not overwrite, so they are regenerated from the new image.
 */
final class NoPictureThumbnailDeleter
{
    /**
     * @throws ImageException
     */
    public function delete(string $isoCode, string $directory): void
    {
        // Invalidate disabled formats too, so they cannot become stale when enabled again.
        foreach (array_diff(ImageFormatConfiguration::SUPPORTED_FORMATS, ['jpg', 'png']) as $format) {
            $thumbnails = glob($directory . $isoCode . '-default-*.' . $format);
            if (false === $thumbnails) {
                throw new ImageException(sprintf('Cannot list "No Picture" thumbnails in "%s"', $directory));
            }

            foreach ($thumbnails as $thumbnail) {
                if (!@unlink($thumbnail)) {
                    throw new ImageException(sprintf('Cannot remove "No Picture" thumbnail "%s"', $thumbnail));
                }
            }
        }
    }
}
