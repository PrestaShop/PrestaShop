<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\File\Uploader;

use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\AttributeFileUploaderInterface;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Exception\AttributeUploadFailedException;
use PrestaShop\PrestaShop\Core\File\Exception\FileException;

class AttributeFileUploader implements AttributeFileUploaderInterface
{
    public function upload(string $filePath, int $id): void
    {
        try {
            if (file_exists($filePath)) {
                move_uploaded_file($filePath, _PS_IMG_DIR_ . 'co/' . $id . '.jpg');
            }
        } catch (FileException) {
            throw new AttributeUploadFailedException(sprintf('Failed to copy the file %s.', $filePath));
        }
    }

    public function deleteOldFile(int $id): void
    {
        if (file_exists(_PS_IMG_DIR_ . 'co/' . $id . '.jpg')) {
            unlink(_PS_IMG_DIR_ . 'co/' . $id . '.jpg');
        }
    }
}
