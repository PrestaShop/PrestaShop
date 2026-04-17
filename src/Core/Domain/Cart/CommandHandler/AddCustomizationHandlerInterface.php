<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Cart\Command\AddCustomizationCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationId;

/**
 * Defines contract to handle @var AddCustomizationCommand
 */
interface AddCustomizationHandlerInterface
{
    /**
     * @param AddCustomizationCommand $command
     *
     * @return CustomizationId|null customizationId
     */
    public function handle(AddCustomizationCommand $command): ?CustomizationId;
}
