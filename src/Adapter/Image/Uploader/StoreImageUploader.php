<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Image\Uploader;

use PrestaShop\PrestaShop\Core\Image\Uploader\ImageUploaderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class StoreImageUploader extends AbstractImageUploader implements ImageUploaderInterface
{
    public function upload($storeId, UploadedFile $image): void
    {
        $this->checkImageIsAllowedForUpload($image);

        $temporaryImageName = $this->createTemporaryImage($image);

        $this->uploadFromTemp($temporaryImageName, _PS_STORE_IMG_DIR_ . $storeId . '.jpg');

        $this->generateDifferentSize($storeId, _PS_STORE_IMG_DIR_, 'stores');
    }
}
