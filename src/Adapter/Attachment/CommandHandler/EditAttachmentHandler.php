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

namespace PrestaShop\PrestaShop\Adapter\Attachment\CommandHandler;

use Attachment;
use PrestaShop\PrestaShop\Adapter\Attachment\AbstractAttachmentHandler;
use PrestaShop\PrestaShop\Core\Domain\Attachment\AttachmentFileUploaderInterface;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Command\EditAttachmentCommand;
use PrestaShop\PrestaShop\Core\Domain\Attachment\CommandHandler\EditAttachmentHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\CannotUpdateAttachmentException;
use PrestaShopException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Handles editing of attachment and file uploading procedures
 */
final class EditAttachmentHandler extends AbstractAttachmentHandler implements EditAttachmentHandlerInterface
{
    /**
     * @var AttachmentFileUploaderInterface
     */
    protected $fileUploader;

    /**
     * @param ValidatorInterface $validator
     * @param AttachmentFileUploaderInterface $fileUploader
     */
    public function __construct(ValidatorInterface $validator, AttachmentFileUploaderInterface $fileUploader)
    {
        parent::__construct($validator);

        $this->fileUploader = $fileUploader;
    }

    /**
     * {@inheritdoc}
     *
     * @throws AttachmentConstraintException
     * @throws AttachmentException
     * @throws AttachmentNotFoundException
     * @throws CannotUpdateAttachmentException
     */
    public function handle(EditAttachmentCommand $command)
    {
        $attachmentIdValue = $command->getAttachmentId()->getValue();

        try {
            $attachment = new Attachment($attachmentIdValue);
        } catch (PrestaShopException $e) {
            throw new AttachmentNotFoundException(sprintf('Attachment with id "%s" was not found.', $attachmentIdValue));
        }

        if ($attachment->id !== $attachmentIdValue) {
            throw new AttachmentNotFoundException(sprintf('Attachment with id "%s" was not found.', $attachmentIdValue));
        }

        $this->updateAttachmentFromCommandData($attachment, $command);
    }

    /**
     * @param Attachment $attachment
     * @param EditAttachmentCommand $command
     *
     * @throws AttachmentConstraintException
     * @throws AttachmentException
     * @throws AttachmentNotFoundException
     * @throws CannotUpdateAttachmentException
     */
    private function updateAttachmentFromCommandData(Attachment $attachment, EditAttachmentCommand $command)
    {
        try {
            if (!$attachment->validateFields(false) && !$attachment->validateFieldsLang(false)) {
                throw new AttachmentConstraintException('Attachment contains invalid field values', AttachmentConstraintException::INVALID_FIELDS);
            }

            $this->assertDescriptionContainsCleanHtml($command->getLocalizedDescriptions());
            $this->assertHasDefaultLanguage($command->getLocalizedNames());

            $attachment->description = $command->getLocalizedDescriptions();
            $attachment->name = $command->getLocalizedNames();

            $this->assertValidFields($attachment);

            if (null !== $command->getPathName()) {
                $uniqueFileName = $this->getUniqueFileName();

                $attachment->file_name = $command->getOriginalFileName();
                $attachment->file = $uniqueFileName;
                $attachment->mime = $command->getMimeType();

                $this->assertValidFields($attachment);

                $this->fileUploader->upload(
                    $command->getPathName(),
                    $uniqueFileName,
                    $command->getFileSize(),
                    $command->getAttachmentId()->getValue()
                );
            }

            if (false === $attachment->update()) {
                throw new CannotUpdateAttachmentException('Failed to update attachment');
            }
        } catch (PrestaShopException $e) {
            throw new AttachmentException('An unexpected error occurred when updating attachment', 0, $e);
        }
    }
}
