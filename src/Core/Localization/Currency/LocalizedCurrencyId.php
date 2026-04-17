<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Localization\Currency;

/**
 * Value-object representing an identifier for a currency, "translated" in a given locale (language + region).
 */
class LocalizedCurrencyId
{
    /**
     * ISO 4217 code of the currency.
     *
     * @var string
     */
    private $currencyCode;

    /**
     * CurrencyData's data is translated in this locale.
     * IETF tag (e.g.: fr-FR, en-US...).
     *
     * @var string
     */
    private $localeCode;

    /**
     * @param string $currencyCode ISO 4217 currency code
     * @param string $localeCode IETF tag (e.g.: fr-FR, en-US...)
     */
    public function __construct($currencyCode, $localeCode)
    {
        $this->currencyCode = $currencyCode;
        $this->localeCode = $localeCode;
    }

    public function __toString()
    {
        return $this->currencyCode . '-' . $this->localeCode;
    }

    /**
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    /**
     * @return string
     */
    public function getLocaleCode()
    {
        return $this->localeCode;
    }
}
