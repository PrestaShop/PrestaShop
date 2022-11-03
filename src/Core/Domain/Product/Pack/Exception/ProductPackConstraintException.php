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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Pack\Exception;

/**
 * Thrown when product packing constraints are violated
 */
class ProductPackConstraintException extends ProductPackException
{
    /**
     * When trying to pack a product which is already a pack itself
     */
    public const CANNOT_ADD_PACK_INTO_PACK = 10;

    /**
     * When product for packing quantity is invalid
     */
    public const INVALID_QUANTITY = 20;

    /**
     * When invalid pack stock type is used
     */
    public const INVALID_STOCK_TYPE = 30;

    /**
     * Code is used when trying to link a pack stock with its product and one of them
     * has no advanced stock
     */
    public const INCOMPATIBLE_STOCK_TYPE = 40;
}
