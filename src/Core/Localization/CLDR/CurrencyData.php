<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Localization\CLDR;

/**
 * The CurrencyData class is the exact representation of Currency's data structure inside CLDR xml data files.
 *
 * This class is only used internally, it is mutable and overridable until fully built. It can then be used as
 * an intermediary data bag to build a real CLDR Currency (immutable) object.
 */
class CurrencyData
{
    /**
     * Alphabetic ISO 4217 currency code.
     *
     * @var string
     */
    protected $isoCode;

    /**
     * Numeric ISO 4217 currency code.
     *
     * @var string
     */
    protected $numericIsoCode;

    /**
     * Number of decimal digits to display for a price in this currency.
     *
     * @var int
     */
    protected $decimalDigits;

    /**
     * Possible names depending on count context.
     *
     * e.g.: "Used currency is dollar" (default), "I need one dollar" (one), "I need five dollars" (other)
     * [
     *     'default' => 'dollar',
     *     'one'     => 'dollar',
     *     'other'   => 'dollars',
     * ]
     *
     * @var string[]|null
     */
    protected $displayNames;

    /**
     * Possible symbols (PrestaShop is using narrow).
     *
     * e.g.:
     * [
     *     'default' => 'US$',
     *     'narrow' => '$',
     * ]
     *
     * @var string[]|null
     */
    protected $symbols;

    /**
     * Is the currency used somewhere, or was it deactivated in all territories
     *
     * @var bool|null
     */
    protected $active;

    /**
     * Override this object's data with another CurrencyData object.
     *
     * @param CurrencyData $currencyData
     *                                   Currency data to use for the override
     *
     * @return $this
     *               Fluent interface
     */
    public function overrideWith(CurrencyData $currencyData)
    {
        if (null !== $currencyData->getIsoCode()) {
            $this->setIsoCode($currencyData->getIsoCode());
        }

        if (null !== $currencyData->getNumericIsoCode()) {
            $this->setNumericIsoCode($currencyData->getNumericIsoCode());
        }

        if (null !== $currencyData->isActive()) {
            $this->setActive($currencyData->isActive());
        }

        if (null !== $currencyData->getDecimalDigits()) {
            $this->setDecimalDigits($currencyData->getDecimalDigits());
        }

        if (null !== $currencyData->getDisplayNames()) {
            $this->displayNames = array_merge($this->displayNames ?? [], $currencyData->getDisplayNames());
        }

        if (null !== $currencyData->getSymbols()) {
            $this->symbols = array_merge($this->symbols ?? [], $currencyData->getSymbols());
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getIsoCode()
    {
        return $this->isoCode;
    }

    /**
     * @param string $isoCode
     *
     * @return CurrencyData
     */
    public function setIsoCode($isoCode)
    {
        $this->isoCode = $isoCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getNumericIsoCode()
    {
        return $this->numericIsoCode;
    }

    /**
     * @param string $numericIsoCode
     *
     * @return CurrencyData
     */
    public function setNumericIsoCode($numericIsoCode)
    {
        $this->numericIsoCode = $numericIsoCode;

        return $this;
    }

    /**
     * @return int
     */
    public function getDecimalDigits()
    {
        return $this->decimalDigits;
    }

    /**
     * @param int $decimalDigits
     *
     * @return CurrencyData
     */
    public function setDecimalDigits($decimalDigits)
    {
        $this->decimalDigits = $decimalDigits;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getDisplayNames()
    {
        return $this->displayNames;
    }

    /**
     * @param string[] $displayNames
     *
     * @return CurrencyData
     */
    public function setDisplayNames($displayNames)
    {
        $this->displayNames = $displayNames;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getSymbols()
    {
        return $this->symbols;
    }

    /**
     * @param string[] $symbols
     *
     * @return CurrencyData
     */
    public function setSymbols($symbols)
    {
        $this->symbols = $symbols;

        return $this;
    }

    /**
     * is currency still active in some territory
     *
     * @return bool|null
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive($active)
    {
        $this->active = (bool) $active;
    }
}
