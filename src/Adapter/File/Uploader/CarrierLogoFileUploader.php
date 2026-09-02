<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\File\Uploader;

use ErrorException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\CarrierLogoFileUploaderInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierLogoUploadFailedException;

/**
 * Uploads carrier logo file
 */
class CarrierLogoFileUploader implements CarrierLogoFileUploaderInterface
{
    public function upload(string $filePath, int $id): void
    {
        // The file is copied then removed, and not moved with move_uploaded_file: that function only accepts a file
        // uploaded by the running request, so it silently did nothing for every caller but a form submission, the
        // Admin API included. The image itself is left untouched, and it has already been checked by
        // CarrierValidator::validateLogoUpload() at this point.
        try {
            copy($filePath, _PS_SHIP_IMG_DIR_ . $id . '.jpg');
        } catch (ErrorException $e) {
            throw new CarrierLogoUploadFailedException(sprintf('Failed to copy the file %s.', $filePath), 0, $e);
        }

        if (file_exists($filePath)) {
            unlink($filePath);
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
