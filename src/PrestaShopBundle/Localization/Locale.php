<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Localization;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShopBundle\Currency\Manager as CurrencyManager;
use PrestaShopBundle\Localization\CLDR\LocaleData;
use PrestaShopBundle\Localization\Exception\InvalidArgumentException;
use PrestaShopBundle\Localization\Formatter\Number as NumberFormatter;
use PrestaShopBundle\Localization\Formatter\NumberFactory as NumberFormatterFactory;

/**
 * Class Locale
 *
 * This class is the representation of a locale's properties translated for a given locale.
 *
 * @package PrestaShopBundle\Localization
 */
class Locale
{
    /**
     * Finance numbering system identifier. This identifier is used in CLDR XML files.
     */
    const NUMBERING_SYSTEM_FINANCE = 'finance';

    /**
     * Native numbering system identifier. This identifier is used in CLDR XML files.
     */
    const NUMBERING_SYSTEM_NATIVE = 'native';

    /**
     * Traditional numbering system identifier. This identifier is used in CLDR XML files.
     */
    const NUMBERING_SYSTEM_TRADITIONAL = 'traditional';

    /**
     * Latin script identifier. This identifier is used in CLDR XML files.
     * We use this value here when we need to force usage of this specific script over the others.
     * This need may disappear in the future.
     */
    const SCRIPT_LATIN = 'latn';

    /**
     * The locale code (IETF notation)
     *
     * @var string
     */
    protected $localeCode;

    /**
     * The number formatter used by this locale object to format numbers (decimal, percentage, price).
     *
     * @var NumberFormatter
     */
    protected $numberFormatter;

    /**
     * The factory used to get the number formatter.
     *
     * @var NumberFormatterFactory
     */
    protected $numberFormatterFactory;

    /**
     * Data bag containing all local specific values (depending on the language and localization).
     *
     * @var LocaleData
     */
    protected $specification;

    /**
     * The locale id
     *
     * @var int
     */
    protected $id;

    /**
     * The saved currency manager.
     * Used to get currencies when needed.
     *
     * @var CurrencyManager
     */
    protected $currencyManager;

    /**
     * Configured round mode in PrestaShop
     *
     * @var int
     */
    protected $roundMode;

    public function __construct(
        $localeCode,
        NumberFormatterFactory $numberFormatterFactory,
        LocaleData $specification,
        CurrencyManager $currencyManager,
        Configuration $config
    ) {
        $this->localeCode             = $this->convertLocaleAsIETF($localeCode);
        $this->numberFormatterFactory = $numberFormatterFactory;
        $this->specification          = $specification;
        $this->currencyManager        = $currencyManager;
        $this->roundMode              = (int)$config->get('PS_PRICE_ROUND_MODE');
    }

    /**
     * Get this locale id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get this locale code (IETF tag)
     *
     * @return string
     */
    public function getLocaleCode()
    {
        return $this->localeCode;
    }

    /**
     * Format a decimal number according to locale's specification
     *
     * @param $number
     *   Number to be formatted
     *
     * @return string
     *   Formatted number
     */
    public function formatNumber($number)
    {
        return $this->getNumberFormatter()->format((string)$number);
    }

    /**
     * Format a currency (price) number according to locale's and currency's specifications.
     *
     * @param $number
     *   number to be formatted
     *
     * @param $currencyCode
     *   Currency's (ISO) code
     *   eg : USD
     *
     * @return string
     */
    public function formatCurrency($number, $currencyCode)
    {
        $currency = $this->getCurrencyManager()->getCurrencyByIsoCode($currencyCode);

        return $this->getNumberFormatter()->formatCurrency((string)$number, $currency);
    }

    /**
     * Get pattern to format a decimal number.
     * This pattern may contain both positive and negative version, separated by ";".
     * If there is no negative version, default is positive version preceded by "-" character.
     *
     * eg : '#,##0.###;-#,##0.###'
     *
     * @return string
     */
    public function getDecimalPattern()
    {
        $spec = $this->getSpecification();

        return $spec->decimalPatterns[$this->getNumberingSystem()];
    }

    /**
     * Get pattern to format a percent number.
     * This pattern may contain both positive and negative version, separated by ";".
     * If there is no negative version, default is positive version preceded by "-" character.
     *
     * eg : '#,##0.### %;-#,##0.### %'
     *
     * @return string
     */
    public function getPercentPattern()
    {
        $spec = $this->getSpecification();

        return $spec->percentPatterns[$this->getNumberingSystem()];
    }

    /**
     * Get pattern to format a currency (price) number.
     * This pattern may contain both positive and negative version, separated by ";".
     * If there is no negative version, default is positive version preceded by "-" character.
     *
     * eg : '#,##0.00 ¤;-#,##0.00 ¤'
     *
     * @return string
     */
    public function getCurrencyPattern()
    {
        $spec = $this->getSpecification();

        return $spec->currencyPatterns[$this->getNumberingSystem()];
    }

    /**
     * Get the currency manager
     *
     * @return CurrencyManager
     */
    public function getCurrencyManager()
    {
        return $this->currencyManager;
    }

    /**
     * Get the configured rounding mode
     *
     * @return int
     */
    public function getRoundMode()
    {
        return $this->roundMode;
    }

    /**
     * Get this locale's specification
     *
     * @return LocaleData
     */
    public function getSpecification()
    {
        return $this->specification;
    }

    /**
     * Get the number formatter
     *
     * @return NumberFormatter
     */
    public function getNumberFormatter()
    {
        if (!isset($this->numberFormatter)) {
            $this->numberFormatter = $this->numberFormatterFactory->build($this);
        }

        return $this->numberFormatter;
    }
    /**
     * Get the numbering system to use.
     *
     * It should be the default numbering system declared by CLDR. But for now, and in order to delay the work on
     * different available numbering systems (and specifically on non-latin number digits) for a given locale, we will
     * stick to latn whenever possible, and use the default as a fallback.
     *
     * TODO : with "numbering system choice" feature => should return default numbering system as a fallback only.
     *
     * @return string The numbering system to use
     */
    public function getNumberingSystem()
    {
        $availableNumberingSystems = $this->getSpecification()->numberingSystems;

        // TODO : get rid of this when numbering system choice is implemented.
        if ($availableNumberingSystems && in_array(self::SCRIPT_LATIN, $availableNumberingSystems)) {
            return self::SCRIPT_LATIN;
        }

        $system = $this->getSpecification()->getDefaultNumberingSystem();

        if (!$system) {
            $numSystems = array(
                self::NUMBERING_SYSTEM_NATIVE,
                self::NUMBERING_SYSTEM_TRADITIONAL,
                self::NUMBERING_SYSTEM_FINANCE,
            );

            foreach ($numSystems as $thisSystem) {
                if (isset($availableNumberingSystems[$thisSystem])) {
                    $system = $availableNumberingSystems[$thisSystem];
                    break;
                }
            }
        }

        return $system;
    }

    /**
     * Converts any locale code as IETF standard tag
     *
     * Example : en_us => en-US
     * If passed $localName's structure cannot be recognized, it won't be converted and an InvalidArgumentException will
     * be thrown.
     *
     * @param string $localeCode
     *
     * @return string the locale name converted as IETF locale tag
     *
     * @throws InvalidArgumentException
     */
    protected function convertLocaleAsIETF($localeCode)
    {
        $matches = array();
        if (preg_match('#^([a-zA-Z]{2})[-_]([a-zA-Z]{2})$#', $localeCode, $matches)) {
            return $matches[1] . '-' . strtoupper($matches[2]);
        }

        throw new InvalidArgumentException('Unrecognized locale code (' . $localeCode . ')');
    }
}
