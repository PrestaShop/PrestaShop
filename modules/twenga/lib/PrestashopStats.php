<?php
/**
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
 *  @copyright 2007-2013 PrestaShop SA : 6 rue lacepede, 75005 PARIS
 *  @version  Release: $Revision: 14011 $
 *  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 **/

class PrestashopStats
{
    /**
     * @var array of urls to use for using method
     */
    private static $arr_ps_stats_url;
    
    /**
     * @var string url of the shop
     */
    private static $site_url;
    
    /**
     * Constructor save all necessaries infos to make transaction with tracker page.
     * @param string $site_url to identify the shop 
     */
    public function __construct($site_url)
    {
        if (self::$arr_ps_stats_url === NULL)
        {
            self::$arr_ps_stats_url = array();
            self::$arr_ps_stats_url['actSubscription'] = 'http://www.prestashop.com/modules/tracker_twenga.php?act_subscription=1';
            self::$arr_ps_stats_url['validateSubscription'] = 'http://www.prestashop.com/modules/tracker_twenga.php?validate_subscription=1';
            self::$arr_ps_stats_url['cancelOrder'] = 'http://www.prestashop.com/modules/tracker_twenga.php?cancel_order=1';
            self::$arr_ps_stats_url['validateOrder'] = 'http://www.prestashop.com/modules/tracker_twenga.php?validate_order=1';
            self::$site_url = $site_url;
        }
    }
    
    /**
     * Build url for curl use with good params and good encode.
     * @param string $url url which defined the method to use for the tracker.
     * @param array $params params to passed for the tracker.
     */
    private static function buildUrlToQuery($url, $params = array())
    {
        $params['url'] = self::$site_url;
        $params['module'] = 'twenga';
        if (Configuration::get('PS_TWENGA_KEY') !== false && Configuration::get('PS_TWENGA_KEY') !== '') $params['key'] = Configuration::get('PS_TWENGA_KEY');
        $str_params = http_build_query($params);
	    $str_url = $url.(($str_params !== '') ? '&'.$str_params : '');
	    return $str_url;
    }
    
    /**
     * Use cURL to connect with the tracker.
     * @param string $query_string
     * @throws Exception if error occurred.
     * @return array with the statut code and the response return from curl connection.
     */
    private static function executeQuery($query_string)
    {
        $defaultParams = array(
			CURLOPT_HEADER => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLINFO_HEADER_OUT => TRUE,
			CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
		);
	    $session = curl_init($query_string);
		curl_setopt_array($session, $defaultParams);
		$response = curl_exec($session);
		$response = explode("\r\n\r\n", $response);
		
		$header = $response[0];
		$response = $response[1];
		
		$status_code = (int)curl_getinfo($session, CURLINFO_HTTP_CODE);
		if ($status_code === 0)
			throw new Exception('CURL Error: '.curl_error($session));
		curl_close($session);
		return array('status_code' => $status_code, 'response' => $response);
    }
    public function actSubscription ()
    {
        $str = self::buildUrlToQuery(self::$arr_ps_stats_url[__FUNCTION__]);
        $return = $this->executeQuery($str);
        if (trim($return['response']) !== '' && Validate::isMd5($return['response']))
            Configuration::updateValue('PS_TWENGA_KEY', $return['response']);
    }
    public function validateSubscription ()
    {
        $str = self::buildUrlToQuery(self::$arr_ps_stats_url[__FUNCTION__]);
        $this->executeQuery($str);
    }
    public function cancelOrder ()
    {
        $str = self::buildUrlToQuery(self::$arr_ps_stats_url[__FUNCTION__]);
        $this->executeQuery($str);
    }
    public function validateOrder ($amount_ht, $amount_ttc)
    {
        $params = array('order_amount_ttc' => (float)$amount_ttc, 'order_amount_ht' => (float)$amount_ht);
        $str = self::buildUrlToQuery(self::$arr_ps_stats_url[__FUNCTION__], $params);
        $this->executeQuery($str);
        
    }
}