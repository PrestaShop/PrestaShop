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

class LocaleData
{
    /**
     * The locale code for this data (either language code or EITF tag)
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
     * Override properties with data from another CLDRLocaleData object
     *
     * @param LocaleData $newData
     *
     * @return $this
     */
    public function merge(LocaleData $newData)
    {
        $simpleProps         = array(
            'localeCode',
            'minimumGroupingDigits',
        );
        $objectProps         = array();
        $arrayProps          = array(
            'numberingSystems',
            'decimalPatterns',
            'percentPatterns',
            'currencyPatterns',
        );
        $arrayOfObjectsProps = array(
            'numberSymbols',
        );
        // Simple properties -> simple override
        foreach ($simpleProps as $propName) {
            if (!empty($newData->$propName)) {
                $this->$propName = $newData->$propName;
            }
        }

        // Object properties -> override properties via internal merge() method
        foreach ($objectProps as $propName) {
            if (!empty($newData->$propName)) {
                if (!isset($this->$propName)) {
                    $this->$propName = $newData->$propName;
                    continue;
                }

                $this->$propName->merge($newData->$propName);
            }
        }

        // Array of scalar types -> simple array replace
        foreach ($arrayProps as $propName) {
            if (!empty($newData->$propName)) {
                $this->$propName = array_replace_recursive($this->$propName, $newData->$propName);
            }
        }

        // Array of objects properties -> for each object, override properties via internal merge() method
        foreach ($arrayOfObjectsProps as $propName) {
            if (!empty($newData->$propName)) {
                foreach ($newData->$propName as $propKey => $propObject) {
                    if (!isset($this->$propName[$propKey])) {
                        $this->$propName[$propKey] = $propObject;
                        continue;
                    }

                    $this->$propName[$propKey]->merge($propObject);
                }
            }
        }

        return $this;
    }
}
