<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Command\BulkDeleteAttributeCommand;

/**
 * Interface for handling command which deletes attributes in bulk action
 */
interface BulkDeleteAttributeHandlerInterface
{
    /**
     * @param BulkDeleteAttributeCommand $command
     */
    public function handle(BulkDeleteAttributeCommand $command);
}
