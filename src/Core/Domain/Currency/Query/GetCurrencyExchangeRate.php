<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Currency\Query;

use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\AlphaIsoCode;

/**
 * Retrieves the exchange rate for a currency compared to the shop's default
 */
class GetCurrencyExchangeRate
{
    /**
     * @var AlphaIsoCode
     */
    private $isoCode;

    /**
     * @param string $isoCode Currency ISO code
     *
     * @throws CurrencyException
     */
    public function __construct(string $isoCode)
    {
        $this->isoCode = new AlphaIsoCode($isoCode);
    }

    /**
     * @return AlphaIsoCode
     */
    public function getIsoCode(): AlphaIsoCode
    {
        return $this->isoCode;
    }
}
