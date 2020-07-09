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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Exception;

/**
 * Thrown when some product packing actions fails
 */
class ProductPackException extends ProductException
{
    /**
     * When fails to add product to pack
     */
    const FAILED_ADDING_TO_PACK = 10;

    /**
     * When fails to delete products from a pack
     */
    const FAILED_DELETING_PRODUCTS_FROM_PACK = 20;

    /**
     * When trying to pack a product which is already a pack itself
     */
    const CANNOT_ADD_PACK_INTO_PACK = 30;

    /**
     * When product for packing quantity is invalid
     */
    const INVALID_QUANTITY = 40;
}
