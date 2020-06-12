<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Exception;

/**
 * Is thrown when product constraints are violated
 */
class ProductConstraintException extends ProductException
{
    /**
     * Code is used when invalid id is supplied.
     */
    const INVALID_ID = 10;

    /**
     * When invalid product type is supplied.
     */
    const INVALID_PRODUCT_TYPE = 20;

    /**
     * When invalid product name in one or another language is supplied
     */
    const INVALID_NAME = 30;

    /**
     * When invalid product condition is supplied
     */
    const INVALID_CONDITION = 40;

    /**
     * When invalid product description is supplied
     */
    const INVALID_DESCRIPTION = 50;

    /**
     * When invalid product short description is supplied
     */
    const INVALID_SHORT_DESCRIPTION = 60;

    /**
     * When invalid product price is supplied
     */
    const INVALID_PRICE = 70;

    /**
     * When invalid product ecotax is supplied
     */
    const INVALID_ECOTAX = 80;

    /**
     * When invalid product tax rules group id is supplied
     */
    const INVALID_TAX_RULES_GROUP_ID = 90;

    /**
     * When invalid product unit price is supplied
     */
    const INVALID_UNIT_PRICE = 100;

    /**
     * When invalid product wholesale_price is supplied
     */
    const INVALID_WHOLESALE_PRICE = 110;

    /**
     * When product visibility value is invalid
     */
    const INVALID_VISIBILITY = 120;

    /**
     * When product Ean13 code value is invalid
     */
    const INVALID_EAN_13 = 130;

    /**
     * When product ISBN code value is invalid
     */
    const INVALID_ISBN = 140;

    /**
     * When product mpn code value is invalid
     */
    const INVALID_MPN = 150;

    /**
     * When product upc code value is invalid
     */
    const INVALID_UPC = 160;
}
