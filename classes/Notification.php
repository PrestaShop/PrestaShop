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

/**
 * Class NotificationCore.
 */
class NotificationCore
{
    public $types;

    /**
     * NotificationCore constructor.
     */
    public function __construct()
    {
        $this->types = ['order', 'customer_message', 'customer'];
    }

    /**
     * getLastElements return all the notifications (new order, new customer registration, and new customer message)
     * Get all the notifications.
     *
     * @return array containing the notifications
     */
    public function getLastElements()
    {
        $notifications = [];
        $employeeInfos = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT id_last_order, id_last_customer_message, id_last_customer
		FROM `' . _DB_PREFIX_ . 'employee`
		WHERE `id_employee` = ' . (int) Context::getContext()->employee->id);

        foreach ($this->types as $type) {
            $notifications[$type] = Notification::getLastElementsIdsByType($type, $employeeInfos['id_last_' . $type]);
        }

        return $notifications;
    }

    /**
     * getLastElementsIdsByType return all the element ids to show (order, customer registration, and customer message)
     * Get all the element ids.
     *
     * @param string $type contains the field name of the Employee table
     * @param int $idLastElement contains the id of the last seen element
     *
     * @return array containing the notifications
     */
    public static function getLastElementsIdsByType($type, $idLastElement)
    {
        global $cookie;

        switch ($type) {
            case 'order':
                $sql = '
					SELECT SQL_CALC_FOUND_ROWS o.`id_order`, o.`id_customer`, o.`total_paid`, o.`id_currency`, o.`date_upd`, c.`firstname`, c.`lastname`, ca.`name`, co.`iso_code`
					FROM `' . _DB_PREFIX_ . 'orders` as o
					LEFT JOIN `' . _DB_PREFIX_ . 'customer` as c ON (c.`id_customer` = o.`id_customer`)
					LEFT JOIN `' . _DB_PREFIX_ . 'carrier` as ca ON (ca.`id_carrier` = o.`id_carrier`)
					LEFT JOIN `' . _DB_PREFIX_ . 'address` as a ON (a.`id_address` = o.`id_address_delivery`)
					LEFT JOIN `' . _DB_PREFIX_ . 'country` as co ON (co.`id_country` = a.`id_country`)
					WHERE `id_order` > ' . (int) $idLastElement .
                    Shop::addSqlRestriction(false, 'o') . '
					ORDER BY `id_order` DESC
					LIMIT 5';

                break;

            case 'customer_message':
                $sql = '
					SELECT SQL_CALC_FOUND_ROWS c.`id_customer_message`, ct.`id_customer`, ct.`id_customer_thread`, ct.`email`, ct.`status`, c.`date_add`, cu.`firstname`, cu.`lastname`
					FROM `' . _DB_PREFIX_ . 'customer_message` as c
					LEFT JOIN `' . _DB_PREFIX_ . 'customer_thread` as ct ON (c.`id_customer_thread` = ct.`id_customer_thread`)
					LEFT JOIN `' . _DB_PREFIX_ . 'customer` as cu ON (cu.`id_customer` = ct.`id_customer`)
					WHERE c.`id_customer_message` > ' . (int) $idLastElement . '
						AND c.`id_employee` = 0
						AND ct.id_shop IN (' . implode(', ', Shop::getContextListShopID()) . ')
					ORDER BY c.`id_customer_message` DESC
					LIMIT 5';

                break;
            default:
                $sql = '
					SELECT SQL_CALC_FOUND_ROWS t.`id_' . bqSQL($type) . '`, t.*
					FROM `' . _DB_PREFIX_ . bqSQL($type) . '` t
					WHERE t.`deleted` = 0 AND t.`id_' . bqSQL($type) . '` > ' . (int) $idLastElement .
                    Shop::addSqlRestriction(false, 't') . '
					ORDER BY t.`id_' . bqSQL($type) . '` DESC
					LIMIT 5';

                break;
        }

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql, true, false);
        $total = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT FOUND_ROWS()', false);
        $json = ['total' => $total, 'results' => []];
        foreach ($result as $value) {
            $customerName = '';
            if (isset($value['firstname'], $value['lastname'])) {
                $customerName = Tools::safeOutput($value['firstname'] . ' ' . $value['lastname']);
            } elseif (isset($value['email'])) {
                $customerName = Tools::safeOutput($value['email']);
            }

            $json['results'][] = [
                'id_order' => ((!empty($value['id_order'])) ? (int) $value['id_order'] : 0),
                'id_customer' => ((!empty($value['id_customer'])) ? (int) $value['id_customer'] : 0),
                'id_customer_message' => ((!empty($value['id_customer_message'])) ? (int) $value['id_customer_message'] : 0),
                'id_customer_thread' => ((!empty($value['id_customer_thread'])) ? (int) $value['id_customer_thread'] : 0),
                'total_paid' => ((!empty($value['total_paid'])) ? Tools::getContextLocale(Context::getContext())->formatPrice((float) $value['total_paid'], Currency::getIsoCodeById((int) $value['id_currency'])) : 0),
                'carrier' => ((!empty($value['name'])) ? Tools::safeOutput($value['name']) : ''),
                'iso_code' => ((!empty($value['iso_code'])) ? Tools::safeOutput($value['iso_code']) : ''),
                'company' => ((!empty($value['company'])) ? Tools::safeOutput($value['company']) : ''),
                'status' => ((!empty($value['status'])) ? Tools::safeOutput($value['status']) : ''),
                'customer_name' => $customerName,
                'date_add' => isset($value['date_add']) ? Tools::displayDate($value['date_add']) : 0,
                'customer_view_url' => Context::getContext()->link->getAdminLink(
                    'AdminCustomers',
                    true,
                    [
                        'customerId' => $value['id_customer'],
                        'viewcustomer' => true,
                    ]
                ),
            ];
        }

        return $json;
    }

    /**
     * updateEmployeeLastElement return 0 if the field doesn't exists in Employee table.
     * Updates the last seen element by the employee.
     *
     * @param string $type contains the field name of the Employee table
     *
     * @return bool if type exists or not
     */
    public function updateEmployeeLastElement($type)
    {
        if (in_array($type, $this->types)) {
            // We update the last item viewed
            return Db::getInstance()->execute('
			UPDATE `' . _DB_PREFIX_ . 'employee`
			SET `id_last_' . bqSQL($type) . '` = (
				SELECT IFNULL(MAX(`id_' . bqSQL($type) . '`), 0)
				FROM `' . _DB_PREFIX_ . (($type == 'order') ? bqSQL($type) . 's' : bqSQL($type)) . '`
			)
			WHERE `id_employee` = ' . (int) Context::getContext()->employee->id);
        }

        return false;
    }
}
