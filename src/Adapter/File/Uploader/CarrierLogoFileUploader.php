<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\File\Uploader;

use PrestaShop\PrestaShop\Core\Domain\Carrier\CarrierLogoFileUploaderInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierLogoUploadFailedException;
use PrestaShop\PrestaShop\Core\File\Exception\FileException;

/**
 * Uploads carrier logo file
 */
class CarrierLogoFileUploader implements CarrierLogoFileUploaderInterface
{
    public function upload(string $filePath, int $id): void
    {
        try {
            move_uploaded_file($filePath, _PS_SHIP_IMG_DIR_ . $id . '.jpg');
        } catch (FileException) {
            throw new CarrierLogoUploadFailedException(sprintf('Failed to copy the file %s.', $filePath));
        }
    }

    public function deleteOldFile(int $id): void
    {
        if (file_exists(_PS_SHIP_IMG_DIR_ . $id . '.jpg')) {
            unlink(_PS_SHIP_IMG_DIR_ . $id . '.jpg');
        }

        if (file_exists(_PS_TMP_IMG_DIR_ . '/carrier_mini_' . $id . '.jpg')) {
            unlink(_PS_TMP_IMG_DIR_ . '/carrier_mini_' . $id . '.jpg');
        }
    }
}
