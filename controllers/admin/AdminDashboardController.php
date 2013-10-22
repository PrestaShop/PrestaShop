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

class AdminDashboardControllerCore extends AdminController
{
	public function __construct()
	{
		$this->bootstrap = true;
		$this->display = 'view';

		parent::__construct();
		
		if (Tools::isSubmit('profitability_conf') || Tools::isSubmit('submitOptionsconfiguration'))
			$this->fields_options = $this->getOptionFields();
	}

	public function setMedia()
	{
		$admin_webpath = str_ireplace(_PS_ROOT_DIR_, '', _PS_ADMIN_DIR_);
		$admin_webpath = preg_replace('/^'.preg_quote(DIRECTORY_SEPARATOR, '/').'/', '', $admin_webpath);
		parent::setMedia();
		$this->addJqueryUI('ui.datepicker');
		$this->addJS(array(
			_PS_JS_DIR_.'/vendor/d3.js',
			__PS_BASE_URI__.$admin_webpath.'/themes/'.$this->bo_theme.'/js/vendor/nv.d3.min.js',
			_PS_JS_DIR_.'/admin-dashboard.js',
		));
		$this->addCSS(array(
			__PS_BASE_URI__.$admin_webpath.'/themes/'.$this->bo_theme.'/css/nv.d3.css',
		));
	}

	public function initPageHeaderToolbar()
	{
		parent::initPageHeaderToolbar();

		$this->page_header_toolbar_title = $this->l('Dashboard');
		$this->page_header_toolbar_btn = array();
	}
	
	protected function getOptionFields()
	{
		$forms = array();
		$currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
		$carriers = Carrier::getCarriers($this->context->language->id, true);
		$modules = Module::getModulesOnDisk(true);
		
		$forms = array(
			'payment' => array('title' => $this->l('Average bank fees per payment method')),
			'carriers' => array('title' => $this->l('Average shipping fees per shipping method')),
			'other' => array('title' => $this->l('Other settings')),
			'expenses' => array('title' => $this->l('Other expenses'))
		);
		foreach ($forms as &$form)
		{
			$form['icon'] = 'tab-preferences';
			$form['fields'] = array();
			$form['submit'] = array('title' => $this->l('Save'), 'class' => 'button');
		}

		foreach ($modules as $module)
			if ($module->tab == 'payments_gateways' && $module->id)
			{
				$forms['payment']['fields']['CONF_'.strtoupper($module->name).'_FIXED'] = array(
					'title' => $module->displayName,
					'desc' => sprintf($this->l('Choose a fixed fee for each order placed in %s with %s.'), $currency->iso_code, $module->displayName),
					'validation' => 'isPrice',
					'cast' => 'floatval',
					'type' => 'text',
					'default' => '0',
					'suffix' => $currency->iso_code
				);
				$forms['payment']['fields']['CONF_'.strtoupper($module->name).'_VAR'] = array(
					'title' => $module->displayName,
					'desc' => sprintf($this->l('Choose a variable fee for each order placed in %s with %s. It will be applied on the total paid with taxes.'), $currency->iso_code, $module->displayName),
					'validation' => 'isPercentage',
					'cast' => 'floatval',
					'type' => 'text',
					'default' => '0',
					'suffix' => '%'
				);
				
				if (Currency::isMultiCurrencyActivated())
				{
					$forms['payment']['fields']['CONF_'.strtoupper($module->name).'_FIXED_FOREIGN'] = array(
						'title' => $module->displayName,
						'desc' => sprintf($this->l('Choose a fixed fee for each order placed with a foreign currency with %s.'), $module->displayName),
						'validation' => 'isPrice',
						'cast' => 'floatval',
						'type' => 'text',
						'default' => '0',
						'suffix' => $currency->iso_code
					);
					$forms['payment']['fields']['CONF_'.strtoupper($module->name).'_VAR_FOREIGN'] = array(
						'title' => $module->displayName,
						'desc' => sprintf($this->l('Choose a variable fee for each order placed with a foreign currency with %s. It will be applied on the total paid with taxes.'), $module->displayName),
						'validation' => 'isPercentage',
						'cast' => 'floatval',
						'type' => 'text',
						'default' => '0',
						'suffix' => '%'
					);
				}
			}

		foreach ($carriers as $carrier)
		{
			$forms['carriers']['fields']['CONF_'.strtoupper($carrier['id_reference']).'_SHIP'] = array(
				'title' => $carrier['name'],
				'desc' => sprintf($this->l('%% of what you charged the customer for domestic delivery with %s.'), $carrier['name']),
				'validation' => 'isPercentage',
				'cast' => 'floatval',
				'type' => 'text',
				'default' => '0',
				'suffix' => '%'
			);
			$forms['carriers']['fields']['CONF_'.strtoupper($carrier['id_reference']).'_SHIP_OVERSEAS'] = array(
				'title' => $carrier['name'],
				'desc' => sprintf($this->l('%% of what you charged the customer for overseas delivery with %s.'), $carrier['name']),
				'validation' => 'isPercentage',
				'cast' => 'floatval',
				'type' => 'text',
				'default' => '0',
				'suffix' => '%'
			);
		}

		$forms['other']['fields']['CONF_AVERAGE_PRODUCT_MARGIN'] = array(
			'title' => $this->l('Average gross margin (Selling price / Buying price)'),
			'desc' => $this->l('Only used if you do not specify your buying price for each product.'),
			'validation' => 'isPercentage',
			'cast' => 'intval',
			'type' => 'text',
			'default' => '0',
			'suffix' => '%'
		);

		$forms['other']['fields']['CONF_ORDER_FIXED'] = array(
			'title' => $this->l('Other fee per order'),
			'validation' => 'isPrice',
			'cast' => 'floatval',
			'type' => 'text',
			'default' => '0',
			'suffix' => $currency->iso_code
		);

		$expense_types = array(
			'hosting' => $this->l('Hosting'),
			'tools' => $this->l('Tools (E-mailing, etc.)'),
			'acounting' => $this->l('Accounting'),
			'development' => $this->l('Development'),
			'marketing' => $this->l('Marketing (Adwords, etc.)'),
			'others' => $this->l('Others')
		);

		foreach ($expense_types as $expense_type => $expense_label)
			$forms['expenses']['fields']['CONF_MONTHLY_'.strtoupper($expense_type)] = array(
				'title' => $expense_label,
				'validation' => 'isPrice',
				'cast' => 'floatval',
				'type' => 'text',
				'default' => '0',
				'suffix' => $currency->iso_code
			);

		return $forms;
	}

	public function renderView()
	{
		if (Tools::isSubmit('profitability_conf'))
			return parent::renderOptions();

		$translations = array(
			'Calendar' => $this->l('Calendar', 'AdminStatsTab'),
			'Day' => $this->l('Day', 'AdminStatsTab'),
			'Month' => $this->l('Month', 'AdminStatsTab'),
			'Year' => $this->l('Year', 'AdminStatsTab'),
			'From' => $this->l('From:', 'AdminStatsTab'),
			'To' => $this->l('To:', 'AdminStatsTab'),
			'Save' => $this->l('Save', 'AdminStatsTab')
		);

		$calendar_helper = new HelperCalendar();

		$calendar_helper->setDateFrom(Tools::getValue('date_from', $this->context->employee->stats_date_from));
		$calendar_helper->setDateTo(Tools::getValue('date_to', $this->context->employee->stats_date_to));

		$stats_compare_from = $this->context->employee->stats_compare_from;
		$stats_compare_to = $this->context->employee->stats_compare_to;

		if (is_null($stats_compare_from) || $stats_compare_from == '0000-00-00')
			$stats_compare_from = null;

		if (is_null($stats_compare_to) || $stats_compare_to == '0000-00-00')
			$stats_compare_to = null;

		$calendar_helper->setCompareDateFrom($stats_compare_from);
		$calendar_helper->setCompareDateTo($stats_compare_to);
		$calendar_helper->setCompareOption(Tools::getValue('compare_date_option', $this->context->employee->stats_compare_option));

		$this->tpl_view_vars = array(
			'hookDashboardZoneOne' => Hook::exec('dashboardZoneOne'),
			'hookDashboardZoneTwo' => Hook::exec('dashboardZoneTwo'),
			'translations' => $translations,
			'action' => '#',
			'warning' => $this->getWarningDomainName(),
			'new_version_url' => Tools::getCurrentUrlProtocolPrefix().'api.prestashop.com/version/check_version.php?v='._PS_VERSION_.'&lang='.$this->context->language->iso_code,
			'dashboard_use_push' => Configuration::get('PS_DASHBOARD_USE_PUSH'),
			'calendar' => $calendar_helper->generate(),
			'datepickerFrom' => Tools::getValue('datepickerFrom', $this->context->employee->stats_date_from),
			'datepickerTo' => Tools::getValue('datepickerTo', $this->context->employee->stats_date_to)
		);
		return parent::renderView();
	}

	public function postProcess()
	{
		if (Tools::isSubmit('submitDateRange'))
		{
			$this->context->employee->stats_date_from = Tools::getValue('date_from');
			$this->context->employee->stats_date_to = Tools::getValue('date_to');

			if (Tools::getValue('datepicker_compare'))
			{
				$this->context->employee->stats_compare_from = Tools::getValue('compare_date_from');
				$this->context->employee->stats_compare_to = Tools::getValue('compare_date_to');
				$this->context->employee->stats_compare_option = Tools::getValue('compare_date_option');
			}
			else
			{
				$this->context->employee->stats_compare_from = null;
				$this->context->employee->stats_compare_to = null;
				$this->context->employee->stats_compare_option = HelperCalendar::DEFAULT_COMPARE_OPTION;
			}

			$this->context->employee->update();
		}

		parent::postProcess();
	}
	
	protected function getWarningDomainName()
	{
		$warning = false;
		if (Shop::isFeatureActive())
			return;

		$shop = Context::getContext()->shop;
		if ($_SERVER['HTTP_HOST'] != $shop->domain && $_SERVER['HTTP_HOST'] != $shop->domain_ssl && Tools::getValue('ajax') == false)
		{
			$warning = $this->l('You are currently connected under the following domain name:').' <span style="color: #CC0000;">'.$_SERVER['HTTP_HOST'].'</span><br />';
			if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE'))
				$warning .= sprintf($this->l('This is different from the shop domain name set in the Multistore settings: "%s".'), $shop->domain).'
				'.preg_replace('@{link}(.*){/link}@', '<a href="index.php?controller=AdminShopUrl&id_shop_url='.(int)$shop->id.'&updateshop_url&token='.Tools::getAdminTokenLite('AdminShopUrl').'">$1</a>', $this->l('If this is your main domain, please {link}change it now{/link}.'));
			else
				$warning .= $this->l('This is different from the domain name set in the "SEO & URLs" tab.').'
				'.preg_replace('@{link}(.*){/link}@', '<a href="index.php?controller=AdminMeta&token='.Tools::getAdminTokenLite('AdminMeta').'#conf_id_domain">$1</a>', $this->l('If this is your main domain, please {link}change it now{/link}.'));
		}
		return $warning;
	}
	
	public function ajaxProcessRefreshDashboard()
	{
		$id_module = null;
		if ($module = Tools::getValue('module'))
		{
			$module_obj = Module::getInstanceByName($module);
			if (Validate::isLoadedObject($module_obj))
				$id_module = $module_obj->id;
		}

		$params = array(
			'date_from' => $this->context->employee->stats_date_from,
			'date_to' => $this->context->employee->stats_date_to,
			'compare_from' => $this->context->employee->stats_compare_from,
			'compare_to' => $this->context->employee->stats_compare_to,
			'dashboard_use_push' => (int)Tools::getValue('dashboard_use_push')
		);
		
		die(Tools::jsonEncode(Hook::exec('dashboardData', $params, $id_module, true, true, (int)Tools::getValue('dashboard_use_push'))));
	}
	
	public function ajaxProcessGetBlogRss()
	{
		$return = array('has_errors' => false, 'rss' => array());
		if (!$this->isFresh('/config/xml/blog-'.$this->context->language->iso_code.'.xml', 604800))
			if (!$this->refresh('/config/xml/blog-'.$this->context->language->iso_code.'.xml', 'https://api.prestashop.com/rss/blog/blog-'.$this->context->language->iso_code.'.xml'))
				$return['has_errors'] = true;		
		
		if (!$return['has_errors'])
		{
			$rss = simpleXML_load_file(_PS_ROOT_DIR_.'/config/xml/blog-'.$this->context->language->iso_code.'.xml');
			$articles_limit = 3;
			foreach ($rss->channel->item as $item)
			{
				if ($articles_limit > 0 && Validate::isCleanHtml((string)$item->title) && Validate::isCleanHtml((string)$item->description))
					$return['rss'][] = array(
						'title' => (string)$item->title,
						'short_desc' => substr((string)$item->description, 0, 100).'...',
						'link' => (string)$item->link,
					);
				else
					break;
				$articles_limit --;
			}
		}
		die(Tools::jsonEncode($return));
	}
	
	public function ajaxProcessSaveDashConfig()
	{
		$return = array('has_errors' => false);
		$module = Tools::getValue('module');
		$hook = Tools::getValue('hook');
		$configs = Tools::getValue('configs');

		if (Validate::isModuleName($module) && $module_obj = Module::getInstanceByName($module))
			if (Validate::isLoadedObject($module_obj) && method_exists($module_obj, 'saveDashConfig'))
				$return['has_errors'] = $module_obj->saveDashConfig($configs);
		else if (is_array($configs) && count($configs))
				foreach ($configs as $name => $value)
					if (Validate::isConfigName($name))
						Configuration::updateValue($name, $value);
		
		if (Validate::isHookName($hook) && method_exists($module_obj, $hook))
			$return['widget_html'] = $module_obj->$hook(array());
		
		die(Tools::jsonEncode($return));
	}
}


