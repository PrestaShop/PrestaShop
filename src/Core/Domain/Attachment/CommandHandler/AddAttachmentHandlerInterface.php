<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Attachment\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Attachment\Command\AddAttachmentCommand;
use PrestaShop\PrestaShop\Core\Domain\Attachment\ValueObject\AttachmentId;

interface AddAttachmentHandlerInterface
{
    /**
     * @param AddAttachmentCommand $command
     *
     * @return AttachmentId
     */
    public function handle(AddAttachmentCommand $command): AttachmentId;
}
