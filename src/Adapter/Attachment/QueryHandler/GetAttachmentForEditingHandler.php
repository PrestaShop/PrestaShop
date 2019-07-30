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

namespace PrestaShop\PrestaShop\Adapter\Attachment\QueryHandler;

use Attachment;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Query\GetAttachmentForEditing;
use PrestaShop\PrestaShop\Core\Domain\Attachment\QueryHandler\GetAttachmentForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Attachment\QueryResult\EditableAttachment;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Handles command that gets attachment for editing
 *
 * @internal
 */
final class GetAttachmentForEditingHandler implements GetAttachmentForEditingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetAttachmentForEditing $query): EditableAttachment
    {
        $attachmentIdValue = $query->getAttachmentId()->getValue();
        $attachment = new Attachment($attachmentIdValue);

        if ($attachment->id !== $attachmentIdValue) {
            throw new AttachmentNotFoundException(
                sprintf('Attachment with id "%s" was not found.', $attachmentIdValue)
            );
        }

        $filePath = _PS_DOWNLOAD_DIR_ . $attachment->file;
        $file = file_exists($filePath) ? new File($filePath) : null;

        return new EditableAttachment(
            $attachment->file_name,
            $attachment->name,
            $attachment->description,
            $file
        );
    }
}
