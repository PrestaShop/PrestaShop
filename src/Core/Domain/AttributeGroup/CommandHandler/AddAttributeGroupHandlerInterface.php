<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\AttributeGroup\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Command\AddAttributeGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\ValueObject\AttributeGroupId;

/**
 * Describes add attribute group command handler
 */
interface AddAttributeGroupHandlerInterface
{
    /**
     * @param AddAttributeGroupCommand $command
     *
     * @return AttributeGroupId
     */
    public function handle(AddAttributeGroupCommand $command): AttributeGroupId;
}
