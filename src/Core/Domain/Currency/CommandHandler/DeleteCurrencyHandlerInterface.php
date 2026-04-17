<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Currency\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Currency\Command\DeleteCurrencyCommand;

/**
 * Interface DeleteCurrencyHandlerInterface defines contract for DeleteCurrencyHandler.
 */
interface DeleteCurrencyHandlerInterface
{
    /**
     * Handles the deletion logic of currency.
     *
     * @param DeleteCurrencyCommand $command
     */
    public function handle(DeleteCurrencyCommand $command);
}
