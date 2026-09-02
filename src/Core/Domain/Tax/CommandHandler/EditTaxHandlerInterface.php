<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Tax\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Tax\Command\EditTaxCommand;

/**
 * Defines contract for EditTaxHandler
 */
interface EditTaxHandlerInterface
{
    /**
     * @param EditTaxCommand $command
     */
    public function handle(EditTaxCommand $command);
}
