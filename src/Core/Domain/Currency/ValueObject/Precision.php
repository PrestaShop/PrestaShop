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
}
