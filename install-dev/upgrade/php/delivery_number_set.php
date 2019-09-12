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

function delivery_number_set()
{
    Configuration::loadConfiguration();
    $number = 1;

    // Update each order with a number
    $result = Db::getInstance()->executeS('
	SELECT id_order
	FROM '._DB_PREFIX_.'orders
	ORDER BY id_order');
    foreach ($result as $row) {
        $order = new Order((int)($row['id_order']));
        $history = $order->getHistory(false);
        foreach ($history as $row2) {
            $oS = new OrderState((int)($row2['id_order_state']), Configuration::get('PS_LANG_DEFAULT'));
            if ($oS->delivery) {
                Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'orders SET delivery_number = '.(int)($number++).', `delivery_date` = `date_add` WHERE id_order = '.(int)($order->id));

                break ;
            }
        }
    }
    // Add configuration var
    Configuration::updateValue('PS_DELIVERY_NUMBER', (int)($number));
}
