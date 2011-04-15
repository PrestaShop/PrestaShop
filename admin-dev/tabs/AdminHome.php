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

class AdminHome extends AdminTab
{
	public function postProcess()
	{
	}

	private function _displayOptimizationTips()
	{
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
		if (!file_exists(dirname(__FILE__).'/../.htaccess'))
		{
			if (Configuration::get('PS_HTACCESS_CACHE_CONTROL'))
				$htaccessOptimized = 1;
		}
		else
		{
			$stat = stat(dirname(__FILE__).'/../.htaccess');
			$dateUpdHtaccess = Db::getInstance()->getValue('SELECT date_upd FROM '._DB_PREFIX_.'configuration WHERE name = "PS_HTACCESS_CACHE_CONTROL"');
			if (Configuration::get('PS_HTACCESS_CACHE_CONTROL') AND strtotime($dateUpdHtaccess) > $stat['mtime'])
				$htaccessOptimized = 1;
				
			$dateUpdate = Configuration::get('PS_LAST_SHOP_UPDATE');
			if ($dateUpdate AND strtotime($dateUpdate) > $stat['mtime'])
				$htaccessAfterUpdate = 0;
		}
		
		$smartyOptimized = 0;
		if (!Configuration::get('PS_SMARTY_FORCE_COMPILE'))
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
		
		
		if ($rewrite + $htaccessOptimized + $smartyOptimized + $cccOptimized + $shopEnabled + $htaccessAfterUpdate != 12)	
			echo '
			<div class="admin-box1">
				<h5>'.$this->l('A good beginning...')
				.'
					<span style="float:right">
						<a id="optimizationTipsFold"'.
						(Configuration::get('PS_HIDE_OPTIMIZATION_TIPS')
						?'" href="#"><img alt="v" style="padding-top:0px; padding-right: 5px;" src="../img/admin/down-white.gif" /></a>':'href="?hideOptimizationTips" >
						<img alt="X" style="padding-top:0px; padding-right: 5px;" src="../img/admin/close-white.png" />
						</a>').'</span></h5>';
			echo '
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
			echo '<ul id="list-optimization-tips" class="admin-home-box-list" '
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
				<li style="background-color:'.$lights[$htaccessAfterUpdate]['color'].'">
					<img src="../img/admin/'.$lights[$htaccessAfterUpdate]['image'].'" class="pico" />
		<a href="index.php?tab=AdminGenerator&token='.Tools::getAdminTokenLite('AdminGenerator').'">'.$this->l('.htaccess up-to-date').'</a></li>
					</ul>
			</div>';
	}
	public function display()
	{
		global $cookie;
		
		$tab = get_class();
		$protocol = (!empty($_SERVER['HTTPS']) AND strtolower($_SERVER['HTTPS']) != 'off')?'https':'http';
		$isoDefault = Language::getIsoById(intval(Configuration::get('PS_LANG_DEFAULT')));
		$isoUser = Language::getIsoById(intval($cookie->id_lang));
		$isoCountry = Country::getIsoById(Configuration::get('PS_COUNTRY_DEFAULT'));
		$currency = new Currency((int)(Configuration::get('PS_CURRENCY_DEFAULT')));
		echo '<div>
		<h1>'.$this->l('Dashboard').'</h1>
		<hr style="background-color: #812143;color: #812143;" />
		<br />';
		if (@ini_get('allow_url_fopen') AND $update = checkPSVersion())
			echo '<div class="warning warn" style="margin-bottom:30px;"><h3>'.$this->l('New PrestaShop version available').' : <a style="text-decoration: underline;" href="'.$update['link'].'">'.$this->l('Download').'&nbsp;'.$update['name'].'</a> !</h3></div>';
	    elseif (!@ini_get('allow_url_fopen'))
	    {
			echo '<p>'.$this->l('Update notification unavailable').'</p>';
			echo '<p>&nbsp;</p>';
			echo '<p>'.$this->l('To receive PrestaShop update warnings, you need to activate the <b>allow_url_fopen</b> command in your <b>php.ini</b> config file.').' [<a href="http://www.php.net/manual/'.$isoUser.'/ref.filesystem.php">'.$this->l('more info').'</a>]</p>';
			echo '<p>'.$this->l('If you don\'t know how to do that, please contact your host administrator !').'</p><br>';
		}
	  echo '</div>';
	
	  	if (!isset($cookie->show_screencast))
	  		$cookie->show_screencast = true;
	  	if ($cookie->show_screencast)
			echo'
			<div id="adminpresentation">
				<iframe src="http://screencasts.prestashop.com/screencast.php?iso_lang='.Tools::strtolower($isoUser).'" style="border:none;width:100%;height:420px;" scrolling="no"></iframe>
				<div id="footer_iframe_home">
					<!--<a href="#">'.$this->l('View more video tutorials').'</a>-->
					<input type="checkbox" id="screencast_dont_show_again"><label for="screencast_dont_show_again">'.$this->l('don\'t show again').'</label>
				</div>
			</div>
			<script type="text/javascript">
			$(document).ready(function() {
				$(\'#screencast_dont_show_again\').click(function() {
					if ($(this).is(\':checked\'))
					{
						$.ajax({
							type: \'POST\',
							async: true,
							url: \'ajax.php?toggleScreencast\',
							success: function(data) {
								$(\'#adminpresentation\').slideUp(\'slow\');
							}
						});
					}
				});
			});
			</script>
			<div class="clear"></div><br />';
	
	
		echo '
		<div id="column_left">
			<ul class="F_list clearfix">
				<li id="first_block">
					<h4><a href="index.php?tab=AdminCatalog&addcategory&token='.Tools::getAdminTokenLite('AdminCatalog').'">'.$this->l('New category').'</a></h4>
					<p>'.$this->l('Create a new category and organize your products.').'</p>
				</li>
				<li id="second_block">
					<h4><a href="index.php?tab=AdminCatalog&id_category=1&addproduct&token='.Tools::getAdminTokenLite('AdminCatalog').'">'.$this->l('New product').'</a></h4>
					<p>'.$this->l('Fill up your catalog with new articles and attributes.').'</p>
				</li>
				<li id="third_block">
					<h4><a href="index.php?tab=AdminStats&token='.Tools::getAdminTokenLite('AdminStats').'">'.$this->l('Statistics').'</a></h4>
					<p>'.$this->l('Manage your activity with a thorough analysis of your e-shop.').'</p>
				</li>
				<li id="fourth_block">
					<h4><a href="index.php?tab=AdminEmployees&addemployee&token='.Tools::getAdminTokenLite('AdminEmployees').'">'.$this->l('New employee').'</a></h4>
					<p>'.$this->l('Add a new employee account and discharge a part of your duties of shop owner.').'</p>
				</li>
			</ul>
			';
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
			echo '
			<div class="table_info">
				<h5><a href="index.php?tab=AdminStats&token='.Tools::getAdminTokenLite('AdminStats').'">'.$this->l('View more').'</a> '.$this->l('Monthly Statistics').' </h5>
				<table class="table_info_details">
					<tr class="tr_odd">
						<td class="td_align_left">
						'.$this->l('Sales').'
						</td>
						<td>
							'.Tools::displayPrice($results['total_sales'], $currency).'
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
			</div>
			';
			$all = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'customer_thread');
			$unread = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'customer_thread` WHERE `status` = "open"');
			$pending = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'customer_thread` WHERE `status` LIKE "%pending%"');
			$close = $all - ($unread + $pending);
			echo '
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
			</div>
	
			<div id="table_info_large">
				<h5><a href="index.php?tab=AdminStats&token='.Tools::getAdminTokenLite('AdminStats').'">'.$this->l('View more').'</a> <strong>'.$this->l('Statistics').'</strong> / '.$this->l('Sales of the week').'</h5>
				<div id="stat_google">';
	
		define('PS_BASE_URI', __PS_BASE_URI__);
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
		$chart->getCurve(1)->setLabel($this->l('Sales +Tx').' ('.strtoupper($currency->iso_code).')');
		$chart->display();
		echo '	</div>
			</div>
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
			echo '
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
	
		echo '
				</tbody>
			</table>
		</div>
		<div id="column_right">
			<script type="text/javascript">
				$(document).ready(function() {
					$.ajax({
						url: "ajax.php",
						dataType: "json",
						data: "getAdminHomeElement",
						success: function(json) {
							$(\'#partner_preactivation\').fadeOut(\'slow\', function() {
								$(\'#partner_preactivation\').html(json.partner_preactivation);
								$(\'#partner_preactivation\').fadeIn(\'slow\');
							});
							
							$(\'#discover_prestashop\').fadeOut(\'slow\', function() {
								$(\'#discover_prestashop\').html(json.discover_prestashop);
								$(\'#discover_prestashop\').fadeIn(\'slow\');
							});
						},
						error: function(XMLHttpRequest, textStatus, errorThrown)
						{
							$(\'#adminpresentation\').fadeOut(\'slow\');
							$(\'#partner_preactivation\').fadeOut(\'slow\');	
							$(\'#discover_prestashop\').fadeOut(\'slow\');
						}
					});
				});
			</script>
			<div id="partner_preactivation">
				<p class="center"><img src="../img/loader.gif" alt="" /> '.translate('Loading...').'</p>
			</div>
		';

		if (Tools::isSubmit('hideOptimizationTips'))
			Configuration::updateValue('PS_HIDE_OPTIMIZATION_TIPS', 1);
			
		$this->_displayOptimizationTips();

		echo '
			<div id="discover_prestashop">
				<p class="center"><img src="../img/loader.gif" alt="" /> '.translate('Loading...').'</p>
			</div>
		</div>
		<div class="clear"></div>';
	
		echo Module::hookExec('backOfficeHome');
	}
}


