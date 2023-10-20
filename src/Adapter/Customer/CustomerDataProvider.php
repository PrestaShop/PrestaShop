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

namespace PrestaShop\PrestaShop\Adapter\Customer;

use Customer;
use Db;
use Symfony\Component\Process\Exception\LogicException;

/**
 * This class will provide data from DB / ORM about Customer.
 */
class CustomerDataProvider
{
    /**
     * @param int $id
     *
     * @throws LogicException If the customer id is not set
     *
     * @return object customer
     */
    public function getCustomer($id)
    {
        if (!$id) {
            throw new LogicException('You need to provide a customer id', 5002);
        }

        $customer = new Customer($id);

        return $customer;
    }

    public function getIdByEmail(string $email)
    {
        $id = null;
        $customers = Customer::getCustomersByEmail($email);
        if (!empty($customers)) {
            $id = current($customers)['id_customer'];
        }

        return $id;
    }

    /**
     * @param int $customerId
     * @param int $langId
     *
     * @return array
     */
    public function getCustomerAddresses($customerId, $langId)
    {
        $customer = $this->getCustomer($customerId);

        return $customer->getAddresses($langId);
    }

    /**
     * Get Default Customer Group ID.
     *
     * @param int $idCustomer Customer ID
     *
     * @return mixed|string|null
     */
    public function getDefaultGroupId($idCustomer)
    {
        return Customer::getDefaultGroupId($idCustomer);
    }

    /**
     * Provides customer messages
     *
     * @param int $customerId
     */
    public function getCustomerMessages(int $customerId, ?int $orderId = null, ?int $limit = null)
    {
        $mainSql = 'SELECT cm.*, c.`firstname` AS cfirstname, c.`lastname` AS clastname,
            e.`firstname` AS efirstname, e.`lastname` AS elastname
            FROM ' . _DB_PREFIX_ . 'customer_thread ct
			LEFT JOIN ' . _DB_PREFIX_ . 'customer_message cm
				ON ct.id_customer_thread = cm.id_customer_thread
            LEFT JOIN `' . _DB_PREFIX_ . 'customer` c
                ON ct.`id_customer` = c.`id_customer`
            LEFT OUTER JOIN `' . _DB_PREFIX_ . 'employee` e
                ON e.`id_employee` = cm.`id_employee`
			WHERE ct.id_customer = ' . $customerId;

        if ($orderId) {
            $mainSql .= ' AND ct.`id_order` = ' . $orderId;
        }

        $mainSql .= ' GROUP BY cm.id_customer_message
            ORDER BY cm.date_add DESC';

        $count = Db::getInstance()->executeS("SELECT COUNT(*) AS total FROM ($mainSql) AS messages");

        if ($limit) {
            $mainSql .= " LIMIT $limit";
        }

        return [
            'total' => empty($count) ? 0 : (int) $count[0]['total'],
            'messages' => Db::getInstance()->executeS($mainSql),
        ];
    }
}
