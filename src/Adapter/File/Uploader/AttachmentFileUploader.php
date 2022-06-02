<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
        int $id = null,
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
        } catch (PrestaShopException $e) {
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
                    number_format(($fileSize / 1024), 2, '.', '')
                ),
                AttachmentConstraintException::INVALID_FILE_SIZE
            );
        }

        try {
            move_uploaded_file($filePath, _PS_DOWNLOAD_DIR_ . $uniqid);
        } catch (FileException $e) {
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
