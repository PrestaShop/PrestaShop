<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\SetCombinationCarriersCommand;

/**
 * Defines contract to handle @see SetCombinationCarriersCommand
 */
interface SetCombinationCarriersHandlerInterface
{
    public function handle(SetCombinationCarriersCommand $command): void;
}
