<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;

/**
 * Class AlphaIsoCode
 */
class AlphaIsoCode
{
    /**
     * @var string ISO Code validation pattern
     */
    public const PATTERN = '/^[a-zA-Z]{2,3}$/';

    /**
     * @var string
     */
    private $isoCode;

    /**
     * @param string $isoCode
     *
     * @throws CurrencyConstraintException
     */
    public function __construct($isoCode)
    {
        $this->assertIsValidIsoCode($isoCode);
        $this->isoCode = $isoCode;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->isoCode;
    }

    /**
     * @param string $isoCode
     *
     * @throws CurrencyConstraintException
     */
    private function assertIsValidIsoCode($isoCode)
    {
        if (!is_string($isoCode) || !preg_match(self::PATTERN, $isoCode)) {
            throw new CurrencyConstraintException(sprintf('Given iso code "%s" is not valid. It did not matched given regex %s', var_export($isoCode, true), self::PATTERN), CurrencyConstraintException::INVALID_ISO_CODE);
        }
    }
}
