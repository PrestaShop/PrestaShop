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
use PrestaShop\PrestaShop\Core\Domain\Attachment\Command\CreateAttachmentCommand;
use PrestaShop\PrestaShop\Core\Domain\Attachment\CommandHandler\CreateAttachmentHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\CannotAddAttachmentException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\ValueObject\AttachmentId;
use PrestaShopException;

/**
 * Handles attachment creation
 */
final class CreateAttachmentHandler extends AbstractAttachmentHandler implements CreateAttachmentHandlerInterface
{
    /**
     * @var AttachmentFileUploader
     */
    protected $fileUploader;

    /**
     * @param AttachmentFileUploader $fileUploader
     */
    public function __construct(AttachmentFileUploader $fileUploader)
    {
        $this->fileUploader = $fileUploader;
    }

    /**
     * {@inheritdoc}
     *
     * @throws AttachmentConstraintException
     * @throws AttachmentException
     * @throws AttachmentNotFoundException
     */
    public function handle(CreateAttachmentCommand $command)
    {
        try {
            $attachment = new Attachment();

            $this->assertDescriptionContainsCleanHtml($command->getLocalizedDescriptions());
            $this->assertHasDefaultLanguage($command->getLocalizedNames());

            $uniqueFileName = $this->getUniqueFileName();

            $attachment->file_name = $command->getOriginalName();
            $attachment->file = $uniqueFileName;
            $attachment->description = $command->getLocalizedDescriptions();
            $attachment->name = $command->getLocalizedNames();
            $attachment->mime = $command->getMimeType();

            if (!$attachment->validateFields(false) && !$attachment->validateFieldsLang(false)) {
                throw new AttachmentConstraintException(
                    'Attachment contains invalid field values',
                    AttachmentConstraintException::INVALID_FIELDS
                );
            }

            if (null !== $command->getFilePathName()) {
                $this->fileUploader->upload($command->getFilePathName(), $uniqueFileName, $command->getFileSize());
            }

            if (false === $attachment->add()) {
                throw new CannotAddAttachmentException(
                    'Failed to add attachment'
                );
            }
        } catch (PrestaShopException $e) {
            throw new AttachmentException(
                'An unexpected error occurred when adding attachment',
                0,
                $e
            );
        }

        return new AttachmentId($attachment->id);
    }
}
