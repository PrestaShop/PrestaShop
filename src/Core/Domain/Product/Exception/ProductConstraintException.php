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
    public const INVALID_NAME = 1;
    public const NAME_TOO_LONG = 2;
    public const INVALID_UNIT_PRICE = 3;
    public const INVALID_RETAIL_PRICE = 5;
    public const INVALID_COST_PRICE = 6;
    public const INVALID_CUSTOMIZABLE_FEATURE_VALUE = 7;
    public const CUSTOMIZABLE_FEATURE_VALUE_TOO_LONG = 8;
    public const INVALID_META_TITLE = 9;
    public const META_TITLE_NAME_TOO_LONG = 10;
    public const INVALID_META_KEYWORDS = 11;
    public const META_KEYWORDS_NAME_TOO_LONG = 12;
    public const INVALID_META_DESCRIPTION = 13;
    public const META_DESCRIPTION_NAME_TOO_LONG = 14;
    public const FRIENDLY_URL_TOO_LONG = 15;
    public const INVALID_RESPONSE_CODE = 16;
    public const INVALID_CONDITION_TYPE = 17;
    public const INVALID_VISIBILITY_TYPE = 18;
    public const INVALID_REFERENCE = 19;
    public const INVALID_ISBN_REFERENCE = 20;
    public const INVALID_EAN13_REFERENCE = 21;
    public const INVALID_UPC_REFERENCE = 22;
    public const CUSTOMIZATION_FIELD_LABEL_TOO_LONG = 23;
    public const INVALID_ATTACHMENT_TITLE = 24;
    public const INVALID_SUPPLIER_REFERENCE = 25;
}
