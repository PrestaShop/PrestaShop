<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
     * @return object customer
     *
     * @throws LogicException If the customer id is not set
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
