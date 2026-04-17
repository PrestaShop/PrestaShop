<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Customization\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Command\SetProductCustomizationFieldsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationFieldId;

/**
 * Defines contract to handle @see SetProductCustomizationFieldsCommand
 */
interface SetProductCustomizationFieldsHandlerInterface
{
    /**
     * @param SetProductCustomizationFieldsCommand $command
     *
     * @return CustomizationFieldId[]
     */
    public function handle(SetProductCustomizationFieldsCommand $command): array;
}
