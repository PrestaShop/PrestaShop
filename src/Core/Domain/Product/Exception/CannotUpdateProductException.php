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
 * Is thrown when updating a product failed
 */
class CannotUpdateProductException extends ProductException
{
    /**
     * When basic information update fails
     */
    public const FAILED_UPDATE_BASIC_INFO = 10;

    /**
     * When updating product fields associated with price fails
     */
    public const FAILED_UPDATE_PRICES = 20;

    /**
     * When product options update fails
     */
    public const FAILED_UPDATE_OPTIONS = 30;

    /**
     * When product details update fails
     */
    public const FAILED_UPDATE_DETAILS = 40;

    /**
     * When product tags update fails
     */
    public const FAILED_UPDATE_TAGS = 50;

    /**
     * When product categories update fails
     */
    public const FAILED_UPDATE_CATEGORIES = 60;

    /**
     * When product properties associated with customization fields update fails
     */
    public const FAILED_UPDATE_CUSTOMIZATION_FIELDS = 70;

    /**
     * When product shipping options update fails
     */
    public const FAILED_UPDATE_SHIPPING_OPTIONS = 80;

    /**
     * When product default supplier update fails
     */
    public const FAILED_UPDATE_DEFAULT_SUPPLIER = 90;

    /**
     * When product default category update fails
     */
    public const FAILED_UPDATE_DEFAULT_CATEGORY = 100;

    /**
     * When product seo options update fails
     */
    public const FAILED_UPDATE_SEO = 110;

    /**
     * When product attachments association update fails
     */
    public const FAILED_UPDATE_ATTACHMENTS = 120;

    /**
     * When product default combination update fails
     */
    public const FAILED_UPDATE_DEFAULT_ATTRIBUTE = 130;

    /**
     * When search indexation update for product fails
     */
    public const FAILED_UPDATE_SEARCH_INDEXATION = 140;

    /**
     * When stock update fails
     */
    public const FAILED_UPDATE_STOCK = 150;

    /**
     * When type update fails
     */
    public const FAILED_UPDATE_TYPE = 160;

    /**
     * When product status update fails
     */
    public const FAILED_UPDATE_STATUS = 170;

    /**
     * When type update fails
     */
    public const FAILED_SHOP_COPY = 170;
}
