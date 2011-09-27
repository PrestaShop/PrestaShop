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

class AdminHomeControllerCore extends AdminController
{
	const TIPS_TIMEOUT = 5;

	private function _displayOptimizationTips()
	{
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
			if (Configuration::get('PS_HTACCESS_CACHE_CONTROL') AND strtotime($dateUpdHtaccess) > $stat['mtime'])
				$htaccessOptimized = 1;
			
			$dateUpdate = Configuration::get('PS_LAST_SHOP_UPDATE');
			if ($dateUpdate AND strtotime($dateUpdate) > $stat['mtime'])
				$htaccessAfterUpdate = 0;
		}
		$indexRebuiltAfterUpdate = 0;
		$needRebuild=Configuration::get('PS_NEED_REBUILD_INDEX');
		if($needRebuild !='0');
			$indexRebuiltAfterUpdate = 2;
		
		$smartyOptimized = 0;
		if (Configuration::get('PS_SMARTY_FORCE_COMPILE') == _PS_SMARTY_NO_COMPILE_)
			++$smartyOptimized;
		if (Configuration::get('PS_SMARTY_CACHE'))
			++$smartyOptimized;

		$cccOptimized = Configuration::get('PS_CSS_THEME_CACHE')
		+ Configuration::get('PS_JS_THEME_CACHE')
		+ Configuration::get('PS_HTML_THEME_COMPRESSION')
		+ Configuration::get('PS_JS_HTML_THEME_COMPRESSION');
		if ($cccOptimized == 4)
			$cccOptimized = 2;
		else
			$cccOptimized = 1;
			
		$shopEnabled = (Configuration::get('PS_SHOP_ENABLE') ? 2 : 1);
		
		$lights = array(
		0 => array('image'=>'error2.png','color'=>'#fbe8e8'), 
		1 => array('image'=>'warn2.png','color'=>'#fffac6'),
		2 => array('image'=>'ok2.png','color'=>'#dffad3'));
		
		
		if ($rewrite + $htaccessOptimized + $smartyOptimized + $cccOptimized + $shopEnabled + $htaccessAfterUpdate + $indexRebuiltAfterUpdate != 14)
		{
			$content .= '
			<div class="admin-box1">
				<h5>'.$this->l('A good beginning...')
				.'
					<span style="float:right">
						<a id="optimizationTipsFold"'.
						(Configuration::get('PS_HIDE_OPTIMIZATION_TIPS')
						?'" href="#"><img alt="v" style="padding-top:0px; padding-right: 5px;" src="../img/admin/down-white.gif" /></a>':'href="?hideOptimizationTips" >
						<img alt="X" style="padding-top:0px; padding-right: 5px;" src="../img/admin/close-white.png" />
						</a>').'</span></h5>';
			$content .= '
			<script type="text/javascript">
			$(document).ready(function(){
				$("#optimizationTipsFold").click(function(e){
					$("#list-optimization-tips").toggle(function(){
						if($("#optimizationTipsFold").children("img").attr("src") == "../img/admin/down-white.gif")
							$("#optimizationTipsFold").children("img").attr("src","../img/admin/close-white.png");
						else
							$("#optimizationTipsFold").children("img").attr("src","../img/admin/down-white.gif");
					});
				})
			});
						</script>
			';
			$content .= '<ul id="list-optimization-tips" class="admin-home-box-list" '
				.(Configuration::get('PS_HIDE_OPTIMIZATION_TIPS')?'style="display:none"':'').'>
				<li style="background-color:'.$lights[$rewrite]['color'].'">
				<img src="../img/admin/'.$lights[$rewrite]['image'].'" class="pico" />
					<a href="index.php?tab=AdminGenerator&token='.Tools::getAdminTokenLite('AdminGenerator').'">'.$this->l('URL rewriting').'</a>
				</li>
				<li style="background-color:'.$lights[$htaccessOptimized]['color'].'">
				<img src="../img/admin/'.$lights[$htaccessOptimized]['image'].'" class="pico" />
				<a href="index.php?tab=AdminGenerator&token='.Tools::getAdminTokenLite('AdminGenerator').'">'.$this->l('Browser cache & compression').'</a>
				</li>
				<li style="background-color:'.$lights[$smartyOptimized]['color'].'">
				<img src="../img/admin/'.$lights[$smartyOptimized]['image'].'" class="pico" />
				<a href="index.php?tab=AdminPerformance&token='.Tools::getAdminTokenLite('AdminPerformance').'">'.$this->l('Smarty optimization').'</a></li>
				<li style="background-color:'.$lights[$cccOptimized]['color'].'">
				<img src="../img/admin/'.$lights[$cccOptimized]['image'].'" class="pico" />
				<a href="index.php?tab=AdminPerformance&token='.Tools::getAdminTokenLite('AdminPerformance').'">'.$this->l('Combine, Compress & Cache').'</a></li>
				<li style="background-color:'.$lights[$shopEnabled]['color'].'">
				<img src="../img/admin/'.$lights[$shopEnabled]['image'].'" class="pico" />
				<a href="index.php?tab=AdminPreferences&token='.Tools::getAdminTokenLite('AdminPreferences').'">'.$this->l('Shop enabled').'</a></li>
				<li style="background-color:'.$lights[$indexRebuiltAfterUpdate]['color'].'">
					<img src="../img/admin/'.$lights[$indexRebuiltAfterUpdate]['image'].'" class="pico" />
		<a href="index.php?tab=AdminSearchConf&token='.Tools::getAdminTokenLite('AdminSearchConf').'">'.$this->l('index rebuilt after update').'</a></li>
				<li style="background-color:'.$lights[$htaccessAfterUpdate]['color'].'">
					<img src="../img/admin/'.$lights[$htaccessAfterUpdate]['image'].'" class="pico" />
		<a href="index.php?tab=AdminGenerator&token='.Tools::getAdminTokenLite('AdminGenerator').'">'.$this->l('.htaccess up-to-date').'</a></li>
					</ul>
			</div>';
		}
		return $content;
	}

	public function setMedia()
	{
		parent::setMedia();
		if (strpos($_SERVER['HTTP_USER_AGENT'],'MSIE') !== false)
			$this->addJS(_PS_JS_DIR_.'jquery/excanvas.min.js');
		$this->addJS(_PS_JS_DIR_.'jquery/jquery.flot.min.js');
	}

	protected function warnDomainName()
	{
		if ($_SERVER['HTTP_HOST'] != Configuration::get('PS_SHOP_DOMAIN') AND $_SERVER['HTTP_HOST'] != Configuration::get('PS_SHOP_DOMAIN_SSL'))
			$this->displayWarning($this->l('Your are currently connected with the following domain name:').' <span style="color: #CC0000;">'.$_SERVER['HTTP_HOST'].'</span><br />'.
			$this->l('This one is different from the main shop domain name set in "Preferences > SEO & URLs":').' <span style="color: #CC0000;">'.Configuration::get('PS_SHOP_DOMAIN').'</span><br />
			<a href="index.php?tab=AdminMeta&token='.Tools::getAdminTokenLite('AdminMeta').'#SEO%20%26%20URLs">'.
			$this->l('Click here if you want to modify the main shop domain name').'</a>');
	}

	private function getQuickLinks()
	{
		$quick_links['first'] = array(
			'href' => 'index.php?controller=AdminCatalog&amp;addcategory&amp;token='.Tools::getAdminTokenLite('AdminCatalog'),
			'title' => $this->l('New category'),
			'description' => $this->l('Create a new category and organize your products.'),
		);

		$quick_links['second'] = array(
			'href' => 'index.php?controller=AdminCatalog&amp;addproduct&amp;token='.Tools::getAdminTokenLite('AdminCatalog'),
			'title' => $this->l('New product'),
			'description' => $this->l('Fill up your catalog with new articles and attributes.'),
		);
	
		$quick_links['third'] = array(
			'href' => 'index.php?controller=AdminStats&amp;addproduct&amp;token='.Tools::getAdminTokenLite('AdminStats'),
			'title' => $this->l('Statistics'),
			'description' => $this->l('Manage your activity with a thorough analysis of your e-shop.'),
		);

		$quick_links['fourth'] = array(
			'href' => 'index.php?controller=AdminEmployee&amp;addproduct&amp;token='.Tools::getAdminTokenLite('AdminEmployee'),
			'title' => $this->l('New employee'),
			'description' => $this->l('Add a new employee account and discharge a part of your duties of shop owner.'),
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
				<h5><a href="index.php?tab=AdminCustomerThreads&token='.Tools::getAdminTokenLite('AdminCustomerThreads').'">'.$this->l('View more').'</a> '.$this->l('Customers service').'</h5>
				<table class="table_info_details">
					<tr class="tr_odd">
						<td class="td_align_left">
						'.$this->l('Thread unread').'
						</td>
						<td>
							'.$unread.'
						</td>
					</tr>
					<tr>
						<td class="td_align_left">
							'.$this->l('Thread pending').'
						</td>
						<td>
							'.$pending.'
						</td>
					</tr>
					<tr class="tr_odd">
						<td class="td_align_left">
							'.$this->l('Thread closed').'
						</td>
						<td>
							'.$close.'
						</td>
					</tr>
					<tr>
						<td class="td_align_left">
							'.$this->l('Total thread').'
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
		SELECT SUM(o.`total_paid_real` / o.conversion_rate) as total_sales, COUNT(*) as total_orders
		FROM `'._DB_PREFIX_.'orders` o
		WHERE o.valid = 1
		AND o.`invoice_date` BETWEEN \''.date('Y-m').'-01 00:00:00\' AND \''.date('Y-m').'-31 23:59:59\' ');
		$result2 = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT COUNT(`id_customer`) AS total_registrations
		FROM `'._DB_PREFIX_.'customer` c
		WHERE c.`date_add` BETWEEN \''.date('Y-m').'-01 00:00:00\' AND \''.date('Y-m').'-31 23:59:59\'');
		$result3 = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT SUM(pv.`counter`) AS total_viewed
		FROM `'._DB_PREFIX_.'page_viewed` pv
		LEFT JOIN `'._DB_PREFIX_.'date_range` dr ON pv.`id_date_range` = dr.`id_date_range`
		LEFT JOIN `'._DB_PREFIX_.'page` p ON pv.`id_page` = p.`id_page`
		LEFT JOIN `'._DB_PREFIX_.'page_type` pt ON pt.`id_page_type` = p.`id_page_type`
		WHERE pt.`name` = \'product.php\'
		AND dr.`time_start` BETWEEN \''.date('Y-m').'-01 00:00:00\' AND \''.date('Y-m').'-31 23:59:59\'
		AND dr.`time_end` BETWEEN \''.date('Y-m').'-01 00:00:00\' AND \''.date('Y-m').'-31 23:59:59\'');
		$results = array_merge($result, array_merge($result2, $result3));

		$content = '<div class="table_info">
			<h5><a href="index.php?tab=AdminStats&token='.Tools::getAdminTokenLite('AdminStats').'">'.$this->l('View more').'</a> '.$this->l('Monthly Statistics').' </h5>
			<table class="table_info_details">
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
				<h5><a href="index.php?tab=AdminStats&token='.Tools::getAdminTokenLite('AdminStats').'">'.$this->l('View more').'</a> <strong>'.$this->l('Statistics').'</strong> / '.$this->l('Sales of the week').'</h5>
				<div id="stat_google">';
	
		$chart = new Chart();
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT total_paid / conversion_rate as total_converted, invoice_date
			FROM '._DB_PREFIX_.'orders o
			WHERE valid = 1
			AND invoice_date BETWEEN \''.date('Y-m-d', strtotime('-7 DAYS', time())).' 00:00:00\' AND \''.date('Y-m-d H:i:s').'\'');
		foreach ($result as $row)
			$chart->getCurve(1)->setPoint(strtotime($row['invoice_date']), $row['total_converted']);
		$chart->setSize(580, 170);
		$chart->setTimeMode(strtotime('-7 DAYS', time()), time(), 'd');
		$currency = Tools::setCurrency($this->context->cookie);
		$chart->getCurve(1)->setLabel($this->l('Sales +Tx').' ('.strtoupper($currency->iso_code).')');
		
		$content .= $chart->fetch();
		$content .= '	</div>
		</div>';
		return $content;
	}
	
	public function getLastOrders()
	{
		$content = '
			<table cellpadding="0" cellspacing="0" id="table_customer">
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
		foreach ($orders AS $order)
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
		</div>';
		return $content;
	}

	public function ajaxProcess()
	{
	}

	public function ajaxProcessGetAdminHomeElement()
	{
		$result = array();
		$content = '';

		$protocol = Tools::usingSecureMode() ? 'https' : 'http';
		$isoUser = Context::getContext()->language->iso_code;
		$isoCountry = Context::getContext()->country->iso_code;
		$stream_context = @stream_context_create(array('http' => array('method'=> 'GET', 'timeout' => 2)));

		// SCREENCAST
		if (@fsockopen('www.prestashop.com', 80, $errno, $errst, AdminHomeController::TIPS_TIMEOUT))
			$result['screencast'] = 'OK';
		else
			$result['screencast'] = 'NOK';

		// PREACTIVATION
		$result['partner_preactivation'] = $this->getBlockPartners();

		// PREACTIVATION PAYPAL WARNING
		$content = @file_get_contents('https://www.prestashop.com/partner/preactivation/preactivation-warnings.php?version=1.0&partner=paypal&iso_country='.Tools::strtolower(Context::getContext()->country->iso_code).'&iso_lang='.Tools::strtolower(Context::getContext()->language->iso_code).'&id_lang='.(int)Context::getContext().'&email='.urlencode(Configuration::get('PS_SHOP_EMAIL')).'&security='.md5(Configuration::get('PS_SHOP_EMAIL')._COOKIE_IV_), false, $stream_context);
		$content = explode('|', $content);
		if ($content[0] == 'OK' && Validate::isCleanHtml($content[1]))
			Configuration::updateValue('PS_PREACTIVATION_PAYPAL_WARNING', $content[1]);
		else
			Configuration::updateValue('PS_PREACTIVATION_PAYPAL_WARNING', '');

		// DISCOVER PRESTASHOP
		$result['discover_prestashop'] = $this->getBlockDiscover();


			if (@fsockopen('www.prestashop.com', 80, $errno, $errst, AdminHomeController::TIPS_TIMEOUT))
				$result['discover_prestashop'] .= '<iframe frameborder="no" style="margin: 0px; padding: 0px; width: 315px; height: 290px;" src="'.$protocol.'://www.prestashop.com/rss/news2.php?v='._PS_VERSION_.'&lang='.$isoUser.'"></iframe>';
			else
				$result['discover_prestashop'] .= '';

		// SHOW PAYPAL TIPS
			$content = '';
			$content = @file_get_contents($protocol.'://www.prestashop.com/partner/paypal/paypal-tips.php?protocol='.$protocol.'&iso_country='.$isoCountry.'&iso_lang='.Tools::strtolower($isoUser).'&id_lang='.(int)Context::getContext()->language->id, false, $stream_context);
			$content = explode('|', $content);
			if ($content[0] == 'OK' && Validate::isCleanHtml($content[1]))
				$result['discover_prestashop'] .= $content[1];

		die(Tools::jsonEncode($result));
	}

	public function getBlockPartners()
	{
		$content = @file_get_contents($protocol.'://www.prestashop.com/partner/preactivation/preactivation-block.php?version=1.0&shop='.urlencode(Configuration::get('PS_SHOP_NAME')).'&protocol='.$protocol.'&url='.urlencode($_SERVER['HTTP_HOST']).'&iso_country='.$isoCountry.'&iso_lang='.Tools::strtolower($isoUser).'&id_lang='.(int)Context::getContext()->language->id.'&email='.urlencode(Configuration::get('PS_SHOP_EMAIL')).'&date_creation='._PS_CREATION_DATE_.'&v='._PS_VERSION_.'&security='.md5(Configuration::get('PS_SHOP_EMAIL')._COOKIE_IV_), false, $stream_context);
		if (!$content)
			$return = ''; // NOK
		else
		{
			$content = explode('|', $content);
			if ($content[0] == 'OK' && Validate::isCleanHtml($content[2]) && Validate::isCleanHtml($content[1]))
			{
				$return = $content[2];
				$content[1] = explode('#%#', $content[1]);
				foreach ($content[1] as $partnerPopUp)
					if ($partnerPopUp)
					{
						$partnerPopUp = explode('%%', $partnerPopUp);
						if (!Configuration::get('PS_PREACTIVATION_'.strtoupper($partnerPopUp[0])))
						{
							$return .= $partnerPopUp[1];
							Configuration::updateValue('PS_PREACTIVATION_'.strtoupper($partnerPopUp[0]), 'TRUE');
						}
					}
			}
			else
				$return = ''; // NOK
		}
		return $return;
	}

	public function getBlockDiscover()
	{
		$content = '';

		$stream_context = @stream_context_create(array('http' => array('method'=> 'GET', 'timeout' => AdminHomeController::TIPS_TIMEOUT)));
		$protocol = Tools::usingSecureMode() ? 'https' : 'http';
		$isoUser = Context::getContext()->language->iso_code;
		$isoCountry = Context::getContext()->country->iso_code;

		$content = @file_get_contents($protocol.'://www.prestashop.com/partner/prestashop/prestashop-link.php?iso_country='.$isoCountry.'&iso_lang='.Tools::strtolower($isoUser).'&id_lang='.(int)Context::getContext()->language->id, false, $stream_context);
		if (!$content)
			return ''; // NOK
		else
		{
			if(strpos($content, '|') !== false);
				$content = explode('|', $content);
			if ($content[0] == 'OK' && Validate::isCleanHtml($content[1]))
				return $content[1];
			else
				return ''; // NOK
		}
	}
	public function display()
	{
		$smarty = $this->context->smarty;
		$smarty->assign('token',$this->token);

		$this->warnDomainName();

		$tab = get_class();
		$protocol = Tools::usingSecureMode()?'https':'http';
		$smarty->assign('protocol',$protocol);
		$isoUser = $this->context->language->iso_code;
		$smarty->assign('isoUser',$isoUser);
		$currency = $this->context->currency;
		$upgrade = null;
		if (@ini_get('allow_url_fopen'))
		{
			$upgrade = new Upgrader();
			$upgrade->checkPSVersion();
		}
		$smarty->assign('upgrade', $upgrade);
	
  	$smarty->assign('show_screencast', $this->context->employee->show_screencast);

		// quick link to add a products, category, attributes etc. 
		$quick_links = $this->getQuickLinks();
		$smarty->assign('quick_links',$quick_links);
		$monthly_statistics = $this->getMonthlyStatistics();
		$smarty->assign('monthly_statistics', $monthly_statistics);

		$customers_service = $this->getCustomersService();
		$smarty->assign('customers_service', $customers_service);

		$stats_sales = $this->getStatsSales();
		$smarty->assign('stats_sales', $stats_sales);

		$last_orders = $this->getLastOrders();
		$smarty->assign('last_orders', $last_orders);


		if (Tools::isSubmit('hideOptimizationTips'))
			Configuration::updateValue('PS_HIDE_OPTIMIZATION_TIPS', 1);
			
		$tips_optimization = $this->_displayOptimizationTips();
		$smarty->assign('tips_optimization', $tips_optimization);

		$discover_prestashop = '<div id="discover_prestashop">
				<p class="center"><img src="../img/loader.gif" alt="" /> '.$this->l('Loading...').'</p>
			</div>
		</div>';
		$smarty->assign('discover_prestashop', $discover_prestashop);
	
		$HOOK_BACKOFFICEHOME = Module::hookExec('backOfficeHome');
		$smarty->assign('HOOK_BACKOFFICEHOME', $HOOK_BACKOFFICEHOME);

		parent::display();
	}
}


