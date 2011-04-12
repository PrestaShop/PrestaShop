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

class GuestCore extends ObjectModel
{
	public $id_operating_system;
	public $id_web_browser;
	public $id_customer;
	public $javascript;
	public $screen_resolution_x;
	public $screen_resolution_y;
	public $screen_color;
	public $sun_java;
	public $adobe_flash;
	public $adobe_director;
	public $apple_quicktime;
	public $real_player;
	public $windows_media;
	public $accept_language;
	
 	protected 	$fieldsSize = array('accept_language' => 8);
 	protected 	$fieldsValidate = array(
		'id_operating_system' => 'isUnsignedId',
		'id_web_browser' => 'isUnsignedId',
		'id_customer' => 'isUnsignedId',
		'javascript' => 'isBool',
		'screen_resolution_x' => 'isInt',
		'screen_resolution_y' => 'isInt',
		'screen_color' => 'isInt',
		'sun_java' => 'isBool',
		'adobe_flash' => 'isBool',
		'adobe_director' => 'isBool',
		'apple_quicktime' => 'isBool',
		'real_player' => 'isBool',
		'windows_media' => 'isBool',
		'accept_language' => 'isGenericName'
	);

	protected 	$table = 'guest';
	protected 	$identifier = 'id_guest';
	
	
	protected	$webserviceParameters = array(
		'fields' => array(
			'id_customer' => array('xlink_resource' => 'customers'),
		),
	);
	
	public function getFields()
	{
		parent::validateFields();
		
		$fields['id_operating_system'] = (int)($this->id_operating_system);
		$fields['id_web_browser'] = (int)($this->id_web_browser);
		$fields['id_customer'] = (int)($this->id_customer);
		$fields['javascript'] = (int)($this->javascript);
		$fields['screen_resolution_x'] = (int)($this->screen_resolution_x);
		$fields['screen_resolution_y'] = (int)($this->screen_resolution_y);
		$fields['screen_color'] = (int)($this->screen_color);
		$fields['sun_java'] = (int)($this->sun_java);
		$fields['adobe_flash'] = (int)($this->adobe_flash);
		$fields['adobe_director'] = (int)($this->adobe_director);
		$fields['apple_quicktime'] = (int)($this->apple_quicktime);
		$fields['real_player'] = (int)($this->real_player);
		$fields['windows_media'] = (int)($this->windows_media);
		$fields['accept_language'] = pSQL($this->accept_language);
		
		return $fields;
	}
	
	function userAgent()
	{
		$userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		$acceptLanguage = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
		$this->id_operating_system = $this->getOs($userAgent);
		$this->id_web_browser = $this->getBrowser($userAgent);
		$this->accept_language = $this->getLanguage($acceptLanguage);
	}
	
	protected function getLanguage($acceptLanguage)
	{
		// $langsArray is filled with all the languages accepted, ordered by priority
		$langsArray = array();
		preg_match_all('/([a-z]{2}(-[a-z]{2})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/', $acceptLanguage, $array);
		if (count($array[1]))
		{
			$langsArray = array_combine($array[1], $array[4]);
			foreach ($langsArray as $lang => $val)
				if ($val === '')
					$langsArray[$lang] = 1;
			arsort($langsArray, SORT_NUMERIC);
		}
		
		// Only the first language is returned
		return (sizeof($langsArray) ? key($langsArray) : '');
	}

	protected function getBrowser($userAgent)
	{
		$browserArray = array(
			'Google Chrome' => 'Chrome/',
			'Safari' => 'Safari',
			'Firefox 3.x' => 'Firefox/3',
			'Firefox 2.x' => 'Firefox/2',
			'Opera' => 'Opera',
			'IE 8.x' => 'MSIE 8',
			'IE 7.x' => 'MSIE 7',
			'IE 6.x' => 'MSIE 6'
		);
		foreach ($browserArray as $k => $value)
			if (strstr($userAgent, $value))
			{
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
				SELECT `id_web_browser`
				FROM `'._DB_PREFIX_.'web_browser` wb
				WHERE wb.`name` = \''.pSQL($k).'\'');
				
				return $result['id_web_browser'];
			}
		return NULL;
	}
	
	protected function getOs($userAgent)
	{
		$osArray = array(
			'Windows Vista' => 'Windows NT 6',
			'Windows XP' => 'Windows NT 5',
			'MacOsX' => 'Mac OS X',
			'Linux' => 'X11'
		);
		foreach ($osArray as $k => $value)
			if (strstr($userAgent, $value))
			{
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
				SELECT `id_operating_system`
				FROM `'._DB_PREFIX_.'operating_system` os
				WHERE os.`name` = \''.pSQL($k).'\'');
				
				return $result['id_operating_system'];
			}
		return NULL;
	}
	
	public static function getFromCustomer($id_customer)
	{
		if (!Validate::isUnsignedId($id_customer))
			return false;
		$result = Db::getInstance()->getRow('
		SELECT `id_guest`
		FROM `'._DB_PREFIX_.'guest`
		WHERE `id_customer` = '.(int)($id_customer));
		return $result['id_guest'];
	}
	
	public function mergeWithCustomer($id_guest, $id_customer)
	{
		// Since the guests are merged, the guest id in the connections table must be changed too
		Db::getInstance()->Execute('
		UPDATE `'._DB_PREFIX_.'connections` c
		SET c.`id_guest` = '.(int)($id_guest).'
		WHERE c.`id_guest` = '.(int)($this->id));
		
		// The current guest is removed from the database
		$this->delete();
		
		// $this is still filled with values, so it's id is changed for the old guest
		$this->id = (int)($id_guest);
		$this->id_customer = (int)($id_customer);
		
		// $this is now the old guest but filled with the most up to date values
		$this->update();
	}
	
	public static function setNewGuest($cookie)
	{
		$guest = new Guest(isset($cookie->id_customer) ? Guest::getFromCustomer((int)($cookie->id_customer)) : NULL);
		$guest->userAgent();
		if ($guest->id_operating_system OR $guest->id_web_browser)
		{
			$guest->save();
			$cookie->id_guest = (int)($guest->id);
		}
	}
}


