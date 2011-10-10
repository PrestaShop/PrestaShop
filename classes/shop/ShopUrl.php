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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class ShopUrlCore extends ObjectModel
{
	public $id_shop;
	public $domain;
	public $domain_ssl;
	public $physical_uri;
	public $virtual_uri;
	public $main;
	public $active;

	private static $main_domain = null;
	private static $main_domain_ssl = null;

	protected $fieldsRequired = array('domain', 'id_shop');
	protected $fieldsSize = array('domain' => 255, 'physical_uri' => 64, 'virtual_uri' => 64);
	protected $fieldsValidate = array('active' => 'isBool');

	protected $table = 'shop_url';
	protected $identifier = 'id_shop_url';

	public function getFields()
	{
		$this->validateFields();

		$this->physical_uri = trim($this->physical_uri, '/');
		if ($this->physical_uri)
			$this->physical_uri = preg_replace('#/+#', '/', '/'.$this->physical_uri.'/');
		else
			$this->physical_uri = '/';

		$this->virtual_uri = trim($this->virtual_uri, '/');
		if ($this->virtual_uri)
			$this->virtual_uri = preg_replace('#/+#', '/', trim($this->virtual_uri, '/')).'/';

		$fields['domain'] = pSQL($this->domain);
		$fields['domain_ssl'] = pSQL($this->domain_ssl);
		$fields['physical_uri'] = pSQL($this->physical_uri);
		$fields['virtual_uri'] = pSQL($this->virtual_uri);
		$fields['id_shop'] = (int)$this->id_shop;
		$fields['main'] = (int)$this->main;
		$fields['active'] = (int)$this->active;
		return $fields;
	}

	public function getURL($ssl = false)
	{
		if (!$this->id)
			return;

		$url = ($ssl) ? 'https://'.$this->domain_ssl : 'http://'.$this->domain;
		return $url.$this->physical_uri.$this->virtual_uri;
	}

	public static function getShopUrls($id_shop = false)
	{
		$sql = 'SELECT *
				FROM '._DB_PREFIX_.'shop_url
				WHERE 1
					'.($id_shop ? ' AND id_shop = '.(int)$id_shop : '');
		return Db::getInstance()->executeS($sql);
	}

	public function setMain()
	{
		$res = Db::getInstance()->autoExecute(_DB_PREFIX_.'shop_url', array('main' => 0), 'UPDATE', 'id_shop = '.(int)$this->id_shop);
		$res &= Db::getInstance()->autoExecute(_DB_PREFIX_.'shop_url', array('main' => 1), 'UPDATE', 'id_shop_url = '.(int)$this->id);
		$this->main = true;

		return $res;
	}

	public function canAddThisUrl($domain, $domain_ssl, $physical_uri, $virtual_uri)
	{
		$physical_uri = trim($physical_uri, '/');
		if ($physical_uri)
			$physical_uri = preg_replace('#/+#', '/', '/'.$physical_uri.'/');
		else
			$this->physical_uri = '/';

		$virtual_uri = trim($virtual_uri, '/');
		if ($virtual_uri)
			$virtual_uri = preg_replace('#/+#', '/', trim($virtual_uri, '/')).'/';

		$sql = 'SELECT id_shop_url
				FROM '._DB_PREFIX_.'shop_url
				WHERE physical_uri = \''.pSQL($physical_uri).'\'
					AND virtual_uri = \''.pSQL($virtual_uri).'\'
					AND (domain = \''.pSQL($domain).'\' '.(($domain_ssl) ? ' OR domain_ssl = \''.pSQL($domain_ssl).'\'' : '').')'
					.($this->id ? ' AND id_shop_url != '.(int)$this->id : '');
		return Db::getInstance()->getValue($sql);
	}

	public static function getMainShopDomain()
	{
		if (!self::$main_domain)
			self::$main_domain = Db::getInstance()->getValue('SELECT domain
															FROM '._DB_PREFIX_.'shop_url
															WHERE main=1 AND id_shop = '.Context::getContext()->shop->getID(true));
		return self::$main_domain;
	}

	public static function getMainShopDomainSSL()
	{
		if (!self::$main_domain)
		{
			$sql = 'SELECT domain
					FROM '._DB_PREFIX_.'shop_url
					WHERE main = 1
						AND id_shop='.Context::getContext()->shop->getID(true);
			self::$main_domain = Db::getInstance()->getValue($sql);
		}
		return	self::$main_domain_ssl;
	}
}
