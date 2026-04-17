<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Attachment\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Attachment\AbstractAttachmentHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Command\DeleteAttachmentCommand;
use PrestaShop\PrestaShop\Core\Domain\Attachment\CommandHandler\DeleteAttachmentHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\DeleteAttachmentException;

/**
 * Delete attachment handler
 */
#[AsCommandHandler]
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
