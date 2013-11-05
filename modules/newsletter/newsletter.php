<?php
/*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class Newsletter extends Module
{
	private $_postErrors = array();
	private $_html = '';
	private $_postSucess;

	public function __construct()
	{
		$this->name = 'newsletter';
		$this->tab = 'administration';
		$this->version = 2.0;
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();	

		$this->displayName = $this->l('Newsletter');
		$this->description = $this->l('Generates a .CSV file for mass mailings');

		if ($this->id)
		{
			$this->_file = 'export_'.Configuration::get('PS_NEWSLETTER_RAND').'.csv';
			$this->_postValid = array();

			// Getting data...
			$_countries = Country::getCountries($this->context->language->id);

			// ...formatting array
			$countries[0] = $this->l('All countries');
			foreach ($_countries as $country)
				$countries[$country['id_country']] = $country['name'];

			// And filling fields to show !
			$this->_fieldsExport = array(
			'COUNTRY' => array(
				'title' => $this->l('Customers\' country'),
				'desc' => $this->l('Operate a filter on customers\' country.'),
				'type' => 'select',
				'value' => $countries,
				'value_default' => 0
				),
			'SUSCRIBERS' => array(
				'title' => $this->l('Newsletter subscribers'),
				'desc' => $this->l('Filter newsletter subscribers.'),
				'type' => 'select',
				'value' => array(0 => $this->l('All customers'), 2 => $this->l('Subscribers'), 1 => $this->l('Non-subscribers')),
				'value_default' => 2
				),
			'OPTIN' => array(
				'title' => $this->l('Opted-in subscribers'),
				'desc' => $this->l('Filter opted-in subscribers.'),
				'type' => 'select',
				'value' => array(0 => $this->l('All customers'), 2 => $this->l('Subscribers'), 1 => $this->l('Non-subscribers')),
				'value_default' => 0
				),
			);
		}
	}

	public function install()
	{
		return (parent::install() AND Configuration::updateValue('PS_NEWSLETTER_RAND', rand().rand()));
	}

	private function _postProcess()
	{
		if (Tools::isSubmit('submitExport') && $action = Tools::getValue('action'))
		{
			if ($action == 'customers')
				$result = $this->_getCustomers();
			else
			{
				if (!Module::isInstalled('blocknewsletter'))
					$this->_html .= $this->displayError('The module "blocknewsletter" is required for this feature');
				else
					$result = $this->_getBlockNewsletter();
			}
			if (!$nb = (int)(Db::getInstance(_PS_USE_SQL_SLAVE_)->NumRows()))
				$this->_html .= $this->displayError($this->l('No customers found with these filters!'));
			elseif ($fd = @fopen(dirname(__FILE__).'/'.strval(preg_replace('#\.{2,}#', '.', $_POST['action'])).'_'.$this->_file, 'w'))
			{
				foreach ($result AS $tab)
					$this->_my_fputcsv($fd, $tab);
				fclose($fd);
				$this->_html .= $this->displayConfirmation(
				sprintf($this->l('The .CSV file has been successfully exported. (%d customers found)'), $nb).'<br />
				<a href="../modules/newsletter/'.Tools::safeOutput(strval($_POST['action'])).'_'.$this->_file.'"><b>'.$this->l('Download the file').' '.$this->_file.'</b></a>
				<br />
				<ol style="margin-top: 10px;">
					<li style="color: red;">'.$this->l('WARNING: If opening this .csv file with Excel, remember to choose UTF-8 encoding or you may see strange characters.').'</li>
				</ol>');
			}
			else
				$this->_html .= $this->displayError($this->l('Error: cannot write').' '.dirname(__FILE__).'/'.strval($_POST['action']).'_'.$this->_file.' !');
		}
	}

	private function _getCustomers()
	{
		$dbquery = new DbQuery();
		$dbquery->select('c.`id_customer`, c.`lastname`, c.`firstname`, c.`email`, c.`ip_registration_newsletter`, c.`newsletter_date_add`')
				->from('customer', 'c')
				->groupBy('c.`email`');

		if (Tools::getValue('SUSCRIBERS'))
			$dbquery->where('c.`newsletter` = '.((int)Tools::getValue('SUSCRIBERS') - 1));

		if (Tools::getValue('OPTIN'))
			$dbquery->where('c.`optin` = '.((int)Tools::getValue('OPTIN') - 1));

		if (Tools::getValue('COUNTRY'))
			$dbquery->where('(SELECT COUNT(a.`id_address`) as nb_country
								FROM `'._DB_PREFIX_.'address` a
								WHERE a.deleted = 0
								AND a.`id_customer` = c.`id_customer`
								AND a.`id_country` = '.(int)Tools::getValue('COUNTRY').') >= 1');

		if (Context::getContext()->cookie->shopContext)
			$dbquery->where('c.id_shop = '.(int)Context::getContext()->shop->id); 

		$rq = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($dbquery->build());

		$header = array('id_customer', 'lastname', 'firstname', 'email', 'ip_address', 'newsletter_date_add');
		$result = (is_array($rq) ? array_merge(array($header), $rq) : $header);
		return $result;
	}

	private function _getBlockNewsletter()
	{
		$rqSql = 'SELECT `id`, `email`, `newsletter_date_add`, `ip_registration_newsletter`
		FROM `'._DB_PREFIX_.'newsletter`
		WHERE `active` = 1';

		if (Context::getContext()->cookie->shopContext)
			$rqSql .= ' AND `id_shop` = '.(int)Context::getContext()->shop->id;

		$rq = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($rqSql); 

		$header = array('id_customer', 'email', 'newsletter_date_add', 'ip_address', 'http_referer');
		$result = (is_array($rq) ? array_merge(array($header), $rq) : $header);
		return $result;
	}

	private function _my_fputcsv($fd, $array)
	{
		$line = implode(';', $array);
		$line .= "\n";
		if (!fwrite($fd, $line, 4096))
			$this->_postErrors[] = $this->l('Error: cannot write').' '.dirname(__FILE__).'/'.$this->_file.' !';
	}

	private function _displayForm()
	{
		$this->_displayFormExport();
	}

	public function getContent()
	{
		$this->_html .= '';

		if (!empty($_POST))
			$this->_html .= $this->_postProcess();
		$this->_html .= $this->renderForm();

		return $this->_html;
	}
	
	public function renderForm()
	{
		
		// Getting data...
		$_countries = Country::getCountries($this->context->language->id);

		// ...formatting array
		$countries[0] = array('id' => 0, 'name' => $this->l('All countries'));
		foreach ($_countries as $country)
			$countries[] = array('id' => $country['id_country'], 'name' => $country['name']);

		// And filling fields to show !
		$this->_fieldsExport = array(
		'COUNTRY' => array(
			'title' => $this->l('Customers\' country'),
			'desc' => $this->l('Operate a filter on customers\' country.'),
			'type' => 'select',
			'value' => $countries,
			'value_default' => 0
			),
		'SUSCRIBERS' => array(
			'title' => $this->l('Newsletter subscribers'),
			'desc' => $this->l('Filter newsletter subscribers.'),
			'type' => 'select',
			'value' => array(0 => $this->l('All customers'), 2 => $this->l('Subscribers'), 1 => $this->l('Non-subscribers')),
			'value_default' => 2
			),
		'OPTIN' => array(
			'title' => $this->l('Opted-in subscribers'),
			'desc' => $this->l('Filter opted-in subscribers.'),
			'type' => 'select',
			'value' => array(0 => $this->l('All customers'), 2 => $this->l('Subscribers'), 1 => $this->l('Non-subscribers')),
			'value_default' => 0
			),
		);
				
		$fields_form_1 = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Export Newsletter Subscribers'),
					'icon' => 'icon-envelope'
				),
				'desc' => array(
					array('text' => $this->l('Generate a .CSV file based on BlockNewsletter subscribers data. Only subscribers without an account on the shop will be exported.'))
				),
			'submit' => array(
				'title' => $this->l('Export .CSV file'),
				'class' => 'btn btn-default',
				'name' => 'submitExport',
				)
			),
		);
		
		$fields_form_2 = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Export customers'),
					'icon' => 'icon-envelope'
				),
				'input' => array(
					array(
						'type' => 'select',
						'label' => $this->l('Customers\' country :'),
						'desc' => $this->l('Operate a filter on customers\' country.'),
						'name' => 'COUNTRY',
						'required' => false,
						'default_value' => (int)$this->context->country->id,
						'options' => array(
							'query' => $countries,
							'id' => 'id',
							'name' => 'name',
						)
					),
					array(
						'type' => 'select',
						'label' => $this->l('Newsletter subscribers :'),
						'desc' => $this->l('Filter newsletter subscribers.'),
						'name' => 'SUSCRIBERS',
						'required' => false,
						'default_value' => (int)$this->context->country->id,
						'options' => array(
							'query' => array(array('id' => 0, 'name' => $this->l('All customers')), array('id' => 2, 'name' => $this->l('Subscribers')), array('id' => 1, 'name' => $this->l('Non-subscribers'))),
							'id' => 'id',
							'name' => 'name',
						)
					),
					array(
						'type' => 'select',
						'label' => $this->l('Opted-in subscribers :'),
						'desc' => $this->l('Filter opted-in subscribers.'),
						'name' => 'OPTIN',
						'required' => false,
						'default_value' => (int)$this->context->country->id,
						'options' => array(
							'query' => array(array('id' => 0, 'name' => $this->l('All customers')), array('id' => 2, 'name' => $this->l('Subscribers')), array('id' => 1, 'name' => $this->l('Non-subscribers'))),
							'id' => 'id',
							'name' => 'name',
						)
					),
					array(
						'type' => 'hidden',
						'name' => 'action',
						)
				),
			'submit' => array(
				'title' => $this->l('Export .CSV file'),
				'class' => 'btn btn-default',
				'name' => 'submitExport',
				)
			),
		);
		
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table =  $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->id = (int)Tools::getValue('id_carrier');
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'btnSubmit';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form_1, $fields_form_2));
	}
	
	public function getConfigFieldsValues()
	{
		return array(
			'COUNTRY' => Tools::getValue('COUNTRY'),
			'SUSCRIBERS' => Tools::getValue('SUSCRIBERS'),
			'OPTIN' => Tools::getValue('OPTIN'),
			'action' => 'customers',
		);
	}

}

