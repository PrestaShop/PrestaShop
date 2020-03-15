<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
class CustomerThreadCore extends ObjectModel
{
    public $id;
    public $id_shop;
    public $id_lang;
    public $id_contact;
    public $id_customer;
    public $id_order;
    public $id_product;
    public $status;
    public $email;
    public $token;
    public $date_add;
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'customer_thread',
        'primary' => 'id_customer_thread',
        'fields' => [
            'id_lang' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true,
            ],
            'id_contact' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true,
            ],
            'id_shop' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
            ],
            'id_customer' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
            ],
            'id_order' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
            ],
            'id_product' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
            ],
            'email' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isEmail',
                'size' => 255,
            ],
            'token' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'required' => true,
            ],
            'status' => [
                'type' => self::TYPE_STRING,
            ],
            'date_add' => [
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
            ],
            'date_upd' => [
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
            ],
        ],
    ];

    protected $webserviceParameters = [
        'fields' => [
            'id_lang' => [
                'xlink_resource' => 'languages',
            ],
            'id_shop' => [
                'xlink_resource' => 'shops',
            ],
            'id_customer' => [
                'xlink_resource' => 'customers',
            ],
            'id_order' => [
                'xlink_resource' => 'orders',
            ],
            'id_product' => [
                'xlink_resource' => 'products',
            ],
        ],
        'associations' => [
            'customer_messages' => [
                'resource' => 'customer_message',
                'id' => [
                    'required' => true,
                ],
            ],
        ],
    ];

    public function getWsCustomerMessages()
    {
        return Db::getInstance()->executeS('
		SELECT `id_customer_message` id
		FROM `' . _DB_PREFIX_ . 'customer_message`
		WHERE `id_customer_thread` = ' . (int) $this->id);
    }

    public function delete()
    {
        if (!Validate::isUnsignedId($this->id)) {
            return false;
        }

        $return = true;
        $result = Db::getInstance()->executeS(
            '
			SELECT `id_customer_message`
			FROM `' . _DB_PREFIX_ . 'customer_message`
			WHERE `id_customer_thread` = ' . (int) $this->id
        );

        if (count($result)) {
            foreach ($result as $res) {
                $message = new CustomerMessage((int) $res['id_customer_message']);
                if (!Validate::isLoadedObject($message)) {
                    $return = false;
                } else {
                    $return &= $message->delete();
                }
            }
        }
        $return &= parent::delete();

        return $return;
    }

    public static function getCustomerMessages($id_customer, $read = null, $id_order = null)
    {
        $sql = 'SELECT *
			FROM ' . _DB_PREFIX_ . 'customer_thread ct
			LEFT JOIN ' . _DB_PREFIX_ . 'customer_message cm
				ON ct.id_customer_thread = cm.id_customer_thread
			WHERE id_customer = ' . (int) $id_customer;

        if ($read !== null) {
            $sql .= ' AND cm.`read` = ' . (int) $read;
        }
        if ($id_order !== null) {
            $sql .= ' AND ct.`id_order` = ' . (int) $id_order;
        }

        return Db::getInstance()->executeS($sql);
    }

    public static function getIdCustomerThreadByEmailAndIdOrder($email, $id_order)
    {
        return Db::getInstance()->getValue(
            '
			SELECT cm.id_customer_thread
			FROM ' . _DB_PREFIX_ . 'customer_thread cm
			WHERE cm.email = \'' . pSQL($email) . '\'
				AND cm.id_shop = ' . (int) Context::getContext()->shop->id . '
				AND cm.id_order = ' . (int) $id_order
        );
    }

    public static function getContacts()
    {
        return Db::getInstance()->executeS('
			SELECT cl.*, COUNT(*) as total, (
				SELECT id_customer_thread
				FROM ' . _DB_PREFIX_ . 'customer_thread ct2
				WHERE status = "open" AND ct.id_contact = ct2.id_contact
				' . Shop::addSqlRestriction() . '
				ORDER BY date_upd ASC
				LIMIT 1
			) as id_customer_thread
			FROM ' . _DB_PREFIX_ . 'customer_thread ct
			LEFT JOIN ' . _DB_PREFIX_ . 'contact_lang cl
				ON (cl.id_contact = ct.id_contact AND cl.id_lang = ' . (int) Context::getContext()->language->id . ')
			WHERE ct.status = "open"
				AND ct.id_contact IS NOT NULL
				AND cl.id_contact IS NOT NULL
				' . Shop::addSqlRestriction() . '
			GROUP BY ct.id_contact HAVING COUNT(*) > 0
		');
    }

    public static function getTotalCustomerThreads($where = null)
    {
        if (null === $where) {
            return (int) Db::getInstance()->getValue(
                '
				SELECT COUNT(*)
				FROM ' . _DB_PREFIX_ . 'customer_thread
				WHERE 1 ' . Shop::addSqlRestriction()
            );
        } else {
            return (int) Db::getInstance()->getValue(
                '
				SELECT COUNT(*)
				FROM ' . _DB_PREFIX_ . 'customer_thread
				WHERE ' . $where . Shop::addSqlRestriction()
            );
        }
    }

    public static function getMessageCustomerThreads($id_customer_thread)
    {
        return Db::getInstance()->executeS('
			SELECT ct.*, cm.*, cl.name subject, CONCAT(e.firstname, \' \', e.lastname) employee_name,
				CONCAT(c.firstname, \' \', c.lastname) customer_name, c.firstname
			FROM ' . _DB_PREFIX_ . 'customer_thread ct
			LEFT JOIN ' . _DB_PREFIX_ . 'customer_message cm
				ON (ct.id_customer_thread = cm.id_customer_thread)
			LEFT JOIN ' . _DB_PREFIX_ . 'contact_lang cl
				ON (cl.id_contact = ct.id_contact AND cl.id_lang = ' . (int) Context::getContext()->language->id . ')
			LEFT JOIN ' . _DB_PREFIX_ . 'employee e
				ON e.id_employee = cm.id_employee
			LEFT JOIN ' . _DB_PREFIX_ . 'customer c
				ON (IFNULL(ct.id_customer, ct.email) = IFNULL(c.id_customer, c.email))
			WHERE ct.id_customer_thread = ' . (int) $id_customer_thread . '
			ORDER BY cm.date_add ASC
		');
    }

    public static function getNextThread($id_customer_thread)
    {
        $context = Context::getContext();

        return Db::getInstance()->getValue('
			SELECT id_customer_thread
			FROM ' . _DB_PREFIX_ . 'customer_thread ct
			WHERE ct.status = "open"
			AND ct.date_upd = (
				SELECT date_add FROM ' . _DB_PREFIX_ . 'customer_message
				WHERE (id_employee IS NULL OR id_employee = 0)
					AND id_customer_thread = ' . (int) $id_customer_thread . '
				ORDER BY date_add DESC LIMIT 1
			)
			' . ($context->cookie->{'customer_threadFilter_cl!id_contact'} ?
                'AND ct.id_contact = ' . (int) $context->cookie->{'customer_threadFilter_cl!id_contact'} : '') . '
			' . ($context->cookie->{'customer_threadFilter_l!id_lang'} ?
                'AND ct.id_lang = ' . (int) $context->cookie->{'customer_threadFilter_l!id_lang'} : '') .
            ' ORDER BY ct.date_upd ASC
		');
    }

    public static function getCustomerMessagesOrder($id_customer, $id_order)
    {
        $sql = 'SELECT cm.*, c.`firstname` AS cfirstname, c.`lastname` AS clastname,
                e.`firstname` AS efirstname, e.`lastname` AS elastname
			FROM ' . _DB_PREFIX_ . 'customer_thread ct
			LEFT JOIN ' . _DB_PREFIX_ . 'customer_message cm
				ON ct.id_customer_thread = cm.id_customer_thread
            LEFT JOIN `' . _DB_PREFIX_ . 'customer` c 
                ON ct.`id_customer` = c.`id_customer`
            LEFT OUTER JOIN `' . _DB_PREFIX_ . 'employee` e 
                ON e.`id_employee` = cm.`id_employee`
			WHERE ct.id_customer = ' . (int) $id_customer .
                ' AND ct.`id_order` = ' . (int) $id_order . '
            GROUP BY cm.id_customer_message
		 	ORDER BY cm.date_add DESC
            LIMIT 2';

        return Db::getInstance()->executeS($sql);
    }
}
