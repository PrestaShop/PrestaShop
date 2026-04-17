<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Image\Uploader;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Interface ImageUploaderInterface is contract for entity (e.g. Category, Product & etc.) image uploader.
 */
interface ImageUploaderInterface
{
    /**
     * Upload entity image.
     *
     * @param int $entityId
     * @param UploadedFile $uploadedImage
     */
    public function upload($entityId, UploadedFile $uploadedImage);
}
