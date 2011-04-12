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

class ConnectionCore extends ObjectModel
{	
	/** @var integer */
	public $id_guest;
	
	/** @var integer */
	public $id_page;

	/** @var string */
	public $ip_address;

	/** @var string */
	public $http_referer;

	/** @var string */	
	public $date_add;

	protected	$fieldsRequired = array ('id_guest', 'id_page');	
	protected	$fieldsValidate = array ('id_guest' => 'isUnsignedId', 'id_page' => 'isUnsignedId',
										 'ip_address' => 'isInt', 'http_referer' => 'isAbsoluteUrl');

	/* MySQL does not allow 'connection' for a table name */ 
	protected 	$table = 'connections';
	protected 	$identifier = 'id_connections';
	
	public function getFields()
	{
		parent::validateFields();
		$fields['id_guest'] = (int)($this->id_guest);
		$fields['id_page'] = (int)($this->id_page);
		$fields['ip_address'] = (int)($this->ip_address);
		if (Validate::isAbsoluteUrl($this->http_referer))
			$fields['http_referer'] = pSQL($this->http_referer);
		$fields['date_add'] = pSQL($this->date_add);
		return $fields;
	}
	
	public static function setPageConnection($cookie, $full = true)
	{
		// The connection is created if it does not exist yet and we get the current page id
		if (!isset($cookie->id_connections) OR !strstr(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '', Tools::getHttpHost(false, false)))	
			$id_page = Connection::setNewConnection($cookie);
		if (!isset($id_page) OR !$id_page)
			$id_page = Page::getCurrentId();
		if (!Configuration::get('PS_STATSDATA_CUSTOMER_PAGESVIEWS'))
			return array('id_page' => $id_page);
		
		// The ending time will be updated by an ajax request when the guest will close the page
		$time_start = date('Y-m-d H:i:s');
		Db::getInstance()->AutoExecute(_DB_PREFIX_.'connections_page', array('id_connections' => (int)($cookie->id_connections), 'id_page' => (int)($id_page), 'time_start' => $time_start), 'INSERT');
		
		// This array is serialized and used by the ajax request to identify the page
		return array(
			'id_connections' => (int)($cookie->id_connections),
			'id_page' => (int)($id_page),
			'time_start' => $time_start);
	}
	
	public static function setNewConnection($cookie)
	{	
		if (isset($_SERVER['HTTP_USER_AGENT'])
			AND preg_match('/BotLink|ahoy|AlkalineBOT|anthill|appie|arale|araneo|AraybOt|ariadne|arks|ATN_Worldwide|Atomz|bbot|Bjaaland|Ukonline|borg\-bot\/0\.9|boxseabot|bspider|calif|christcrawler|CMC\/0\.01|combine|confuzzledbot|CoolBot|cosmos|Internet Cruiser Robot|cusco|cyberspyder|cydralspider|desertrealm, desert realm|digger|DIIbot|grabber|downloadexpress|DragonBot|dwcp|ecollector|ebiness|elfinbot|esculapio|esther|fastcrawler|FDSE|FELIX IDE|ESI|fido|H�m�h�kki|KIT\-Fireball|fouineur|Freecrawl|gammaSpider|gazz|gcreep|golem|googlebot|griffon|Gromit|gulliver|gulper|hambot|havIndex|hotwired|htdig|iajabot|INGRID\/0\.1|Informant|InfoSpiders|inspectorwww|irobot|Iron33|JBot|jcrawler|Teoma|Jeeves|jobo|image\.kapsi\.net|KDD\-Explorer|ko_yappo_robot|label\-grabber|larbin|legs|Linkidator|linkwalker|Lockon|logo_gif_crawler|marvin|mattie|mediafox|MerzScope|NEC\-MeshExplorer|MindCrawler|udmsearch|moget|Motor|msnbot|muncher|muninn|MuscatFerret|MwdSearch|sharp\-info\-agent|WebMechanic|NetScoop|newscan\-online|ObjectsSearch|Occam|Orbsearch\/1\.0|packrat|pageboy|ParaSite|patric|pegasus|perlcrawler|phpdig|piltdownman|Pimptrain|pjspider|PlumtreeWebAccessor|PortalBSpider|psbot|Getterrobo\-Plus|Raven|RHCS|RixBot|roadrunner|Robbie|robi|RoboCrawl|robofox|Scooter|Search\-AU|searchprocess|Senrigan|Shagseeker|sift|SimBot|Site Valet|skymob|SLCrawler\/2\.0|slurp|ESI|snooper|solbot|speedy|spider_monkey|SpiderBot\/1\.0|spiderline|nil|suke|http:\/\/www\.sygol\.com|tach_bw|TechBOT|templeton|titin|topiclink|UdmSearch|urlck|Valkyrie libwww\-perl|verticrawl|Victoria|void\-bot|Voyager|VWbot_K|crawlpaper|wapspider|WebBandit\/1\.0|webcatcher|T\-H\-U\-N\-D\-E\-R\-S\-T\-O\-N\-E|WebMoose|webquest|webreaper|webs|webspider|WebWalker|wget|winona|whowhere|wlm|WOLP|WWWC|none|XGET|Nederland\.zoek/i', $_SERVER['HTTP_USER_AGENT']))
		{
			// This is a bot and we have to retrieve its connection ID
			if ($id_connections = Db::getInstance()->getValue('
				SELECT `id_connections` FROM `'._DB_PREFIX_.'connections` c
				WHERE ip_address = '.ip2long(Tools::getRemoteAddr()).'
				AND DATE_ADD(c.`date_add`, INTERVAL 30 MINUTE) > \''.pSQL(date('Y-m-d H:i:00')).'\'
				ORDER BY c.`date_add` DESC'))
			{
				$cookie->id_connections = (int)$id_connections;
				return Page::getCurrentId();
			}
		}
	
		// A new connection is created if the guest made no actions during 30 minutes
		$result = Db::getInstance()->getRow('
		SELECT c.`id_guest`
		FROM `'._DB_PREFIX_.'connections` c
		WHERE c.`id_guest` = '.(int)($cookie->id_guest).'
		AND DATE_ADD(c.`date_add`, INTERVAL 30 MINUTE) > \''.pSQL(date('Y-m-d H:i:00')).'\'
		ORDER BY c.`date_add` DESC');
		if (!$result['id_guest'] AND (int)($cookie->id_guest))
		{
			// The old connections details are removed from the database in order to spare some memory
			Connection::cleanConnectionsPages();
		
			$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
			$arrayUrl = parse_url($referer);
			if (!isset($arrayUrl['host']) OR preg_replace('/^www./', '', $arrayUrl['host']) == preg_replace('/^www./', '', Tools::getHttpHost(false, false)))
				$referer = '';
			$connection = new Connection();
			$connection->id_guest = (int)($cookie->id_guest);
			$connection->id_page = Page::getCurrentId();
			$connection->ip_address = Tools::getRemoteAddr() ? ip2long(Tools::getRemoteAddr()) : '';
			if (Validate::isAbsoluteUrl($referer))
				$connection->http_referer = $referer;
			$connection->add();
			$cookie->id_connections = $connection->id;
			return $connection->id_page;
		}
	}
	
	public static function setPageTime($id_connections, $id_page, $time_start, $time)
	{
		if (!Validate::isUnsignedId($id_connections)
			OR !Validate::isUnsignedId($id_page)
			OR !Validate::isDate($time_start))
			return;
	
		// Limited to 5 minutes because more than 5 minutes is considered as an error
		if ($time > 300000)
			$time = 300000;
		Db::getInstance()->Execute('
		UPDATE `'._DB_PREFIX_.'connections_page`
		SET `time_end` = `time_start` + INTERVAL '.(int)($time / 1000).' SECOND
		WHERE `id_connections` = '.(int)($id_connections).'
		AND `id_page` = '.(int)($id_page).'
		AND `time_start` = \''.pSQL($time_start).'\'');
	}
	
	public static function cleanConnectionsPages()
	{
		$period = Configuration::get('PS_STATS_OLD_CONNECT_AUTO_CLEAN');

		if ($period === 'week')
			$interval = '1 WEEK';
		else if ($period === 'month')
			$interval = '1 MONTH';
		else if ($period === 'year')
			$interval = '1 YEAR';
		else
			return;
			
		if ($interval != null)
		{
			// Records of connections details older than the beginning of the  specified interval are deleted
			Db::getInstance()->Execute('
			DELETE FROM `'._DB_PREFIX_.'connections_page`
			WHERE time_start < LAST_DAY(DATE_SUB(NOW(), INTERVAL '.$interval.'))');
		}
	}
}


