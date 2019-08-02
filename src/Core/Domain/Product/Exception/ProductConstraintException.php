<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Exception;

class ProductConstraintException extends ProductException
{
    public const INVALID_UNIT_PRICE = 1;
    public const INVALID_RETAIL_PRICE = 2;
    public const INVALID_COST_PRICE = 3;
    public const INVALID_META_KEYWORDS = 4;
    public const INVALID_META_DESCRIPTION = 5;
    public const INVALID_RESPONSE_CODE = 6;
    public const INVALID_CONDITION_TYPE = 7;
    public const INVALID_VISIBILITY_TYPE = 8;
    public const INVALID_REFERENCE = 9;
    public const INVALID_ISBN_REFERENCE = 10;
    public const INVALID_EAN13_REFERENCE = 11;
    public const INVALID_UPC_REFERENCE = 12;
}
