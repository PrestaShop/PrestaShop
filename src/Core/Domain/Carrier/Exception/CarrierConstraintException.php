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

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\Exception;

/**
 * Is thrown when carrier is invalid
 */
class CarrierConstraintException extends CarrierException
{
    /**
     * Thrown when provided carrier id is not valid
     */
    public const INVALID_ID = 10;

    /**
     * Thrown when carrier reference id is not valid
     */
    public const INVALID_REFERENCE_ID = 20;

    /**
     * Thrown when carrier name is not valid
     */
    public const INVALID_NAME = 30;

    /**
     * Thrown when carrier grade is not valid
     */
    public const INVALID_GRADE = 40;

    /**
     * Thrown when carrier tracking url is not valid
     */
    public const INVALID_TRACKING_URL = 50;

    /**
     * Thrown when carrier position is not valid
     */
    public const INVALID_POSITION = 60;

    /**
     * Thrown when carrier delay is not valid
     */
    public const INVALID_DELAY = 70;

    /**
     * Thrown when carrier max_width is not valid
     */
    public const INVALID_MAX_WIDTH = 80;

    /**
     * Thrown when carrier max_height is not valid
     */
    public const INVALID_MAX_HEIGHT = 80;

    /**
     * Thrown when carrier max_depth is not valid
     */
    public const INVALID_MAX_DEPTH = 90;

    /**
     * Thrown when carrier max_weight is not valid
     */
    public const INVALID_MAX_WEIGHT = 100;

    /**
     * Thrown when carrier group_access is not valid
     */
    public const INVALID_GROUP_ACCESS = 110;

    /**
     * Thrown when carrier shipping handling is not valid
     */
    public const INVALID_SHIPPING_HANDLING = 120;

    /**
     * Thrown when carrier is free option is not valid
     */
    public const INVALID_IS_FREE = 130;

    /**
     * Thrown when carrier shipping method is not valid
     */
    public const INVALID_SHIPPING_METHOD = 140;

    /**
     * Thrown when carrier tax rule group is not valid
     */
    public const INVALID_TAX_RULE_GROUP = 150;

    /**
     * Thrown when carrier range behavior is not valid
     */
    public const INVALID_RANGE_BEHAVIOR = 160;

    /**
     * Thrown when carrier shipping handling is set with free shipping
     */
    public const INVALID_HAS_ADDITIONAL_HANDLING_FEE_WITH_FREE_SHIPPING = 170;

    /**
     * Thrown when carrier ranges are overlapping
     */
    public const INVALID_RANGES_OVERLAPPING = 180;

    /**
     * Thrown when zone id in Carrier range are not valid
     */
    public const INVALID_ZONE_ID = 190;

    /**
     * Thrown when shop constraint isn't valid
     */
    public const INVALID_SHOP_CONSTRAINT = 200;

    /**
     * Thrown when carrier is save without at least one zone
     */
    public const INVALID_ZONE_MISSING = 210;

    /**
     * Thrown when carrier range is a negative value
     */
    public const INVALID_RANGE_NEGATIVE = 220;
}
