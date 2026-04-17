<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Currency\Query;

use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\AlphaIsoCode;

/**
 * Get reference currency data, which are data from the unicode CLDR database, thus
 * only official currencies have one. The result is exposed with a ReferenceCurrency
 * object, and if the currency doesn't exist a CurrencyNotFoundException is thrown.
 */
class GetReferenceCurrency
{
    /**
     * @var AlphaIsoCode
     */
    private $isoCode;

    /**
     * @param string $isoCode
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
