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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

include(_PS_MODULE_DIR_.'/trustedshops/lib/TrustedShopsSoapApi.php');
include(_PS_MODULE_DIR_.'/trustedshops/lib/WidgetCache.php');
include(_PS_MODULE_DIR_.'/trustedshops/lib/RatingAlert.php');

class TrustedShopsRating extends AbsTrustedShops
{
	const APPLY_URL = 'https://www.trustedshops.com/buyerrating/signup.html';
	const PARTNER_PACKAGE = 'presta';
	const SHOP_SW = 'PrestaShop';
	
	private $allowed_languages = array();
	private $available_languages = array('en', 'fr', 'de');
	
	
	private $rating_url_base = array('en' => 'https://www.trustedshops.com/buyerrating/rate_',
									 'de' => 'https://www.trustedshops.com/bewertung/bewerten_',
									 'fr' => 'https://www.trustedshops.com/evaluation/evaluer_');
									 
	private $apply_url_base = array('en' => 'https://www.trustedshops.com/buyerrating/signup.html',
									 'de' => 'https://www.trustedshops.com/bewertung/anmeldung.html',
									 'fr' => 'https://www.trustedshops.com/evaluation/inscription.html');
									 
	private $apply_url_tracker = array('en' => '&et_cid=53&et_lid=3361',
									   'de' => '',
									   'fr' => '&et_cid=53&et_lid=3362');
	public function __construct()
	{
		$this->tab_name = $this->l('Customer Rating');
		
		// @todo : That gonna be change - Be worry it's false, countries have not the same ISO code as Languages, waiting Truste Shop's answer
		$this->limited_countries = $this->available_languages;
	}
	
	public function install()
	{
		foreach ($this->available_languages AS $language)
		{	
			Configuration::updateValue('TS_TAB0_ID_'.(int)(Language::getIdByIso($language)), '');
			Configuration::updateValue('TS_TAB0_ID_ACTIVE_'.(int)(Language::getIdByIso($language)), '');
		}

		Configuration::updateValue('TS_TAB0_DISPLAY_IN_SHOP', '');
		Configuration::updateValue('TS_TAB0_DISPLAY_RATING_FRONT_END', '');
		Configuration::updateValue('TS_TAB0_DISPLAY_RATING_OC', '');
		Configuration::updateValue('TS_TAB0_SEND_RATING', '');
		Configuration::updateValue('TS_TAB0_SEND_SEPERATE_MAIL', '');
		Configuration::updateValue('TS_TAB0_SEND_SEPERATE_MAIL_DELAY', '');

		return (RatingAlert::createTable() AND Configuration::updateValue('PS_TS_TAB0_SECURE_KEY', strtoupper(Tools::passwdGen(16))));
	}
	
	
	public function uninstall()
	{
		foreach ($this->available_languages AS $language)
		{	
			Configuration::deleteByName('TS_TAB0_ID_'.(int)(Language::getIdByIso($language)));
			Configuration::deleteByName('TS_TAB0_ID_ACTIVE_'.(int)(Language::getIdByIso($language)));
		}

		Configuration::deleteByName('TS_TAB0_DISPLAY_IN_SHOP');
		Configuration::deleteByName('TS_TAB0_DISPLAY_RATING_FRONT_END');
		Configuration::deleteByName('TS_TAB0_DISPLAY_RATING_OC');
		Configuration::deleteByName('TS_TAB0_SEND_RATING');
		Configuration::deleteByName('TS_TAB0_SEND_SEPERATE_MAIL');
		Configuration::deleteByName('TS_TAB0_SEND_SEPERATE_MAIL_DELAY');
		Configuration::deleteByName('PS_TS_TAB0_SECURE_KEY');

		return (RatingAlert::dropTable());
	}
	
	private function _initAllowedLanguages()
	{
		$languages = Language::getLanguages();
		foreach ($languages AS $key => $language)
		{
			if (in_array($language['iso_code'], $this->available_languages))
				$this->allowed_languages[] = $languages[$key];
		}
	}

	
	static public function getHttpHost($http = false, $entities = false)
	{
		if (method_exists('Tools', 'getHttpHost'))
			return call_user_func(array('Tools', 'getHttpHost'), array($http, $entities));
			
		$host = (isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST']);
		if ($entities)
			$host = htmlspecialchars($host, ENT_COMPAT, 'UTF-8');
		if ($http)
			$host = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').$host;
			
		return $host;
	}
	
	private function _isTsIdActive($id_lang, $ts_id = NULL)
	{
		if (is_null($ts_id))
			$ts_id = Configuration::get('TS_TAB0_ID_'.(int)($id_lang));
			
		return (!empty($ts_id) AND ($ts_id == Configuration::get('TS_TAB0_ID_ACTIVE_'.$id_lang)));
	}
	
	// Return true if at least one TS_ID is active
	private function _isConfigurable() 
	{
		foreach ($this->allowed_languages AS $language)
			if ($this->_isTsIdActive($language['id_lang']))
				return true;

		return false;
	}
	
	private function _getLastOrderId($id_customer)
	{
		return (int)(Db::getInstance()->getValue('
		SELECT `id_order`
		FROM `'._DB_PREFIX_.'orders`
		WHERE `id_customer` = '.(int)($id_customer).'
		ORDER BY `date_add` DESC'));
	}
	
	private function _getAllowedIsobyId($id_lang)
	{
		$lang = Language::getIsoById($id_lang);
		$lang = in_array($lang, $this->available_languages) ? $lang : 'en';
		
		return $lang;
	}
	
	public function getContent()
	{
		$this->_initAllowedLanguages();
		$out = '';
		if (is_writable(_PS_MODULE_DIR_.'/trustedshops/cache') === FALSE)
			$this->errors[] = $this->l('This module requires write and read permissions on the module cache directory.');
			
		if (Tools::isSubmit('submitTrustedShops')) 
		{
			$this->_validateForm();

			if (empty($this->errors)) 
				$this->_postProcess();
		} 
		
		$out .= $this->displayInformationsPage();
		$out .= $this->displayForm();
		
		return $out;
	}
	
	private function _validateForm()
	{
		if (!extension_loaded('soap'))
		{
			$this->errors[] = $this->l('This module requires the SOAP PHP extension to function properly.');
			return false;
		}

		$flag_return = true;
		foreach ($this->allowed_languages AS $language)
		{
			$ts_id = Tools::getValue('trusted_shops_id_'.(int)($language['id_lang']));
			if (!empty($ts_id))
			{
				if (!preg_match('/^[[:alnum:]]{33}$/', $ts_id))
				{
					$this->errors[] = $this->l('Invalid Trusted Shops ID').' ['.$language['iso_code'].']';
					$flag_return = false;
				}
				elseif (!$this->_isTsIdActive((int)($language['id_lang']), $ts_id))
				{
					$error = $this->_validateTrustedShopId($ts_id, (int)($language['id_lang']));
					if ($error != '') $this->errors[] = $error;
					$flag_return = false;
				}
			} 
		}
		
		if (Tools::getValue('send_seperate_mail') AND !Validate::isUnsignedInt(Tools::getValue('send_seperate_mail_delay')))
		{
			$this->errors[] = $this->l('Invalid delay');
			$flag_return = false;
		}
		
		return $flag_return;
	}
	
	private function _validateTrustedShopId($ts_id, $iso_lang)
	{
		$error = '';
		$result = TrustedShopsSoapApi::validate(self::PARTNER_PACKAGE, $ts_id);
		
		if ($result != TrustedShopsSoapApi::RT_OK)
		{
			switch($result)
			{
				case TrustedShopsSoapApi::RT_INVALID_TSID:
					$error = $this->l('Invalid Trusted Shops ID').' ['.Language::getIsoById($iso_lang).']. '.$this->l('Please register').' <a href="'.$this->getApplyUrl().'">' .$this->l('here').'</a> '. $this->l('or contact service@trustedshops.co.uk.');
					break;
				case TrustedShopsSoapApi::RT_NOT_REGISTERED: 
					$error = $this->l('Customer Rating has not yet been activated for this Trusted Shops ID').' ['.Language::getIsoById($iso_lang).']. '.$this->l('Please register').' <a href="'.$this->getApplyUrl().'">' .$this->l('here').'</a> '. $this->l('or contact service@trustedshops.co.uk.');
					break;
				default:
					$error = $this->l('An error has occurred');
			}
		}
		
		return empty($error) ? '' : $error;
	}
	
	
	private function _postProcess()
	{
		Configuration::updateValue('TS_TAB0_DISPLAY_IN_SHOP', (int)(Tools::getValue('display_in_shop')));
		Configuration::updateValue('TS_TAB0_DISPLAY_RATING_FRONT_END', (int)(Tools::getValue('display_rating_front_end')));
		Configuration::updateValue('TS_TAB0_DISPLAY_RATING_OC', (int)(Tools::getValue('display_rating_order_confirmation')));
		Configuration::updateValue('TS_TAB0_SEND_RATING', (int)(Tools::getValue('send_rating')));
		Configuration::updateValue('TS_TAB0_SEND_SEPERATE_MAIL', (int)(Tools::getValue('send_seperate_mail')));
		
		foreach ($this->allowed_languages AS $language)
		{
			$ts_id = Tools::getValue('trusted_shops_id_'.(int)($language['id_lang']));
			Configuration::updateValue('TS_TAB0_ID_'.(int)($language['id_lang']), $ts_id);
			if (!empty($ts_id)) 
				Configuration::updateValue('TS_TAB0_ID_ACTIVE_'.(int)($language['id_lang']), $ts_id);
		}

		if (Configuration::get('TS_TAB0_SEND_SEPERATE_MAIL'))
			Configuration::updateValue('TS_TAB0_SEND_SEPERATE_MAIL_DELAY', (int)(Tools::getValue('send_seperate_mail_delay')));
		else
			RatingAlert::truncateTable();

		$params = '';
		$delim = '?';
		$dataSync = '';
		$key = 1;
		foreach($this->allowed_languages AS $language)
		{
			if ($this->_isTsIdActive($language['id_lang']))
			{
				$params .= $delim.'lang'.$key.'='.$language['iso_code'].'&ts_id'.$key.'='.Configuration::get('TS_TAB0_ID_ACTIVE_'.$language['id_lang']);
				$key++;
				$delim = '&';
			}	
		}

		if (!empty($params))
			$dataSync = '<img src="http://www.prestashop.com/modules/'.self::$module_name.'.png'.$params.'" style="float:right" />';
		
		$this->confirmations[] = $this->l('Settings updated').$dataSync;
		return true;
	}

	public function displayForm()
	{
		// I18N TS_ID
		$i18n_ts_id_fields = '';
		foreach ($this->allowed_languages AS $key => $language)
		{
			$i18n_ts_id_fields .= '
				<div id="trusted_shops_id_'.(int)($language['id_lang']).'">
					<p style="line-height: 25px;">
						<img src="'._PS_IMG_.'/l/'.(int)($language['id_lang']).'.jpg" style="vertical-align: middle;" alt="" />'.strtoupper($language['iso_code']).'
						<input type="text" name="trusted_shops_id_'.(int)($language['id_lang']).'" id="trusted_shops_id_'.(int)($language['id_lang']).'" style="width: 270px;" value="'.Configuration::get('TS_TAB0_ID_'.(int)($language['id_lang'])).'" /> <span style="font-size: 10px;">'.($this->_isTsIdActive($language['id_lang']) ? $this->l('Active') : $this->l('Inactive unless you haven\'t specified your Trusted Shops ID')).'</span>
					</p>
				</div>';
		}
		
		// JAVASCRIPT 
		$javascript = '<script language="javascript">';
							
		if (!Configuration::get('TS_TAB0_SEND_SEPERATE_MAIL'))
			$javascript .=	'$("document").ready( function() { $("#send_seperate_mail_infos").hide(); });';
							
		$javascript .=	'function toggleSendMailInfos()
					 	 {
							$("#send_seperate_mail_infos").toggle();
							
							if (!$("input[name=send_seperate_mail]").attr("checked"))
								alert("'.$this->l('Warning, all the existing rating alerts will be deleted').'");
						}
						</script>';
		
		$content = $javascript .
				  '<form action="'.$this->_makeFormAction($_SERVER['REQUEST_URI'], $this->id_tab).'" method="post">
					<fieldset>
						<legend><img src="../img/admin/cog.gif" alt="" />'.$this->l('Basic Settings').'</legend>
						<p>'.$this->l('Please fill your Trusted Shops ID (one different ID per language):').'</p>
						<div>
						'.$i18n_ts_id_fields.'
						</div>
					</fieldset>';
		
		if ($this->_isConfigurable())
			$content .=	'<br />
						<fieldset>
							<legend><img src="../img/admin/appearance.gif" alt="" />'.$this->l('Display settings').'</legend>
							<label>'.$this->l('Display widget in shop').'</label>
							<div class="margin-form">
								<input type="checkbox" name="display_in_shop" value="1" '.(Configuration::get('TS_TAB0_DISPLAY_IN_SHOP') ? 'checked' : '').'/>
							</div>
							<br />
							<label>'.$this->l('Display rating link in shop front-end').'</label>
							<div class="margin-form">
								<input type="checkbox" name="display_rating_front_end" value="1" '.(Configuration::get('TS_TAB0_DISPLAY_RATING_FRONT_END') ? 'checked' : '').'/>
							</div>
							<br />
							<label>'.$this->l('Display rating link on order confirmation page').'</label>
							<div class="margin-form">
								<input type="checkbox" name="display_rating_order_confirmation" value="1" '.(Configuration::get('TS_TAB0_DISPLAY_RATING_OC') ? 'checked' : '').'/>
							</div>
							<br />
							<label>'.$this->l('Send rating link in seperate email').'</label>
							<div class="margin-form">
								<input onclick="toggleSendMailInfos()" type="checkbox" name="send_seperate_mail" value="1" '.(Configuration::get('TS_TAB0_SEND_SEPERATE_MAIL') ? 'checked' : '').'/> <br />
								<div id="send_seperate_mail_infos">'.
								$this->l('Send the email after').'<input size="2" type="text" name="send_seperate_mail_delay" value="'.(int)(Configuration::get('TS_TAB0_SEND_SEPERATE_MAIL_DELAY')).'" />'.$this->l('days').'.<br />
								<span style="color: #CC0000; font-weight: bold;">'.$this->l('IMPORTANT:').'</span> '.$this->l('Put this URL in crontab or call it manually daily:').'<br />'
								.self::getHttpHost(true, true)._MODULE_DIR_.self::$module_name.'/cron.php?secure_key='.Configuration::get('PS_TAB0_TS_SECURE_KEY').
								'</div>
							</div>
						</fieldset>';
						
		$content .= 	'<br /><center><input type="submit" class="button" name="submitTrustedShops" value="'.$this->l('Save').'" /></center>
					</form>';
				
		return $content;
	}
	
	public function displayInformationsPage()
	{
		global $cookie;
		
		return '<fieldset>
					<legend><img src="'.__PS_BASE_URI__.'modules/'.self::$module_name.'/logo.gif" alt="" />'.$this->l('Learn More').'</legend>
		
					<img src="'._MODULE_DIR_.self::$module_name.'/img/ts_rating_'.$this->_getAllowedIsobyId($cookie->id_lang).'.jpg" />
			
					<h3>'.$this->l('Trusted Shops Customer Rating').'</h3>
					<p>'.$this->l('For online buyers, positive and verifiable customer ratings are an important indication of an online shop\'s trustworthiness. The required software is already included in Prestashop, so you can start collecting customer ratings in your online shop too. Integration is easy with just a few clicks.').'</p>
					
					<h4>'.$this->l('Orientation support for your customers').'</h4>
					<p>'.$this->l('Satisfied customers are your best sales people. Let your customers speak for themselves as to how	safe and easy it is to buy from your online shop.').'</p>

					<h4>'.$this->l('Basis for shop optimisation').'</h4>
					<p>'.$this->l('Your customers are happy to help you optimise your shop with their feedback. After all, the better your online shop works, the more satisfied your customers will be.').'</p>
					
					<h4>'.$this->l('Increase reach via Facebook, Twitter and Google').'</h4>
					<p>'.$this->l('Your customers spread reviews and rating on Facebook and Twitter directly to friends and followers. Ratings are automatically listed in the Google-Index and are displayed in Google Shopping.').'</p>
					<br />
					<a style="text-decoration: underline; font-weight: bold; color: #0000CC;" target="_blank" href="'.$this->getApplyUrl().'">'.$this->l('Register for Trusted Shops Customer Rating').'</a>
				</fieldset>
				<br />';
	}
	
	public function getApplyUrl()
	{
		global $cookie;

		$lang = $this->_getAllowedIsobyId($cookie->id_lang);
		
		return $this->apply_url_base[$lang].'?partnerPackage='.self::PARTNER_PACKAGE.'&shopsw='.self::SHOP_SW.'&website='.
		urlencode(_PS_BASE_URL_.__PS_BASE_URI__).'&firstName='.urlencode($cookie->firstname).'&lastName='.
		urlencode($cookie->lastname).'&email='.urlencode(Configuration::get('PS_TAB0_SHOP_EMAIL')).'&language='.strtoupper(Language::getIsoById((int)($cookie->id_lang))).
		'&ratingProduct=RATING_PRO'.$this->apply_url_tracker[$lang];
	}
	
	public function getRatingUrl($id_order = '')
	{
		global $cookie;
		
		$buyer_email = '';
		
		if ($cookie->isLogged()) 
		{
			if (empty($id_order) && !empty($cookie->id_customer))
				$id_order = $this->_getLastOrderId($cookie->id_customer);
		
			$buyer_email = $cookie->email;
		}
				
		return $this->getRatingUrlWithBuyerEmail((int)($cookie->id_lang), $id_order, $buyer_email);
	}
	
	public function getRatingUrlWithBuyerEmail($id_lang, $id_order = '', $buyer_email = '')
	{
		$language = Language::getIsoById((int)($id_lang));
		$base_url = $this->rating_url_base[$language].Configuration::get('TS_TAB0_ID_'.(int)($id_lang)).'.html';
		
		if (!empty($buyer_email))
			$base_url .= '&buyerEmail='.urlencode(base64_encode($buyer_email)).($id_order ? '&orderID='.urlencode(base64_encode((int)($id_order))) : '');
		
		return $base_url;
	}
	
	public function hookLeftColumn($params)
	{
		if (!$this->_isTsIdActive((int)($params['cookie']->id_lang))) return false;

		self::$smarty->assign('display_widget', Configuration::get('TS_TAB0_DISPLAY_IN_SHOP'));
		if (Configuration::get('TS_TAB0_DISPLAY_IN_SHOP'))
		{
			$filename = $this->getWidgetFilename((int)($params['cookie']->id_lang));
			$cache = new WidgetCache(_PS_MODULE_DIR_.$filename, Configuration::get('TS_TAB0_ID_'.(int)($params['cookie']->id_lang)));

			if (!$cache->isFresh()) 
				$cache->refresh();

			self::$smarty->assign(array('ts_id' => Configuration::get('TS_TAB0_ID_'.(int)($params['cookie']->id_lang)), 'filename' => _MODULE_DIR_.$filename));
		}
		
		self::$smarty->assign('display_rating_link', (int)(Configuration::get('TS_TAB0_DISPLAY_RATING_FRONT_END')));
		if (Configuration::get('TS_TAB0_DISPLAY_RATING_FRONT_END'))
			self::$smarty->assign(array('rating_url' => $this->getRatingUrl(), 'language' => Language::getIsoById((int)($params['cookie']->id_lang))));
		
		return $this->display(self::$module_name, 'widget.tpl');
	}
	
	public function hookRightColumn($params)
	{
		return $this->hookLeftColumn($params);
	}
	
	public function getWidgetFilename()
	{
		global $cookie;

		return self::$module_name.'/cache/'.Configuration::get('TS_TAB0_ID_'.(int)($cookie->id_lang)).'.gif';
	}

	public function hookOrderConfirmation($params)
	{
		if (!Configuration::get('TS_TAB0_DISPLAY_RATING_OC'))
	
		if (!$this->_isTsIdActive((int)($params['cookie']->id_lang)))
			return false;
		
		self::$smarty->assign(array('rating_url' => $this->getRatingUrl((int)($params['objOrder']->id)), 'language' => Language::getIsoById((int)($params['cookie']->id_lang))));
		
		return $this->display(self::$module_name, 'order-confirmation.tpl');
	}
	

	public function hookNewOrder($params)
	{
		if (!Configuration::get('TS_TAB0_SEND_SEPERATE_MAIL') OR !$this->_isTsIdActive((int)($params['order']->id_lang)))
			return false;
		
		RatingAlert::save((int)($params['order']->id));
	}
	
	public function getL($key)
	{
		$translations = array(
			'title_part_1' => $this->l('Are you satisfied with'),
			'title_part_2' => $this->l('? Please write a review!')
		);
		
		return $translations[$key];
	}
}


