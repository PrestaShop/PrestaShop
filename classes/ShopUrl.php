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

class ShopUrlCore extends ObjectModel
{
	public $id_shop;
	public $domain;
	public $domain_ssl;
	public $uri;
	public $main;
	public $active;
	
	private static $main_domain = NULL;
	private static $main_domain_ssl = NULL;
	

	protected $fieldsRequired = array('domain', 'id_shop');
	protected $fieldsSize = array('domain' => 255, 'uri' => 64);
	protected $fieldsValidate = array('active' => 'isBool');

	protected $table = 'shop_url';
	protected $identifier = 'id_shop_url';

	public function getFields()
	{
		parent::validateFields();
		$fields['domain'] = pSQL($this->domain);
		$fields['domain_ssl'] = pSQL($this->domain_ssl);
		$fields['uri'] = pSQL($this->uri);
		$fields['id_shop'] = (int)$this->id_shop;
		$fields['main'] = (int)$this->main;
		$fields['active'] = (int)$this->active;
		return $fields;
	}
	
	public static function getShopUrls($id_shop = false)
	{
		return Db::getInstance()->ExecuteS('SELECT *
														FROM '._DB_PREFIX_.'shop_url
														WHERE 1
														'.($id_shop ? ' AND id_shop='.(int)$id_shop : ''));
	}
		
	public function setMain()
	{
		$res = Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'shop_url SET main=0 WHERE id_shop='.(int)$this->id_shop);
		$res &= Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'shop_url SET main=1 WHERE id_shop_url='.(int)$this->id);
		return $res;
	}
		
	public function canAddThisUrl($domain, $domain_ssl, $uri)
	{
		$sql = 'SELECT id_shop_url
				FROM '._DB_PREFIX_.'shop_url
				WHERE uri=\''.pSQL($uri).'\'
					AND (domain=\''.pSQL($domain).'\' '.(($domain_ssl) ? 'OR domain_ssl=\''.pSQL($domain_ssl).'\'' : '').')'
					.($this->id ? ' AND id_shop_url !='.(int)$this->id : '');
		return Db::getInstance()->getValue($sql);
	}
	
	public static function getMainShopDomain()
	{
		if (!self::$main_domain)
			self::$main_domain = Db::getInstance()->getValue('SELECT domain
															FROM '._DB_PREFIX_.'shop_url
															WHERE main=1 AND id_shop='.(int)Shop::getCurrentShop());
		return self::$main_domain;
	}
	
	public static function getMainShopDomainSSL()
	{
		if (!self::$main_domain)
			self::$main_domain = Db::getInstance()->getValue('SELECT domain
																												FROM '._DB_PREFIX_.'shop_url
																												WHERE main=1 AND id_shop='.(int)Shop::getCurrentShop());
		return	self::$main_domain_ssl;
	}
}

