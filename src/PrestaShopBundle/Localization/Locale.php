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

use InvalidArgumentException;
use PrestaShopBundle\Localization\CLDR\LocaleData;
use PrestaShopBundle\Localization\CLDR\NumberSymbolList;
use PrestaShopBundle\Localization\Formatter\Number as NumberFormatter;
use PrestaShopBundle\Localization\Formatter\NumberFactory as NumberFormatterFactory;
use PrestaShopBundle\Localization\Repository as LocaleRepository;

class Locale
{
    protected $localeCode;
    protected $repository;
    protected $numberFormatter;
    protected $numberFormatterFactory;
    protected $specification;
    protected $id;

    public function __construct(
        $localeCode,
        NumberFormatterFactory $numberFormatterFactory,
        LocaleData $specification,
        LocaleRepository $repository
    ) {
        $this->localeCode             = $this->convertLocaleAsIETF($localeCode);
        $this->numberFormatterFactory = $numberFormatterFactory;
        $this->specification          = $specification;
        $this->repository             = $repository;
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

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLocaleCode()
    {
        return $this->localeCode;
    }

    public function formatNumber($number)
    {
        $number = (string)$number;

        return $this->getNumberFormatter()->format($number);
    }

    public function formatCurrency($number, $currencyId)
    {
        $number   = (string)$number;
        $currency = $this->getRepository()->getCurrencyCollection()->getCurrency($currencyId);

        return $this->getNumberFormatter()->formatCurrency($number, $currency);
    }

    /**
     * @return NumberFormatter
     */
    protected function getNumberFormatter()
    {
        if (!isset($this->numberFormatter)) {
            $this->numberFormatter = $this->numberFormatterFactory->build($this);
        }

        return $this->numberFormatter;
    }

    /**
     * @return LocaleRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    public function getDecimalPattern()
    {
        $spec = $this->getSpecification();

        return $spec->decimalPatterns[$this->getUsedNumberingSystem()];
    }

    public function getPercentPattern()
    {
        $spec = $this->getSpecification();

        return $spec->percentPatterns[$this->getUsedNumberingSystem()];
    }

    public function getCurrencyPattern()
    {
        $spec = $this->getSpecification();

        return $spec->currencyPatterns[$this->getUsedNumberingSystem()];
    }

    /**
     * @return LocaleData
     */
    protected function getSpecification()
    {
        return $this->specification;
    }

    public function getNumberSymbols()
    {
        $spec = $this->getSpecification();
        /** @var NumberSymbolList $specSymbols */
        if (isset($spec->numberSymbols['latn'])) {
            // TODO : get rid of this case when numbering system choice is implemented.
            $specSymbols = $spec->numberSymbols['latn'];
        } else {
            $specSymbols = $spec->numberSymbols[$this->getUsedNumberingSystem()];
        }

        return array(
            '.' => $specSymbols->decimal,
            ',' => $specSymbols->group,
            '+' => $specSymbols->plusSign,
            '-' => $specSymbols->minusSign,
            '%' => $specSymbols->percentSign,
        );
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
    public function getUsedNumberingSystem()
    {
        $availableNumberingSystems = $this->getSpecification()->numberingSystems;

        if (!$availableNumberingSystems) {
            return $this->getDefaultNumberingSystem();
        }

        // TODO : get rid of this case when numbering system choice is implemented.
        if (in_array('latn', $availableNumberingSystems)) {
            return 'latn';
        }

        foreach (array('traditional', 'native', 'finance') as $system) {
            if (isset($availableNumberingSystems[$system])) {
                return $availableNumberingSystems[$system];
            }
        }

        return $this->getDefaultNumberingSystem();
    }

    /**
     * Get the numbering system defined as default for this locale
     *
     * @return string The default numbering system
     */
    protected function getDefaultNumberingSystem()
    {
        $spec = $this->getSpecification();
        if (isset($spec->defaultNumberingSystem)) {
            return $spec->defaultNumberingSystem;
        }

        return $spec->numberingSystems[0];
    }
}
