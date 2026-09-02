<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationStockAvailableCommand;

/**
 * Defines contract to handle @see UpdateCombinationStockAvailableCommand
 */
interface UpdateCombinationStockAvailableHandlerInterface
{
    public function handle(UpdateCombinationStockAvailableCommand $command): void;
}
