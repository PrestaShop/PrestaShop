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

namespace PrestaShop\PrestaShop\Core\Domain\SpecificPrice\Exception;

use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Exception\SpecificPriceConstraintException as ProductSpecificPriceConstraintException;

@trigger_error(
    sprintf(
        '%s is deprecated since version 8.0.0 and will be removed in the next major version.',
        SpecificPriceConstraintException::class
    ),
    E_USER_DEPRECATED
);

/**
 * @deprecated since 8.0.0 and will be removed in the next major version.
 * @see ProductSpecificPriceConstraintException
 */
class SpecificPriceConstraintException extends SpecificPriceException
{
    /**
     * When catalog price rule id is not valid
     */
    public const INVALID_ID = 1;

    /**
     * When date-time format is invalid
     */
    public const INVALID_DATETIME = 2;

    /**
     * When date range is not valid
     */
    public const INVALID_DATE_RANGE = 3;

    /**
     * When specific price priority value is not valid
     */
    public const INVALID_PRIORITY = 4;

    /**
     * When there is duplicated priorities in specific price priority list
     */
    public const DUPLICATE_PRIORITY = 5;

    /**
     * When specific price from quantity value is not valid
     */
    public const INVALID_FROM_QUANTITY = 6;

    /**
     * When specific price tax included value is not valid
     */
    public const INVALID_TAX_INCLUDED = 7;

    /**
     * When specific price reduction amount value is not valid
     */
    public const INVALID_REDUCTION_AMOUNT = 8;

    /**
     * When specific price explicit value is not valid
     */
    public const INVALID_PRICE = 9;

    /**
     * When specific price lower validity limit is not valid
     */
    public const INVALID_FROM_DATETIME = 10;

    /**
     * When specific price upper validity limit is not valid
     */
    public const INVALID_TO_DATETIME = 11;

    /**
     * When specific price reduction type value is not valid
     */
    public const INVALID_REDUCTION_TYPE = 12;

    /**
     * When specific price relation ID is not valid
     */
    public const INVALID_RELATION_ID = 13;
}
