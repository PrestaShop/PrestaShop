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
use PrestaShop\PrestaShop\Core\Domain\Attachment\Command\EditAttachmentCommand;
use PrestaShop\PrestaShop\Core\Domain\Attachment\CommandHandler\EditAttachmentHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentNotFoundException;

/**
 * Class EditAttachmentHandler
 */
final class EditAttachmentHandler implements EditAttachmentHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws AttachmentNotFoundException
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function handle(EditAttachmentCommand $command)
    {
        $attachmentIdValue = $command->getAttachmentId()->getValue();
        $attachment = new Attachment($attachmentIdValue);

        if ($attachment->id !== $attachmentIdValue) {
            throw new AttachmentNotFoundException(
                sprintf('Attachment with id "%s" was not found.', $attachmentIdValue)
            );
        }

        $this->updateAttachmentFromCommandData($attachment, $command);
    }

    private function updateAttachmentFromCommandData(Attachment $attachment, EditAttachmentCommand $command)
    {
        if (null !== $command->getFileName()) {
            $attachment->file_name = $command->getFileName();
        }

        if (null !== $command->getFile()) {
            $attachment->file = $command->getFile();
        }

        if (null !== $command->getFileSize()) {
            $attachment->file_size = $command->getFileSize();
        }

//        if (null !== $command->getLocalizedDescriptions()) {
//            foreach ()
//            $attachment->description = $command->getLocalizedLinkRewrites();
//        }

//        if (null !== $command->getLocalizedDescriptions()) {
//            $attachment->description = $command->getLocalizedDescriptions();
//        }

        if (null !== $command->getMimeType()) {
            $attachment->mime = $command->getMimeType();
        }

        $attachment->update();
    }
}
