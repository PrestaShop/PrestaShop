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

use PrestaShop\PrestaShop\Adapter\Attachment\AbstractAttachmentHandler;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Command\BulkDeleteAttachmentsCommand;
use PrestaShop\PrestaShop\Core\Domain\Attachment\CommandHandler\BulkDeleteAttachmentsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\BulkDeleteAttachmentsException;

/**
 * Bulk delete attachments handler
 */
final class BulkDeleteAttachmentsHandler extends AbstractAttachmentHandler implements BulkDeleteAttachmentsHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws BulkDeleteAttachmentsException
     */
    public function handle(BulkDeleteAttachmentsCommand $command)
    {
        $errors = [];

        foreach ($command->getAttachmentIds() as $attachmentId) {
            try {
                $attachment = $this->getAttachment($attachmentId);

                if (!$this->deleteAttachment($attachment)) {
                    $errors[] = $attachment->id;
                }
            } catch (AttachmentException $e) {
                $errors[] = $attachmentId->getValue();
            }
        }

        if (!empty($errors)) {
            throw new BulkDeleteAttachmentsException($errors, 'Failed to delete all of selected attachments');
        }
    }
}
