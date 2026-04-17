<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Attachment\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Attachment\Command\EditAttachmentCommand;

interface EditAttachmentHandlerInterface
{
    /**
     * @param EditAttachmentCommand $command
     */
    public function handle(EditAttachmentCommand $command);
}
