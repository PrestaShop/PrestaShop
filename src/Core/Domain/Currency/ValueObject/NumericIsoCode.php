<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;

/**
 * Class NumericIsoCode
 */
class NumericIsoCode
{
    /**
     * @var string Numeric ISO Code validation pattern
     */
    public const PATTERN = '/^[0-9]{3}$/';

    /**
     * @var string
     */
    private $numericIsoCode;

    /**
     * @param string $numericIsoCode
     *
     * @throws CurrencyConstraintException
     */
    public function __construct($numericIsoCode)
    {
        $this->assertIsValidNumericIsoCode($numericIsoCode);
        $this->numericIsoCode = $numericIsoCode;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->numericIsoCode;
    }

    /**
     * @param string $numericIsoCode
     *
     * @throws CurrencyConstraintException
     */
    private function assertIsValidNumericIsoCode($numericIsoCode)
    {
        if (!is_string($numericIsoCode) || !preg_match(self::PATTERN, $numericIsoCode)) {
            throw new CurrencyConstraintException(sprintf('Given numeric iso code "%s" is not valid. It must be a string composed of three digits', var_export($numericIsoCode, true)), CurrencyConstraintException::INVALID_NUMERIC_ISO_CODE);
        }
    }
}
