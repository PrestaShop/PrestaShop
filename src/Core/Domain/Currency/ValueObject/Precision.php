<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;

class Precision
{
    public const DEFAULT_PRECISION = 2;

    /**
     * Every price column in the database is decimal(20,6), so digits beyond the sixth are always zero.
     * For reference, the highest fraction digits CLDR declares for any currency is 4.
     */
    public const MAX_PRECISION = 6;

    /**
     * @var int
     */
    private $precision;

    /**
     * @param int $precision
     *
     * @throws CurrencyConstraintException
     */
    public function __construct(int $precision)
    {
        $this->assertIsPositiveInteger($precision);
        $this->assertIsNotAboveMaximum($precision);
        $this->precision = $precision;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->precision;
    }

    /**
     * @param int $precision
     *
     * @throws CurrencyConstraintException
     */
    private function assertIsPositiveInteger(int $precision)
    {
        if ((int) $precision < 0) {
            throw new CurrencyConstraintException(sprintf('Given precision "%s" is not valid. It must be a positive integer', var_export($precision, true)), CurrencyConstraintException::INVALID_PRECISION);
        }
    }

    /**
     * @param int $precision
     *
     * @throws CurrencyConstraintException
     */
    private function assertIsNotAboveMaximum(int $precision)
    {
        if ($precision > self::MAX_PRECISION) {
            throw new CurrencyConstraintException(sprintf('Given precision "%s" is not valid. It must not be greater than %d', var_export($precision, true), self::MAX_PRECISION), CurrencyConstraintException::INVALID_PRECISION);
        }
    }
}
