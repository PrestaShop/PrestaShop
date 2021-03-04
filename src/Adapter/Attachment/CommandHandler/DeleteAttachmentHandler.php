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
use PrestaShop\PrestaShop\Core\Domain\Attachment\Command\DeleteAttachmentCommand;
use PrestaShop\PrestaShop\Core\Domain\Attachment\CommandHandler\DeleteAttachmentHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\DeleteAttachmentException;

/**
 * Delete attachment handler
 */
final class DeleteAttachmentHandler extends AbstractAttachmentHandler implements DeleteAttachmentHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws DeleteAttachmentException
     * @throws AttachmentNotFoundException
     */
    public function handle(DeleteAttachmentCommand $command)
    {
        $attachment = $this->getAttachment($command->getAttachmentId());

        if (!$this->deleteAttachment($attachment)) {
            throw new DeleteAttachmentException(sprintf('Cannot delete Attachment object with id "%s".', $attachment->id));
        }
    }
}
