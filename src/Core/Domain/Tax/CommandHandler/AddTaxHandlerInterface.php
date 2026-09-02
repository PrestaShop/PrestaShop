<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Tax\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Tax\Command\AddTaxCommand;

/**
 * Defines contract for AddTaxHandler
 */
interface AddTaxHandlerInterface
{
    /**
     * @param AddTaxCommand $command
     */
    public function handle(AddTaxCommand $command);
}
