<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\PDF;

use Smarty;
use SmartyLazyRegister;

/**
 * Get smarty that is ready for PDF generation.
 */
class SmartyFactory
{
    private Smarty $smarty;

    public function __construct(Smarty $smarty)
    {
        $this->smarty = $smarty;
    }

    public function getSmarty(): Smarty
    {
        /*
        * We need a Smarty instance that does NOT escape HTML.
        * Since in BO Smarty does not autoescape
        * and in FO Smarty does autoescape, we use
        * a new Smarty of which we're sure it does not escape
        * the HTML.
        */
        $smarty = clone $this->smarty;
        $smarty->escape_html = false;

        /* We need to get the old instance of the LazyRegister
         * because some of the functions are already defined
         * and we need to check in the old one first
         */
        $originalLazyRegister = SmartyLazyRegister::getInstance($this->smarty);

        /* For PDF we restore some functions from Smarty
         * they've been removed in PrestaShop 1.7 so
         * new themes don't use them. Although PDF haven't been
         * reworked so every PDF controller must extend this class.
         */
        smartyRegisterFunction($smarty, 'function', 'convertPrice', ['Product', 'convertPrice'], true, $originalLazyRegister);
        smartyRegisterFunction($smarty, 'function', 'convertPriceWithCurrency', ['Product', 'convertPriceWithCurrency'], true, $originalLazyRegister);
        smartyRegisterFunction($smarty, 'function', 'displayWtPrice', ['Product', 'displayWtPrice'], true, $originalLazyRegister);
        smartyRegisterFunction($smarty, 'function', 'displayWtPriceWithCurrency', ['Product', 'displayWtPriceWithCurrency'], true, $originalLazyRegister);
        smartyRegisterFunction($smarty, 'function', 'displayPrice', ['Tools', 'displayPriceSmarty'], true, $originalLazyRegister);
        smartyRegisterFunction($smarty, 'modifier', 'convertAndFormatPrice', ['Product', 'convertAndFormatPrice'], true, $originalLazyRegister); // used twice
        smartyRegisterFunction($smarty, 'function', 'displayAddressDetail', ['AddressFormat', 'generateAddressSmarty'], true, $originalLazyRegister);
        smartyRegisterFunction($smarty, 'function', 'getWidthSize', ['Image', 'getWidth'], true, $originalLazyRegister);
        smartyRegisterFunction($smarty, 'function', 'getHeightSize', ['Image', 'getHeight'], true, $originalLazyRegister);

        return $smarty;
    }
}
