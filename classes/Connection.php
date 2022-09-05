<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

/**
 * Class ConnectionCore.
 */
class ConnectionCore extends ObjectModel
{
    /** @var int */
    public $id_guest;

    /** @var int */
    public $id_page;

    /** @var string */
    public $ip_address;

    /** @var string */
    public $http_referer;

    /** @var int */
    public $id_shop;

    /** @var int */
    public $id_shop_group;

    /** @var string */
    public $date_add;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'connections',
        'primary' => 'id_connections',
        'fields' => [
            'id_guest' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_page' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'ip_address' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'http_referer' => ['type' => self::TYPE_STRING, 'validate' => 'isAbsoluteUrl'],
            'id_shop' => ['type' => self::TYPE_INT, 'required' => true],
            'id_shop_group' => ['type' => self::TYPE_INT, 'required' => true],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
        ],
    ];

    /**
     * @see ObjectModel::getFields()
     *
     * @return array
     */
    public function getFields()
    {
        if (!$this->id_shop_group) {
            $this->id_shop_group = Context::getContext()->shop->id_shop_group;
        }

        $fields = parent::getFields();

        return $fields;
    }

    /**
     * @param Cookie $cookie
     * @param bool $full
     *
     * @return array
     */
    public static function setPageConnection($cookie, $full = true)
    {
        $idPage = false;
        // The connection is created if it does not exist yet and we get the current page id
        if (!isset($cookie->id_connections) || !isset($_SERVER['HTTP_REFERER']) || strstr($_SERVER['HTTP_REFERER'], Tools::getHttpHost(false, false) . '/') === false) {
            $idPage = Connection::setNewConnection($cookie);
        }
        // If we do not track the pages, no need to get the page id
        if (!Configuration::get('PS_STATSDATA_PAGESVIEWS') && !Configuration::get('PS_STATSDATA_CUSTOMER_PAGESVIEWS')) {
            return [];
        }
        if (!$idPage) {
            $idPage = Page::getCurrentId();
        }
        // If we do not track the page views by customer, the id_page is the only information needed
        if (!Configuration::get('PS_STATSDATA_CUSTOMER_PAGESVIEWS')) {
            return ['id_page' => $idPage];
        }

        // The ending time will be updated by an ajax request when the guest will close the page
        $timeStart = date('Y-m-d H:i:s');
        Db::getInstance()->insert(
            'connections_page',
            [
                'id_connections' => (int) $cookie->id_connections,
                'id_page' => (int) $idPage,
                'time_start' => $timeStart,
            ],
            false,
            true,
            Db::INSERT_IGNORE
        );

        // This array is serialized and used by the ajax request to identify the page
        return [
            'id_connections' => (int) $cookie->id_connections,
            'id_page' => (int) $idPage,
            'time_start' => $timeStart,
        ];
    }

    /**
     * Check if the current visitor is a bot
     *
     * @return bool
     */
    public static function isBot()
    {
        if (isset($_SERVER['HTTP_USER_AGENT'])
            && preg_match('/BotLink|ahoy|AlkalineBOT|anthill|appie|arale|araneo|AraybOt|ariadne|arks|ATN_Worldwide|Atomz|bbot|Bjaaland|Ukonline|borg\-bot\/0\.9|boxseabot|bspider|calif|christcrawler|CMC\/0\.01|combine|confuzzledbot|CoolBot|cosmos|Internet Cruiser Robot|cusco|cyberspyder|cydralspider|desertrealm, desert realm|digger|DIIbot|grabber|downloadexpress|DragonBot|dwcp|ecollector|ebiness|elfinbot|esculapio|esther|fastcrawler|FDSE|FELIX IDE|ESI|fido|H�m�h�kki|KIT\-Fireball|fouineur|Freecrawl|gammaSpider|gazz|gcreep|golem|googlebot|griffon|Gromit|gulliver|gulper|hambot|havIndex|hotwired|htdig|iajabot|INGRID\/0\.1|Informant|InfoSpiders|inspectorwww|irobot|Iron33|JBot|jcrawler|Teoma|Jeeves|jobo|image\.kapsi\.net|KDD\-Explorer|ko_yappo_robot|label\-grabber|larbin|legs|Linkidator|linkwalker|Lockon|logo_gif_crawler|marvin|mattie|mediafox|MerzScope|NEC\-MeshExplorer|MindCrawler|udmsearch|moget|Motor|msnbot|muncher|muninn|MuscatFerret|MwdSearch|sharp\-info\-agent|WebMechanic|NetScoop|newscan\-online|ObjectsSearch|Occam|Orbsearch\/1\.0|packrat|pageboy|ParaSite|patric|pegasus|perlcrawler|phpdig|piltdownman|Pimptrain|pjspider|PlumtreeWebAccessor|PortalBSpider|psbot|Getterrobo\-Plus|Raven|RHCS|RixBot|roadrunner|Robbie|robi|RoboCrawl|robofox|Scooter|Search\-AU|searchprocess|Senrigan|Shagseeker|sift|SimBot|Site Valet|skymob|SLCrawler\/2\.0|slurp|ESI|snooper|solbot|speedy|spider_monkey|SpiderBot\/1\.0|spiderline|nil|suke|http:\/\/www\.sygol\.com|tach_bw|TechBOT|templeton|titin|topiclink|UdmSearch|urlck|Valkyrie libwww\-perl|verticrawl|Victoria|void\-bot|Voyager|VWbot_K|crawlpaper|wapspider|WebBandit\/1\.0|webcatcher|T\-H\-U\-N\-D\-E\-R\-S\-T\-O\-N\-E|WebMoose|webquest|webreaper|webs|webspider|WebWalker|wget|winona|whowhere|wlm|WOLP|WWWC|none|XGET|Nederland\.zoek|AISearchBot|woriobot|NetSeer|Nutch|YandexBot/i', $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        }

        return false;
    }

    /**
     * @param Cookie $cookie
     *
     * @return int|bool Connection ID
     *                  `false` if failure
     */
    public static function setNewConnection($cookie)
    {
        // This is a bot : don't log connection
        if (static::isBot()) {
            return false;
        }

        // A new connection is created if the guest made no actions during 30 minutes
        $sql = 'SELECT SQL_NO_CACHE `id_guest`
				FROM `' . _DB_PREFIX_ . 'connections`
				WHERE `id_guest` = ' . (int) $cookie->id_guest . '
					AND `date_add` > \'' . pSQL(date('Y-m-d H:i:00', time() - 1800)) . '\'
					' . Shop::addSqlRestriction(Shop::SHARE_CUSTOMER) . '
				ORDER BY `date_add` DESC';
        $result = Db::getInstance()->getRow($sql, false);
        if (empty($result['id_guest']) && (int) $cookie->id_guest) {
            // The old connections details are removed from the database in order to spare some memory
            Connection::cleanConnectionsPages();

            $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
            $arrayUrl = parse_url($referer);
            if (!isset($arrayUrl['host']) || preg_replace('/^www./', '', $arrayUrl['host']) == preg_replace('/^www./', '', Tools::getHttpHost(false, false))) {
                $referer = '';
            }
            $connection = new Connection();
            $connection->id_guest = (int) $cookie->id_guest;
            $connection->id_page = Page::getCurrentId();
            $connection->ip_address = Tools::getRemoteAddr() ? (int) ip2long(Tools::getRemoteAddr()) : '';
            $connection->id_shop = Context::getContext()->shop->id;
            $connection->id_shop_group = Context::getContext()->shop->id_shop_group;
            $connection->date_add = $cookie->date_add;
            if (Validate::isAbsoluteUrl($referer)) {
                $connection->http_referer = substr($referer, 0, 254);
            }
            $connection->add();
            $cookie->id_connections = (int) $connection->id;

            return $connection->id_page;
        }

        return false;
    }

    /**
     * @param int $idConnections
     * @param int $idPage
     * @param string $timeStart
     * @param int $time
     */
    public static function setPageTime($idConnections, $idPage, $timeStart, $time)
    {
        if (!Validate::isUnsignedId($idConnections)
            || !Validate::isUnsignedId($idPage)
            || !Validate::isDate($timeStart)) {
            return;
        }

        // Limited to 5 minutes because more than 5 minutes is considered as an error
        if ($time > 300000) {
            $time = 300000;
        }
        Db::getInstance()->execute('
		UPDATE `' . _DB_PREFIX_ . 'connections_page`
		SET `time_end` = `time_start` + INTERVAL ' . (int) ($time / 1000) . ' SECOND
		WHERE `id_connections` = ' . (int) $idConnections . '
		AND `id_page` = ' . (int) $idPage . '
		AND `time_start` = \'' . pSQL($timeStart) . '\'');
    }

    /**
     * Clean connections page.
     */
    public static function cleanConnectionsPages()
    {
        $period = Configuration::get('PS_STATS_OLD_CONNECT_AUTO_CLEAN');

        if ($period === 'week') {
            $interval = '1 WEEK';
        } elseif ($period === 'month') {
            $interval = '1 MONTH';
        } elseif ($period === 'year') {
            $interval = '1 YEAR';
        } else {
            return;
        }

        if ($interval != null) {
            // Records of connections details older than the beginning of the  specified interval are deleted
            Db::getInstance()->execute('
			DELETE FROM `' . _DB_PREFIX_ . 'connections_page`
			WHERE time_start < LAST_DAY(DATE_SUB(NOW(), INTERVAL ' . $interval . '))');
        }
    }
}
