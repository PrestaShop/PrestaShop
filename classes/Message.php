<?php
/*
* 2007-2011 PrestaShop 
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class MessageCore extends ObjectModel
{
	public 		$id;
	
	/** @var string message content */
	public 		$message;
	
	/** @var integer Cart ID (if applicable) */
	public		$id_cart;
	
	/** @var integer Order ID (if applicable) */
	public		$id_order;
	
	/** @var integer Customer ID (if applicable) */
	public		$id_customer;
	
	/** @var integer Employee ID (if applicable) */
	public		$id_employee;
	
	/** @var boolean Message is not displayed to the customer */
	public		$private;
	
	/** @var string Object creation date */
	public 		$date_add;
	
	protected	$fieldsRequired = array('message');
	protected	$fieldsSize = array('message' => 1600);
	protected	$fieldsValidate = array(
		'message' => 'isCleanHtml', 'id_cart' => 'isUnsignedId', 'id_order' => 'isUnsignedId',
		'id_customer' => 'isUnsignedId', 'id_employee' => 'isUnsignedId', 'private' => 'isBool');

	protected 	$table = 'message';
	protected 	$identifier = 'id_message';

	public function getFields()
	{
		parent::validateFields();

		$fields['message'] = pSQL($this->message, true);
		$fields['id_cart'] = (int)($this->id_cart);
		$fields['id_order'] = (int)($this->id_order);
		$fields['id_customer'] = (int)($this->id_customer);
		$fields['id_employee'] = (int)($this->id_employee);
		$fields['private'] = (int)($this->private);
		$fields['date_add'] = pSQL($this->date_add);

		return $fields;
	}

	/**
	  * Return the last message from cart
	  *
	  * @param integer $id_cart Cart ID
	  * @return array Message
	  */
	static public function getMessageByCartId($id_cart)
	{
		$db = Db::getInstance();
		$result = $db->getRow('
		SELECT *
		FROM `'._DB_PREFIX_.'message`
		WHERE `id_cart` = '.(int)($id_cart));
		
		return $result;
	}
	
	/**
	  * Return messages from Order ID
	  *
	  * @param integer $id_order Order ID
	  * @param boolean $private return WITH private messages
	  * @return array Messages
	  */
	static public function getMessagesByOrderId($id_order, $private = false)
	{
	 	if (!Validate::isBool($private))
	 		die(Tools::displayError());

		global $cookie;
		
		return Db::getInstance()->ExecuteS('
		SELECT m.*, c.`firstname` AS cfirstname, c.`lastname` AS clastname, e.`firstname` AS efirstname, e.`lastname` AS elastname, (COUNT(mr.id_message) = 0 AND m.id_customer != 0) AS is_new_for_me
		FROM `'._DB_PREFIX_.'message` m
		LEFT JOIN `'._DB_PREFIX_.'customer` c ON m.`id_customer` = c.`id_customer`
		LEFT JOIN `'._DB_PREFIX_.'message_readed` mr ON (mr.id_message = m.id_message AND mr.id_employee = '.(int)$cookie->id_employee.')
		LEFT OUTER JOIN `'._DB_PREFIX_.'employee` e ON e.`id_employee` = m.`id_employee`
		WHERE id_order = '.(int)$id_order.'
		'.(!$private ? ' AND m.`private` = 0' : '').'
		GROUP BY m.id_message
		ORDER BY m.date_add DESC');
	}
	
	/**
	  * Return messages from Cart ID
	  *
	  * @param integer $id_order Order ID
	  * @param boolean $private return WITH private messages
	  * @return array Messages
	  */
	static public function getMessagesByCartId($id_cart, $private = false)
	{
	 	if (!Validate::isBool($private))
	 		die(Tools::displayError());

		global $cookie;

		return Db::getInstance()->ExecuteS('
		SELECT m.*, c.`firstname` AS cfirstname, c.`lastname` AS clastname, e.`firstname` AS efirstname, e.`lastname` AS elastname, (COUNT(mr.id_message) = 0 AND m.id_customer != 0) AS is_new_for_me
		FROM `'._DB_PREFIX_.'message` m
		LEFT JOIN `'._DB_PREFIX_.'customer` c ON m.`id_customer` = c.`id_customer`
		LEFT JOIN `'._DB_PREFIX_.'message_readed` mr ON (mr.id_message = m.id_message AND mr.id_employee = '.(int)$cookie->id_employee.')
		LEFT OUTER JOIN `'._DB_PREFIX_.'employee` e ON e.`id_employee` = m.`id_employee`
		WHERE id_cart = '.(int)$id_cart.'
		'.(!$private ? ' AND m.`private` = 0' : '').'
		GROUP BY m.id_message
		ORDER BY m.date_add DESC');
	}
	
	/**
	  * Registered a message 'readed'
	  *
	  * @param integer $id_message Message ID
	  * @param integer $id_emplyee Employee ID
	  */
	static public function markAsReaded($id_message, $id_employee)
	{
	 	if (!Validate::isUnsignedId($id_message) OR !Validate::isUnsignedId($id_employee))
	 		die(Tools::displayError());

		$result = Db::getInstance()->Execute('
		INSERT INTO '._DB_PREFIX_.'message_readed (id_message , id_employee , date_add) VALUES
		('.(int)($id_message).', '.(int)($id_employee).', NOW());
		');
		return $result;
	}
}


