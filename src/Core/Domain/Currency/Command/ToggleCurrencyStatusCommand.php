<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Currency\Command;

use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;

/**
 * Class ToggleCurrencyStatusCommand is responsible for changing the status of the currency.
 */
class ToggleCurrencyStatusCommand
{
    /**
     * @var CurrencyId
     */
    private $currencyId;

    /**
     * @param int $currencyId
     *
     * @throws CurrencyException
     */
    public function __construct($currencyId)
    {
        $this->currencyId = new CurrencyId($currencyId);
    }

    /**
     * @return CurrencyId
     */
    public function getCurrencyId()
    {
        return $this->currencyId;
    }
}
