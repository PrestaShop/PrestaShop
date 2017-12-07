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

namespace PrestaShopBundle\Localization\Specification;

/**
 * Class NumberSymbolList
 *
 * Number's symbols data bag. Regroups all symbols used when formatting a number
 * (decimal separator, thousands separator, etc.).*
 */
interface NumberSymbolListInterface
{
    /**
     * @return string
     */
    public function getDecimal();

    /**
     * @return string
     */
    public function getGroup();

    /**
     * @return string
     */
    public function getList();

    /**
     * @return string
     */
    public function getPercentSign();

    /**
     * @return string
     */
    public function getMinusSign();

    /**
     * @return string
     */
    public function getPlusSign();

    /**
     * @return string
     */
    public function getExponential();

    /**
     * @return string
     */
    public function getSuperscriptingExponent();

    /**
     * @return string
     */
    public function getPerMille();

    /**
     * @return string
     */
    public function getInfinity();

    /**
     * @return string
     */
    public function getNan();

    /**
     * Fills missing items of this list with default data
     *
     * @param NumberSymbolListInterface $defaultList
     *   Used to fill missing items
     *
     * @return $this
     *   Fluent interface
     */
    public function hydrate(NumberSymbolListInterface $defaultList);
}
