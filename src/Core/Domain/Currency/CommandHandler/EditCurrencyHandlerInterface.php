<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Currency\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Currency\Command\EditCurrencyCommand;

/**
 * Interface EditCurrencyHandlerInterface defines contract for UpdateCurrencyHandler.
 */
interface EditCurrencyHandlerInterface
{
    /**
     * @param EditCurrencyCommand $command
     */
    public function handle(EditCurrencyCommand $command);
}
