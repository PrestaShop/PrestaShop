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

class Dashtrends extends Module
{
	public function __construct()
	{
		$this->name = 'dashtrends';
		$this->displayName = 'Dashboard Trends';
		$this->tab = '';
		$this->version = '0.1';
		$this->author = 'PrestaShop';

		parent::__construct();
	}

	public function install()
	{
		if (!parent::install() || !$this->registerHook('dashboardZoneTwo') || !$this->registerHook('dashboardData') || !$this->registerHook('displayBackOfficeHeader'))
			return false;
		return true;
	}
	
	public function hookDisplayBackOfficeHeader()
	{
		if (get_class($this->context->controller) == 'AdminDashboardController')
			$this->context->controller->addJs($this->_path.'views/js/'.$this->name.'.js');
	}

	public function hookDashboardZoneTwo($params)
	{
		return $this->display(__FILE__, 'dashboard_zone_two.tpl');
	}
	
	public function hookDashboardData($params)
	{
		$gapi = Module::isInstalled('gapi') ? Module::getInstanceByName('gapi') : false;
		if (Validate::isLoadedObject($gapi) && $gapi->isConfigured())
		{
			$visits_score = 0;
			if ($result = $gapi->requestReportData('', 'ga:visits', $params['date_from'], $params['date_to'], null, null, 1, 1))
				$visits_score = $result[0]['metrics']['visits'];
		}
		else
		{
			$visits_score = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT COUNT(`id_connections`)
			FROM `'._DB_PREFIX_.'connections`
			WHERE `date_add` BETWEEN "'.pSQL($params['date_from']).'" AND "'.pSQL($params['date_to']).'"
			'.Shop::addSqlRestriction(false));
		}
		$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT
			COUNT(`id_order`) as orders_score,
			SUM(`total_paid_tax_excl` / `conversion_rate`) as total_paid_tax_excl,
			SUM(`total_discounts_tax_excl` / `conversion_rate`) as total_discounts_tax_excl
		FROM `'._DB_PREFIX_.'orders`
		WHERE `invoice_date` BETWEEN "'.pSQL($params['date_from']).'" AND "'.pSQL($params['date_to']).'"
		'.Shop::addSqlRestriction(Shop::SHARE_ORDER));
		extract($row);
		$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT SUM(os.`amount` / o.`conversion_rate`) as total_credit_tax_excl, SUM(os.`shipping_cost_amount` / o.`conversion_rate`) as total_credit_shipping_tax_excl
		FROM `'._DB_PREFIX_.'orders` o
		LEFT JOIN `'._DB_PREFIX_.'order_slip` os ON o.id_order = os.id_order
		WHERE os.`date_add` BETWEEN "'.pSQL($params['date_from']).'" AND "'.pSQL($params['date_to']).'"
		'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o'));
		extract($row);
		$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT
			SUM(od.`total_price_tax_excl` / `conversion_rate`) as total_product_price_tax_excl,
			SUM(od.`product_quantity` * od.`purchase_supplier_price` / `conversion_rate`) as total_purchase_price
		FROM `'._DB_PREFIX_.'orders` o
		LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON o.id_order = od.id_order
		WHERE `invoice_date` BETWEEN "'.pSQL($params['date_from']).'" AND "'.pSQL($params['date_to']).'"
		'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o'));
		extract($row);

		return array(
			'data_value' => array(
				'sales_score' => Tools::displayPrice($total_paid_tax_excl - $total_credit_tax_excl - $total_credit_shipping_tax_excl),
				'orders_score' => $orders_score,
				'cart_value_score' => Tools::displayPrice($orders_score ? $total_paid_tax_excl / $orders_score : 0),
				'visits_score' => $visits_score,
				'convertion_rate_score' => $visits_score ? round(100 * $orders_score / $visits_score, 2) : 0,
				'net_profits_score' => Tools::displayPrice($total_product_price_tax_excl - $total_discounts_tax_excl - $total_purchase_price - $total_credit_tax_excl),
			),
			'data_trends' => array(
				'sales_score_trends' => array('way' => 'up', 'value' => 0.42),
				'orders_score_trends' => array('way' => 'down', 'value' => 0.42),
				'cart_value_score_trends' => array('way' => 'up', 'value' => 0.42),
				'visits_score_trends' => array('way' => 'down', 'value' => 0.42),
				'convertion_rate_score_trends' => array('way' => 'up', 'value' => 0.42),
				'net_profits_score_trends' => array('way' => 'up', 'value' => 0.42)
			),
			'data_chart' => array(
				'dash_trends_chart1' => $this->getChartTrends($params['date_from'], $params['date_to']),
			),
		);
	}
	
	public function getChartTrends($date_from, $date_to)
	{
		return array(
			'chart_type' => 'line_chart_trends',
			'data' => $this->getChartTrendsData($date_from, $date_to)
			);
	}
	
	public function getChartTrendsData($date_from, $date_to)
	{
		return array(
			array(
				'key' => 'Sales',
				'values' => array(
					array(1083297600000, -2.974623048543), array(1085976000000, -1.7740300785979), array(1088568000000, 4.4681318138177), array(1091246400000, 7.0242541001353), array(1093924800000, 7.5709603667586), array(1096516800000, 20.612245065736), array(1099195200000, 21.698065237316), array(1101790800000, 40.501189458018), array(1104469200000, 50.464679413194), array(1107147600000, 48.917421973355), array(1109566800000, 63.750936549160), array(1112245200000, 59.072499126460), array(1114833600000, 43.373158880492), array(1117512000000, 54.490918947556), array(1120104000000, 56.661178852079), array(1122782400000, 73.450103545496), array(1125460800000, 71.714526354907), array(1128052800000, 85.221664349607), array(1130734800000, 77.769261392481), array(1133326800000, 95.966528716500), array(1136005200000, 107.59132116397), array(1138683600000, 127.25740096723), array(1141102800000, 122.13917498830), array(1143781200000, 126.53657279774), array(1146369600000, 132.39300992970), array(1149048000000, 120.11238242904), array(1151640000000, 118.41408917750), array(1154318400000, 107.92918924621), array(1156996800000, 110.28057249569), array(1159588800000, 117.20485334692), array(1162270800000, 141.33556756948), array(1164862800000, 159.59452727893), array(1167541200000, 167.09801853304), array(1170219600000, 185.46849659215), array(1172638800000, 184.82474099990), array(1175313600000, 195.63155213887), array(1177905600000, 207.40597044171), array(1180584000000, 230.55966698196), array(1183176000000, 239.55649035292), array(1185854400000, 241.35915085208), array(1188532800000, 239.89428956243), array(1191124800000, 260.47781917715), array(1193803200000, 276.39457482225), array(1196398800000, 258.66530682672), array(1199077200000, 250.98846121893), array(1201755600000, 226.89902618127), array(1204261200000, 227.29009273807), array(1206936000000, 218.66476654350), array(1209528000000, 232.46605902918), array(1212206400000, 253.25667081117), array(1214798400000, 235.82505363925), array(1217476800000, 229.70112774254), array(1220155200000, 225.18472705952), array(1222747200000, 189.13661746552), array(1225425600000, 149.46533007301), array(1228021200000, 131.00340772114), array(1230699600000, 135.18341728866), array(1233378000000, 109.15296887173), array(1235797200000, 84.614772549760), array(1238472000000, 100.60810015326), array(1241064000000, 141.50134895610), array(1243742400000, 142.50405083675), array(1246334400000, 139.81192372672), array(1249012800000, 177.78205544583), array(1251691200000, 194.73691933074), array(1254283200000, 209.00838460225), array(1256961600000, 198.19855877420), array(1259557200000, 222.37102417812), array(1262235600000, 234.24581081250), array(1264914000000, 228.26087689346), array(1267333200000, 248.81895126250), array(1270008000000, 270.57301075186), array(1272600000000, 292.64604322550), array(1275278400000, 265.94088520518), array(1277870400000, 237.82887467569), array(1280548800000, 265.55973314204), array(1283227200000, 248.30877330928), array(1285819200000, 278.14870066912), array(1288497600000, 292.69260960288), array(1291093200000, 300.84263809599), array(1293771600000, 326.17253914628), array(1296450000000, 337.69335966505), array(1298869200000, 339.73260965121), array(1301544000000, 346.87865120765), array(1304136000000, 347.92991526628), array(1306814400000, 342.04627502669), array(1309406400000, 333.45386231233), array(1312084800000, 323.15034181243), array(1314763200000, 295.66126882331), array(1317355200000, 251.48014579253), array(1320033600000, 295.15424257905), array(1322629200000, 294.54766764397), array(1325307600000, 295.72906119051), array(1327986000000, 325.73351347613), array(1330491600000, 340.16106061186), array(1333166400000, 345.15514071490), array(1335758400000, 337.10259395679), array(1338436800000, 318.68216333837), array(1341028800000, 317.03683945246), array(1343707200000, 318.53549659997), array(1346385600000, 332.85381464104), array(1348977600000, 337.36534373477), array(1351656000000, 350.27872156161), array(1354251600000, 349.45128876100)
					)
				),
			array(
				'key' => 'Orders',
				'values' => array(
					array(1083297600000 , -1.7798428181819) , array( 1085976000000 , -0.36883324836999) , array( 1088568000000 , 1.7312581046040) , array( 1091246400000 , -1.8356125950460) , array( 1093924800000 , -1.5396564170877) , array( 1096516800000 , -0.16867791409247) , array( 1099195200000 , 1.3754263993413) , array( 1101790800000 , 5.8171640898041) , array( 1104469200000 , 9.4350145241608) , array( 1107147600000 , 6.7649081510160) , array( 1109566800000 , 9.1568499314776) , array( 1112245200000 , 7.2485090994419) , array( 1114833600000 , 4.8762222306595) , array( 1117512000000 , 8.5992339354652) , array( 1120104000000 , 9.0896517982086) , array( 1122782400000 , 13.394644048577) , array( 1125460800000 , 12.311842010760) , array( 1128052800000 , 13.221003650717) , array( 1130734800000 , 11.218481009206) , array( 1133326800000 , 15.565352598445) , array( 1136005200000 , 15.623703865926) , array( 1138683600000 , 19.275255326383) , array( 1141102800000 , 19.432433717836) , array( 1143781200000 , 21.232881244655) , array( 1146369600000 , 22.798299192958) , array( 1149048000000 , 19.006125095476) , array( 1151640000000 , 19.151889158536) , array( 1154318400000 , 19.340022855452) , array( 1156996800000 , 22.027934841859) , array( 1159588800000 , 24.903300681329) , array( 1162270800000 , 29.146492833877) , array( 1164862800000 , 31.781626082589) , array( 1167541200000 , 33.358770738428) , array( 1170219600000 , 35.622684613497) , array( 1172638800000 , 33.332821711366) , array( 1175313600000 , 34.878748635832) , array( 1177905600000 , 40.582332613844) , array( 1180584000000 , 45.719535502920) , array( 1183176000000 , 43.239344722386) , array( 1185854400000 , 38.550955100342) , array( 1188532800000 , 40.585368816283) , array( 1191124800000 , 45.601374057981) , array( 1193803200000 , 48.051404337892) , array( 1196398800000 , 41.582581696032) , array( 1199077200000 , 40.650580792748) , array( 1201755600000 , 32.252222066493) , array( 1204261200000 , 28.106390258553) , array( 1206936000000 , 27.532698196687) , array( 1209528000000 , 33.986390463852) , array( 1212206400000 , 36.302660526438) , array( 1214798400000 , 25.015574480172) , array( 1217476800000 , 23.989494069029) , array( 1220155200000 , 25.934351445531) , array( 1222747200000 , 14.627592011699) , array( 1225425600000 , -5.2249403809749) , array( 1228021200000 , -12.330933408050) , array( 1230699600000 , -11.000291508188) , array( 1233378000000 , -18.563864948088) , array( 1235797200000 , -27.213097001687) , array( 1238472000000 , -20.834133840523) , array( 1241064000000 , -12.717886701719) , array( 1243742400000 , -8.1644613083526) , array( 1246334400000 , -7.9108408918201) , array( 1249012800000 , -0.77002391591209) , array( 1251691200000 , 2.8243816569672) , array( 1254283200000 , 6.8761411421070) , array( 1256961600000 , 4.5060912230294) , array( 1259557200000 , 10.487179794349) , array( 1262235600000 , 13.251375597594) , array( 1264914000000 , 9.2207594803415) , array( 1267333200000 , 12.836276936538) , array( 1270008000000 , 19.816793904978) , array( 1272600000000 , 22.156787167211) , array( 1275278400000 , 12.518039090576) , array( 1277870400000 , 6.4253587440854) , array( 1280548800000 , 13.847372028409) , array( 1283227200000 , 8.5454736090364) , array( 1285819200000 , 18.542801953304) , array( 1288497600000 , 23.037064683183) , array( 1291093200000 , 23.517422401888) , array( 1293771600000 , 31.804723416068) , array( 1296450000000 , 34.778247386072) , array( 1298869200000 , 39.584883855230) , array( 1301544000000 , 40.080647664875) , array( 1304136000000 , 44.180050667889) , array( 1306814400000 , 42.533535927221) , array( 1309406400000 , 40.105374449011) , array( 1312084800000 , 37.014659267156) , array( 1314763200000 , 29.263745084262) , array( 1317355200000 , 19.637463417584) , array( 1320033600000 , 33.157645345770) , array( 1322629200000 , 32.895053150988) , array( 1325307600000 , 34.111544824647) , array( 1327986000000 , 40.453985817473) , array( 1330491600000 , 46.435700783313) , array( 1333166400000 , 51.062385488671) , array( 1335758400000 , 50.130448220658) , array( 1338436800000 , 41.035476682018) , array( 1341028800000 , 46.591932296457) , array( 1343707200000 , 48.349391180634) , array( 1346385600000 , 51.913011286919) , array( 1348977600000 , 55.747238313752) , array( 1351656000000 , 52.991824077209) , array( 1354251600000 , 49.556311883284)
					)
				),
			);
	}
}