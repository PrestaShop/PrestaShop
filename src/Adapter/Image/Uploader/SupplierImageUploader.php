<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Image\Uploader;

use PrestaShop\PrestaShop\Core\Image\Uploader\ImageUploaderInterface;
use Supplier;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Uploads supplier logo image
 */
final class SupplierImageUploader extends AbstractImageUploader implements ImageUploaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function upload($supplierId, UploadedFile $image)
    {
        $this->checkImageIsAllowedForUpload($image);
        $tempImageName = $this->createTemporaryImage($image);
        $this->deleteOldImage($supplierId);

        $destination = _PS_SUPP_IMG_DIR_ . $supplierId . '.jpg';
        $this->uploadFromTemp($tempImageName, $destination);

        if (file_exists($destination)) {
            $this->generateDifferentSize($supplierId, _PS_SUPP_IMG_DIR_, 'suppliers');
        }
    }

    /**
     * Deletes old image
     *
     * @param int $id
     */
    private function deleteOldImage($id)
    {
        $supplier = new Supplier($id);
        $supplier->deleteImage();

        $currentLogo = _PS_TMP_IMG_DIR_ . 'supplier_mini_' . $id . '.jpg';

        if (file_exists($currentLogo)) {
            unlink($currentLogo);
        }
    }
}
