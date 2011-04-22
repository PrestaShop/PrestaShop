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

class StoresControllerCore extends FrontController
{
	public function __construct()
	{
		$this->php_self = 'stores.php';
	
		parent::__construct();
	}
	
	public function preProcess()
	{
		global $smarty, $cookie;
		
		$simplifiedStoreLocator = Configuration::get('PS_STORES_SIMPLIFIED');
		$distanceUnit = Configuration::get('PS_DISTANCE_UNIT');
		if (!in_array($distanceUnit, array('km', 'mi')))
			$distanceUnit = 'km';
		
		if ($simplifiedStoreLocator)
		{
			$stores = Db::getInstance()->ExecuteS('
			SELECT s.*, cl.name country, st.iso_code state
			FROM '._DB_PREFIX_.'store s
			LEFT JOIN '._DB_PREFIX_.'country_lang cl ON (cl.id_country = s.id_country)
			LEFT JOIN '._DB_PREFIX_.'state st ON (st.id_state = s.id_state)
			WHERE s.active = 1 AND cl.id_lang = '.(int)($cookie->id_lang));
			
			foreach ($stores AS &$store)
				$store['has_picture'] = file_exists(_PS_STORE_IMG_DIR_.(int)($store['id_store']).'.jpg');
		}
		else
		{		
			if (Tools::getValue('all') == 1)
			{		
				$stores = Db::getInstance()->ExecuteS('
				SELECT s.*, cl.name country, st.iso_code state
				FROM '._DB_PREFIX_.'store s
				LEFT JOIN '._DB_PREFIX_.'country_lang cl ON (cl.id_country = s.id_country)
				LEFT JOIN '._DB_PREFIX_.'state st ON (st.id_state = s.id_state)
				WHERE s.active = 1 AND cl.id_lang = '.(int)($cookie->id_lang));
			}
			else
			{
				$distance = (int)(Tools::getValue('radius', 100));
				$multiplicator = ($distanceUnit == 'km' ? 6371 : 3959);
					
				$stores = Db::getInstance()->ExecuteS('
				SELECT s.*, cl.name country, st.iso_code state,
				('.(int)($multiplicator).' * acos(cos(radians('.(float)(Tools::getValue('latitude')).')) * cos(radians(latitude)) * cos(radians(longitude) - radians('.(float)(Tools::getValue('longitude')).')) + sin(radians('.(float)(Tools::getValue('latitude')).')) * sin(radians(latitude)))) distance, cl.id_country id_country
				FROM '._DB_PREFIX_.'store s
				LEFT JOIN '._DB_PREFIX_.'country_lang cl ON (cl.id_country = s.id_country)
				LEFT JOIN '._DB_PREFIX_.'state st ON (st.id_state = s.id_state)
				WHERE s.active = 1 AND cl.id_lang = '.(int)($cookie->id_lang).'
				HAVING distance < '.(int)($distance).'
				ORDER BY distance ASC
				LIMIT 0,20');
			}

			if (Tools::getValue('ajax') == 1)
			{
				$dom = new DOMDocument('1.0');
				$node = $dom->createElement('markers');
				$parnode = $dom->appendChild($node);

				$days[1] = 'Monday';
				$days[2] = 'Tuesday';
				$days[3] = 'Wednesday';
				$days[4] = 'Thursday';
				$days[5] = 'Friday';
				$days[6] = 'Saturday';
				$days[7] = 'Sunday';
				
				foreach ($stores AS $store)
				{
					$days_datas = array();
					$node = $dom->createElement('marker');
					$newnode = $parnode->appendChild($node);
					$newnode->setAttribute('name', $store['name']);
					$address =  $this->_processStoreAddress($store);
 
					$other = '';
					if (!empty($store['hours']))
					{
						$hours = unserialize($store['hours']);

						for ($i = 1; $i < 8; $i++)
						{
							$hours_datas = array();
							$hours_datas['day'] = $days[$i];
							$hours_datas['hours'] = $hours[(int)($i) - 1];
							$days_datas[] = $hours_datas;
						}
						$smarty->assign('days_datas', $days_datas);
						$smarty->assign('id_country', $store['id_country']);
					
						$other .= self::$smarty->fetch(_PS_THEME_DIR_.'store_infos.tpl');
					}
					
					$newnode->setAttribute('addressNoHtml', strip_tags(str_replace('<br />', ' ', $address)));
					$newnode->setAttribute('address', $address);
					$newnode->setAttribute('other', $other);
					$newnode->setAttribute('phone', $store['phone']);
					$newnode->setAttribute('id_store', (int)($store['id_store']));
					$newnode->setAttribute('has_store_picture', file_exists(_PS_STORE_IMG_DIR_.(int)($store['id_store']).'.jpg'));
					$newnode->setAttribute('lat', (float)($store['latitude']));
					$newnode->setAttribute('lng', (float)($store['longitude']));
					
					if (isset($store['distance']))
						$newnode->setAttribute('distance', (int)($store['distance']));
				}
				
				header('Content-type: text/xml');
				die($dom->saveXML());
			}
			else
				$smarty->assign('hasStoreIcon', file_exists(dirname(__FILE__).'/../img/logo_stores.gif'));
		}
		
		$smarty->assign(array('distance_unit' => $distanceUnit, 'simplifiedStoresDiplay' => $simplifiedStoreLocator, 'stores' => $stores, 'mediumSize' => Image::getSize('medium')));
	}

	private function _processStoreAddress($store)
	{
		$ignore_field = array(
					'firstname'	=>1
					, 'lastname'	=>1
				);

		$out = '';
		$out_datas = array();

		$address_datas = AddressFormat::getOrderedAddressFields($store['id_country']);
		
		foreach ($address_datas as $data_line)
		{
			$data_fields = explode(' ', $data_line);
			$adr_out = array();
			
			$data_fields_mod = false;
			foreach ($data_fields as $field_item)
			{
				$field_item = trim($field_item);
				if (!isset($ignore_field[$field_item])  && !empty($store[$field_item]) && $store[$field_item] != '')
				{
					$adr_out[] = $store[$field_item];
					$data_fields_mod = true;
				}
			}
			if ($data_fields_mod)
				$out_datas[] = implode(' ', $adr_out);
		}

		$out = implode('<br />', $out_datas);
		return $out;
	}


	public function process()
	{
		parent::process();
		
		self::$smarty->assign(array(
			'defaultLat' => (float)Configuration::get('PS_STORES_CENTER_LAT'),
			'defaultLong' => (float)Configuration::get('PS_STORES_CENTER_LONG')
		));
	}

	public function setMedia()
	{
		parent::setMedia();
		Tools::addCSS(_THEME_CSS_DIR_.'stores.css');
		if (!Configuration::get('PS_STORES_SIMPLIFIED'))
			Tools::addJS(_THEME_JS_DIR_.'stores.js');
		Tools::addJS('http://maps.google.com/maps/api/js?sensor=true');
	}
	
	public function displayContent()
	{
		parent::displayContent();
		self::$smarty->display(_PS_THEME_DIR_.'stores.tpl');
	}
}
