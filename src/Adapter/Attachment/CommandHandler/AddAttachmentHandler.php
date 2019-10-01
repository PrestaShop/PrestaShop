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

namespace PrestaShop\PrestaShop\Adapter\Attachment\CommandHandler;

use Attachment;
use PrestaShop\PrestaShop\Adapter\File\Uploader\AttachmentFileUploader;
use PrestaShop\PrestaShop\Core\Domain\Attachment\AttachmentFileUploaderInterface;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Command\AddAttachmentCommand;
use PrestaShop\PrestaShop\Core\Domain\Attachment\CommandHandler\AddAttachmentHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\CannotAddAttachmentException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\ValueObject\AttachmentId;
use PrestaShopException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Handles attachment creation and file uploading procedures
 */
final class AddAttachmentHandler extends AbstractAttachmentHandler implements AddAttachmentHandlerInterface
{
    /**
     * @var AttachmentFileUploader
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
     */
    public function handle(AddAttachmentCommand $command): AttachmentId
    {
        try {
            $attachment = new Attachment();

            $this->assertDescriptionContainsCleanHtml($command->getLocalizedDescriptions());
            $this->assertHasDefaultLanguage($command->getLocalizedNames());

            $uniqueFileName = $this->getUniqueFileName();

            $attachment->description = $command->getLocalizedDescriptions();
            $attachment->name = $command->getLocalizedNames();
            $attachment->file_name = $command->getOriginalName();
            $attachment->file = $uniqueFileName;
            $attachment->mime = $command->getMimeType();

            $this->assertValidFields($attachment);

            $this->fileUploader->upload($command->getFilePathName(), $uniqueFileName, $command->getFileSize());

            if (false === $attachment->add()) {
                throw new CannotAddAttachmentException('Failed to add attachment');
            }
        } catch (PrestaShopException $e) {
            throw new AttachmentException('An unexpected error occurred when adding attachment', 0, $e);
        }

        return new AttachmentId($attachment->id);
    }
}
