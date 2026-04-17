<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Currency\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Currency\Command\AddCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;

/**
 * Interface AddCurrencyHandlerInterface defines contract for AddOfficialCurrencyHandler
 */
interface AddCurrencyHandlerInterface
{
    /**
     * @param AddCurrencyCommand $command
     *
     * @return CurrencyId
     */
    public function handle(AddCurrencyCommand $command);
}
