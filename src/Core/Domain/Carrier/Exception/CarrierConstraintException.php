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

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\Exception;

/**
 * Is thrown when carrier constraints are violated
 */
class CarrierConstraintException extends CarrierException
{
    /**
     * When carrier id constraints are violated
     */
    const INVALID_CARRIER_ID = 1;

    /**
     * When carrier name constraints are violated
     */
    const INVALID_CARRIER_NAME = 2;

    /**
     * When carrier shipping delay constraints are violated
     */
    const INVALID_SHIPPING_DELAY = 3;

    /**
     * When carrier shipping method value is invalid
     */
    const INVALID_SHIPPING_METHOD = 4;

    /**
     * When shipping range contains invalid values
     */
    const INVALID_SHIPPING_RANGE = 5;

    /**
     * When carrier package size measure contains invalid value
     */
    const INVALID_PACKAGE_MEASURE = 6;

    /**
     * When carrier speed grade contains invalid value
     */
    const INVALID_SPEED_GRADE = 7;

    /**
     * When carrier tracking url constraints are violated
     */
    const INVALID_TRACKING_URL = 8;

    /**
     * When out of range behavior value is invalid
     */
    const INVALID_OUT_OF_RANGE_BEHAVIOR = 9;

    /**
     * When module name provided for carrier is invalid
     */
    const INVALID_MODULE_NAME = 10;
}
