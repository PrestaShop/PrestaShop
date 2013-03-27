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

class AdminHomeControllerCore extends AdminController
{
	const TIPS_TIMEOUT = 5;

	protected function _displayOptimizationTips()
	{
		$link = $this->context->link;

		$content = '';
		$rewrite = 0;
		if (Configuration::get('PS_REWRITING_SETTINGS'))
		{
			$rewrite = 2;
			if (!file_exists(dirname(__FILE__).'/../../.htaccess'))
				$rewrite = 1;
			else
			{
				$stat = stat(dirname(__FILE__).'/../../.htaccess');
				if (strtotime(Db::getInstance()->getValue('SELECT date_upd FROM '._DB_PREFIX_.'configuration WHERE name = "PS_REWRITING_SETTINGS"')) > $stat['mtime'])
					$rewrite = 0;
			}
		}

		$htaccessAfterUpdate = 2;
		$htaccessOptimized = (Configuration::get('PS_HTACCESS_CACHE_CONTROL') ? 2 : 0);
		if (!file_exists(dirname(__FILE__).'/../../.htaccess'))
		{
			if (Configuration::get('PS_HTACCESS_CACHE_CONTROL'))
				$htaccessOptimized = 1;
		}
		else
		{
			$stat = stat(dirname(__FILE__).'/../../.htaccess');
			$dateUpdHtaccess = Db::getInstance()->getValue('SELECT date_upd FROM '._DB_PREFIX_.'configuration WHERE name = "PS_HTACCESS_CACHE_CONTROL"');
			if (Configuration::get('PS_HTACCESS_CACHE_CONTROL') && strtotime($dateUpdHtaccess) > $stat['mtime'])
				$htaccessOptimized = 1;

			$dateUpdate = Configuration::get('PS_LAST_SHOP_UPDATE');
			if ($dateUpdate && strtotime($dateUpdate) > $stat['mtime'])
				$htaccessAfterUpdate = 0;
		}
		$indexRebuiltAfterUpdate = 0;
		$needRebuild = Configuration::get('PS_NEED_REBUILD_INDEX');
		if ($needRebuild != '0');
			$indexRebuiltAfterUpdate = 2;

		$smartyOptimized = 0;
		if (Configuration::get('PS_SMARTY_FORCE_COMPILE') == _PS_SMARTY_NO_COMPILE_)
			++$smartyOptimized;
		if (Configuration::get('PS_SMARTY_CACHE'))
			++$smartyOptimized;

		$cccOptimized = Configuration::get('PS_CSS_THEME_CACHE');
		$cccOptimized += Configuration::get('PS_JS_THEME_CACHE');
		$cccOptimized += Configuration::get('PS_HTML_THEME_COMPRESSION');
		$cccOptimized += Configuration::get('PS_JS_HTML_THEME_COMPRESSION');
		if ($cccOptimized == 4)
			$cccOptimized = 2;
		else
			$cccOptimized = 1;

		$shopEnabled = (Configuration::get('PS_SHOP_ENABLE') ? 2 : 1);

		$lights = array(
		0 => array('image'=>'cross.png','color'=>'red'),
		1 => array('image'=>'error.png','color'=>'orange'),
		2 => array('image'=>'tick.png','color'=>'green'));

		$opti_list = array();
		if ($rewrite + $htaccessOptimized + $smartyOptimized + $cccOptimized + $shopEnabled + $htaccessAfterUpdate + $indexRebuiltAfterUpdate != 14)
		{
			$opti_list[] = array(
				'title' => $this->l('URL rewriting'),
				'href' => $link->getAdminLink('AdminMeta'),
				'color' => $lights[$rewrite]['color'],
				'image' => $lights[$rewrite]['image'],
			);

			$opti_list[] = array(
				'title' => $this->l('Browser cache & compression'),
				'href' => $link->getAdminLink('AdminPerformance'),
				'color' => $lights[$htaccessOptimized]['color'],
				'image' => $lights[$htaccessOptimized]['image'],
			);

			$opti_list[] = array(
				'title' => $this->l('Smarty optimization'),
				'href' => $link->getAdminLink('AdminPerformance'),
				'color' => $lights[$smartyOptimized]['color'],
				'image' => $lights[$smartyOptimized]['image'],
			);

			$opti_list[] = array(
				'title' => $this->l('Combine, Compress & Cache'),
				'href' => $link->getAdminLink('AdminPerformance'),
				'color' => $lights[$cccOptimized]['color'],
				'image' => $lights[$cccOptimized]['image'],
			);

			$opti_list[] = array(
				'title' => $this->l('Shop enabled'),
				'href' => $link->getAdminLink('AdminMaintenance'),
				'color' => $lights[$shopEnabled]['color'],
				'image' => $lights[$shopEnabled]['image'],
			);

			$opti_list[] = array(
				'title' => $this->l('Index rebuilt after update'),
				'href' => $link->getAdminLink('AdminSearchConf'),
				'color' => $lights[$indexRebuiltAfterUpdate]['color'],
				'image' => $lights[$indexRebuiltAfterUpdate]['image'],
			);

			$opti_list[] = array(
				'title' => $this->l('.htaccess file up-to-date'),
				'href' => $link->getAdminLink('AdminMeta'),
				'color' => $lights[$htaccessAfterUpdate]['color'],
				'image' => $lights[$htaccessAfterUpdate]['image'],
			);
		}
		$this->context->smarty->assign(array(
			'opti_list' => $opti_list,
			'content' => $content,
			'hide_tips' => Configuration::get('PS_HIDE_OPTIMIZATION_TIPS'))
		);

		$template = $this->createTemplate('optimizationTips.tpl');
		return $template->fetch();
	}

	public function setMedia()
	{
		parent::setMedia();
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)
			$this->addJqueryPlugin('excanvas');
		$this->addJqueryPlugin('flot');
		$this->addJqueryPlugin('fancybox');
	}

	protected function warnDomainName()
	{
		if (Shop::isFeatureActive())
			return;

		$shop = Context::getContext()->shop;
		if ($_SERVER['HTTP_HOST'] != $shop->domain && $_SERVER['HTTP_HOST'] != $shop->domain_ssl && Tools::getValue('ajax') == false)
			$this->displayWarning($this->l('You are currently connected under the following domain name:').' <span style="color: #CC0000;">'.$_SERVER['HTTP_HOST'].'</span><br />'.
			$this->l('This is different from the main shop domain name set in the "Multistore" page under the "Advanced Parameters" menu:').' <span style="color: #CC0000;">'.$shop->domain.'</span><br />
			<a href="index.php?controller=AdminMeta&token='.Tools::getAdminTokenLite('AdminMeta').'#conf_id_domain">'.
			$this->l('Click here if you want to modify your main shop\'s domain name.').'</a>');
	}

	protected function getQuickLinks()
	{
		$quick_links = array();
		
		$profile_access = Profile::getProfileAccesses($this->context->employee->id_profile);
		if ($profile_access[(int)Tab::getIdFromClassName('AdminStats')]['view'])
			$quick_links['first'] = array(
				'href' => $this->context->link->getAdminLink('AdminStats').'&amp;module=statsbestproducts',
				'title' => $this->l('Recently sold products.'),
				'description' => $this->l('Create a new category and organize your catalog.'),
			);
		
		if ($profile_access[(int)Tab::getIdFromClassName('AdminOrders')]['add'])
			$quick_links['second'] = array(
				'href' => $this->context->link->getAdminLink('AdminOrders').'&amp;addorder',
				'title' => $this->l('New order'),
				'description' => $this->l('Fill your catalog with new products.'),
			);
		
		if ($profile_access[(int)Tab::getIdFromClassName('AdminSpecificPriceRule')]['add'])
			$quick_links['third'] = array(
				'href' => $this->context->link->getAdminLink('AdminSpecificPriceRule').'&amp;addspecific_price_rule',
				'title' => $this->l('New price rule for catalog'),
				'description' => $this->l('Monitor your activity with a thorough analysis of your shop.'),
			);
		
		if ($profile_access[(int)Tab::getIdFromClassName('AdminProducts')]['add'])
			$quick_links['fourth'] = array(
				'href' => $this->context->link->getAdminLink('AdminProducts').'&amp;addproduct',
				'title' => $this->l('New product'),
				'description' => $this->l('Add a new employee account and discharge a part of your duties as shop owner.'),
			);

		if ($profile_access[(int)Tab::getIdFromClassName('AdminModules')]['view'])
			$quick_links['fifth'] = array(
				'href' => $this->context->link->getAdminLink('AdminModules'),
				'title' => $this->l('New module'),
				'description' => $this->l('Configure your modules'),
			);
			
		if ($profile_access[(int)Tab::getIdFromClassName('AdminCartRules')]['add'])
			$quick_links['sixth'] = array(
				'href' => $this->context->link->getAdminLink('AdminCartRules').'&amp;addcart_rule',
				'title' => $this->l('New price rule for cart'),
				'description' => $this->l('Add new cart rule'),
			);
			
		if ($profile_access[(int)Tab::getIdFromClassName('AdminCmsContent')]['add'])
			$quick_links['seventh'] = array(
				'href' => $this->context->link->getAdminLink('AdminCmsContent').'&amp;addcms',
				'title' => $this->l('New CMS page'),
				'description' => $this->l('Add a new CMS page.'),
			);

		if ($profile_access[(int)Tab::getIdFromClassName('AdminCarts')]['view'])
			$quick_links['eighth'] = array(
				'href' => $this->context->link->getAdminLink('AdminCarts').'&amp;id_cart',
				'title' => $this->l('Abandoned shopping carts'),
				'description' => $this->l('View your customer\'s carts.'),
			);
		return $quick_links;
	}

	public function getCustomersService()
	{
			$all = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'customer_thread');
			$unread = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'customer_thread` WHERE `status` = "open"');
			$pending = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'customer_thread` WHERE `status` LIKE "%pending%"');
			$close = $all - ($unread + $pending);
			$content = '
			<div class="table_info" id="table_info_last">
				<h5><a href="index.php?tab=AdminCustomerThreads&token='.Tools::getAdminTokenLite('AdminCustomerThreads').'">'.$this->l('View more').'</a> '.$this->l('Customer service').'</h5>
				<table class="table_info_details" style="width:100%;">
					<colgroup>
						<col width="">
						<col width="80px">
					</colgroup>
					<tr class="tr_odd">
						<td class="td_align_left">
						'.$this->l('Unread threads').'
						</td>
						<td>
							'.$unread.'
						</td>
					</tr>
					<tr>
						<td class="td_align_left">
							'.$this->l('Pending threads').'
						</td>
						<td>
							'.$pending.'
						</td>
					</tr>
					<tr class="tr_odd">
						<td class="td_align_left">
							'.$this->l('Closed threads').'
						</td>
						<td>
							'.$close.'
						</td>
					</tr>
					<tr>
						<td class="td_align_left">
							'.$this->l('Total threads').'
						</td>
						<td>
							'.$all.'
						</td>
					</tr>
				</table>
			</div>';
			return $content;
	}

	public function getMonthlyStatistics()
	{
		$currency = Tools::setCurrency($this->context->cookie);
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT IFNULL(SUM(`total_paid_real` / conversion_rate), "0") as total_sales, COUNT(*) as total_orders
			FROM `'._DB_PREFIX_.'orders`
			WHERE valid = 1
				AND `invoice_date` BETWEEN \''.date('Y-m').'-01 00:00:00\' AND \''.date('Y-m').'-31 23:59:59\'
				'.Shop::addSqlRestriction(Shop::SHARE_ORDER).'
		');

		$result2 = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT COUNT(`id_customer`) AS total_registrations
			FROM `'._DB_PREFIX_.'customer` c
			WHERE c.`date_add` BETWEEN \''.date('Y-m').'-01 00:00:00\' AND \''.date('Y-m').'-31 23:59:59\'
				'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER).'
		');

		$result3 = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT SUM(pv.`counter`) AS total_viewed
			FROM `'._DB_PREFIX_.'page_viewed` pv
			LEFT JOIN `'._DB_PREFIX_.'date_range` dr ON pv.`id_date_range` = dr.`id_date_range`
			LEFT JOIN `'._DB_PREFIX_.'page` p ON pv.`id_page` = p.`id_page`
			LEFT JOIN `'._DB_PREFIX_.'page_type` pt ON pt.`id_page_type` = p.`id_page_type`
			WHERE pt.`name` = \'product\'
				AND dr.`time_start` BETWEEN \''.date('Y-m').'-01 00:00:00\' AND \''.date('Y-m').'-31 23:59:59\'
				AND dr.`time_end` BETWEEN \''.date('Y-m').'-01 00:00:00\' AND \''.date('Y-m').'-31 23:59:59\'
				'.Shop::addSqlRestriction().'
		');

		$results = array_merge($result, array_merge($result2, $result3));

		$content = '<div class="table_info">
			<h5><a href="index.php?tab=AdminStats&token='.Tools::getAdminTokenLite('AdminStats').'">'.$this->l('View more').'</a> '.$this->l('This month\'s activity').' </h5>
			<table class="table_info_details" style="width:100%;">
					<colgroup>
						<col width="">
						<col width="80px">
					</colgroup>
				<tr class="tr_odd">
					<td class="td_align_left">
					'.$this->l('Sales').'
					</td>
					<td>
						'
						.Tools::displayPrice($results['total_sales'], $currency)
						.'
					</td>
				</tr>
				<tr>
					<td class="td_align_left">
						'.$this->l('Total registrations').'
					</td>
					<td>
						'.(int)($results['total_registrations']).'
					</td>
				</tr>
				<tr class="tr_odd">
					<td class="td_align_left">
						'.$this->l('Total orders').'
					</td>
					<td>
						'.(int)($results['total_orders']).'
					</td>
				</tr>
				<tr>
					<td class="td_align_left">
						'.$this->l('Product pages viewed').'
					</td>
					<td>
						'.(int)($results['total_viewed']).'
					</td>
				</tr>
			</table>
		</div>';
		return $content;
	}

	public function getStatsSales()
	{
		$content = '<div id="table_info_large">
				<h5><a href="index.php?tab=AdminStats&token='.Tools::getAdminTokenLite('AdminStats').'">'.$this->l('View more').'</a> <strong>'.$this->l('Statistics').'</strong> / '.$this->l('This week\'s sales').'</h5>
				<div id="stat_google">';

		$chart = new Chart();
		$chart->getCurve(1)->setType('bars');
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT total_paid / conversion_rate as total_converted, left(invoice_date, 10) as invoice_date
			FROM '._DB_PREFIX_.'orders o
			WHERE valid = 1
			AND total_paid > 0
			AND invoice_date BETWEEN \''.date('Y-m-d', strtotime('-7 DAYS', time())).' 00:00:00\' AND \''.date('Y-m-d H:i:s').'\'
			'.Shop::addSqlRestriction(Shop::SHARE_ORDER).'
		');
		foreach ($result as $row)
			$chart->getCurve(1)->setPoint(strtotime($row['invoice_date'].' 02:00:00'), $row['total_converted']);
		$chart->setSize(580, 170);
		$chart->setTimeMode(strtotime('-7 DAYS', time()), time(), 'd');
		$currency = Tools::setCurrency($this->context->cookie);
		$chart->getCurve(1)->setLabel($this->l('Sales + Tax').' ('.strtoupper($currency->iso_code).')');

		$content .= $chart->fetch();
		$content .= '	</div>
		</div>';
		return $content;
	}

	public function getLastOrders()
	{
		$content = '
			<table cellpadding="0" cellspacing="0" id="table_customer" style="width:100%;">
				<thead>
					<tr>
						<th class="order_id"><span class="first">'.$this->l('ID').'</span></th>
						<th class="order_customer"><span>'.$this->l('Customer Name').'</span></th>
						<th class="order_status"><span>'.$this->l('Status').'</span></th>
						<th class="order_total"><span>'.$this->l('Total').'</span></th>
						<th class="order_action"><span class="last">'.$this->l('Action').'</span></th>
					<tr>
				</thead>
				<tbody>';

		$orders = Order::getOrdersWithInformations(10);
		$i = 0;
		foreach ($orders as $order)
		{
			$currency = Currency::getCurrency((int)$order['id_currency']);
			$content .= '
					<tr'.($i % 2 ? ' id="order_line1"' : '').'>
						<td class="order_td_first order_id">'.(int)$order['id_order'].'</td>
						<td class="order_customer">'.Tools::htmlentitiesUTF8($order['firstname']).' '.Tools::htmlentitiesUTF8($order['lastname']).'</td>
						<td class="order_status">'.Tools::htmlentitiesUTF8($order['state_name']).'</td>
						<td class="order_total">'.Tools::displayPrice((float)$order['total_paid'], $currency).'</td>
						<td class="order_action">
							<a href="index.php?tab=AdminOrders&id_order='.(int)$order['id_order'].'&vieworder&token='.Tools::getAdminTokenLite('AdminOrders').'" title="'.$this->l('Details').'"><img src="../img/admin/details.gif" alt="'.$this->l('See').'" /></a>
						</td>
					</tr>
				';
			$i++;
		}

		$content .= '
				</tbody>
			</table>
	';
		return $content;
	}

	public function ajaxProcessRefreshCheckVersion()
	{
		$upgrade = new Upgrader(true);
		if ($upgrade)
		{
			$json['status'] = 'ok';
			$json['upgrade']['need_upgrade'] = $upgrade->need_upgrade;
			$json['upgrade']['link'] = $upgrade->link;
			$json['upgrade']['version_name'] = $upgrade->version_name;
			$this->content = Tools::jsonEncode($json);
		}
		else
			$this->content = '{"status":"error"}';
	}
	public function ajaxProcessHideOptimizationTips()
	{
		if (Configuration::updateValue('PS_HIDE_OPTIMIZATION_TIPS', 1))
		{
			$result['result'] = 'ok';
			$result['msg'] = $this->l('Optimization Tips will be hidden by default.');
		}
		else
		{
			$result['result'] = 'error';
			$result['msg'] = $this->l('an error occurred');
		}
		$this->content = Tools::jsonEncode($result);

	}

	public function ajaxProcessGetAdminHomeElement()
	{
		$this->content_only = true;
		$result = array();
		$content = '';

		$protocol = Tools::usingSecureMode() ? 'https' : 'http';
		$isoUser = Context::getContext()->language->iso_code;
		$isoCountry = Context::getContext()->country->iso_code;
		$stream_context = @stream_context_create(array('http' => array('method'=> 'GET', 'timeout' => 2)));

		// SCREENCAST
		$result['screencast'] = 'OK';


		// PREACTIVATION
		$result['partner_preactivation'] = $this->getBlockPartners();

		// DISCOVER PRESTASHOP
		$result['discover_prestashop'] = '<div id="block_tips">'.$this->getBlockDiscover().'</div>';

		$result['discover_prestashop'] .= '<div class="row-news"><div id="block_discover"><iframe frameborder="no" style="margin: 0px; padding: 0px; width: 100%; height:300px; overflow:hidden;" src="'.$protocol.'://api.prestashop.com/rss2/news2.php?v='._PS_VERSION_.'&lang='.$isoUser.'"></iframe></div>';

		// SHOW TIPS OF THE DAY
		$content = @file_get_contents($protocol.'://api.prestashop.com/partner/tipsoftheday/?protocol='.$protocol.'&iso_country='.$isoCountry.'&iso_lang='.Tools::strtolower($isoUser), false, $stream_context);
		$content = explode('|', $content);
		if ($content[0] == 'OK' && Validate::isCleanHtml($content[1]))
			$result['discover_prestashop'] .= '<div id="block_partner_tips">'.$content[1].'</div></div>';

		$this->content = Tools::jsonEncode($result);
	}

	public function ajaxProcessHideScreencast()
	{
		if ($employee = new Employee((int)Tools::getValue('id_employee')))
		{
			$employee->bo_show_screencast = 0;
			if ($employee->save())
				$this->content = '{"status":"ok"}';
			else
				$this->content = '{"status":"error","msg":"not saved"}';
		}
		else
			$this->content = '{"status":"error", "msg":"employee does not exists"}';
	}

	public function getBlockPartners()
	{
		// Init var
		$return = '';
		$protocol = Tools::getShopProtocol();
		$isoCountry = Context::getContext()->country->iso_code;
		$isoUser = Context::getContext()->language->iso_code;

		// Refresh preactivation xml file if needed
		if (is_writable('../config/xml/') && (!file_exists('../config/xml/preactivation.xml') || (time() - filemtime('../config/xml/preactivation.xml')) > 86400))
		{
			$stream_context = @stream_context_create(array('http' => array('method'=> 'GET', 'timeout' => AdminHomeController::TIPS_TIMEOUT)));
			$content = Tools::file_get_contents('http://api.prestashop.com/partner/premium/get_partners.php?protocol='.$protocol.'&iso_country='.Tools::strtoupper($isoCountry).'&iso_lang='.Tools::strtolower($isoUser).'&ps_version='._PS_VERSION_.'&ps_creation='._PS_CREATION_DATE_.'&host='.urlencode($_SERVER['HTTP_HOST']).'&email='.urlencode(Configuration::get('PS_SHOP_EMAIL')), false, $stream_context);
			@unlink('../config/xml/preactivation.xml');
			file_put_contents('../config/xml/preactivation.xml', $content);
		}

		$count = 0;
		libxml_use_internal_errors(true);
		// If preactivation xml file exists, we load it
		if (file_exists('../config/xml/preactivation.xml') && filesize('../config/xml/preactivation.xml') > 0 && $preactivation = simplexml_load_file('../config/xml/preactivation.xml'))
			foreach ($preactivation->partner as $partner)
			{
				// Cache the logo
				if (!file_exists('../img/tmp/preactivation_'.htmlentities((string)$partner->module).'.png'))
				{
					$logo = @Tools::file_get_contents(htmlentities((string)$partner->logo));
					if (sizeof($logo) > 0)
						file_put_contents('../img/tmp/preactivation_'.htmlentities((string)$partner->module).'.png', $logo);
				}

				// Check if module is not already installed and configured
				$display = 0;
				if (file_exists('../config/xml/default_country_modules_list.xml') && filesize('../config/xml/default_country_modules_list.xml') > 10)
					foreach ($partner->checkconfiguration->key as $key)
						if (Configuration::get(pSQL((string)$key)) == '')
							$display = 1;
			
				// Display the module
				if ($display == 1 && $count < 2)
				{
					$label_final = '';
					foreach ($partner->labels->label as $label)
						if (empty($label_final) || (string)$label->attributes()->iso == $isoUser)
							$label_final = (string)$label;

					$optional_final = '';
					if (isset($partner->optionals))
						foreach ($partner->optionals->optional as $optional)
							if (empty($optional_final) && (string)$optional->attributes()->iso == $isoUser)
								$optional_final = (string)$optional;

					$link = 'index.php?controller=adminmodules&install='.htmlentities((string)$partner->module).'&token='.Tools::getAdminTokenLite('AdminModules').'&module_name='.htmlentities((string)$partner->module).'&redirect=config';
					$return .= '<div style="width:46.5%;min-height:85px;border:1px solid #cccccc;background-color:white;padding-left:5px;padding-right:5px;'.(empty($return) ? 'float:left' : 'float:right').'">
						<p align="center">
							<a href="'.$link.'" class="preactivationLink" rel="'.htmlentities((string)$partner->module).'"><img src="../img/tmp/preactivation_'.htmlentities((string)$partner->module).'.png" alt="'.htmlentities((string)$partner->name).'" border="0" /></a><br />
							<b><a href="'.$link.'" class="preactivationLink" rel="'.htmlentities((string)$partner->module).'">'.htmlentities(utf8_decode((string)$label_final)).'</a></b>
							'.(($optional_final != '') ? '<a href="'.$link.'" class="preactivationLink" rel="'.htmlentities((string)$partner->module).'"><img src="'.htmlentities((string)$optional_final).'" /></a>' : '').'
						</p>
					</div>';
					$count++;
				}
			}
		libxml_clear_errors();

		if (!empty($return))
			$return .= '<br clear="left" />
			<script>
				$(".preactivationLink").click(function() {
					var module = $(this).attr("rel");
					var ajaxCurrentIndex = "'.str_replace('index', 'ajax-tab', self::$currentIndex).'";
					try
					{
						resAjax = $.ajax({
								type:"POST",
								url : ajaxCurrentIndex,
								async: true,
								data : {
									ajax : "1",
									controller : "AdminHome",
									action : "savePreactivationRequest",
									module : module,
								},
								success : function(data)
								{
								},
								error: function(res,textStatus,jqXHR)
								{
								}
						});
					}
					catch(e){}
				});
			</script>';

		return $return;
	}

	public function ajaxProcessSavePreactivationRequest()
	{
		$isoUser = Context::getContext()->language->iso_code;
		$isoCountry = Context::getContext()->country->iso_code;
		$employee = new Employee((int)Context::getContext()->cookie->id_employee);
		$firstname = $employee->firstname;
		$lastname = $employee->lastname;
		$email = $employee->email;
		$return = @Tools::file_get_contents('http://api.prestashop.com/partner/premium/set_request.php?iso_country='.strtoupper($isoCountry).'&iso_lang='.strtolower($isoUser).'&host='.urlencode($_SERVER['HTTP_HOST']).'&ps_version='._PS_VERSION_.'&ps_creation='._PS_CREATION_DATE_.'&partner='.htmlentities(Tools::getValue('module')).'&shop='.urlencode(Configuration::get('PS_SHOP_NAME')).'&email='.urlencode($email).'&firstname='.urlencode($firstname).'&lastname='.urlencode($lastname).'&type=home');
		die($return);
	}

	public function getBlockDiscover()
	{
		$stream_context = @stream_context_create(array('http' => array('method'=> 'GET', 'timeout' => AdminHomeController::TIPS_TIMEOUT)));
		$content = '';

		$protocol = Tools::usingSecureMode() ? 'https' : 'http';
		$isoUser = Context::getContext()->language->iso_code;
		$isoCountry = Context::getContext()->country->iso_code;

		$content = @Tools::file_get_contents($protocol.'://api.prestashop.com/partner/prestashop/prestashop-link.php?iso_country='.$isoCountry.'&iso_lang='.Tools::strtolower($isoUser).'&id_lang='.(int)Context::getContext()->language->id.'&ps_version='.urlencode(_PS_VERSION_), false, $stream_context);

		if (!$content)
			return ''; // NOK
		else
		{
			if (strpos($content, '|') !== false)
				$content = explode('|', $content);
			if ($content[0] == 'OK' && Validate::isCleanHtml($content[1]))
				return $content[1];
			else
				return ''; // NOK
		}
	}
	public function initContent()
	{
		parent::initContent();
		$smarty = $this->context->smarty;

		$this->warnDomainName();

		$protocol = Tools::usingSecureMode()?'https':'http';
		$smarty->assign('protocol', $protocol);
		$isoUser = $this->context->language->iso_code;
		$smarty->assign('isoUser', $isoUser);
		$upgrade = null;
		$tpl_vars['refresh_check_version'] = 0;
		if (@ini_get('allow_url_fopen'))
		{
			$upgrade = new Upgrader(true);
			// if this information is outdated, the version will be checked after page loading
			if (Configuration::get('PS_LAST_VERSION_CHECK') < time() - (3600 * Upgrader::DEFAULT_CHECK_VERSION_DELAY_HOURS))
				$tpl_vars['refresh_check_version'] = 1;
		}
		
		if (!$this->isFresh(Module::CACHE_FILE_DEFAULT_COUNTRY_MODULES_LIST, 86400))
			file_put_contents(_PS_ROOT_DIR_.Module::CACHE_FILE_DEFAULT_COUNTRY_MODULES_LIST, Tools::addonsRequest('native'));

		$tpl_vars['upgrade'] = $upgrade;

		if ($this->context->employee->bo_show_screencast)
			$tpl_vars['employee_token'] = Tools::getAdminTokenLite('AdminEmployees');

		$tpl_vars['employee'] = $this->context->employee;
		$tpl_vars['quick_links'] = $this->getQuickLinks();
		$tpl_vars['monthly_statistics'] = $this->getMonthlyStatistics();
		$tpl_vars['customers_service'] = $this->getCustomersService();
		$tpl_vars['stats_sales'] = $this->getStatsSales();
		$tpl_vars['last_orders'] = $this->getLastOrders();
		$tpl_vars['tips_optimization'] = $this->_displayOptimizationTips();

		$smarty->assign($tpl_vars);
	}
}
