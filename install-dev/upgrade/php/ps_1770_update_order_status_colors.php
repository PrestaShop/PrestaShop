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

use PrestaShop\PrestaShop\Core\Domain\Order\Status\OrderStatusColor;

/**
 * Updates order status colors according to new color schema
 */
function ps_1770_update_order_status_colors() {
    $statusColorMap = [
        OrderStatusColor::AWAITING_PAYMENT => Configuration::getMultiple([
            'PS_OS_CHEQUE',
            'PS_OS_BANKWIRE',
            'PS_OS_OUTOFSTOCK_UNPAID',
            'PS_OS_COD_VALIDATION',
        ]),
        OrderStatusColor::ACCEPTED_PAYMENT => Configuration::getMultiple([
            'PS_OS_PAYMENT',
            'PS_OS_PREPARATION',
            'PS_OS_OUTOFSTOCK_PAID',
            'PS_OS_WS_PAYMENT',
        ]),
        OrderStatusColor::COMPLETED => Configuration::getMultiple([
            'PS_OS_SHIPPING',
            'PS_OS_DELIVERED',
            'PS_OS_REFUND',
        ]),
        OrderStatusColor::ERROR => Configuration::getMultiple([
            'PS_OS_ERROR',
        ]),
        OrderStatusColor::SPECIAL => Configuration::getMultiple([
            'PS_OS_CANCELED',
        ]),
    ];

    foreach ($statusColorMap as $color => $statuses) {
        foreach ($statuses as $statusId) {
            Db::getInstance()->execute(
                'UPDATE `'._DB_PREFIX_.'order_state` SET `color` = "' . pSQL($color) . '" WHERE `id_order_state` = ' . (int) $statusId
            );
        }
    }

    // Some of the statuses can be deduced by their parameters, this allows to update the modules status colors
    $statusColorConditions = [
        OrderStatusColor::ACCEPTED_PAYMENT => ['paid' => 1, 'shipped' => 0],
        OrderStatusColor::COMPLETED => ['paid' => 1, 'shipped' => 1],
        OrderStatusColor::AWAITING_PAYMENT => ['color' => '#4169E1'], // Former color of awaiting payment
    ];
    foreach ($statusColorConditions as $color => $conditions) {
        $whereCondition = ' WHERE 1';
        foreach ($conditions as $field => $expectedValue) {
            $whereCondition .= ' AND `' . $field . '` = "' . pSQL($expectedValue) . '"';
        }
        Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'order_state` SET `color` = "' . pSQL($color) . '"' . $whereCondition
        );
    }
}
