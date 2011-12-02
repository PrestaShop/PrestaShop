<?php
/*
* 2007-2011 PrestaShop
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 9702 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class MoneyBookers extends PaymentModule
{
	const LEFT_COLUMN = 0;
	const RIGHT_COLUMN = 1;
	const DISABLE = -1;

	public function __construct()
	{
		$this->name = 'moneybookers';
		$this->tab = 'payments_gateways';
		$this->version = '1.6.2';

		parent::__construct();

		$this->page = basename(__FILE__, '.php');
		$this->displayName = $this->l('Moneybookers');
		$this->description = $this->l('Accepts payments by Moneybookers.');
		$this->confirmUninstall = $this->l('Are you sure you want to delete your details ?');
		if (Configuration::get('MB_PAY_TO_EMAIL') == 'testmerchant@moneybookers.com')
			$this->warning = $this->l('You are currently using the default Moneybookers e-mail address, please use your own e-mail address.');

		/* For 1.4.3 and less compatibility */
		$updateConfig = array('PS_OS_CHEQUE' => 1, 'PS_OS_PAYMENT' => 2, 'PS_OS_PREPARATION' => 3, 'PS_OS_SHIPPING' => 4, 'PS_OS_DELIVERED' => 5, 'PS_OS_CANCELED' => 6,
				      'PS_OS_REFUND' => 7, 'PS_OS_ERROR' => 8, 'PS_OS_OUTOFSTOCK' => 9, 'PS_OS_BANKWIRE' => 10, 'PS_OS_PAYPAL' => 11, 'PS_OS_WS_PAYMENT' => 12);
		foreach ($updateConfig as $u => $v)
			if (!Configuration::get($u) || (int)Configuration::get($u) < 1)
			{
				if (defined('_'.$u.'_') && (int)constant('_'.$u.'_') > 0)
					Configuration::updateValue($u, constant('_'.$u.'_'));
				else
					Configuration::updateValue($u, $v);
			}

		/* MoneyBookers payment methods */
		$this->_internationalPaymentMethods = array(
		0 => array('file' => 'amex', 'name' => 'American Express', 'code' => 'AMX'),
		1 => array('file' => 'diners', 'name' => 'Diners', 'code' => 'DIN'),
		2 => array('file' => 'jcb', 'name' => 'JCB', 'code' => 'JCB'),
		3 => array('file' => 'maestro', 'name' => 'Maestro', 'code' => 'MAE'),
		4 => array('file' => 'mastercard', 'name' => 'Mastercard', 'code' => 'MSC'),
		5 => array('file' => 'visa', 'name' => 'Visa', 'code' => 'VSA'),
		6 => array('file' => 'visadebit', 'name' => 'Visa Debit', 'code' => 'VSD'),
		7 => array('file' => 'ewallet', 'name' => 'Moneybookers eWallet', 'code' => 'WLT'));

		$this->_localPaymentMethods = array(
		0 => array('file' => '4b', 'name' => '4B', 'code' => 'MSC'),
		1 => array('file' => 'cartasi', 'name' => 'Cartasi', 'code' => 'CSI'),
		2 => array('file' => 'cartebleue', 'name' => 'Carte bleue', 'code' => 'GCB'),
		3 => array('file' => 'dankort', 'name' => 'Dankort' , 'code' => 'DNK'),
		4 => array('file' => 'ec', 'name' => 'EC', 'code' => 'ELV'),
		5 => array('file' => 'enets', 'name' => 'eNets', 'code' => 'ENT'),
		6 => array('file' => 'epay', 'name' => 'ePay', 'code' => 'EPY'),
		7 => array('file' => 'eps', 'name' => 'EPS', 'code' => 'NPY'),
		8 => array('file' => 'euro6000', 'name' => 'Euro 6000', 'code' => 'MSC'),
		9 => array('file' => 'giropay', 'name' => 'Giropay', 'code' => 'GIR'),
		10 => array('file' => 'ideal', 'name' => 'iDeal', 'code' => 'IDL'),
		11 => array('file' => 'instantbanktransfer', 'name' => 'Instant Bank Transfer', 'code' => 'PWY'),
		12 => array('file' => 'laser', 'name' => 'Laser', 'code' => 'LSR'),
		13 => array('file' => 'nordea', 'name' => 'Nordea', 'code' => 'EBT,SO2'),
		14 => array('file' => 'p24', 'name' => 'P24', 'code' => ''),
		15 => array('file' => 'pekao', 'name' => 'Pekao', 'code' => 'PWY18'),
		16 => array('file' => 'poli', 'name' => 'Poli', 'code' => 'PLI'),
		17 => array('file' => 'postepay', 'name' => 'PostePay', 'code' => 'PSP'),
		18 => array('file' => 'sofort', 'name' => 'Sofort', 'code' => 'SFT'),
		19 => array('file' => 'solo', 'name' => 'Solo', 'code' => 'SLO'),
		20 => array('file' => 'unionpay', 'name' => 'UnionPay', 'code' => ''),
		21 => array('file' => 'visaelectron', 'name' => 'Visa Electron', 'code' => 'VSE'));

		/* MoneyBookers countries (for iso 3 letters compatibility) */
		$this->_country = array('AF' => 'AFG', 'AL' => 'ALB', 'DZ' => 'DZA', 'AS' => 'ASM', 'AD' => 'AND', 'AO' => 'AGO', 'AI' => 'AIA', 'AQ' => 'ATA', 'AG' => 'ATG', 'AR' => 'ARG',
		'AM' => 'ARM', 'AW' => 'ABW', 'AU' => 'AUS', 'AT' => 'AUT', 'AZ' => 'AZE', 'BS' => 'BHS', 'BH' => 'BHR', 'BD' => 'BGD', 'BB' => 'BRB', 'BY' => 'BLR', 'BE' => 'BEL',
		'BZ' => 'BLZ', 'BJ' => 'BEN', 'BM' => 'BMU', 'BT' => 'BTN', 'BO' => 'BOL', 'BA' => 'BIH', 'BW' => 'BWA', 'BV' => 'BVT', 'BR' => 'BRA', 'IO' => 'IOT', 'BN' => 'BRN',
		'BG' => 'BGR', 'BF' => 'BFA', 'BI' => 'BDI', 'KH' => 'KHM', 'CM' => 'CMR', 'CA' => 'CAN', 'CV' => 'CPV', 'KY' => 'CYM', 'CF' => 'CAF', 'TD' => 'TCD', 'CL' => 'CHL',
		'CN' => 'CHN', 'CX' => 'CXR', 'CC' => 'CCK', 'CO' => 'COL', 'KM' => 'COM', 'CG' => 'COG', 'CK' => 'COK', 'CR' => 'CRI', 'CI' => 'CIV', 'HR' => 'HRV', 'CU' => 'CUB',
		'CY' => 'CYP', 'CZ' => 'CZE', 'DK' => 'DNK', 'DJ' => 'DJI', 'DM' => 'DMA', 'DO' => 'DOM', 'TP' => 'TMP', 'EC' => 'ECU', 'EG' => 'EGY', 'SV' => 'SLV', 'GQ' => 'GNQ',
		'ER' => 'ERI', 'EE' => 'EST', 'ET' => 'ETH', 'FK' => 'FLK', 'FO' => 'FRO', 'FJ' => 'FJI', 'FI' => 'FIN', 'FR' => 'FRA', 'FX' => 'FXX', 'GF' => 'GUF', 'PF' => 'PYF',
		'TF' => 'ATF', 'GA' => 'GAB', 'GM' => 'GMB', 'GE' => 'GEO', 'DE' => 'DEU', 'GH' => 'GHA', 'GI' => 'GIB', 'GR' => 'GRC', 'GL' => 'GRL', 'GD' => 'GRD', 'GP' => 'GLP',
		'GU' => 'GUM', 'GT' => 'GTM', 'GN' => 'GIN', 'GW' => 'GNB', 'GY' => 'GUY', 'HT' => 'HTI', 'HM' => 'HMD', 'HN' => 'HND', 'HK' => 'HKG', 'HU' => 'HUN', 'IS' => 'ISL',
		'IN' => 'IND', 'ID' => 'IDN', 'IR' => 'IRN', 'IQ' => 'IRQ', 'IE' => 'IRL', 'IL' => 'ISR', 'IT' => 'ITA', 'JM' => 'JAM', 'JP' => 'JPN', 'JO' => 'JOR', 'KZ' => 'KAZ',
		'KE' => 'KEN', 'KI' => 'KIR', 'KP' => 'PRK', 'KR' => 'KOR', 'KW' => 'KWT', 'KG' => 'KGZ', 'LA' => 'LAO', 'LV' => 'LVA', 'LB' => 'LBN', 'LS' => 'LSO', 'LR' => 'LBR',
		'LY' => 'LBY', 'LI' => 'LIE', 'LT' => 'LTU', 'LU' => 'LUX', 'MO' => 'MAC', 'MK' => 'MKD', 'MG' => 'MDG', 'MW' => 'MWI', 'MY' => 'MYS', 'MV' => 'MDV', 'ML' => 'MLI',
		'MT' => 'MLT', 'MH' => 'MHL', 'MQ' => 'MTQ', 'MR' => 'MRT', 'MU' => 'MUS', 'YT' => 'MYT', 'MX' => 'MEX', 'FM' => 'FSM', 'MD' => 'MDA', 'MC' => 'MCO', 'MN' => 'MNG',
		'MS' => 'MSR', 'MA' => 'MAR', 'MZ' => 'MOZ', 'MM' => 'MMR', 'NA' => 'NAM', 'NR' => 'NRU', 'NP' => 'NPL', 'NL' => 'NLD', 'AN' => 'ANT', 'NC' => 'NCL', 'NZ' => 'NZL',
		'NI' => 'NIC', 'NE' => 'NER', 'NG' => 'NGA', 'NU' => 'NIU', 'NF' => 'NFK', 'MP' => 'MNP', 'NO' => 'NOR', 'OM' => 'OMN', 'PK' => 'PAK', 'PW' => 'PLW', 'PA' => 'PAN',
		'PG' => 'PNG', 'PY' => 'PRY', 'PE' => 'PER', 'PH' => 'PHL', 'PN' => 'PCN', 'PL' => 'POL', 'PT' => 'PRT', 'PR' => 'PRI', 'QA' => 'QAT', 'RE' => 'REU', 'RO' => 'ROM',
		'RU' => 'RUS', 'RW' => 'RWA', 'KN' => 'KNA', 'LC' => 'LCA', 'VC' => 'VCT', 'WS' => 'WSM', 'SM' => 'SMR', 'ST' => 'STP', 'SA' => 'SAU', 'SN' => 'SEN', 'SC' => 'SYC',
		'SL' => 'SLE', 'SG' => 'SGP', 'SK' => 'SVK', 'SI' => 'SVN', 'SB' => 'SLB', 'SO' => 'SOM', 'ZA' => 'ZAF', 'GS' => 'SGS', 'ES' => 'ESP', 'LK' => 'LKA', 'SH' => 'SHN',
		'PM' => 'SPM', 'SD' => 'SDN', 'SR' => 'SUR', 'SJ' => 'SJM', 'SZ' => 'SWZ', 'SE' => 'SWE', 'CH' => 'CHE', 'SY' => 'SYR', 'TW' => 'TWN', 'TJ' => 'TJK', 'TZ' => 'TZA',
		'TH' => 'THA', 'TG' => 'TGO', 'TK' => 'TKL', 'TO' => 'TON', 'TT' => 'TTO', 'TN' => 'TUN', 'TR' => 'TUR', 'TM' => 'TKM', 'TC' => 'TCA', 'TV' => 'TUV', 'UG' => 'UGA',
		'UA' => 'UKR', 'AE' => 'ARE', 'GB' => 'GBR', 'US' => 'USA', 'UM' => 'UMI', 'UY' => 'URY', 'UZ' => 'UZB', 'VU' => 'VUT', 'VA' => 'VAT', 'VE' => 'VEN', 'VN' => 'VNM',
		'VG' => 'VGB', 'VI' => 'VIR', 'WF' => 'WLF', 'EH' => 'ESH', 'YE' => 'YEM', 'YU' => 'YUG', 'ZR' => 'ZAR', 'ZM' => 'ZMB', 'ZW' => 'ZWE');
	}

	public function install()
	{
		if (!parent::install() OR
			!$this->registerHook('payment') OR
			!$this->registerHook('paymentReturn'))
			return false;
		Configuration::updateValue('MB_HIDE_LOGIN', 1);
		Configuration::updateValue('MB_PAY_TO_EMAIL', '');
		Configuration::updateValue('MB_CANCEL_URL', (Configuration::get('PS_SSL_ENABLED') ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__);
		Configuration::updateValue('MB_ID_LOGO', 1);
		Configuration::updateValue('MB_ID_LOGO_WALLET', 1);
		Configuration::updateValue('MB_PARAMETERS', 0);
		Configuration::updateValue('MB_DISPLAY_MODE', 1);
		Configuration::updateValue('MB_LOCAL_METHODS', 2);
		Configuration::updateValue('MB_INTER_METHODS', 5);

		return true;
	}

	public function uninstall()
	{
		if (!parent::uninstall())
			return false;

		/* Clean configuration table */
		Configuration::deleteByName('MB_PAY_TO_EMAIL');
		Configuration::deleteByName('MB_CANCEL_URL');
		Configuration::deleteByName('MB_HIDE_LOGIN');
		Configuration::deleteByName('MB_SECRET_WORD');
		Configuration::deleteByName('MB_ID_LOGO');
		Configuration::deleteByName('MB_ID_LOGO_WALLET');
		Configuration::deleteByName('MB_PARAMETERS');
		Configuration::deleteByName('MB_DISPLAY_MODE');

		return true;
	}

	/*
	** Fetch a distant content trying to use all the available function
	** if one of theme doesn't exist of failed
	*/
	private function _fetchWebContent($url, $timeout = 5, $contextOptions = array())
	{
		$stream_context = NULL;
		$defaultContextOptions = array(
				'http' => array(
       	'user_agent'			=> $_SERVER['HTTP_USER_AGENT'],
       	'max_redirects'		=> 10,
       	'timeout'       	=> $timeout,
       	'header'					=> array(
       		'Accept-language: en',
          'Cookie: foo=bar')));

    if (is_callable('curl_init') && ($ch = curl_init()))
		{
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

			$content = curl_exec($ch);
			curl_close($ch);
		}
		else
    {
    	// Check availability of the context options
			if (!is_array($contextOptions) || !count($contextOptions))
				$contextOptions = $defaultContextOptions;

			// Create a stream context
			$stream_context = @stream_context_create($contextOptions);

			if (($fp = @fopen($url, $mode, false, $stream_context)))
			{
				$content = fgets($fp, 4096);
				fclose($fp);
			}
			else if (!($content = @file_get_contents($url, false, $stream_context)))
				if (($fp = @fsockopen($url, 80, $errnom, $errstr, $timeout)))
					{
						preg_match('@^(?:http://)?([^/]+)@i', $url, $matches);
						$host = $matches[1];
						$out = "GET / HTTP/1.1\r\n";
    				$out .= "Host: ".$host."\r\n";
    				$out .= "Connection: Close\r\n\r\n";

    				fwrite($fp, $out);
    				$fetched = '';
    				while (!feof($fp))
        			$fetched .= fgets($fp, 1024);
        		if (strlen($fetched))
        			$content = $fetched;
    				fclose($fp);
					}
		}
		if (!$content)
			throw new Exception($this->l('Unable to fetch content'));
		return $content;
	}

	public function getContent()
	{
		global $cookie;
		$errors = array();
		$output = '
			<p><img src="'.__PS_BASE_URI__.'modules/moneybookers/logo-mb.gif" alt="Moneybookers" /></p><br />';

		/* Validate account */
		if (isset($_POST['SubmitValidation']))
			if (isset($_POST['mb_email_to_validate']) &&
				!empty($_POST['mb_email_to_validate']))
			{
				try
				{
					$url = 'http://moneybookers.prestashop.com/email_check.php?email='.$_POST['mb_email_to_validate'].'&url=http://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__;
					$content = $this->_fetchWebContent($url);
					$response = trim(strtolower($content));
					if (!strstr('ok', $response))
						$errors[] = $this->l('Account validation failed, please check your e-mail.');
					else
					{
						Configuration::updateValue('MB_PAY_TO_EMAIL', $_POST['mb_email_to_validate']);
						Configuration::updateValue('MB_PARAMETERS', 1);

						$output .= '
							<ul style="color: green; font-weight: bold; margin-bottom: 30px; width: 506px; background: #E1FFE9; border: 1px dashed #BBB; padding: 10px;">
								<li>'.$this->l('E-mail activation successful, you can now validate your secret word.').'<img src="http://www.prestashop.com/modules/moneybookers.png?email='.urlencode($_POST['mb_email_to_validate']).'" style="float:right" /></li>
							</ul>';
					}
				}
				catch(Exception $e)
				{
					$errors[] = $this->l('Unable to contact activation server, please try again later.');
				}
			}
			else
				$errors[] = $this->l('E-mail field is required');

		/* Validate secret word */
		if (isset($_POST['SubmitSecret']))
		{
			if (isset($_POST['mb_sw_to_validate']) AND !empty($_POST['mb_sw_to_validate']))
			{
				try
				{
					$url = 'http://moneybookers.prestashop.com/email_check.php?email='.Configuration::get('MB_PAY_TO_EMAIL').'&url=http://'
						.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'&sw=1&secret_word='.md5($_POST['mb_sw_to_validate']);
					$content = $this->_fetchWebContent($url);
					$response = trim(strtolower($content));
					if (strstr('velocity_check_exceeded', $response))
						$errors[] = $this->l('Secret word validation failed, exceeded max tries (3 per hour)');
					elseif (!strstr('ok', $response))
						$errors[] = $this->l('Secret word validation failed, please check your secret word.');
					else
					{
						Configuration::updateValue('MB_SECRET_WORD', $_POST['mb_sw_to_validate']);
						Configuration::updateValue('MB_PARAMETERS_2', 1);

						$output .= '
						<ul style="color: green; font-weight: bold; margin-bottom: 30px; width: 506px; background: #E1FFE9; border: 1px dashed #BBB; padding: 10px;">
							<li>'.$this->l('Account activation successful, secret word OK').'</li>
						</ul>';
					}
				}
				catch(Exception $e)
				{
					$errors[] = $this->l('Unable to contact activation server, please try again later.');
				}
			}
			else
				$errors[] = $this->l('Secret word field is required');
		}

		/* Update configuration variables */
		if (isset($_POST['submitMoneyBookers']))
		{
			if (!isset($_POST['mb_hide_login']))
				$_POST['mb_hide_login'] = 0;

			Configuration::updateValue('MB_CANCEL_URL', $_POST['mb_cancel_url']);
			Configuration::updateValue('MB_HIDE_LOGIN', (int)($_POST['mb_hide_login']));

			$local = '';
			$inter = '';
			foreach ($_POST AS $key => $value)
			{
				if (strstr($key, 'mb_local_'))
				{
					preg_match('/mb_local_([0-9]+)/', $key, $matches);
					if (isset($matches[1]))
						$local .= $matches[1].'|';
				}
				elseif (strstr($key, 'mb_inter_'))
				{
					preg_match('/mb_inter_([0-9]+)/', $key, $matches);
					if (isset($matches[1]))
						$inter .= $matches[1].'|';
				}
			}
			$local = rtrim($local, '|');
			$inter = rtrim($inter, '|');

			Configuration::updateValue('MB_LOCAL_METHODS', $local);
			Configuration::updateValue('MB_INTER_METHODS', $inter);
			Configuration::updateValue('MB_DISPLAY_MODE', (int)($_POST['mb_display_mode']));
		}

		if (Tools::getValue('submitSettings'))
		{
			foreach(array('leftColumn', 'rightColumn') as $hookName)
				if ($this->isRegisteredInHook($hookName))
					$this->unregisterHook(Hook::get($hookName));
			if (Tools::getValue('logo_position') == self::LEFT_COLUMN)
				$this->registerHook('leftColumn');
			else if (Tools::getValue('logo_position') == self::RIGHT_COLUMN)
				$this->registerHook('rightColumn');
		}

		/* Display errors */
		if (sizeof($errors))
		{
			$output .= '<ul style="color: red; font-weight: bold; margin-bottom: 30px; width: 506px; background: #FFDFDF; border: 1px dashed #BBB; padding: 10px;">';
			foreach ($errors AS $error)
				$output .= '<li>'.$error.'</li>';
			$output .= '</ul>';
		}

		$lang = new Language((int)($cookie->id_lang));
		$iso_img = $lang->iso_code;
		if ($lang->iso_code != 'fr' AND $lang->iso_code != 'en')
			$iso_img = 'en';

		$manual_links = array(
			'en' => 'http://moneybookers.com/creatives/integrationmanual/MB-Prestashop-Integration_en.pdf',
			'es' => 'http://www.moneybookers.com/creatives/integrationmanual/MB-Prestashop-Integration_es.pdf',
			'fr' => 'http://www.moneybookers.com/creatives/integrationmanual/MB-Prestashop-Integration_fr.pdf');

		$iso_manual = $lang->iso_code;
		if (!array_key_exists($lang->iso_code, $manual_links))
			$iso_manual = 'en';

		$blockPositionList = array(
			self::DISABLE => $this->l('Disable'),
			self::LEFT_COLUMN => $this->l('Left Column'),
			self::RIGHT_COLUMN => $this->l('Right Column'));

		$currentLogoBlockPosition = ($this->isRegisteredInHook('leftColumn')) ? self::LEFT_COLUMN :
			(($this->isRegisteredInHook('rightColumn')) ? self::RIGHT_COLUMN : -1);

	/* Display settings form */
		$output .= '
		<b>'.$this->l('About Moneybookers').'</b><br /><br /><p style="font-size: 11px;">'.
		$this->l('Take advantage of the special fees offered by Moneybookers to PrestaShop merchants !').'<br /><br />'.
		$this->l('Moneybookers, controlled by Skrill Holdings, is one of the biggest online payment systems in Europe, and proposes more than 100 payment options and 41 currencies in more than 200 countries and territories. More than 80,000 merchants already use this solution among which eBay.com, Skype and Thomas Cook.').'<br /><br />'.
		$this->l('With more than 17 million users and more than 15,000 new accounts created per day, Moneybookers also offers one of the biggest electronic wallet in the world. Your customers can also pay by using their e-mail and password thanks to the e-Wallet solution.').'<br /><br />'.
		$this->l('Moneybookers changes its name and becomes Skrill!').'<br /><br />
                <div style="clear: both;"></div>

		<form method="post" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" id="form-settings">
			<fieldset class="width2" style="margin: 20px 0; width: 800px;">
				<legend><img src="'.__PS_BASE_URI__.'modules/moneybookers/logo.gif" alt="" />'.$this->l('Settings').'</legend>
				<div class="margin-form" style="margin:0; padding:0 0 1em 20px">'.$this->l('You can display the Skrill/Moneybookers logo on your shop, this may reassure your customers about the fact that you are a serious merchant.').'</div>
				<div class="margin-form" style="margin:0; padding:0 0 1em 20px">
					<b>'.$this->l('Select the logo position').'</b> :
					<select name="logo_position">';
					foreach($blockPositionList as $position => $translation)
					{
						$selected = ($currentLogoBlockPosition == $position) ? 'selected="selected"' : '';
						$output .= '<option value="'.$position.'" '.$selected.'>'.$translation.'</option>';
					}
		$link = new Link();
		$admin_dir =  substr(_PS_ADMIN_DIR_, strrpos(_PS_ADMIN_DIR_,'/') + 1);
		$iso_code = strtolower(Country::getIsoById(Configuration::get('PS_COUNTRY_DEFAULT')));
		$landingPage = ($iso_code == 'en' || $iso_code == 'us') ? 'http://www.moneybookers.com/ads/partners/index.html?p=Prestashop' :
			'http://www.moneybookers.com/ads/partners/'.$iso_code.'/index.html?p=Prestashop';


		$output .= '
					</select>
					<p>'.$this->l('Change your logo position in the Front Office. Works with').'
						<a href="'.$link->getPageLink('index.php').'?live_edit&ad='.$admin_dir.'&liveToken='.sha1($admin_dir._COOKIE_KEY_).'">'.$this->l(' Live edit.').'</a></p>
				</div>
				<div style="text-align:center;">
					<input type="submit" name="submitSettings" value="'.$this->l('Submit settings').'" />
				</div>
			</fieldset>
		</form>
		<form method="post" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" id="form-opening">
			<fieldset class="width2" style="margin: 20px 0; width: 800px;">
				<legend><img src="'.__PS_BASE_URI__.'modules/moneybookers/logo.gif" alt="" />'.$this->l('Open Account').'</legend>
				'.$this->l('Start by opening a').' <b>'.$this->l('free account').'</b> '.$this->l('with Moneybookers:').'
				<p><a href="http://www.moneybookers.com/partners/prestashop/'.($lang->iso_code == 'fr' ? '' : strtolower($lang->iso_code).'/').'"><img src="../modules/moneybookers/prestashop_mb_'.$iso_img.'.gif" alt="PrestaShop & Moneybookers" /></a><br /><br />
				<p style="margin: 0;">
					'.$this->l('Then click here:').' <input type="button" class="button" value="'.$this->l('I already have a Moneybookers account').'" style="margin: 0 auto;" onclick="$(\'#form-activation\').show(1500);" />
					<hr size="1" style="margin: 0 0 15px 0;" noshade />
				</p>
				<span style="color: #CC0000; font-weight: bold; line-height: 20px;"><img src="../img/admin/gold.gif" alt="" /> '.$this->l('Thanks to the PrestaShop/Moneybookers partnership,').' '.$this->l('you will get a preferential commission rate!').'</span></p>

<br /><br />
<style>
.tdMBL { border-left: 1px solid black; border-top: 1px solid black; }
.tdMBR { border-left: 1px solid black; border-top: 1px solid black; border-right: 1px solid black; }
.tdMBLast { border-top: 1px solid black; border-right: 1px solid black; }
</style>

<p>'.$this->l('Fees available since March 21st 2011').'</p>


<table cellpadding="2" cellspacing="0" style="width: 750px;">
 <tr><td class="tdMBL" style="background-color: grey;"><b>PrestaShop</b></td><td colspan="2" class="tdMBR" style="background-color: grey;"><b>'.$this->l('Online payment solution by Moneybookers').'</b></td></tr>
 <tr><td class="tdMBL">'.$this->l('Monthly volume for payments made via Moneybookers').'</td><td class="tdMBL">Quick Checkout Moneybookers ***</td><td class="tdMBR">Moneybookers eWallet **</td></tr>
 <tr><td class="tdMBL">€ 0 - € 1,000</td><td class="tdMBR">2.9% + 0.19€</td><td rowspan="5" class="tdMBLast">0.9% + 0.19€</td></tr>
 <tr><td class="tdMBL">€ 1,000.01 - € 10,000</td><td class="tdMBR">1.8% + 0.19€</td></tr>
 <tr><td class="tdMBL">€ 10,000.01 - € 50,000</td><td class="tdMBR">1.6% + 0.19€</td></tr>
 <tr><td class="tdMBL">€ 50,000.01 - € 100,000</td><td class="tdMBR">1.4% + 0.19€</td></tr>
 <tr><td class="tdMBL">€ 100,000</td><td class="tdMBR">1.2% + 0.19€</td></tr>
 <tr><td colspan="3" style="border-top: 1px solid black;"><small>'.$this->l('For merchants over €100,000 fees can be negotiated.').' Contact: ecommerce@moneybookers.com</small></td></tr>
</table>
<br />
'.$this->l('To view the last update of the detailed fees').'<a href="'.$landingPage.'"> <b>'.$this->l('Click here').'</b></a>

<p align="left">
** '.$this->l('Moneybookers eWallet').'<br />
*** '.$this->l('Quick Checkout Moneybookers').'
</p>

			</fieldset>
		</form>

		<form method="post" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" id="form-activation"'.((!Configuration::get('MB_PARAMETERS') AND !isset($_POST['SubmitValidation'])) ? ' style="display: none;"' : '').'>
			<fieldset class="width2" style="margin: 20px 0; width: 800px;">
				<legend><img src="'.__PS_BASE_URI__.'modules/moneybookers/logo.gif" alt="" />'.$this->l('Account validation').'</legend>
				'.(Configuration::get('MB_PARAMETERS') == 1 ? '<p style="font-weight: bold; color: green;"><img src="../img/admin/ok.gif" alt="" /> '.$this->l('Your account has been activated').'</p>' : '').'
				<p style="line-height: 20px;">'.$this->l('You need to').' <b>'.$this->l('validate your account').'</b>.<br /><br />
				<input type="text" name="mb_email_to_validate" value="'.Configuration::get('MB_PAY_TO_EMAIL').'" style="width: 250px;" />
				<input type="submit" name="SubmitValidation" class="button" value="'.$this->l('Validate my account').'" /></p>
				<p style="font-size: 14px;"><a href="'.$manual_links[$iso_manual].'" target="_blank"><img src="../img/admin/pdf.gif" alt="" /></a><a href="'.$manual_links[$iso_manual].'" target="_blank"><b>'.$this->l('For help, refer to the activation manual.').'</b></a></p>
				<p style="font-size: 12px;">
'.$this->l('You can test Moneybookers payment with the test account testaccount2@moneybookers.com and the secret word mbtest.').'<br />
'.$this->l('Beware, this is only a test account: you will not receive money if you use this test account on your shop. To receive money, you have to use the login and password of your personal Moneybookers account !').'
				</p>
			</fieldset>
		</form>

		<form method="post" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" id="form-secret"'.((!Configuration::get('MB_PARAMETERS') AND !isset($_POST['SubmitSecret'])) ? ' style="display: none;"' : '').'>
			<fieldset class="width2" style="margin: 20px 0; width: 800px;">
				<legend><img src="'.__PS_BASE_URI__.'modules/moneybookers/logo.gif" alt="" />'.$this->l('Secret word validation').'</legend>
				'.(Configuration::get('MB_PARAMETERS_2') == 1 ? '<p style="font-weight: bold; color: green;"><img src="../img/admin/ok.gif" alt="" /> '.$this->l('Your secret word has been activated').'</p>' : '').'
				<p style="line-height: 20px;">'.$this->l('You need to ').' <b>'.$this->l('validate your secret word').'</b>, '.$this->l('Please enter the secret word entered on your Moneybookers account:').'<br /><br />
				<input type="password" name="mb_sw_to_validate" value="'.Configuration::get('MB_SECRET_WORD').'" style="width: 250px;" />
				<input type="submit" name="SubmitSecret" class="button" value="'.$this->l('Validate my secret word').'" /></p>

				<br />
				<p><b>'.$this->l('What is the secret word ?').'</b></p>
				<p>'.$this->l('The secret word is different from the password. It is used by Moneybookers to securely encrypt the transmission from your server.').'</p>
				<p><b>'.$this->l('Why a secret word different from the password ?').'</b></p>
				<p>'.$this->l('The secret word is used to reinforce the payment security.').'</p>
				<p>'.$this->l('The password is only used to securely connect to your Moneybookers account. If the password changes, it won\'t affect your secret word. So it is recommended to have your password different from your secret word.').'</p>
				<p><b>'.$this->l('Where can I find my secret word ?').'</b></p>
				<p>'.$this->l('Once your account has been validated, go to your account in the "Merchant Tools" section. There, you will be able to define your secret word.').'</p>


			</fieldset>
		</form>

		<form method="post" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" id="form-settings"'.(!Configuration::get('MB_PARAMETERS') ? ' style="display: none;"' : '').'>
			<style type="text/css">
				label {
					width: 300px;
					margin-right: 10px;
					font-size: 12px;
				}
			</style>
			<fieldset style="width: 800px;">';


				$interActivated = Configuration::get('MB_INTER_METHODS') != '' ? explode('|', Configuration::get('MB_INTER_METHODS')) : array();
				$localActivated = Configuration::get('MB_LOCAL_METHODS') != '' ? explode('|', Configuration::get('MB_LOCAL_METHODS')) : array();

				$output .= '
				<p>'.$this->l('Click the').' <b>'.$this->l('international payment methods').'</b> '.$this->l('that you would like to enable:').'</p>
				<div style="width: 200px; float: left; margin-right: 25px; line-height: 75px;">';

				for ($i = 0; $i != 3; $i++)
					$output .= '<input type="checkbox" name="mb_inter_'.(int)($i).'" value="1"'.(in_array($i, $interActivated) ? ' checked="checked"' : '').' /> <img src="'.__PS_BASE_URI__.'modules/moneybookers/logos/international/'.$this->_internationalPaymentMethods[$i]['file'].'.gif" alt="" style="vertical-align: middle;" /><br />';

				$output .= '
				</div>
				<div style="width: 250px; float: left; margin-right: 25px; line-height: 75px;">';

				for ($i = 3; $i != 6; $i++)
					$output .= '<input type="checkbox" name="mb_inter_'.(int)($i).'" value="1"'.(in_array($i, $interActivated) ? ' checked="checked"' : '').' /> <img src="'.__PS_BASE_URI__.'modules/moneybookers/logos/international/'.$this->_internationalPaymentMethods[$i]['file'].'.gif" alt="" style="vertical-align: middle;" /><br />';

				$output .= '
				</div>
				<div style="width: 200px; float: left; line-height: 75px;">';

				for ($i = 6; $i != sizeof($this->_internationalPaymentMethods); $i++)
					$output .= '<input type="checkbox" name="mb_inter_'.(int)($i).'" value="1"'.(in_array($i, $interActivated) ? ' checked="checked"' : '').' /> <img src="'.__PS_BASE_URI__.'modules/moneybookers/logos/international/'.$this->_internationalPaymentMethods[$i]['file'].'.gif" alt="" style="vertical-align: middle;" /><br />';

				$output .= '
				</div>
				<div style="clear: both;"></div>
				<hr size="1" noshade />
				<p>'.$this->l('Click the').' <b>'.$this->l('local payment methods').'</b> '.$this->l('that you would like to enable:').'</p>
				<div style="width: 200px; float: left; margin-right: 25px; line-height: 75px;">';

				for ($i = 0; $i != 7; $i++)
					$output .= '<input type="checkbox" name="mb_local_'.(int)($i).'" value="1"'.(in_array($i, $localActivated) ? ' checked="checked"' : '').' /> <img src="'.__PS_BASE_URI__.'modules/moneybookers/logos/local/'.$this->_localPaymentMethods[$i]['file'].'.gif" alt="" style="vertical-align: middle;" /><br />';

				$output .= '
				</div>
				<div style="width: 250px; float: left; margin-right: 25px; line-height: 75px;">';

				for ($i = 8; $i != 15; $i++)
					$output .= '<input type="checkbox" name="mb_local_'.(int)($i).'" value="1"'.(in_array($i, $localActivated) ? ' checked="checked"' : '').' /> <img src="'.__PS_BASE_URI__.'modules/moneybookers/logos/local/'.$this->_localPaymentMethods[$i]['file'].'.gif" alt="" style="vertical-align: middle;" /><br />';

				$output .= '
				</div>
				<div style="width: 200px; float: left; line-height: 75px;">';

				for ($i = 16; $i != sizeof($this->_localPaymentMethods); $i++)
					$output .= '<input type="checkbox" name="mb_local_'.(int)($i).'" value="1"'.(in_array($i, $localActivated) ? ' checked="checked"' : '').' /> <img src="'.__PS_BASE_URI__.'modules/moneybookers/logos/local/'.$this->_localPaymentMethods[$i]['file'].'.gif" alt="" style="vertical-align: middle;" /><br />';

				$output .= '
				</div>
				<div style="clear: both;"></div>

				<hr size="1" noshade />
				<legend><img src="'.__PS_BASE_URI__.'modules/moneybookers/logo.gif" alt="" />'.$this->l('Settings and payment methods').'</legend>
				<label>'.$this->l('Page displayed after payment cancellation:').'</label>
				<div class="margin-form">
					<input type="text" name="mb_cancel_url" value="'.Configuration::get('MB_CANCEL_URL').'" style="width: 300px;" />
				</div>
				<div style="clear: both;"></div>
				<label>'.$this->l('Hide the login form on Moneybookers page').'</label>
				<div class="margin-form">
					<input type="checkbox" name="mb_hide_login" value="1" '.(Configuration::get('MB_HIDE_LOGIN') ? 'checked="checked"' : '').' style="margin-top: 4px;" />
				</div>
				<div style="clear: both;"></div>
				<label>'.$this->l('Display mode:').'</label>
				<div class="margin-form">
					<input type="radio" name="mb_display_mode" value="0" '.(!Configuration::get('MB_DISPLAY_MODE') ? 'checked="checked"' : '').' style="vertical-align: text-bottom;" /> '.$this->l('All logos in 1 block').'
					<input type="radio" name="mb_display_mode" value="1" '.(Configuration::get('MB_DISPLAY_MODE') ? 'checked="checked"' : '').' style="vertical-align: text-bottom; margin-left: 10px;" /> '.$this->l('1 block for each logo').'
				</div>
				<div style="clear: both;"></div>

				<center><input type="submit" class="button" name="submitMoneyBookers" value="'.$this->l('Save settings').'" style="margin-top: 25px;" /></center>
			</fieldset>
		</form>';

		return $output;
	}

	private function _displayLogoBlock($position)
	{
		$imgPath = 'http://www.prestashop.com/images/logo_partners/logo-skrill.png';
		if (!@file_get_contents($imgPath))
			$imgPath = __PS_BASE_URI__.'modules/moneybookers/logo-skrill.png';
		return '<div style="text-align:center;"><img src="'.$imgPath.'" width=150 /></div>';
	}

	public function hookRightColumn($params)
	{
		return $this->_displayLogoBlock(self::RIGHT_COLUMN);
	}

	public function hookLeftColumn($params)
	{
		return $this->_displayLogoBlock(self::LEFT_COLUMN);
	}


	public function hookPayment($params)
	{
		global $smarty, $cookie;

		if (!Configuration::get('MB_PARAMETERS') OR !Configuration::get('MB_PARAMETERS_2') OR (Configuration::get('MB_LOCAL_METHODS') == '' AND Configuration::get('MB_INTER_METHODS') == ''))
			return;

		$flag = false;
		$allowedCurrencies = $this->getCurrency((int)$params['cart']->id_currency);
		foreach ($allowedCurrencies AS $allowedCurrency)
			if ($allowedCurrency['id_currency'] == $params['cart']->id_currency)
			{
				$flag = true;
				break;
			}

		if (!$flag)
		{
			/* Uncomment the line below if you'd like to display an error message, rather than not showing the Moneybookers module */
			// return $this->display(__FILE__, 'moneybookers-currency-error.tpl');
		}
		else
		{
			$localMethods = Configuration::get('MB_LOCAL_METHODS');
			$interMethods = Configuration::get('MB_INTER_METHODS');

			$smarty->assign(array(
			'display_mode' => (int)(Configuration::get('MB_DISPLAY_MODE')),
			'local' => $localMethods ? explode('|', $localMethods) : array(),
			'inter' => $interMethods ? explode('|', $interMethods) : array(),
			'local_logos' => $this->_localPaymentMethods,
			'inter_logos' => $this->_internationalPaymentMethods));

			/* Load objects */
			$address = new Address((int)($params['cart']->id_address_delivery));
			$countryObj = new Country((int)($address->id_country), Configuration::get('PS_LANG_DEFAULT'));
			$customer = new Customer((int)($params['cart']->id_customer));
			$currency = new Currency((int)($params['cart']->id_currency));
			$lang = new Language((int)($cookie->id_lang));

			$mbParams = array();

			/* About the merchant */
			$mbParams['pay_to_email'] = Configuration::get('MB_PAY_TO_EMAIL');
			$mbParams['recipient_description'] = Configuration::get('PS_SHOP_NAME');
			$mbParams['hide_login'] = (int)(Configuration::get('MB_HIDE_LOGIN'));
			$mbParams['id_logo'] = (int)(Configuration::get('MB_ID_LOGO'));
			$mbParams['return_url'] = (Configuration::get('PS_SSL_ENABLED') ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'order-confirmation.php?id_cart='.(int)($params['cart']->id).'&id_module='.(int)($this->id).'&key='.$customer->secure_key;
			$mbParams['cancel_url'] = Configuration::get('MB_CANCEL_URL');

			/* About the customer */
			$mbParams['pay_from_email'] = $customer->email;
			$mbParams['firstname'] = $address->firstname;
			$mbParams['lastname'] = $address->lastname;
			$mbParams['address'] = $address->address1;
			$mbParams['address2'] = $address->address2;
			$mbParams['phone_number'] = !empty($address->phone_mobile) ? $address->phone_mobile : $address->phone;
			$mbParams['postal_code'] = $address->postcode;
			$mbParams['city'] = $address->city;
			$mbParams['country'] = isset($this->_country[strtoupper($countryObj->iso_code)]) ? $this->_country[strtoupper($countryObj->iso_code)] : '';
			$mbParams['language'] = strtoupper($lang->iso_code);
			$mbParams['date_of_birth'] = substr($customer->birthday, 5, 2).substr($customer->birthday, 8, 2).substr($customer->birthday, 0, 4);

			/* About the cart */
			$mbParams['transaction_id'] = (int)($params['cart']->id).'_'.date('YmdHis').'_'.$params['cart']->secure_key;
			$mbParams['currency'] = $currency->iso_code;
			$mbParams['amount'] = number_format($params['cart']->getOrderTotal(), 2, '.', '');

			/* URLs */
			$mbParams['status_url'] = (Configuration::get('PS_SSL_ENABLED') ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'modules/'.$this->name.'/validation.php';

			/* Assign settings to Smarty template */
			$smarty->assign($mbParams);

			/* Display the MoneyBookers iframe */
			return $this->display(__FILE__, 'moneybookers.tpl');
		}
	}

	public function hookPaymentReturn($params)
	{
		if (!$this->active)
			return ;

		global $smarty;

		switch($params['objOrder']->getCurrentState())
		{
			case _PS_OS_PAYMENT_:
			case _PS_OS_OUTOFSTOCK_:
				$smarty->assign('status', 'ok');
				break;

			case _PS_OS_BANKWIRE_:
				$smarty->assign('status', 'pending');
				break;

			case _PS_OS_ERROR_:
			default:
				$smarty->assign('status', 'failed');
				break;
		}

		return $this->display(__FILE__, 'confirmation.tpl');
	}

	/**
  * Set the detail of a payment - Call before the validate order init
  * correctly the pcc object
  * See Authorize documentation to know the associated key => value
  * @param array fields
  */
  public function setTransactionDetail($response)
  {
    // If Exist we can store the details
    if (isset($this->pcc))
    {
      $this->pcc->transaction_id = (string)$response['mb_transaction_id'];

      $this->pcc->card_number = '';

      $this->pcc->card_brand = (string)$response['payment_type'];

      $this->pcc->card_expiration = '';

      $this->pcc->card_holder = (string)(isset($response['pay_to_email']) ?
      	$response['pay_to_email'] : '');
    }
  }
}


