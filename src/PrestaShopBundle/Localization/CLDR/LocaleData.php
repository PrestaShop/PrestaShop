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

namespace PrestaShopBundle\Localization\CLDR;

use PrestaShopBundle\Localization\Exception\Exception;

class LocaleData
{
    /**
     * The locale id when it exists in database
     *
     * @var int
     */
    public $id;

    /**
     * The locale code for this data (either language code or IETF tag)
     *
     * @var string
     */
    public $localeCode;

    /**
     * List of available numbering systems
     * Array of strings (codes)
     *
     * @var array
     */
    public $numberingSystems;

    /**
     * Default numbering system
     *
     * @var string
     */
    public $defaultNumberingSystem;

    /**
     * Used to suppress groupings below a certain value
     *
     * 1 -> grouping starts at 4 figures integers (1,000 and more)
     * 2 -> grouping starts at 5 figures integers (10,000 and more)
     *
     * @var int
     */
    public $minimumGroupingDigits;

    /**
     * Collection of all available symbols list (by numbering system)
     * Array of CLDRNumberSymbolList objects
     *
     * @var array
     */
    public $numberSymbols;

    /**
     * Collection of all available decimal patterns (by numbering system)
     * Array of strings (patterns)
     *
     * @var array
     */
    public $decimalPatterns;

    /**
     * Collection of all available percent patterns (by numbering system)
     * Array of strings (patterns)
     *
     * @var array
     */
    public $percentPatterns;

    /**
     * Collection of all available currency patterns (by numbering system)
     * Array of strings (patterns)
     *
     * @var array
     */
    public $currencyPatterns;

    /**
     * Parent locale code.
     *
     * This code can be an IETF tag, an ISO 639-1 language code or a specific code used in CLDR to identify special
     * "parent" locale data files (eg: en_CH parent data will be found in en_150.xml instead of regular en.xml).
     * Parent locale is used when some data is not found in current locale.
     *
     * @var string
     */
    public $parentLocale;

    /**
     * Fills missing properties with "parent" data
     *
     * @param LocaleData $parentData
     *
     * @return $this
     */
    public function fill(LocaleData $parentData)
    {
        $this->fillScalarProperties($parentData)
            ->fillObjectProperties($parentData)
            ->fillArrayOfScalarProperties($parentData)
            ->fillArrayOfObjectProperties($parentData);

        // 'parentLocale' should always be overridden by parent locale's 'parentLocale' (for recursion purpose)
        $this->parentLocale = $parentData->parentLocale;

        return $this;
    }

    /**
     * Fill empty scalar properties with default data.
     *
     * @param LocaleData $defaultData
     *
     * @return $this
     */
    protected function fillScalarProperties(LocaleData $defaultData)
    {
        $propertyNames = array(
            'digits',
            'localeCode',
            'minimumGroupingDigits',
            'parentLocale',
        );
        foreach ($propertyNames as $prop) {
            if (isset($defaultData->$prop) && !isset($this->$prop)) {
                $this->$prop = $defaultData->$prop;
            }
        }

        return $this;
    }

    /**
     * Fill empty or incomplete object properties with default data.
     * Filling will be delegated to the concerned object's fill() method.
     *
     * @param LocaleData $defaultData
     *
     * @return $this
     * @throws Exception
     */
    protected function fillObjectProperties(LocaleData $defaultData)
    {
        $propertyNames = array(); // No object properties in LocaleData yet.
        foreach ($propertyNames as $prop) {
            if (isset($defaultData->$prop)) {
                if (!isset($this->$prop)) {
                    $this->$prop = $defaultData->$prop;
                    continue;
                }

                if (!method_exists($this->$prop, 'fill')) {
                    throw new Exception("$prop object needs a fill() method.");
                }

                $this->$prop->fill($defaultData->$prop);
            }
        }

        return $this;
    }

    /**
     * Fill empty or incomplete array properties containing scalar values.
     *
     * @param LocaleData $defaultData
     *
     * @return $this
     */
    protected function fillArrayOfScalarProperties(LocaleData $defaultData)
    {
        $propertyNames = array(
            'currencyPatterns',
            'decimalPatterns',
            'numberingSystems',
            'percentPatterns',
        );
        foreach ($propertyNames as $prop) {
            if (!empty($defaultData->$prop)) {
                foreach ($defaultData->$prop as $key => $defaultValue) {
                    $thisProp =& $this->$prop;
                    if (!isset($thisProp[$key])) {
                        $thisProp[$key] = $defaultValue;
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Fill empty or incomplete array properties containing objects.
     * Filling will be delegated to the concerned objects' fill() methods.
     *
     * @param LocaleData $defaultData
     *
     * @return $this
     * @throws Exception
     */
    protected function fillArrayOfObjectProperties(LocaleData $defaultData)
    {
        $propertyNames = array(
            'numberSymbols',
        );
        foreach ($propertyNames as $prop) {
            if (!empty($defaultData->$prop)) {
                foreach ($defaultData->$prop as $key => $defaultObject) {
                    $thisProp =& $this->$prop;
                    if (!isset($thisProp[$key])) {
                        $thisProp[$key] = $defaultObject;
                        continue;
                    }

                    if (!method_exists($thisProp[$key], 'fill')) {
                        throw new Exception("$key object of $prop property needs a fill() method.");
                    }

                    $thisProp[$key]->fill($defaultObject);
                }
            }
        }

        return $this;
    }
}
