<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Attachment\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Attachment\Command\DeleteAttachmentCommand;

/**
 * Defines contract for DeleteAttachmentHandler
 */
interface DeleteAttachmentHandlerInterface
{
    /**
     * @param DeleteAttachmentCommand $command
     */
    public function handle(DeleteAttachmentCommand $command);
}
