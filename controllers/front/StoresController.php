<?php
/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class StoresControllerCore extends FrontController
{
	public $php_self = 'stores';

	/**
	 * Initialize stores controller
	 * @see FrontController::init()
	 */
	public function init()
	{
		parent::init();

		if (!extension_loaded('Dom'))
		{
			$this->errors[] = Tools::displayError('PHP "Dom" extension has not been loaded.');
			$this->context->smarty->assign('errors', $this->errors);
		}
	}

	/**
	 * get formatted string address
	 */
	protected function processStoreAddress($store)
	{
		$ignore_field = array(
			'firstname',
			'lastname'
		);

		$out_datas = array();

		$address_datas = AddressFormat::getOrderedAddressFields($store['id_country'], false, true);
		$state = (isset($store['id_state'])) ? new State($store['id_state']) : null;

		foreach ($address_datas as $data_line)
		{
			$data_fields = explode(' ', $data_line);
			$addr_out = array();

			$data_fields_mod = false;
			foreach ($data_fields as $field_item)
			{
				$field_item = trim($field_item);
				if (!in_array($field_item, $ignore_field) && !empty($store[$field_item]))
				{
					$addr_out[] = ($field_item == 'city' && $state && isset($state->iso_code) && strlen($state->iso_code)) ?
						$store[$field_item].', '.$state->iso_code : $store[$field_item];
					$data_fields_mod = true;
				}
			}
			if ($data_fields_mod)
				$out_datas[] = implode(' ', $addr_out);
		}

		$out = implode('<br />', $out_datas);
		return $out;
	}

	/**
	 * Assign template vars for simplified stores
	 */
	protected function assignStoresSimplified()
	{
		$stores = Db::getInstance()->executeS('
		SELECT s.*, cl.name country, st.iso_code state
		FROM '._DB_PREFIX_.'store s
		'.Shop::addSqlAssociation('store', 's').'
		LEFT JOIN '._DB_PREFIX_.'country_lang cl ON (cl.id_country = s.id_country)
		LEFT JOIN '._DB_PREFIX_.'state st ON (st.id_state = s.id_state)
		WHERE s.active = 1 AND cl.id_lang = '.(int)$this->context->language->id);

		foreach ($stores as &$store)
		{
			$store['has_picture'] = file_exists(_PS_STORE_IMG_DIR_.(int)($store['id_store']).'.jpg');
			if ($working_hours = $this->renderStoreWorkingHours($store))
				$store['working_hours'] = $working_hours;
		}

		$this->context->smarty->assign(array(
			'simplifiedStoresDiplay' => true,
			'stores' => $stores
		));
	}
	
	public function renderStoreWorkingHours($store)
	{
		global $smarty;
		
		$days[1] = 'Monday';
		$days[2] = 'Tuesday';
		$days[3] = 'Wednesday';
		$days[4] = 'Thursday';
		$days[5] = 'Friday';
		$days[6] = 'Saturday';
		$days[7] = 'Sunday';
		
		$days_datas = array();
		$hours = array_filter(unserialize($store['hours']));
		if (!empty($hours))
		{
			for ($i = 1; $i < 8; $i++)
			{
				if (isset($hours[(int)($i) - 1]))
				{
					$hours_datas = array();
					$hours_datas['hours'] = $hours[(int)($i) - 1];
					$hours_datas['day'] = $days[$i];
					$days_datas[] = $hours_datas;
				}
			}
			$smarty->assign('days_datas', $days_datas);
			$smarty->assign('id_country', $store['id_country']);
			return $this->context->smarty->fetch(_PS_THEME_DIR_.'store_infos.tpl');
		}
		return false;
	}
	
	public function getStores()
	{
		$distanceUnit = Configuration::get('PS_DISTANCE_UNIT');
		if (!in_array($distanceUnit, array('km', 'mi')))
			$distanceUnit = 'km';

		if (Tools::getValue('all') == 1)
		{
			$stores = Db::getInstance()->executeS('
			SELECT s.*, cl.name country, st.iso_code state
			FROM '._DB_PREFIX_.'store s
			'.Shop::addSqlAssociation('store', 's').'
			LEFT JOIN '._DB_PREFIX_.'country_lang cl ON (cl.id_country = s.id_country)
			LEFT JOIN '._DB_PREFIX_.'state st ON (st.id_state = s.id_state)
			WHERE s.active = 1 AND cl.id_lang = '.(int)$this->context->language->id);
		}
		else
		{
			$distance = (int)(Tools::getValue('radius', 100));
			$multiplicator = ($distanceUnit == 'km' ? 6371 : 3959);

			$stores = Db::getInstance()->executeS('
			SELECT s.*, cl.name country, st.iso_code state,
			('.(int)($multiplicator).'
				* acos(
					cos(radians('.(float)(Tools::getValue('latitude')).'))
					* cos(radians(latitude))
					* cos(radians(longitude) - radians('.(float)(Tools::getValue('longitude')).'))
					+ sin(radians('.(float)(Tools::getValue('latitude')).'))
					* sin(radians(latitude))
				)
			) distance,
			cl.id_country id_country
			FROM '._DB_PREFIX_.'store s
			'.Shop::addSqlAssociation('store', 's').'
			LEFT JOIN '._DB_PREFIX_.'country_lang cl ON (cl.id_country = s.id_country)
			LEFT JOIN '._DB_PREFIX_.'state st ON (st.id_state = s.id_state)
			WHERE s.active = 1 AND cl.id_lang = '.(int)$this->context->language->id.'
			HAVING distance < '.(int)($distance).'
			ORDER BY distance ASC
			LIMIT 0,20');
		}

		return $stores;
	}

	/**
	 * Assign template vars for classical stores
	 */
	protected function assignStores()
	{
		$this->context->smarty->assign('hasStoreIcon', file_exists(_PS_IMG_DIR_.Configuration::get('PS_STORES_ICON')));

		$distanceUnit = Configuration::get('PS_DISTANCE_UNIT');
		if (!in_array($distanceUnit, array('km', 'mi')))
			$distanceUnit = 'km';

		$this->context->smarty->assign(array(
			'distance_unit' => $distanceUnit,
			'simplifiedStoresDiplay' => false,
			'stores' => $this->getStores()
		));
	}

	/**
	 * Display the Xml for showing the nodes in the google map
	 */
	protected function displayAjax()
	{
		$stores = $this->getStores();
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

		foreach ($stores as $store)
		{
			$other = '';
			$node = $dom->createElement('marker');
			$newnode = $parnode->appendChild($node);
			$newnode->setAttribute('name', $store['name']);
			$address = $this->processStoreAddress($store);

			$other .= $this->renderStoreWorkingHours($store);
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

	/**
	 * Assign template vars related to page content
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();

		if (Configuration::get('PS_STORES_SIMPLIFIED'))
			$this->assignStoresSimplified();
		else
			$this->assignStores();

		$this->context->smarty->assign(array(
			'mediumSize' => Image::getSize(ImageType::getFormatedName('medium')),
			'defaultLat' => (float)Configuration::get('PS_STORES_CENTER_LAT'),
			'defaultLong' => (float)Configuration::get('PS_STORES_CENTER_LONG'),
			'searchUrl' => $this->context->link->getPageLink('stores'),
			'logo_store' => Configuration::get('PS_STORES_ICON')
		));

		$this->setTemplate(_PS_THEME_DIR_.'stores.tpl');
	}

	public function setMedia()
	{
		parent::setMedia();
		$this->addCSS(_THEME_CSS_DIR_.'stores.css');
		if (!Configuration::get('PS_STORES_SIMPLIFIED'))
			$this->addJS(_THEME_JS_DIR_.'stores.js');
        $default_country = new Country((int)Configuration::get('PS_COUNTRY_DEFAULT'));
		$this->addJS('http://maps.google.com/maps/api/js?sensor=true&amp;region='.substr($default_country->iso_code, 0, 2));
	}
}
