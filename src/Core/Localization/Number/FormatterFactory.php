<?php

/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Localization\Number;

use PrestaShop\PrestaShop\Core\Localization\Specification\NumberInterface as NumberSpecificationInterface;

class FormatterFactory
{
    /**
     * Build an instance of Number Formatter
     *
     * @param NumberSpecificationInterface $specification
     *  Number specification (can be raw number spec, but also price or percentage spec)
     *
     * @param string $roundingMode
     *  Rounding mode that must be used by formatter
     *  Cf. PrestaShop\Decimal\Operation\Rounding::ROUND_* values
     *
     * @param string $numberingSystem
     *  Numbering system to use when formatting the number
     *  @see http://cldr.unicode.org/translation/numbering-systems
     *
     * @return Formatter
     *  A number formatter
     */
    public function buildFormatter(
        NumberSpecificationInterface $specification,
        $roundingMode,
        $numberingSystem
    ) {
        return new Formatter($specification, $roundingMode, $numberingSystem);
    }
}
