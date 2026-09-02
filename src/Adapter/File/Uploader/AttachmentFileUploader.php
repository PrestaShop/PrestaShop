<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\File\Uploader;

use Attachment;
use ErrorException;
use PrestaShop\PrestaShop\Core\Configuration\UploadSizeConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Attachment\AttachmentFileUploaderInterface;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentUploadFailedException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\CannotUnlinkAttachmentException;
use PrestaShopException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * Uploads attachment file and if needed deletes old attachment file
 */
class AttachmentFileUploader implements AttachmentFileUploaderInterface
{
    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var UploadSizeConfigurationInterface
     */
    protected $uploadSizeConfiguration;

    /**
     * @param ConfigurationInterface $configuration
     * @param UploadSizeConfigurationInterface $uploadSizeConfiguration
     */
    public function __construct(
        ConfigurationInterface $configuration,
        UploadSizeConfigurationInterface $uploadSizeConfiguration
    ) {
        $this->configuration = $configuration;
        $this->uploadSizeConfiguration = $uploadSizeConfiguration;
    }

    /**
     * {@inheritdoc}
     *
     * @param bool $throwExceptionOnFailure
     *
     * @throws AttachmentConstraintException
     * @throws AttachmentNotFoundException
     * @throws AttachmentUploadFailedException
     */
    public function upload(
        string $filePath,
        string $uniqueFileName,
        int $fileSize,
        ?int $id = null,
        bool $throwExceptionOnFailure = true
    ): void {
        $this->checkFileAllowedForUpload($fileSize);
        $this->uploadFile($filePath, $uniqueFileName, $fileSize);
        if ($id !== null) {
            $this->deleteOldFile($id, $throwExceptionOnFailure);
        }
    }

    /**
     * @param int $attachmentId
     * @param bool $throwExceptionOnFailure
     *
     * @throws AttachmentNotFoundException
     * @throws CannotUnlinkAttachmentException
     */
    protected function deleteOldFile(int $attachmentId, bool $throwExceptionOnFailure): void
    {
        try {
            $attachment = new Attachment($attachmentId);
            $fileLink = _PS_DOWNLOAD_DIR_ . $attachment->file;

            try {
                unlink($fileLink);
            } catch (ErrorException $e) {
                if ($throwExceptionOnFailure) {
                    throw new CannotUnlinkAttachmentException($e->getMessage(), 0, null, $fileLink);
                }
            }
        } catch (PrestaShopException) {
            throw new AttachmentNotFoundException(sprintf('Attachment with id "%s" was not found.', $attachmentId));
        }
    }

    /**
     * @param string $filePath
     * @param string $uniqid
     * @param int $fileSize
     *
     * @throws AttachmentConstraintException
     * @throws AttachmentUploadFailedException
     */
    protected function uploadFile(string $filePath, string $uniqid, int $fileSize): void
    {
        if ($fileSize > ($this->configuration->get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024 * 1024)) {
            throw new AttachmentConstraintException(
                sprintf(
                    'Max file size allowed is "%s" bytes. Uploaded file size is "%s".',
                    (string) ($this->configuration->get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024),
                    number_format($fileSize / 1024, 2, '.', '')
                ),
                AttachmentConstraintException::INVALID_FILE_SIZE
            );
        }

        try {
            move_uploaded_file($filePath, _PS_DOWNLOAD_DIR_ . $uniqid);
        } catch (FileException) {
            throw new AttachmentUploadFailedException(sprintf('Failed to copy the file %s.', $filePath));
        }
    }

    /**
     * @param int $fileSize
     *
     * @throws AttachmentConstraintException
     */
    protected function checkFileAllowedForUpload(int $fileSize): void
    {
        $maxFileSize = $this->uploadSizeConfiguration->getMaxUploadSizeInBytes();

        if ($maxFileSize > 0 && $fileSize > $maxFileSize) {
            throw new AttachmentConstraintException(
                sprintf('Max file size allowed is "%s" bytes. Uploaded file size is "%s".', $maxFileSize, $fileSize),
                AttachmentConstraintException::INVALID_FILE_SIZE
            );
        }
    }
}
