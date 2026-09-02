<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\AttributeGroup\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Command\BulkDeleteAttributeGroupCommand;

/**
 * Interface for handling command which deletes mutliple attribute groups
 */
interface BulkDeleteAttributeGroupHandlerInterface
{
    /**
     * @param BulkDeleteAttributeGroupCommand $command
     */
    public function handle(BulkDeleteAttributeGroupCommand $command);
}
