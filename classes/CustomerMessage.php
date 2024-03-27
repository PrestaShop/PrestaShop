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
 * Class CustomerMessageCore.
 */
class CustomerMessageCore extends ObjectModel
{
    public $id;

    /** @var int CustomerThread ID */
    public $id_customer_thread;

    /** @var int */
    public $id_employee;

    /** @var string */
    public $message;

    /** @var string */
    public $file_name;

    /** @var string */
    public $ip_address;

    /** @var string */
    public $user_agent;

    /** @var bool */
    public $private;

    /** @var string */
    public $date_add;

    /** @var string */
    public $date_upd;

    /** @var bool */
    public $read;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'customer_message',
        'primary' => 'id_customer_message',
        'fields' => [
            'id_employee' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_customer_thread' => ['type' => self::TYPE_INT],
            'ip_address' => ['type' => self::TYPE_STRING, 'validate' => 'isIp2Long', 'size' => 15],
            'message' => ['type' => self::TYPE_HTML, 'required' => true, 'size' => 4194303, 'validate' => 'isCleanHtml'],
            'file_name' => ['type' => self::TYPE_STRING],
            'user_agent' => ['type' => self::TYPE_STRING],
            'private' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'read' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
        ],
    ];

    /** @var array */
    protected $webserviceParameters = [
        'fields' => [
            'id_employee' => [
                'xlink_resource' => 'employees',
            ],
            'id_customer_thread' => [
                'xlink_resource' => 'customer_threads',
            ],
        ],
    ];

    /**
     * Get CustomerMessages by Order ID.
     *
     * @param int $idOrder Order ID
     * @param bool $private Private
     *
     * @return array|false|mysqli_result|PDOStatement|resource|null
     */
    public static function getMessagesByOrderId($idOrder, $private = true)
    {
        return Db::getInstance()->executeS('
			SELECT cm.*,
				c.`firstname` AS cfirstname,
				c.`lastname` AS clastname,
				e.`firstname` AS efirstname,
				e.`lastname` AS elastname,
				(COUNT(cm.id_customer_message) = 0 AND ct.id_customer != 0) AS is_new_for_me
			FROM `' . _DB_PREFIX_ . 'customer_message` cm
			LEFT JOIN `' . _DB_PREFIX_ . 'customer_thread` ct
				ON ct.`id_customer_thread` = cm.`id_customer_thread`
			LEFT JOIN `' . _DB_PREFIX_ . 'customer` c
				ON ct.`id_customer` = c.`id_customer`
			LEFT OUTER JOIN `' . _DB_PREFIX_ . 'employee` e
				ON e.`id_employee` = cm.`id_employee`
			WHERE ct.id_order = ' . (int) $idOrder . '
			' . (!$private ? 'AND cm.`private` = 0' : '') . '
			GROUP BY cm.id_customer_message
			ORDER BY cm.date_add DESC
		');
    }

    /**
     * Get total CustomerMessages.
     *
     * @param string|null $where Additional SQL query
     *
     * @return int Amount of CustomerMessages found
     */
    public static function getTotalCustomerMessages($where = null)
    {
        if (null === $where) {
            return (int) Db::getInstance()->getValue(
                '
				SELECT COUNT(*)
				FROM ' . _DB_PREFIX_ . 'customer_message
				LEFT JOIN `' . _DB_PREFIX_ . 'customer_thread` ct ON (cm.`id_customer_thread` = ct.`id_customer_thread`)
				WHERE 1' . Shop::addSqlRestriction()
            );
        } else {
            return (int) Db::getInstance()->getValue(
                '
				SELECT COUNT(*)
				FROM ' . _DB_PREFIX_ . 'customer_message cm
				LEFT JOIN `' . _DB_PREFIX_ . 'customer_thread` ct ON (cm.`id_customer_thread` = ct.`id_customer_thread`)
				WHERE ' . $where . Shop::addSqlRestriction()
            );
        }
    }

    /**
     * Deletes current CustomerMessage from the database.
     *
     * @return bool `true` if delete was successful
     *
     * @throws PrestaShopException
     */
    public function delete()
    {
        if (!empty($this->file_name)) {
            @unlink(_PS_UPLOAD_DIR_ . basename($this->file_name));
        }

        return parent::delete();
    }

    /**
     * Get the last message for a thread customer.
     *
     * @param int $id_customer_thread Thread customer reference
     *
     * @return string Last message
     */
    public static function getLastMessageForCustomerThread($id_customer_thread)
    {
        return (string) Db::getInstance()->getValue(
            '
            SELECT message
            FROM ' . _DB_PREFIX_ . 'customer_message
            WHERE id_customer_thread = ' . (int) $id_customer_thread . '
            ORDER BY date_add DESC'
        );
    }
}
