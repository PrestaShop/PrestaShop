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
use PrestaShop\PrestaShop\Core\Domain\Attachment\Command\CreateAttachmentCommand;
use PrestaShop\PrestaShop\Core\Domain\Attachment\CommandHandler\CreateAttachmentHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentConstraintException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class CreateAttachmentHandler
 */
final class CreateAttachmentHandler implements CreateAttachmentHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws AttachmentConstraintException
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function handle(CreateAttachmentCommand $command)
    {
        $attachment = new Attachment();

        $this->createAttachmentFromCommandData($attachment, $command);
    }

    /**
     * @param Attachment $attachment
     * @param CreateAttachmentCommand $command
     *
     * @throws AttachmentConstraintException
     * @throws \PrestaShopException
     */
    private function createAttachmentFromCommandData(Attachment $attachment, CreateAttachmentCommand $command)
    {
        if (null !== $command->getUniqueFileName()) {
            $attachment->file = $command->getUniqueFileName();
        }

        if ($command->getFile() instanceof UploadedFile) {
            $attachment->file_name = $command->getFile()->getClientOriginalName();
        }

        if (null !== $command->getLocalizedDescriptions()) {
            $attachment->description = $command->getLocalizedDescriptions();
        }

        if (null !== $command->getLocalizedNames()) {
            $attachment->name = $command->getLocalizedNames();
        }

        if (null !== $command->getMimeType()) {
            $attachment->mime = $command->getMimeType();
        }

        if (!$attachment->validateFields(false)) {
            throw new AttachmentConstraintException(
                'Attachment contains invalid field values',
                AttachmentConstraintException::INVALID_FIELDS
            );
        }

        $attachment->add();
    }
}
