<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Localization;

use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use PrestaShop\PrestaShop\Core\Localization\Number\Formatter as NumberFormatter;
use PrestaShop\PrestaShop\Core\Localization\Specification\NumberCollection;
use PrestaShop\PrestaShop\Core\Localization\Specification\NumberInterface;

/**
 * Locale entity.
 *
 * This is the main CLDR entry point. For example, Locale is used to format numbers, prices, percentages.
 * To build a Locale instance, use the Locale repository.
 */
class Locale implements LocaleInterface
{
    public const NUMBERING_SYSTEM_LATIN = LocaleInterface::NUMBERING_SYSTEM_LATIN;

    /**
     * The locale code (simplified IETF tag syntax)
     * Combination of ISO 639-1 (2-letters language code) and ISO 3166-2 (2-letters region code)
     * eg: fr-FR, en-US.
     *
     * @var string
     */
    protected string $code;

    /**
     * Number formatter.
     * Used to format raw numbers in this locale context.
     *
     * @var NumberFormatter
     */
    protected NumberFormatter $numberFormatter;

    /**
     * Number formatting specification.
     */
    protected NumberInterface $numberSpecification;

    /**
     * Price formatting specifications collection (one spec per currency).
     *
     * @var NumberCollection
     */
    protected $priceSpecifications;

    /**
     * Locale constructor.
     *
     * @param string $localeCode
     *                           The locale code (simplified IETF tag syntax)
     *                           Combination of ISO 639-1 (2-letters language code) and ISO 3166-2 (2-letters region code)
     *                           eg: fr-FR, en-US
     * @param NumberInterface $numberSpecification
     *                                             Number specification used when formatting a number
     * @param NumberCollection $priceSpecifications
     *                                              Collection of Price specifications (one per installed currency)
     * @param NumberFormatter $formatter
     *                                   This number formatter will use stored number / price specs
     */
    public function __construct(
        string $localeCode,
        NumberInterface $numberSpecification,
        NumberCollection $priceSpecifications,
        NumberFormatter $formatter
    ) {
        $this->code = $localeCode;
        $this->numberSpecification = $numberSpecification;
        $this->priceSpecifications = $priceSpecifications;
        $this->numberFormatter = $formatter;
    }

    /**
     * Get this locale's code (simplified IETF tag syntax)
     * Combination of ISO 639-1 (2-letters language code) and ISO 3166-2 (2-letters region code)
     * eg: fr-FR, en-US.
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Format a number according to locale rules.
     *
     * @param int|float|string $number
     *                                 The number to be formatted
     *
     * @return string
     *                The formatted number
     *
     * @throws LocalizationException
     */
    public function formatNumber(int|float|string $number): string
    {
        return $this->numberFormatter->format(
            $number,
            $this->numberSpecification
        );
    }

    /**
     * Format a number as a price.
     *
     * @param int|float|string $number
     *                                 Number to be formatted as a price
     * @param string $currencyCode
     *                             Currency of the price
     *
     * @return string The formatted price
     *
     * @throws LocalizationException
     */
    public function formatPrice(int|float|string $number, string $currencyCode): string
    {
        return $this->numberFormatter->format(
            $number,
            $this->getPriceSpecification($currencyCode)
        );
    }

    /**
     * Get price specification
     *
     * @param string $currencyCode Currency of the price
     *
     * @return NumberInterface
     */
    public function getPriceSpecification(string $currencyCode): NumberInterface
    {
        $currencyCode = (string) $currencyCode;
        $priceSpec = $this->priceSpecifications->get($currencyCode);
        if (null === $priceSpec) {
            throw new LocalizationException('Price specification not found for currency: "' . $currencyCode . '"');
        }

        return $priceSpec;
    }

    /**
     * Get number specification
     *
     * @return NumberInterface
     */
    public function getNumberSpecification(): NumberInterface
    {
        return $this->numberSpecification;
    }
}
