<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\AttributeGroup\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Command\DeleteAttributeGroupCommand;

/**
 * Interface for handling command which deletes attribute group
 */
interface DeleteAttributeGroupHandlerInterface
{
    /**
     * @param DeleteAttributeGroupCommand $command
     */
    public function handle(DeleteAttributeGroupCommand $command);
}
