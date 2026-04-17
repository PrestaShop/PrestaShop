<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Currency\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Currency\Command\EditUnofficialCurrencyCommand;

/**
 * Interface EditUnofficialCurrencyHandlerInterface defines contract for UpdateCurrencyHandler.
 */
interface EditUnofficialCurrencyHandlerInterface
{
    /**
     * @param EditUnofficialCurrencyCommand $command
     */
    public function handle(EditUnofficialCurrencyCommand $command);
}
