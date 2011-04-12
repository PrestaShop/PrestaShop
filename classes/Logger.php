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

class	LoggerCore extends ObjectModel
{
	/** @var integer Log id */
	public		$id_log;

	/** @var integer Log severity */
	public		$severity;

	/** @var integer Error code */
	public		$error_code;

	/** @var string Message */
	public 		$message;

	/** @var string Object type (eg. Order, Customer...) */
	public		$object_type;

	/** @var integer Object ID */
	public 		$object_id;

	/** @var string Object creation date */
	public 		$date_add;

	/** @var string Object last modification date */
	public 		$date_upd;

	protected	$fieldsRequired = array('severity', 'message');
	protected	$fieldsSize = array();
	protected	$fieldsValidate = array('id_log' => 'isUnsignedId', 'severity' => 'isInt', 'error_code' => 'isUnsignedInt',
	'message' => 'isMessage', 'object_id' => 'isUnsignedInt', 'object_type' => 'isName');

	protected 	$table = 'log';
	protected 	$identifier = 'id_log';

	protected static $_is_present = array();


	public function getFields()
	{
		parent::validateFields();

		$fields['severity'] = intval($this->severity);
		$fields['error_code'] = intval($this->error_code);
		$fields['message'] = pSQL($this->message);
		$fields['object_type'] = pSQL($this->object_type);
		$fields['object_id'] = intval($this->object_id);
		$fields['date_add'] = pSQL($this->date_add);
		$fields['date_upd'] = pSQL($this->date_upd);

		return $fields;
	}

	static public function sendByMail($log)
	{
		/* Send e-mail to the shop owner only if the minimal severity level has been reached */
		if (intval(Configuration::get('PS_LOGS_BY_EMAIL')) <= intval($log->severity))
			Mail::Send((int)Configuration::get('PS_LANG_DEFAULT'), 'log_alert', Mail::l('[Log] You have a new alert from your shop'), array(), Configuration::get('PS_SHOP_EMAIL'));
	}

	/**
	 * add a log item to the database and send a mail if configured for this $severity 
	 * 
	 * @param string $message the log message
	 * @param int $severity 
	 * @param int $errorCode 
	 * @param string $objectType 
	 * @param int $objectId 
	 * @param boolean $allowDuplicate if set to true, can log several time the same information (not recommended)
	 * @return boolean true if succeed
	 */
	static public function addLog($message, $severity = 1, $errorCode = NULL, $objectType = NULL, $objectId = NULL, $allowDuplicate = false)
	{
		$log = new Logger();
		$log->severity = intval($severity);
		$log->error_code = intval($errorCode);
		$log->message = pSQL($message);
		$log->date_add = date('Y-m-d H:i:s');
		$log->date_upd = date('Y-m-d H:i:s');
		if (!empty($objectType) AND !empty($objectId))
		{
			$log->object_type = pSQL($objectType);
			$log->object_id = intval($objectId);
		}

		self::sendByMail($log);

		if ($allowDuplicate or !$log->_isPresent() )
		{
			$res = $log->add();
			if ($res)
			{
				self::$_is_present[$log->getHash()] = isset(self::$_is_present[$log->getHash()])?self::$_is_present[$log->getHash()] + 1:1;
				return true;
			}
		}
		return false;
	}

	/**
	 * this function md5($this->message.$this->severity.$this->error_code.$this->object_type.$this->object_id)
	 * 
	 * @return string hash
	 */
	public function getHash(){
		if (empty($this->hash))
			$this->hash = md5($this->message.$this->severity.$this->error_code.$this->object_type.$this->object_id);

		return $this->hash;
	}

	/**
	 * check if this log message already exists in database.
	 * 
	 * @param mixed $message 
	 * @return true if exists
	 */
	private function _isPresent()
	{
		if (!isset(self::$_is_present[md5($this->message)]))
			self::$_is_present[$this->getHash()] = Db::getInstance()->getValue('SELECT COUNT(*) 
				FROM `'._DB_PREFIX_.'log`
				WHERE 
					`message` = \''.$this->message.'\'
					AND `severity` = \''.$this->severity.'\'
					AND `error_code` = \''.$this->error_code.'\'
					AND `object_type` = \''.$this->object_type.'\'
					AND `object_id` = \''.$this->object_id.'\'
				');

		return self::$_is_present[$this->getHash()];
	}

}

