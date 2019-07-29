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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Command\EditAttachmentCommand;
use PrestaShop\PrestaShop\Core\Domain\Attachment\ValueObject\AttachmentId;
use PrestaShop\PrestaShop\Core\File\Uploader\FileUploaderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Creates/updates attachment from data submitted in category form
 */
class AttachmentFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @var FileUploaderInterface
     */
    private $fileUploader;

    /**
     * @param CommandBusInterface $commandBus
     * @param FileUploaderInterface $fileUploader
     */
    public function __construct(CommandBusInterface $commandBus, FileUploaderInterface $fileUploader)
    {
        $this->commandBus = $commandBus;
        $this->fileUploader = $fileUploader;
    }

    public function create(array $data)
    {
        $attachment = null;
        $file = $data['file'];
        if (!empty($file)) {
            $fileName = sha1(microtime());
            $attachment = new Attachment();

            foreach ($locales as $locale) {
                $attachment->name[(int) $locale['id_lang']] = $data['name'];
                $attachment->description[(int) $locale['id_lang']] = $data['description'];
            }

            $attachment->file = $fileName;
            $attachment->mime = $file->getMimeType();
            $attachment->file_name = $file->getClientOriginalName();

            $file->move(_PS_DOWNLOAD_DIR_, $fileName);

            if ($attachment->add()) {
                $attachment->attachProduct($product->id);
            }
        }

        return $attachment;
    }

    /**
     * @param int $attachmentId
     * @param array $data
     * @throws \PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentConstraintException
     */
    public function update($attachmentId, array $data)
    {
        $attachmentIdObj = new AttachmentId((int) $attachmentId);

        do {
            $uniqueFileName = sha1(microtime());
        } while (file_exists(_PS_DOWNLOAD_DIR_ . $uniqueFileName));

        $command = $this->createEditAttachmentCommand($attachmentIdObj, $data, $uniqueFileName);

        $this->uploadFile(
            $attachmentIdObj,
            $uniqueFileName,
            $data['file']
        );

        $this->commandBus->handle($command);
    }

    /**
     * Creates edit attachment command from
     *
     * @param $attachmentId
     * @param array $data
     * @param string $file
     * @return EditAttachmentCommand
     * @throws \PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentConstraintException
     */
    private function createEditAttachmentCommand(
        AttachmentId $attachmentId,
        array $data,
        string $file
    ) {
        /** @var UploadedFile|null $file */
        $uploadedFile = $data['file'];

        $command = new EditAttachmentCommand($attachmentId->getValue());
        $command->setFileName($data['file_name']);
        $command->setLocalizedDescriptions([$data['file_description']]);
        $command->setFile($file);
        $command->setAttachmentId($attachmentId);
        $command->setFileSize($uploadedFile->getSize());
        $command->setMimeType($uploadedFile->getMimeType());

        return $command;
    }

    /**
     * @param AttachmentId $attachmentId
     * @param string $uniqueFileName
     * @param UploadedFile|null $file
     */
    private function uploadFile(
        AttachmentId $attachmentId,
        string $uniqueFileName,
        UploadedFile $file = null

    ) {
        if (null !== $file) {
            $this->fileUploader->upload($attachmentId->getValue(), $file, $uniqueFileName);
        }
    }
}
