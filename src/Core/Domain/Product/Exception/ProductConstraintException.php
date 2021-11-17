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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Exception;

/**
 * Is thrown when product constraints are violated
 */
class ProductConstraintException extends ProductException
{
    /**
     * Code is used when invalid id is supplied.
     */
    public const INVALID_ID = 10;

    /**
     * When invalid product type is supplied.
     */
    public const INVALID_PRODUCT_TYPE = 20;

    /**
     * When invalid product name in one or another language is supplied
     */
    public const INVALID_NAME = 30;

    /**
     * When invalid product condition is supplied
     */
    public const INVALID_CONDITION = 40;

    /**
     * When invalid product description is supplied
     */
    public const INVALID_DESCRIPTION = 50;

    /**
     * When invalid product short description is supplied
     */
    public const INVALID_SHORT_DESCRIPTION = 60;

    /**
     * When invalid product price is supplied
     */
    public const INVALID_PRICE = 70;

    /**
     * When invalid product unity value is supplied
     */
    public const INVALID_UNITY = 80;

    /**
     * When invalid product ecotax is supplied
     */
    public const INVALID_ECOTAX = 90;

    /**
     * When invalid product unit price is supplied
     */
    public const INVALID_UNIT_PRICE = 100;

    /**
     * When invalid product wholesale_price is supplied
     */
    public const INVALID_WHOLESALE_PRICE = 110;

    /**
     * When product visibility value is invalid
     */
    public const INVALID_VISIBILITY = 120;

    /**
     * When product Ean13 code value is invalid
     */
    public const INVALID_EAN_13 = 130;

    /**
     * When product ISBN code value is invalid
     */
    public const INVALID_ISBN = 140;

    /**
     * When product mpn code value is invalid
     */
    public const INVALID_MPN = 150;

    /**
     * When product upc code value is invalid
     */
    public const INVALID_UPC = 160;

    /**
     * When product reference value is invalid
     */
    public const INVALID_REFERENCE = 170;

    /**
     * When product tag value is invalid
     */
    public const INVALID_TAG = 180;

    /**
     * When product additional time notes type is invalid
     */
    public const INVALID_ADDITIONAL_TIME_NOTES_TYPE = 190;

    /**
     * When product width is invalid
     */
    public const INVALID_WIDTH = 200;

    /**
     * When product height is invalid
     */
    public const INVALID_HEIGHT = 210;

    /**
     * When product depth is invalid
     */
    public const INVALID_DEPTH = 220;

    /**
     * When product weight is invalid
     */
    public const INVALID_WEIGHT = 230;

    /**
     * When product additional shipping cost is invalid
     */
    public const INVALID_ADDITIONAL_SHIPPING_COST = 240;

    /**
     * When product delivery time in stock notes are invalid
     */
    public const INVALID_DELIVERY_TIME_IN_STOCK_NOTES = 250;

    /**
     * When product delivery time out of stock notes are invalid
     */
    public const INVALID_DELIVERY_TIME_OUT_OF_STOCK_NOTES = 260;

    /**
     * When product redirect type is invalid
     */
    public const INVALID_REDIRECT_TYPE = 270;

    /**
     * When product redirect target
     */
    public const INVALID_REDIRECT_TARGET = 280;

    /**
     * When product meta description is invalid
     */
    public const INVALID_META_DESCRIPTION = 290;

    /**
     * When product meta title is invalid
     */
    public const INVALID_META_TITLE = 300;

    /**
     * When product link rewrite is invalid
     */
    public const INVALID_LINK_REWRITE = 310;

    /**
     * When product minimal quantity is invalid
     */
    public const INVALID_MINIMAL_QUANTITY = 320;

    /**
     * When product available later labels are invalid
     */
    public const INVALID_AVAILABLE_LATER = 330;

    /**
     * When product available now labels are is invalid
     */
    public const INVALID_AVAILABLE_NOW = 340;

    /**
     * When product available date is invalid
     */
    public const INVALID_AVAILABLE_DATE = 350;

    /**
     * When product low stock alert is invalid
     */
    public const INVALID_LOW_STOCK_ALERT = 360;

    /**
     * When product low stock threshold is invalid
     */
    public const INVALID_LOW_STOCK_THRESHOLD = 370;

    /**
     * When available for order option is invalid
     */
    public const INVALID_AVAILABLE_FOR_ORDER = 380;

    /**
     * When online only option is invalid
     */
    public const INVALID_ONLINE_ONLY = 390;

    /**
     * When show price option is invalid
     */
    public const INVALID_SHOW_PRICE = 400;

    /**
     * When manufacturer id option is invalid
     */
    public const INVALID_MANUFACTURER_ID = 410;

    /**
     * When customizability is invalid
     */
    public const INVALID_CUSTOMIZABILITY = 420;

    /**
     * When customizable text fields count is invalid
     */
    public const INVALID_TEXT_FIELDS_COUNT = 430;

    /**
     * When uploaded files count for customization is invalid
     */
    public const INVALID_UPLOADABLE_FILES_COUNT = 440;

    /**
     * When additional delivery time notes type is invalid
     */
    public const INVALID_ADDITIONAL_DELIVERY_TIME_NOTES_TYPE = 450;

    /**
     * When product status is invalid
     */
    public const INVALID_STATUS = 460;

    /**
     * When show_condition is invalid
     */
    public const INVALID_SHOW_CONDITION = 470;
}
