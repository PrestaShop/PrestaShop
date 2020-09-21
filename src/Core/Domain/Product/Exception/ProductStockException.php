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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Exception;

class ProductStockException extends ProductException
{
    /**
     * Code is used when an advanced stock action is performed while
     * advanced stock managed is disabled
     */
    const ADVANCED_STOCK_MANAGEMENT_CONFIGURATION_DISABLED = 10;

    /**
     * Code is used when an advanced stock action is performed while
     * advanced stock managed is disabled on the product
     */
    const ADVANCED_STOCK_MANAGEMENT_PRODUCT_DISABLED = 20;

    /**
     * Code is used the StockAvailable info for a product is not found in database
     */
    const NOT_FOUND = 30;

    /**
     * Code is used when trying to link a pack stock with its product and one of them
     * has no advanced stock
     */
    const INCOMPATIBLE_PACK_STOCK_TYPE = 40;

    /**
     * Code is sent when the StockAvailable object cannot be saved
     */
    const CANNOT_SAVE_STOCK_AVAILABLE = 50;

    /**
     * Code is sent when invalid out of stock type is used
     */
    const INVALID_OUT_OF_STOCK_TYPE = 60;
}
