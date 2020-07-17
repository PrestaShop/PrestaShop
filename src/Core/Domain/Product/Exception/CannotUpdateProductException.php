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
    const FAILED_UPDATE_BASIC_INFO = 10;

    /**
     * When updating product fields associated with price fails
     */
    const FAILED_UPDATE_PRICES = 20;

    /**
     * When product options update fails
     */
    const FAILED_UPDATE_OPTIONS = 30;

    /**
     * When product tags update fails
     */
    const FAILED_UPDATE_TAGS = 40;

    /**
     * When product categories update fails
     */
    const FAILED_UPDATE_CATEGORIES = 50;

    /**
     * When product properties associated with customization fields update fails
     */
    const FAILED_UPDATE_CUSTOMIZATION_FIELDS = 60;

    /**
     * When product shipping options update fails
     */
    const FAILED_UPDATE_SHIPPING_OPTIONS = 70;

    /**
     * When product default supplier update fails
     */
    const FAILED_UPDATE_DEFAULT_SUPPLIER = 80;
}
