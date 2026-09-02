<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\AttributeGroup\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Command\EditAttributeGroupCommand;

/**
 * Describes a service that handles attribute group edit command.
 */
interface EditAttributeGroupHandlerInterface
{
    /**
     * @param EditAttributeGroupCommand $command
     */
    public function handle(EditAttributeGroupCommand $command): void;
}
