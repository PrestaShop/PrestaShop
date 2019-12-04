<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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

namespace PrestaShop\PrestaShop\Adapter\Attachment;

use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\DeleteAttachmentException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\ValueObject\AttachmentId;
use PrestaShopException;
use Attachment;

/**
 * Abstract attachment handler
 */
abstract class AbstractAttachmentHandler
{
    /**
     * @param AttachmentId $attachmentId
     *
     * @return Attachment
     *
     * @throws AttachmentNotFoundException
     */
    protected function getAttachment(AttachmentId $attachmentId): Attachment
    {
        $attachmentIdValue = $attachmentId->getValue();
        try {
            $attachment = new Attachment($attachmentIdValue);
        } catch (PrestaShopException $e) {
            throw new AttachmentNotFoundException(
                sprintf('Attachment with id "%s" was not found.', $attachmentId->getValue())
            );
        }

        if ($attachment->id !== $attachmentId->getValue()) {
            throw new AttachmentNotFoundException(
                sprintf('Attachment with id "%s" was not found.', $attachmentId->getValue())
            );
        }

        return $attachment;
    }

    /**
     * Deletes legacy Attachment
     *
     * @param Attachment $attachment
     *
     * @return bool
     *
     * @throws DeleteAttachmentException
     */
    protected function deleteAttachment(Attachment $attachment): bool
    {
        try {
            return $attachment->delete();
        } catch (PrestaShopException $e) {
            throw new DeleteAttachmentException(
                sprintf('An error occurred when deleting Attachment object with id "%s".', $attachment->id)
            );
        }
    }
}
