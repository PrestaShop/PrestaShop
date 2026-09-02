<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Attachment;

interface AttachmentFileUploaderInterface
{
    /**
     * @param string $filePath
     * @param string $uniqueFileName
     * @param int $fileSize
     * @param int|null $id
     */
    public function upload(string $filePath, string $uniqueFileName, int $fileSize, ?int $id = null): void;
}
