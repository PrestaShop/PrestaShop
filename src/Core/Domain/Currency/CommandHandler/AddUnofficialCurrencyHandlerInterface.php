<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Currency\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Currency\Command\AddUnofficialCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;

/**
 * Interface AddUnofficialCurrencyHandlerInterface defines contract for AddUnofficialCurrencyHandler
 */
interface AddUnofficialCurrencyHandlerInterface
{
    /**
     * @param AddUnofficialCurrencyCommand $command
     *
     * @return CurrencyId
     */
    public function handle(AddUnofficialCurrencyCommand $command);
}
