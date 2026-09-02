<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;

/**
 * Class ExchangeRate
 */
class ExchangeRate
{
    public const DEFAULT_RATE = 1.0;

    /**
     * Get the default exchange rate as a DecimalNumber
     *
     * @return DecimalNumber
     */
    public static function getDefaultExchangeRate(): DecimalNumber
    {
        return new DecimalNumber((string) self::DEFAULT_RATE);
    }

    /**
     * @var float
     */
    private $exchangeRate;

    /**
     * @param float $exchangeRate
     *
     * @throws CurrencyConstraintException
     */
    public function __construct($exchangeRate)
    {
        $this->assertIsNumberAndMoreThanZero($exchangeRate);
        $this->exchangeRate = $exchangeRate;
    }

    /**
     * @return float
     */
    public function getValue()
    {
        return $this->exchangeRate;
    }

    /**
     * @param mixed $exchangeRate
     *
     * @throws CurrencyConstraintException
     */
    private function assertIsNumberAndMoreThanZero($exchangeRate)
    {
        $isIntegerOrFloat = is_int($exchangeRate) || is_float($exchangeRate);

        if (!$isIntegerOrFloat || 0 >= $exchangeRate) {
            throw new CurrencyConstraintException(sprintf('Given exchange rate %s is not valid. It must be more than 0', var_export($exchangeRate, true)), CurrencyConstraintException::INVALID_EXCHANGE_RATE);
        }
    }
}
