<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class ConnectionsSourceCore.
 */
class ConnectionsSourceCore extends ObjectModel
{
    public $id_connections;
    public $http_referer;
    public $request_uri;
    public $keywords;
    public $date_add;
    public static $uri_max_size = 255;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'connections_source',
        'primary' => 'id_connections_source',
        'fields' => array(
            'id_connections' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'http_referer' => array('type' => self::TYPE_STRING, 'validate' => 'isAbsoluteUrl'),
            'request_uri' => array('type' => self::TYPE_STRING, 'validate' => 'isUrl'),
            'keywords' => array('type' => self::TYPE_STRING, 'validate' => 'isMessage'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
        ),
    );

    /**
     * Adds current ConnectionsSource as a new Object to the database.
     *
     * @param bool $autoDate Automatically set `date_upd` and `date_add` columns
     * @param bool $nullValues Whether we want to use NULL values instead of empty quotes values
     *
     * @return bool Indicates whether the ConnectionsSource has been successfully added
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function add($autoDate = true, $nullValues = false)
    {
        if ($result = parent::add($autoDate, $nullValues)) {
            Referrer::cacheNewSource($this->id);
        }

        return $result;
    }

    public static function logHttpReferer(Cookie $cookie = null)
    {
        if (!$cookie) {
            $cookie = Context::getContext()->cookie;
        }
        if (!isset($cookie->id_connections) || !Validate::isUnsignedId($cookie->id_connections)) {
            return false;
        }

        // If the referrer is not correct, we drop the connection
        if (isset($_SERVER['HTTP_REFERER']) && !Validate::isAbsoluteUrl($_SERVER['HTTP_REFERER'])) {
            return false;
        }
        // If there is no referrer and we do not want to save direct traffic (as opposed to referral traffic), we drop the connection
        if (!isset($_SERVER['HTTP_REFERER']) && !Configuration::get('TRACKING_DIRECT_TRAFFIC')) {
            return false;
        }

        $source = new ConnectionsSource();

        // There are a few more operations if there is a referrer
        if (isset($_SERVER['HTTP_REFERER'])) {
            // If the referrer is internal (i.e. from your own website), then we drop the connection
            $parsed = parse_url($_SERVER['HTTP_REFERER']);
            $parsedHost = parse_url(Tools::getProtocol() . Tools::getHttpHost(false, false) . __PS_BASE_URI__);

            if (!isset($parsed['host']) || (!isset($parsed['path']) || !isset($parsedHost['path']))) {
                return false;
            }

            if ((preg_replace('/^www./', '', $parsed['host']) == preg_replace('/^www./', '', Tools::getHttpHost(false, false))) && !strncmp($parsed['path'], $parsedHost['path'], strlen(__PS_BASE_URI__))) {
                return false;
            }

            $source->http_referer = substr($_SERVER['HTTP_REFERER'], 0, ConnectionsSource::$uri_max_size);
            $source->keywords = substr(trim(SearchEngine::getKeywords($_SERVER['HTTP_REFERER'])), 0, ConnectionsSource::$uri_max_size);
        }

        $source->id_connections = (int) $cookie->id_connections;
        $source->request_uri = Tools::getHttpHost(false, false);

        if (isset($_SERVER['REQUEST_URI'])) {
            $source->request_uri .= $_SERVER['REQUEST_URI'];
        } elseif (isset($_SERVER['REDIRECT_URL'])) {
            $source->request_uri .= $_SERVER['REDIRECT_URL'];
        }

        if (!Validate::isUrl($source->request_uri)) {
            $source->request_uri = '';
        }
        $source->request_uri = substr($source->request_uri, 0, ConnectionsSource::$uri_max_size);

        return $source->add();
    }

    /**
     * Get Order sources.
     *
     * @param int $idOrder Order ID
     *
     * @return array|false|mysqli_result|PDOStatement|resource|null
     */
    public static function getOrderSources($idOrder)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT cos.http_referer, cos.request_uri, cos.keywords, cos.date_add
		FROM ' . _DB_PREFIX_ . 'orders o
		INNER JOIN ' . _DB_PREFIX_ . 'guest g ON g.id_customer = o.id_customer
		INNER JOIN ' . _DB_PREFIX_ . 'connections co  ON co.id_guest = g.id_guest
		INNER JOIN ' . _DB_PREFIX_ . 'connections_source cos ON cos.id_connections = co.id_connections
		WHERE id_order = ' . (int) ($idOrder) . '
		ORDER BY cos.date_add DESC');
    }
}
