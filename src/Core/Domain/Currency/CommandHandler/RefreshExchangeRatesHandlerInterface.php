<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Currency\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Currency\Command\RefreshExchangeRatesCommand;

/**
 * Interface RefreshExchangeRatesHandlerInterface defines contract for UpdateExchangeRatesHandler.
 */
interface RefreshExchangeRatesHandlerInterface
{
    /**
     * @param RefreshExchangeRatesCommand $command
     */
    public function handle(RefreshExchangeRatesCommand $command);
}
