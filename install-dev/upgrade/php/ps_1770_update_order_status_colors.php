<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
                'UPDATE `'._DB_PREFIX_.'order_state` SET `color` = ' . pSQL($color) . ' WHERE `id_order_state` = ' . (int) $statusId
            );
        }
    }
}
