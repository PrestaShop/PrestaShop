<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Attachment\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Attachment\Command\RemoveAllAssociatedProductAttachmentsCommand;

/**
 * Defines contract to handle @see RemoveAllAssociatedProductAttachmentsCommand
 */
interface RemoveAllAssociatedProductAttachmentsHandlerInterface
{
    /**
     * @param RemoveAllAssociatedProductAttachmentsCommand $command
     */
    public function handle(RemoveAllAssociatedProductAttachmentsCommand $command): void;
}
