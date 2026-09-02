<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Attachment\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Attachment\Command\BulkDeleteAttachmentsCommand;

/**
 * Defines contract for bulk delete attachments handler
 */
interface BulkDeleteAttachmentsHandlerInterface
{
    /**
     * @param BulkDeleteAttachmentsCommand $command
     */
    public function handle(BulkDeleteAttachmentsCommand $command);
}
