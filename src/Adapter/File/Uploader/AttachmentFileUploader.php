<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\File\Uploader;

use Attachment;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentException;
use PrestaShop\PrestaShop\Core\File\Uploader\FileUploaderInterface;
use PrestaShopBundle\Translation\TranslatorAwareTrait;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tools;

/**
 * Class AttachmentFileUploader
 *
 * @internal
 */
final class AttachmentFileUploader implements FileUploaderInterface
{
    use TranslatorAwareTrait;

    /**
     * {@inheritdoc}
     *
     * @throws AttachmentConstraintException
     * @throws AttachmentException
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function upload($id, UploadedFile $uploadedFile, string $uniqueFileName)
    {
        $this->checkFileAllowedForUpload($uploadedFile);
        $this->uploadFile($uploadedFile, $uniqueFileName);
        $this->deleteOldFile($id);
    }

    /**
     * Delete old attachment file.
     * @param int $attachmentId
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function deleteOldFile(int $attachmentId)
    {
        $attachment = new Attachment($attachmentId);
        @unlink(_PS_DOWNLOAD_DIR_ . $attachment->file);
    }

    /**
     * @param UploadedFile $file
     * @param string $uniqid
     * @throws AttachmentConstraintException
     * @throws AttachmentException
     */
    private function uploadFile(UploadedFile $file, string $uniqid)
    {
        if ($file->getError() === 1) {
            $max_upload = (int) ini_get('upload_max_filesize');
            $max_post = (int) ini_get('post_max_size');
            $upload_mb = min($max_upload, $max_post);
            throw new AttachmentConstraintException($this->trans(
                'The file %file% exceeds the size allowed by the server. The limit is set to %size% MB.',
                array('%file%' => '<b>' . $file->getClientOriginalName() . '</b> ', '%size%' => '<b>' . $upload_mb . '</b>'),
                'Admin.Catalog.Notification'
            ), AttachmentConstraintException::INVALID_FILE_SIZE);
        }

        if ($file->getSize() > (Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024 * 1024)) {
            throw new AttachmentConstraintException($this->trans(
                'The file is too large. Maximum size allowed is: %1$d kB. The file you are trying to upload is %2$d kB.',
                array(
                    '%1$d' => (Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024),
                    '%2$d' => number_format(($file->getSize() / 1024), 2, '.', ''),
                ),
                'Admin.Notifications.Error'
            ), AttachmentConstraintException::INVALID_FILE_SIZE);
        }

        try {
            $file->move(_PS_DOWNLOAD_DIR_, $uniqid);
        } catch (FileException $e) {
            throw new AttachmentException($this->trans('Failed to copy the file.',
                [],
                'Admin.Catalog.Notification')
            );
        }
    }

    /**
     * @param UploadedFile $file
     * @throws AttachmentConstraintException
     */
    private function checkFileAllowedForUpload(UploadedFile $file)
    {
        $maxFileSize = Tools::getMaxUploadSize();

        if ($maxFileSize > 0 && $file->getSize() > $maxFileSize) {
            throw new AttachmentConstraintException(
                sprintf(
                    'Max file size allowed is "%s" bytes. Uploaded file size is "%s".',
                    $maxFileSize,
                    $file->getSize()
                ),
                AttachmentConstraintException::INVALID_FILE_SIZE
            );
        }
    }
}
