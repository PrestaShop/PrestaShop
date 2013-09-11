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

class Gapi extends Module
{
	public function __construct()
	{
		$this->name = 'gapi';
		$this->tab = 'administration';
		$this->version = 0.9;
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Google Analytics API');
	}

	public function isConfigured()
	{
		return ($this->active && $this->api_3_0_isConfigured());
	}

	public function getContent()
	{
		$html = '';

		// Check configuration
		$allow_url_fopen = ini_get('allow_url_fopen');
		$openssl = extension_loaded('openssl');
		$curl = extension_loaded('curl');
		$ping = (($allow_url_fopen || $curl) && $openssl && Tools::file_get_contents('https://www.google.com/'));
		$online = (in_array(Tools::getRemoteAddr(), array('127.0.0.1', '::1')) ? false : true);

		if (!$ping || !$online)
		{
			$html .= $this->displayError('<ul>
				'.(($curl && $allow_url_fopen) ? '' : '<li>'.$this->l('You are not allowed to open external URLs').'</li>').'
				'.(($curl && $allow_url_fopen) ? '' : '<li>'.$this->l('cURL is not enabled').'</li>').'
				'.($openssl ? '' : '<li>'.$this->l('OpenSSL is not enabled').'</li>').'
				'.(($allow_url_fopen && $openssl && !$ping) ? '<li>'.$this->l('Google is unreachable').' ('.$this->l('check your firewall').')</li>' : '').'
				'.($online ? '' : '<li>'.$this->l('Your store is not online').'</li>').'
			</ul>');
		}

		// You can switch to the 1.3 API by replacing the following function call by $this->api_1_3_getContent()
		return $html.$this->api_3_0_getContent();
	}

	public function requestReportData($dimensions, $metrics, $date_from = null, $date_to = null, $sort = null, $filters = null, $start = 1, $limit = 30)
	{
		// You can switch to the 1.3 API by replacing the following function call by $this->api_1_3_requestReportData()
		return $this->api_3_0_requestReportData($dimensions, $metrics, $date_from, $date_to, $sort, $filters, $start, $limit);
	}

	public function api_3_0_authenticate()
	{
		// https://developers.google.com/accounts/docs/OAuth2WebServer
		$params = array(
			'response_type' => 'code',
			'client_id' => Configuration::get('PS_GAPI30_CLIENT_ID_TMP'),
			'scope' => 'https://www.googleapis.com/auth/analytics.readonly',
			'redirect_uri' => Tools::getShopDomain(true, false).__PS_BASE_URI__.'modules/'.$this->name.'/oauth2callback.php',
			'state' => $this->context->employee->id.'-'.Tools::encrypt($this->context->employee->id.Configuration::get('PS_GAPI30_CLIENT_ID_TMP')),
			'approval_prompt' => 'force',
			'access_type' => 'offline'
		);
		Tools::redirectLink('https://accounts.google.com/o/oauth2/auth?'.http_build_query($params));
	}

	public function api_3_0_refreshtoken()
	{
		$params = array(
			'client_id' => Configuration::get('PS_GAPI30_CLIENT_ID'),
			'client_secret' => Configuration::get('PS_GAPI30_CLIENT_SECRET')
		);

		// https://developers.google.com/accounts/docs/OAuth2WebServer#offline
		if (Configuration::get('PS_GAPI30_REFRESH_TOKEN'))
		{
			$params['grant_type'] = 'refresh_token';
			$params['refresh_token'] = Configuration::get('PS_GAPI30_REFRESH_TOKEN');
		}
		else
		{
			$params['grant_type'] = 'authorization_code';
			$params['code'] = Configuration::get('PS_GAPI30_AUTHORIZATION_CODE');
			$params['redirect_uri'] = Tools::getShopDomain(true, false).__PS_BASE_URI__.'modules/'.$this->name.'/oauth2callback.php';
		}

		$content = http_build_query($params);
		$stream_context = stream_context_create(array(
			'http' => array(
				'method'=> 'POST',
				'content' => $content,
				'header'  => "Content-type: application/x-www-form-urlencoded\r\nContent-length: ".strlen($content)."\r\n",
				'timeout' => 5,
			)
		));

		if (!$response_json = Tools::file_get_contents('https://accounts.google.com/o/oauth2/token', false, $stream_context))
			return false;

		$response = Tools::jsonDecode($response_json, true);
		if (isset($response['error']))
			return false;

		Configuration::updateValue('PS_GAPI30_ACCESS_TOKEN', $response['access_token']);
		Configuration::updateValue('PS_GAPI30_TOKEN_EXPIRATION', time() + (int)$response['expires_in']);
		if (isset($response['refresh_token']))
			Configuration::updateValue('PS_GAPI30_REFRESH_TOKEN', $response['refresh_token']);
		return true;
	}

	public function api_3_0_isConfigured()
	{
		return (Configuration::get('PS_GAPI30_CLIENT_ID') && Configuration::get('PS_GAPI30_CLIENT_SECRET') && Configuration::get('PS_GAPI30_PROFILE'));
	}

	public function api_3_0_getContent()
	{
		$html = '';
		if (Tools::getValue('PS_GAPI30_CLIENT_ID'))
		{
			Configuration::updateValue('PS_GAPI30_REQUEST_URI_TMP', dirname($_SERVER['REQUEST_URI']).'/'.AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
			Configuration::updateValue('PS_GAPI30_CLIENT_ID_TMP', trim(Tools::getValue('PS_GAPI30_CLIENT_ID')));
			Configuration::updateValue('PS_GAPI30_CLIENT_SECRET_TMP', trim(Tools::getValue('PS_GAPI30_CLIENT_SECRET')));
			Configuration::updateValue('PS_GAPI30_PROFILE_TMP', trim(Tools::getValue('PS_GAPI30_PROFILE')));
			// This will redirect the user to Google API authentication page
			$this->api_3_0_authenticate();
		}
		elseif (Tools::getValue('oauth2callback') == 'error')
			$html .= $this->displayError('Google API: Access denied');
		elseif (Tools::getValue('oauth2callback') == 'undefined')
			$html .= $this->displayError('Something wrong happened with Google API authorization');
		elseif (Tools::getValue('oauth2callback') == 'success')
		{
			if ($this->api_3_0_refreshtoken())
				$html .= $this->displayConfirmation('Google API Authorization granted');
			else
				$html .= $this->displayError('Google API Authorization granted but access token cannot be retrieved');
		}

		$display_slider = true;
		if ($this->api_3_0_isConfigured())
		{
			$result_test = $this->api_3_0_requestReportData('', 'ga:visits,ga:uniquePageviews', date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('-1 day')), null, null, 1, 1);
			if (!$result_test)
				$html .= $this->displayError('Cannot retrieve test results');
			else
			{
				$display_slider = false;
				$html .= $this->displayConfirmation(sprintf($this->l('Yesterday, your store received the visit of %d people for a total of %d unique page views.'), $result_test[0]['metrics']['visits'], $result_test[0]['metrics']['uniquePageviews']));
			}
		}

		if ($display_slider)
		{
			$slides = array(
				'Google API - 01 - Start.png' => $this->l('Go to https://code.google.com/apis/console and click the "Create project..." button'),
				'Google API - 02 - Services.png' => $this->l('In the "Services" tab, switch on the Analytics API'),
				'Google API - 03 - Terms.png' => $this->l('You will be asked to agree to the Terms of Service of Google APIs'),
				'Google API - 04 - Terms.png' => $this->l('And the Terms of Service of Analytics API'),
				'Google API - 05 - Services OK.png' => $this->l('You should now have something like that'),
				'Google API - 06 - API Access.png' => $this->l('In the "API Access" tab, click the big, blue, "Create an OAuth 2.0 client ID..." button'),
				'Google API - 07 - Create Client ID.png' => $this->l('Fill in the form with the name of your store, the URL of your logo and the URL of your store then click "Next"'),
				'Google API - 08 - Create Client ID.png' => sprintf($this->l('Keep "Web application" select and fill in the "Authorized Redirect URIs" area with the following URL: %s (you may have to click the "more options" link). Then validate by clicking the "Create client ID" button'), Tools::getShopDomain(true, false).__PS_BASE_URI__.'modules/'.$this->name.'/oauth2callback.php'),
				'Google API - 09 - API Access created.png' => $this->l('You should now have the following screen. Copy/Paste the "Client ID" and "Client secret" into the form below'),
				'Google API - 10 - Profile ID.png' => $this->l('Now you need the ID of the Analytics Profile you want to connect. In order to find you Profile ID, connect to the Analytics dashboard look at the URL in the address bar. Your Profile ID is the number following a "p", as shown underlined in red on the screenshot')
			);
			$first_slide = key($slides);

			$html .= '
			<a id="screenshots_button" href="#screenshots"><button class="btn btn-default"><i class="icon-question-sign"></i> How to configure Google Analytics API</button></a> 
			<div style="display:none">
				<div id="screenshots" class="carousel slide">
					<ol class="carousel-indicators">';
				$i = 0;
			foreach ($slides as $slide => $caption)
				$html .= '<li data-target="#screenshots" data-slide-to="'.($i++).'" '.($slide == $first_slide ? 'class="active"' : '').'></li>';
			$html .= '
					</ol>
					<div class="carousel-inner">';
			foreach ($slides as $slide => $caption)
				$html .= '
						<div class="item '.($slide == $first_slide ? 'active' : '').'">
							<img src="'.$this->_path.'screenshots/3.0/'.$slide.'" style="margin:auto">
							<div style="text-align:center;font-size:1.4em;margin-top:10px;font-weight:700">
								'.$caption.'
							</div>
							<div class="clear">&nbsp;</div>
						</div>';
			$html .= '
					</div>
					<a class="left carousel-control" href="#screenshots" data-slide="prev">
						<span class="icon-prev"></span>
					</a>
					<a class="right carousel-control" href="#screenshots" data-slide="next">
						<span class="icon-next"></span>
					</a>
				</div>
			</div>
			<div class="clear">&nbsp;</div>
			<script type="text/javascript">
				$(document).ready(function(){
					$("a#screenshots_button").fancybox();
					$("#screenshots").carousel({interval:false});
				});
			</script>';
		}

		$helper = new HelperOptions($this);
		$helper->id = $this->id;
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->module = $this;

		$fields_options = array(
			'general' => array(
				'title' =>	$this->l('Google Analytics API v3.0'),
				'fields' =>	$fields = array(
					'PS_GAPI30_CLIENT_ID' => array(
						'title' => $this->l('Client ID'),
						'type' => 'text'
					),
					'PS_GAPI30_CLIENT_SECRET' => array(
						'title' => $this->l('Client Secret'),
						'type' => 'text'
					),
					'PS_GAPI30_PROFILE' => array(
						'title' => $this->l('Profile'),
						'type' => 'text'
					)
				),
				'submit' => array('title' => $this->l('Save and Authenticate')),
			)
		);	

		return $html.$helper->generateOptions($fields_options);
	}

	public function api_3_0_oauth2callback()
	{
		if (!Tools::getValue('state'))
			die ('token missing');
		$state = explode('-', Tools::getValue('state'));
		if (count($state) != 2)
			die ('token malformed');
		if ($state[1] != Tools::encrypt($state[0].Configuration::get('PS_GAPI30_CLIENT_ID_TMP')))
			die ('token not valid');

		$oauth2callback = 'undefined';
		$url = Configuration::get('PS_GAPI30_REQUEST_URI_TMP');
		if (Tools::getValue('error'))
			$oauth2callback = 'error';
		elseif (Tools::getValue('code'))
		{
			Configuration::updateValue('PS_GAPI30_CLIENT_ID', Configuration::get('PS_GAPI30_CLIENT_ID_TMP'));
			Configuration::updateValue('PS_GAPI30_CLIENT_SECRET', Configuration::get('PS_GAPI30_CLIENT_SECRET_TMP'));
			Configuration::updateValue('PS_GAPI30_PROFILE', Configuration::get('PS_GAPI30_PROFILE_TMP'));
			Configuration::updateValue('PS_GAPI30_AUTHORIZATION_CODE', Tools::getValue('code'));
			$oauth2callback = 'success';
		}

		Configuration::deleteByName('PS_GAPI30_CLIENT_ID_TMP');
		Configuration::deleteByName('PS_GAPI30_CLIENT_SECRET_TMP');
		Configuration::deleteByName('PS_GAPI30_PROFILE_TMP');
		Configuration::deleteByName('PS_GAPI30_REQUEST_URI_TMP');
		Configuration::deleteByName('PS_GAPI30_REFRESH_TOKEN');

		Tools::redirectAdmin($url.'&oauth2callback='.$oauth2callback);
	}

	// https://developers.google.com/analytics/devguides/reporting/core/dimsmets
	// requestReportData('ga:country', 'ga:visits', '2013-08-25', '2013-08-25', null, null, 1, 1000);
	protected function api_3_0_requestReportData($dimensions, $metrics, $date_from, $date_to, $sort, $filters, $start, $limit)
	{
		if (Configuration::get('PS_GAPI30_TOKEN_EXPIRATION') < time() + 30 && !$this->api_3_0_refreshtoken())
			return false;
		$bearer = Configuration::get('PS_GAPI30_ACCESS_TOKEN');

		$params = array(
			'ids' => 'ga:'.Configuration::get('PS_GAPI30_PROFILE'),
			'dimensions' => $dimensions,
			'metrics' => $metrics,
			'sort' => $sort ? $sort : $metrics,
			'start-date' => $date_from,
			'end-date' => $date_to,
			'start-index' => $start,
			'max-results' => $limit,
		);
		if ($filters !== null)
			$params['filters'] = $filters;
		$content = str_replace('&amp;', '&', urldecode(http_build_query($params)));

		$stream_context = stream_context_create(array(
			'http' => array(
				'method'=> 'GET',
				'header'  => 'Authorization: Bearer '.$bearer."\r\n",
				'timeout' => 5,
			)
		));
		$api = ($date_from && $date_to) ? 'ga' : 'realtime';
		if (!$response_json = Tools::file_get_contents('https://www.googleapis.com/analytics/v3/data/'.$api.'?'.$content, false, $stream_context))
			return false;

		// https://developers.google.com/analytics/devguides/reporting/core/v3/reference
		$response = Tools::jsonDecode($response_json, true);

		$result = array();
		foreach ($response['rows'] as $row)
		{
			$metrics = array();
			$dimensions = array();
			foreach ($row as $key => $value)
				if ($response['columnHeaders'][$key]['columnType'] == 'DIMENSION')
					$dimensions[str_replace('ga:', '', $response['columnHeaders'][$key]['name'])] = $value;
				elseif ($response['columnHeaders'][$key]['columnType'] == 'METRIC')
					$metrics[str_replace('ga:', '', $response['columnHeaders'][$key]['name'])] = $value;
			$result[] = array('metrics' => $metrics, 'dimensions' => $dimensions);
		}
		return $result;
	}

	public function api_1_3_isConfigured()
	{
		return (Configuration::get('PS_GAPI13_EMAIL') && Configuration::get('PS_GAPI13_PASSWORD') && Configuration::get('PS_GAPI13_PROFILE'));
	}

	public function api_1_3_getContent()
	{
		$html = '';
		if (Tools::isSubmit('PS_GAPI13_EMAIL'))
		{
			if ($this->api_1_3_authenticate(Tools::getValue('PS_GAPI13_EMAIL'), Tools::getValue('PS_GAPI13_PASSWORD')))
			{
				Configuration::updateValue('PS_GAPI13_EMAIL', Tools::getValue('PS_GAPI13_EMAIL'));
				Configuration::updateValue('PS_GAPI13_PASSWORD', Tools::getValue('PS_GAPI13_PASSWORD'));
				Configuration::updateValue('PS_GAPI13_PROFILE', Tools::getValue('PS_GAPI13_PROFILE'));
			}
			else
				$html .= $this->displayError($this->l('Authentication failed'));
		}

		if ($this->api_1_3_isConfigured())
		{
			$result_test = $this->api_3_0_requestReportData('', 'ga:visits,ga:uniquePageviews', date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('-1 day')), null, null, 1, 1);
			if (!$result_test)
				$html .= $this->displayError('Cannot retrieve test results');
			else
				$html .= $this->displayConfirmation(sprintf($this->l('Yesterday, your store received the visit of %d people for a total of %d unique page views.'), $result_test[0]['metrics']['visits'], $result_test[0]['metrics']['uniquePageviews']));
		}

		$helper = new HelperOptions($this);
		$helper->id = $this->id;
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->module = $this;

		$fields_options = array(
			'general' => array(
				'title' =>	$this->l('Google Analytics API v1.3'),
				'fields' =>	$fields = array(
					'PS_GAPI13_EMAIL' => array(
						'title' => $this->l('Email'),
						'type' => 'text'
					),
					'PS_GAPI13_PASSWORD' => array(
						'title' => $this->l('Password'),
						'type' => 'password'
					),
					'PS_GAPI13_PROFILE' => array(
						'title' => $this->l('Profile'),
						'type' => 'text',
						'desc' => $this->l('You can find your profile ID in the address bar of your browser while accessing Analytics report.')
					)
				),
				'submit' => array('title' => $this->l('Save and Authenticate')),
			)
		);	

		return $html.$helper->generateOptions($fields_options);
	}

	protected function api_1_3_authenticate($email, $password)
	{
		$stream_context = stream_context_create(array(
			'http' => array(
				'method'=> 'POST',
				'content' => 'accountType=GOOGLE&Email='.urlencode($email).'&Passwd='.urlencode($password).'&source=GAPI-1.3&service=analytics',
				'header'  => 'Content-type: application/x-www-form-urlencoded'."\r\n",
				'timeout' => 5,
			)
		));

		if (!$response = Tools::file_get_contents('https://www.google.com/accounts/ClientLogin', false, $stream_context))
			return false;

		parse_str(str_replace(array("\n", "\r\n"), '&', $response), $response_array);
		if (!is_array($response_array) || !isset($response_array['Auth']) || empty($response_array['Auth']))
			return false;

		$this->auth_token = $response_array['Auth'];
		return true;
	}

	// requestReportData('ga:country', 'ga:visits', '2013-08-25', '2013-08-25', null, null, 1, 1000);
	protected function api_1_3_requestReportData($dimensions, $metrics, $date_from, $date_to, $sort, $filters, $start, $limit)
	{
		if (!$this->api_1_3_authenticate(Configuration::get('PS_GAPI13_EMAIL'), Configuration::get('PS_GAPI13_PASSWORD')))
			return false;

		$params = array(
			'ids' => 'ga:'.Configuration::get('PS_GAPI13_PROFILE'),
			'dimensions' => $dimensions,
			'metrics' => $metrics,
			'sort' => $sort ? $sort : $metrics,
			'start-date' => $date_from,
			'end-date' => $date_to,
			'start-index' => $start,
			'max-results' => $limit,
		);
		if ($filters !== null)
			$params['filters'] = $filters;
		$content = str_replace('&amp;', '&', urldecode(http_build_query($params)));

		$stream_context = stream_context_create(array(
			'http' => array(
				'method'=> 'GET',
				'header'  => 'Authorization: GoogleLogin auth='.$this->auth_token."\r\n",
				'timeout' => 5,
			)
		));
		if (!$response = Tools::file_get_contents('https://www.google.com/analytics/feeds/data?'.$content, false, $stream_context))
			return false;

		$xml = simplexml_load_string($response);

		/* Meta data not useful at this time */
		/*
			$report_root_parameters = array();
			$report_aggregate_metrics = array();
			$google_results = $xml->children('http://schemas.google.com/analytics/2009');
			foreach($google_results->dataSource->property as $property_attributes)
				$report_root_parameters[str_replace('ga:', '', $property_attributes->attributes()->name)] = strval($property_attributes->attributes()->value);
			foreach($google_results->aggregates->metric as $aggregate_metric)
			{
				$key = str_replace('ga:', '', $aggregate_metric->attributes()->name);
				$metric_value = strval($aggregate_metric->attributes()->value);
				if (preg_match('/^(\d+\.\d+)|(\d+E\d+)|(\d+.\d+E\d+)$/', $metric_value))
					$report_aggregate_metrics[$key] = floatval($metric_value);
				else
					$report_aggregate_metrics[$key] = intval($metric_value);
			}
		*/

		$result = array();
		foreach($xml->entry as $entry)
		{
			$metrics = array();
			foreach ($entry->children('http://schemas.google.com/analytics/2009')->metric as $metric)
			{
				$key = str_replace('ga:', '', $metric->attributes()->name);
				$metric_value = strval($metric->attributes()->value);
				if (preg_match('/^(\d+\.\d+)|(\d+E\d+)|(\d+.\d+E\d+)$/', $metric_value))
					$metrics[$key] = floatval($metric_value);
				else
					$metrics[$key] = intval($metric_value);
			}

			$dimensions = array();
			foreach ($entry->children('http://schemas.google.com/analytics/2009')->dimension as $dimension)
				$dimensions[str_replace('ga:', '', $dimension->attributes()->name)] = strval($dimension->attributes()->value);

			$result[] = array('metrics' => $metrics, 'dimensions' => $dimensions);
		}

		return $result;
	}
}