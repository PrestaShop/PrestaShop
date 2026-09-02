<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\GenerateProductCombinationsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;

/**
 * Defines contract to handle @see GenerateProductCombinationsCommand
 */
interface GenerateProductCombinationsHandlerInterface
{
    /**
     * @param GenerateProductCombinationsCommand $command
     *
     * @return CombinationId[]
     */
    public function handle(GenerateProductCombinationsCommand $command): array;
}
