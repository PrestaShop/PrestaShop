<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Command\AddAttributeCommand;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\ValueObject\AttributeId;

/**
 * Describes add attribute command handler
 */
interface AddAttributeHandlerInterface
{
    /**
     * @param AddAttributeCommand $command
     *
     * @return AttributeId
     */
    public function handle(AddAttributeCommand $command): AttributeId;
}
