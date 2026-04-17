<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Attachment\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Attachment\Command\SetAssociatedProductAttachmentsCommand;

/**
 * Defines contract to handle @see SetAssociatedProductAttachmentsCommand
 */
interface SetAssociatedProductAttachmentsHandlerInterface
{
    /**
     * @param SetAssociatedProductAttachmentsCommand $command
     *
     * @return void
     */
    public function handle(SetAssociatedProductAttachmentsCommand $command): void;
}
