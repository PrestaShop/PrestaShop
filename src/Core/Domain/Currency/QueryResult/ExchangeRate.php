<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Currency\QueryResult;

use PrestaShop\Decimal\DecimalNumber;

/**
 * Class ExchangeRate
 */
class ExchangeRate
{
    /**
     * @var DecimalNumber
     */
    private $exchangeRate;

    /**
     * @param DecimalNumber $exchangeRate
     */
    public function __construct(DecimalNumber $exchangeRate)
    {
        $this->exchangeRate = $exchangeRate;
    }

    /**
     * @return DecimalNumber
     */
    public function getValue(): DecimalNumber
    {
        return $this->exchangeRate;
    }
}
