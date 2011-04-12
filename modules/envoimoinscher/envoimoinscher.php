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

class Envoimoinscher extends Module
{
	private $_html = '';
	private $_postErrors = array();
	public $_errors = array();
	public $packaging = array('Pli' => 'Pli', 'Colis' => 'Colis', 'Encombrant' => 'Objet lourd', 'Palette' => 'Palette');
	const INSTALL_SQL_FILE = 'install.sql';
	function __construct()
	{
		global $cookie;
		
		$this->name = 'envoimoinscher';
		$this->tab = 'shipping_logistics';
		$this->version = '1.0';
		$this->limited_countries = array('fr');
		$this->needRange = true;

		parent::__construct ();

		$this->page = basename(__FILE__, '.php');
		$this->displayName = $this->l('Envoimoinscher');
		$this->description = $this->l('Find the best price for your shipment. Compare and order carriers offers at negotiated rates.');

		if (self::isInstalled($this->name))
		{
			$warning = array();
			//Check config and display warning
			if (!Configuration::get('EMC_WIDTH'))
				$warning[] .= $this->l('\'Width\'').' ';
			if (!Configuration::get('EMC_HEIGHT'))
				$warning[] .= $this->l('\'Height\'').' ';
			if (!Configuration::get('EMC_DEPTH'))
				$warning[] .= $this->l('\'Depth\'').' ';
			if (!Configuration::get('EMC_ORDER_STATE'))
				$warning[] .= $this->l('\'Order state\'').' ';
			if (!Configuration::get('EMC_CARRIER'))
				$warning[] .= $this->l('\'Carrier\'').' ';
			if (!Configuration::get('EMC_ORDER_PAST_STATE'))
				$warning[] .= $this->l('\'Order past state\'').' ';
			if (!Configuration::get('EMC_SEND_STATE'))
				$warning[] .= $this->l('\'Send order state\'').' ';
			if (!Configuration::get('EMC_DELIVERY_STATE'))
				$warning[] .= $this->l('\'Delivered order state\'').' ';
										
			if (count($warning))
				$this->warning .= implode(' , ',$warning).$this->l('must be configured to use this module correctly').' ';
		}
	}
	
	public function install()
	{
		global $cookie;
		
		if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
			return false;
		elseif (!$sql = file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
			return false;
		$sql = str_replace(array('PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
		$sql = preg_split("/;\s*[\r\n]+/", $sql);
		foreach ($sql AS $query)
			if ($query AND sizeof($query) AND !Db::getInstance()->Execute(trim($query)))
				return false;
		if (substr(_PS_VERSION_, 2, 3) <= 3.1)
		{
			if (!@copy(dirname(__FILE__).'/AdminEnvoiMoinsCher.gif', _PS_IMG_DIR_.'/t/AdminEnvoiMoinsCher.gif'))
					$this->_errors[] = $this->l('Please manually copy ') .dirname(__FILE__).'/AdminEnvoiMoinsCher.gif'.' in the '._PS_IMG_DIR_.'/t/AdminEnvoiMoinsCher.gif folder located in your admin directory.';
		}
		if (!parent::install() OR  !Configuration::updateValue('EMC_EMAILS', 1) OR !$this->registerHook('AdminOrder') OR !self::adminInstall()
		OR !Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'envoimoinscher` (`id_order` int(10) unsigned NOT NULL, `shipping_number` varchar(30) NOT NULL ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=latin1;'))
			return false;
		return true; 					
	}
	
	private function adminInstall()
	{
		$tab = new Tab();
		$tab->class_name = 'AdminEnvoiMoinsCher';
		$tab->id_parent = 3;
		$tab->module = 'envoimoinscher';
		$tab->name[(int)(Configuration::get('PS_LANG_DEFAULT'))] = $this->l('Envoimoinscher');
		return $tab->add();
	}
	
	public function uninstall()
	{
		global $cookie;
		
		Db::getInstance()->Execute('DROP TABLE `'._DB_PREFIX_.'envoimoinscher_contenu`');
		
		$tab = new Tab(Tab::getIdFromClassName('AdminEnvoiMoinsCher'));
		if (!parent::uninstall() OR !$tab->delete() OR !$this->unregisterHook('AdminOrder'))
			return false;	
		return true;
	}
	
	public function getContent()
	{
		$this->_html .= '<h2>' . $this->l('Envoimoinscher').'</h2>';

		if (!empty($_POST) AND Tools::isSubmit('submitSave'))
		{
			$this->_postValidation();
			if (!sizeof($this->_postErrors))
				$this->_postProcess();
			else
			{
			$nbErrors = sizeof($this->_postErrors);
			$this->_html .= '<div class="alert error" >
								<h3>'.($nbErrors > 1 ? $this->l('There are') : $this->l('There is')).' '.$nbErrors.' '.($nbErrors > 1 ? $this->l('errors') : $this->l('error')).'</h3>
								<ol style="margin: 0 0 0 20px;">';
									foreach ($this->_postErrors AS $err)
										$this->_html .= '<li>- '.$err.'</li>';
			$this->_html .= '</ol></div>';
			}
		}
		$this->_displayForm();
		return $this->_html;
	}
	
	
	private function _displayForm()
	{
		global $cookie;
		$genderTab = array(1 => 'M.', 2 => 'Mme', 9 => '', 0 => '');
		$features = Feature::getFeatures($cookie->id_lang);
		$order_states = OrderState::getOrderStates($cookie->id_lang);
		$carriers = Carrier::getCarriers($cookie->id_lang);
		$countries = Country::getCountries($cookie->id_lang);
		$confs = Configuration::getMultiple(array('PS_SHOP_NAME', 'EMC_LOGIN', 'EMC_GENDER', 'EMC_LAST_NAME', 'EMC_FIRST_NAME', 'EMC_ADDRESS', 'EMC_ZIP_CODE', 'EMC_CITY', 'EMC_COUNTRY',
											 'EMC_PHONE', 'EMC_EMAIL'));
		$link = '<a href="http://www.envoimoinscher.com/inscription.html?tracking=prestashop_module_v1
		&login='.(isset($confs['EMC_LOGIN']) ? htmlspecialchars($confs['EMC_LOGIN'], ENT_COMPAT, 'UTF-8') : '' ).'
		&facturation.contact_civ='.(isset($genderTab[(int)(Configuration::get('EMC_GENDER'))]) ? htmlspecialchars($genderTab[(int)(Configuration::get('EMC_GENDER'))], ENT_COMPAT, 'UTF-8') : '' ).'
		&facturation.contact_ste='.(isset($confs['PS_SHOP_NAME']) ? htmlspecialchars($confs['PS_SHOP_NAME'], ENT_COMPAT, 'UTF-8') : '' ).'
		&facturation.contact_nom='.(isset($confs['EMC_LAST_NAME']) ? htmlspecialchars($confs['EMC_LAST_NAME'], ENT_COMPAT, 'UTF-8') : '' ).'
		&facturation.contact_prenom='.(isset($confs['EMC_FIRST_NAME']) ? htmlspecialchars($confs['EMC_FIRST_NAME'], ENT_COMPAT, 'UTF-8') : '' ).'
		&user_type=entreprise&facturation.pz_id='.(isset($confs['EMC_COUNTRY']) ? htmlspecialchars($confs['EMC_COUNTRY'], ENT_COMPAT, 'UTF-8') : '' ).'
		&facturation.adresse1='.(isset($confs['EMC_ADDRESS']) ? htmlspecialchars($confs['EMC_ADDRESS'], ENT_COMPAT, 'UTF-8') : '' ).'
		&facturation.contact_cp='.(isset($confs['EMC_ZIP_CODE']) ? htmlspecialchars($confs['EMC_ZIP_CODE'], ENT_COMPAT, 'UTF-8') : '' ).'
		&facturation.contact_ville='.(isset($confs['EMC_CITY']) ? htmlspecialchars($confs['EMC_CITY'], ENT_COMPAT, 'UTF-8') : '' ).'
		&facturation.contact_tel='.(isset($confs['EMC_PHONE']) ? htmlspecialchars($confs['EMC_PHONE'], ENT_COMPAT, 'UTF-8') : '' ).'
		&facturation.contact_email='.(isset($confs['EMC_EMAIL']) ? htmlspecialchars($confs['EMC_EMAIL'], ENT_COMPAT, 'UTF-8') : '' ).'
		&url_renvoi='.urlencode(Tools::getProtocol().htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').$_SERVER['REQUEST_URI']).'">';
		
						
		$this->_html .= '<form action="'.$_SERVER['REQUEST_URI'].'" method="post" class="form">
		<div style="float: right; width: 440px; height: 165px; border: 1px dashed rgb(102, 102, 102); padding: 8px; margin-left: 12px;margin-top: 11px;">'.$link.'
			<h3>'.$this->l('Create account Envoimoinscher :').'</h3>
			<p style="text-align:justify">'.$this->l('To create your account on Envoimoinscher, click the image below. You will go to a dedicated personal space where you will find the necessary tools for easy management of your shipments.').'</p>
			<img src="'.$this->_path.'/ps_emc.png" alt="" /></a>
		</div>
		<fieldset style="width:420px;float:left"><legend><img src="'.$this->_path.'logo.gif" alt="" /> '.$this->l('Description').'</legend>
		<img style="float:left;margin-right:10px" src="'.$this->_path.'logocarre.png" alt="" /><p style="text-align:justify"><b>'.$this->l('This module allows you to compare carrier offers and online ordering services\' negotiated delivery rates.').'</b></p>
		<p style="text-align:justify">'.$this->l('Please enter your Envoimoinscher username below. If you are not a customer Envoimoinscher, you can easily create an account by clicking').$link.'
		'.$this->l('here').'.</a></p><br>
		<h3 style="text-align:center"><a href="http://www.prestashop.com/download/partner_modules/docs/doc_emc.pdf">'.$this->l('Download Documentation').'</a></h3>
		</fieldset>
		<div class="clear">&nbsp;</div>
		
		<fieldset class="width2"><legend><img src="'.$this->_path.'logo.gif" alt="" /> '.$this->l('Settings').'</legend>
		<label>'.$this->l('Width').' : </label>
		<div class="margin-form">
			<select name="EMC_WIDTH">
				<option value="O">'.$this->l('Choose a feature ...').'</option>';
			foreach($features as $feature)
			{
				$this->_html .= '<option value="'.(int)($feature['id_feature']).'" '.((int)(Tools::getValue('EMC_WIDTH', (int)(Configuration::get('EMC_WIDTH')))) == (int)($feature['id_feature']) ? ' selected="selected" ' : '').' >
				'.htmlspecialchars($feature['name'], ENT_COMPAT, 'UTF-8').'</option>';
			}
		$this->_html .= '</select>
		<sup> *</sup>
		<p>' . $this->l('Choose width in the list') . '</p>
		</div>	
		<div class="clear">&nbsp;</div>
		
		<label>'.$this->l('Height').' : </label>
		<div class="margin-form">
			<select name="EMC_HEIGHT">
				<option value="O">'.$this->l('Choose a feature ...').'</option>';
				
			foreach($features as $feature)
			{
				$this->_html .= '<option value="'.(int)($feature['id_feature']).'" '.((int)(Tools::getValue('EMC_HEIGHT', (int)(Configuration::get('EMC_HEIGHT')))) == (int)($feature['id_feature']) ? ' selected="selected" ' : '').' >
				'.htmlspecialchars($feature['name'], ENT_COMPAT, 'UTF-8').'</option>';
			}
		$this->_html .= '</select>
		<sup> *</sup>
		<p>' . $this->l('Choose Height in the list') . '</p>
		</div>	
		<div class="clear">&nbsp;</div>
		
		<label>'.$this->l('Depth').' : </label>
		<div class="margin-form">
			<select name="EMC_DEPTH">
				<option value="O">'.$this->l('Choose a feature ...').'</option>';
			foreach($features as $feature)
			{
				$this->_html .= '<option value="'.(int)($feature['id_feature']).'" '.((int)(Tools::getValue('EMC_DEPTH', (int)(Configuration::get('EMC_DEPTH')))) == (int)($feature['id_feature']) ? ' selected="selected" ' : '').' >
				'.htmlspecialchars($feature['name'], ENT_COMPAT, 'UTF-8').'</option>';
			}
		$this->_html .= '</select>
		<sup> *</sup>
		<p>' . $this->l('Choose Depth in the list') . '</p>
		</div>	
		<div class="clear">&nbsp;</div>

		<label for="id_order_state">'.$this->l('Packaging').' :</label>
		<div class="margin-form">
		<select name="EMC_PACKAGING_DEFAULT">
				<option value="">'.$this->l('Choose a Packaging ...').'</option>';
				foreach($this->packaging as $package => $value)
					$this->_html .= '<option '.(Tools::getValue('EMC_PACKAGING_DEFAULT', Configuration::get('EMC_PACKAGING_DEFAULT')) == $value ? ' selected="selected" ' : '').' value="'.$value.'">'.htmlspecialchars($package, ENT_COMPAT, 'UTF-8').'</option>';
		$this->_html .= '</select>
		<p>'. $this->l('Choose the packaging by default.').'</p>
		</div>
		<div class="clear">&nbsp;</div>
		
		<label for="id_order_state">'.$this->l('Nature of content').' :</label>
		<div class="margin-form">'.
		self::selectNature(Tools::getValue('type_objet', Configuration::get('EMC_CONTENT'))).
		'<p>'. $this->l('Choose the Nature of content by default.').'</p>
		</div>
		<div class="clear">&nbsp;</div>

		<label for="id_order_state">'.$this->l('Order state to export').' :</label>
		<div class="margin-form">
		<select name="EMC_ORDER_STATE">
				<option value="O">'.$this->l('Choose a state ...').'</option>';
		foreach ( $order_states as $state)
		{
			$this->_html .= '<option value="'.(int)($state['id_order_state']). '" style="background-color:' .$state['color'].';"';
			if (Tools::getValue('EMC_ORDER_STATE', Configuration::get('EMC_ORDER_STATE')) == (int)($state['id_order_state'])) 
				$this->_html .= ' selected="selected"';
			$this->_html .= '>'.htmlspecialchars($state['name'], ENT_COMPAT, 'UTF-8').'</option>';
		}
		$this->_html .= '</select>
		<sup> *</sup>
		<p>'. $this->l('Choose the order state to export to Envoi Moins Cher.').'</p>
		</div>
		<div class="clear">&nbsp;</div>	
		
		<label>'.$this->l('Carrier').' : </label>
		<div class="margin-form">
			<select name="EMC_CARRIER">
				<option value="O">'.$this->l('Choose a carrier ...').'</option>';
			foreach($carriers as $carrier)
				$this->_html .= '<option value="'.(int)($carrier['id_carrier']).'" '.(Tools::getValue('EMC_CARRIER', Configuration::get('EMC_CARRIER')) == $carrier['id_carrier'] ? ' selected="selected" ' : '').' >
				'.htmlspecialchars($carrier['name'], ENT_COMPAT, 'UTF-8').'</option>';
		$this->_html .= '</select>
		<sup> *</sup>
		<p>' . $this->l('Choose a carrier in the list') . '</p>
		</div>
		<div class="clear">&nbsp;</div>	
		
		<label for="id_order_state">'.$this->l('Order state "Order past"').' :</label>
		<div class="margin-form">
		<select name="EMC_ORDER_PAST_STATE">
				<option value="O">'.$this->l('Choose a state ...').'</option>';
		foreach ( $order_states as $state)
		{
			$this->_html .= '<option value="'.(int)($state['id_order_state']).'" style="background-color:'.$state['color'] . ';"';
			if (Tools::getValue('EMC_ORDER_PAST_STATE', Configuration::get('EMC_ORDER_PAST_STATE')) == $state['id_order_state'] ) $this->_html .= ' selected="selected"';
			$this->_html .= '>'.htmlspecialchars($state['name'], ENT_COMPAT, 'UTF-8'). '</option>';
		}
		$this->_html .= '</select>
		<sup> *</sup>
		<p>'. $this->l('Choose the order state past.').'</p>
		</div>
		<div class="clear">&nbsp;</div>		
		
		<label for="id_order_state">'.$this->l('Order state send').' :</label>
		<div class="margin-form">
		<select name="EMC_SEND_STATE">
				<option value="O">'.$this->l('Choose a state ...').'</option>';
		foreach ( $order_states as $state)
		{
			$this->_html .= '<option value="' . $state['id_order_state'] . '" style="background-color:' . $state['color'] . ';"';
			if (Tools::getValue('EMC_SEND_STATE', Configuration::get('EMC_SEND_STATE')) == $state['id_order_state']) $this->_html .= ' selected="selected"';
			$this->_html .= '>'.htmlspecialchars($state['name'], ENT_COMPAT, 'UTF-8').'</option>';
		}
		$this->_html .= '</select>
		<sup> *</sup>
		<p>'. $this->l('Choose the order state send.').'</p>
		</div>
		<div class="clear">&nbsp;</div>';
		
		//objet livré (pas géré actuellement)
		$this->_html .= '<label for="id_order_state">'.$this->l('Order state delivered').' :</label>
		<div class="margin-form">
		<select name="EMC_DELIVERY_STATE">
				<option value="O">'.$this->l('Choose a state ...').'</option>';
		foreach ( $order_states as $state)
		{
			$this->_html .= '<option value="'.(int)($state['id_order_state']).'" style="background-color:'.$state['color'].';"';
			if (Tools::getValue('EMC_DELIVERY_STATE', (int)(Configuration::get('EMC_DELIVERY_STATE'))) == (int)($state['id_order_state'])) $this->_html .= ' selected="selected"';
			$this->_html .= '>'.htmlspecialchars($state['name'], ENT_COMPAT, 'UTF-8').'</option>';
		}
		$this->_html .= '</select>
		<sup> *</sup>
		<p>'. $this->l('Choose the order state delivered.').'</p>
		</div>
		<div class="clear">&nbsp;</div>	';
		
		$this->_html .= '
		<label>'.$this->l('E-mail').' : </label>
		<div class="margin-form">
			<input type="radio" name="EMC_EMAILS" id="active_on" value="7" '.(Tools::getValue('EMC_EMAILS', Configuration::get('EMC_EMAILS')) ? 'checked="checked" ' : '').'/>
			<label class="t" for="active_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
			<input type="radio" name="EMC_EMAILS" id="active_off" value="0" '.(!Tools::getValue('EMC_EMAILS', Configuration::get('EMC_EMAILS')) ? 'checked="checked" ' : '').'/>
			<label class="t" for="active_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
			<p>'.$this->l('Enables or disables sending mail from EMC');//.', '.$this->l('unless it informs the recipient that it is sent by default.').'</p>
		$this->_html .= '</div>
		<div class="clear">&nbsp;</div>
		
		<label>'.$this->l('EMC Login').' : </label>
		<div class="margin-form">
		<input type="text" name="EMC_LOGIN" value="'.htmlspecialchars(Tools::getValue('EMC_LOGIN', Configuration::get('EMC_LOGIN')), ENT_COMPAT, 'UTF-8').'">
		<sup> *</sup>
		<p>' . $this->l('Set your EMC login') . '</p>
		</div>	
		<div class="clear">&nbsp;</div>
		
		<p>----------------------------------------------------------------------------------------</p>
		<h2>'.$this->l('Sender Information').' : </h2>
		<label>'.$this->l('Gender').' : </label>
		<div class="margin-form">
		<input name="EMC_GENDER" id="id_gender1" value="1" '.(Tools::getValue('EMC_GENDER', Configuration::get('EMC_GENDER')) == 1 ? 'checked="checked"' : '').' type="radio"> M.
		<input name="EMC_GENDER" id="id_gender2" value="2" '.(Tools::getValue('EMC_GENDER', Configuration::get('EMC_GENDER')) == 2 ? 'checked="checked"' : '').' type="radio"> Mme
		<p>' . $this->l('Select gender of sender') . '</p>
		</div>	
		<div class="clear">&nbsp;</div>
		
		<label>'.$this->l('Last name').' : </label>
		<div class="margin-form">
		<input type="text" name="EMC_LAST_NAME" value="'.htmlspecialchars(Tools::getValue('EMC_LAST_NAME', Configuration::get('EMC_LAST_NAME')), ENT_COMPAT, 'UTF-8').'">
		<sup> *</sup>
		<p>' . $this->l('Set the Last Name of sender') . '</p>
		</div>	
		<div class="clear">&nbsp;</div>
		
		<label>'.$this->l('First name').' : </label>
		<div class="margin-form">
		<input type="text" name="EMC_FIRST_NAME" value="'.htmlspecialchars(Tools::getValue('EMC_FIRST_NAME', Configuration::get('EMC_FIRST_NAME')), ENT_COMPAT, 'UTF-8').'">
		<sup> *</sup>
		<p>' . $this->l('Set the First name of sender') . '</p>
		</div>	
		<div class="clear">&nbsp;</div>
		
		<label>'.$this->l('Address').' : </label>
		<div class="margin-form">
		<input size="40" type="text" name="EMC_ADDRESS" value="'.htmlspecialchars(Tools::getValue('EMC_ADDRESS', Configuration::get('EMC_ADDRESS')), ENT_COMPAT, 'UTF-8').'">
		<sup> *</sup>
		<p>' . $this->l('Set the Address of sender') . '</p>
		</div>	
		<div class="clear">&nbsp;</div>	
		
		<label>'.$this->l('Zip code').' : </label>
		<div class="margin-form">
		<input type="text" name="EMC_ZIP_CODE" value="'.htmlspecialchars(Tools::getValue('EMC_ZIP_CODE', Configuration::get('EMC_ZIP_CODE')), ENT_COMPAT, 'UTF-8').'">
		<sup> *</sup>
		<p>' . $this->l('Set the zip code of sender') . '</p>
		</div>	
		<div class="clear">&nbsp;</div>	
		
		<label>'.$this->l('City').' : </label>
		<div class="margin-form">
		<input type="text" name="EMC_CITY" value="'.htmlspecialchars(Tools::getValue('EMC_CITY', Configuration::get('EMC_CITY')), ENT_COMPAT, 'UTF-8').'">
		<sup> *</sup>
		<p>' . $this->l('Set the city of sender') . '</p>
		</div>	
		<div class="clear">&nbsp;</div>	
		
		<label>'.$this->l('Country').' : </label>
		<div class="margin-form">
		<select name="EMC_COUNTRY">
				<option value="">'.$this->l('Choose a country ...').'</option>';
		foreach ($countries as $country)
		{
			$this->_html .= '<option value="'.htmlspecialchars($country['iso_code'], ENT_COMPAT, 'UTF-8').'" ';
			if (Tools::getValue('EMC_COUNTRY',Configuration::get('EMC_COUNTRY')) == $country['iso_code'] ) $this->_html .= ' selected="selected"';
			$this->_html .= '>'.htmlspecialchars($country['name'], ENT_COMPAT, 'UTF-8').'</option>';
		}
		$this->_html .= '</select>		<sup> *</sup>
		<p>' . $this->l('Select the country of sender in the list') . '</p>
		</div>	
		<div class="clear">&nbsp;</div>	
		
		<label>'.$this->l('Phone').' : </label>
		<div class="margin-form">
		<input type="text" name="EMC_PHONE" value="'.htmlspecialchars(Tools::getValue('EMC_PHONE', Configuration::get('EMC_PHONE')), ENT_COMPAT, 'UTF-8').'">
		<sup> *</sup>
		<p>' . $this->l('Set the Phone of sender').'</p>
		</div>	
		<div class="clear">&nbsp;</div>
		
		<label>'.$this->l('Email').' : </label>
		<div class="margin-form">
		<input type="text" name="EMC_EMAIL" value="'.htmlspecialchars(Tools::getValue('EMC_EMAIL', Configuration::get('EMC_EMAIL')), ENT_COMPAT, 'UTF-8').'">
		<sup> *</sup>
		<p>' . $this->l('Set the e-mail of sender').'</p>
		</div>	
		<div class="clear">&nbsp;</div>		
		
		<div class="margin-form">
		<input type="submit" value="'.$this->l('Save').'" name="submitSave" class="button" style="margin:10px 0px 0px 25px;" />
		</div>
		</fieldset></form>
		<div class="clear">&nbsp;</div>';
		
	}
	
	private function _postValidation()
	{				
		if (Tools::getValue('EMC_WIDTH') == 0)
			$this->_postErrors[]  = $this->l('Width not specified');
		if (Tools::getValue('EMC_HEIGHT') == 0)
			$this->_postErrors[]  = $this->l('Height not specified');
		if (Tools::getValue('EMC_DEPTH') == 0)
			$this->_postErrors[]  = $this->l('Depth not specified');
		if (Tools::getValue('EMC_ORDER_STATE') == 0)
			$this->_postErrors[]  = $this->l('Order state not specified');
		if (Tools::getValue('EMC_CARRIER') == 0)
			$this->_postErrors[]  = $this->l('Carrier not specified');
		if (Tools::getValue('EMC_ORDER_PAST_STATE') == 0)
			$this->_postErrors[]  = $this->l('Order state "order past" not specified');
		if (Tools::getValue('EMC_SEND_STATE') == 0)
			$this->_postErrors[]  = $this->l('Order state send not specified');
		if (Tools::getValue('EMC_DELIVERY_STATE') == 0)
			$this->_postErrors[]  = $this->l('Order state delivery not specified');
		if (Tools::getValue('EMC_LAST_NAME') == '')
			$this->_postErrors[]  = $this->l('Last name not specified');
		if (Tools::getValue('EMC_FIRST_NAME') == '')
			$this->_postErrors[]  = $this->l('First name not specified');
		if (Tools::getValue('EMC_ADDRESS') == '')
			$this->_postErrors[]  = $this->l('Address not specified');
		if (Tools::getValue('EMC_ZIP_CODE') == '')
			$this->_postErrors[]  = $this->l('Zip Code not specified');
		if (Tools::getValue('EMC_CITY') == '')
			$this->_postErrors[]  = $this->l('City not specified');
		if (Tools::getValue('EMC_COUNTRY') == '')
			$this->_postErrors[]  = $this->l('Country not specified');
		if (Tools::getValue('EMC_PHONE') == '')
			$this->_postErrors[]  = $this->l('Phone not specified');
		if (Tools::getValue('EMC_EMAIL') == '')
			$this->_postErrors[]  = $this->l('E-mail not specified');
		if (Tools::getValue('EMC_LOGIN') == '')
			$this->_postErrors[]  = $this->l('Login not specified');			
	}
	
	private function _postProcess()
	{	
		if (Configuration::updateValue('EMC_WIDTH', (int)(Tools::getValue('EMC_WIDTH'))) AND Configuration::updateValue('EMC_HEIGHT', (int)(Tools::getValue('EMC_HEIGHT'))) AND 
			Configuration::updateValue('EMC_DEPTH', (int)(Tools::getValue('EMC_DEPTH'))) AND Configuration::updateValue('EMC_ORDER_STATE', (int)(Tools::getValue('EMC_ORDER_STATE'))) AND 
			Configuration::updateValue('EMC_CARRIER', (int)(Tools::getValue('EMC_CARRIER'))) AND Configuration::updateValue('EMC_PACKAGING_DEFAULT', Tools::getValue('EMC_PACKAGING_DEFAULT'))
			AND Configuration::updateValue('EMC_GENDER', Tools::getValue('EMC_GENDER')) AND Configuration::updateValue('EMC_LAST_NAME', Tools::getValue('EMC_LAST_NAME'))
			AND Configuration::updateValue('EMC_FIRST_NAME', Tools::getValue('EMC_FIRST_NAME')) AND Configuration::updateValue('EMC_ADDRESS', Tools::getValue('EMC_ADDRESS'))
			AND Configuration::updateValue('EMC_ZIP_CODE', Tools::getValue('EMC_ZIP_CODE')) AND Configuration::updateValue('EMC_CITY', Tools::getValue('EMC_CITY'))
			AND Configuration::updateValue('EMC_COUNTRY', Tools::getValue('EMC_COUNTRY')) AND Configuration::updateValue('EMC_PHONE', Tools::getValue('EMC_PHONE'))
			AND Configuration::updateValue('EMC_EMAIL', Tools::getValue('EMC_EMAIL')) AND Configuration::updateValue('EMC_ORDER_PAST_STATE', Tools::getValue('EMC_ORDER_PAST_STATE'))
			AND Configuration::updateValue('EMC_SEND_STATE', Tools::getValue('EMC_SEND_STATE')) AND Configuration::updateValue('EMC_EMAILS', Tools::getValue('EMC_EMAILS'))
			AND Configuration::updateValue('EMC_LOGIN', Tools::getValue('EMC_LOGIN')) AND Configuration::updateValue('EMC_CONTENT', Tools::getValue('type_objet_'))
			AND Configuration::updateValue('EMC_DELIVERY_STATE', Tools::getValue('EMC_DELIVERY_STATE')))
		{
			$dataSync = (($emc_login = Configuration::get('EMC_LOGIN'))
				? '<img src="http://www.prestashop.com/modules/envoimoinscher.png?ps_id='.urlencode($emc_login).'" style="float:right" />' : '');
			$this->_html .= $this->displayConfirmation($this->l('Configuration updated').$dataSync);
		}
		else
			$this->_html .= '<div class="alert error"><img src="' . _PS_IMG_ . 'admin/forbbiden.gif" alt="nok" />&nbsp;'.$this->l('Settings failed').'</div>';			
	}
	
	static function selectNature($selected = '', $id = '')
	{
		$select = '<select name="type_objet_'.$id.'">
					<option value="ns">Contenu non spécifié</option>';
		$groups = array(1 => 'Documents', 2 => 'Alimentation et matières périssables', 3 => 'Produits', 4 => 'Habillement et accessoires', 5 => 'Appareils et matériels', 6 => 'Mobilier et décoration',
						7 => 'Effets personnels, cadeaux');
		$select .= '<optgroup label="'.htmlspecialchars($groups[1], ENT_COMPAT, 'UTF-8').'">';
		$optgroup = 1;
		$results = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'envoimoinscher_contenu order by id');
		foreach($results as $result)
		{
			if ($result['id'][0] != $optgroup)
			{
				$optgroup ++;
				
				$select .= '</optgroup><optgroup label="'.htmlspecialchars($groups[$optgroup], ENT_COMPAT, 'UTF-8').'">';
			}
			$select .= '<option '.($selected == $result['id'] ? 'selected="selected"' : '').' value="'.(int)($result['id']).'" >'.htmlspecialchars($result['libelle'], ENT_COMPAT, 'UTF-8').'</option>';
		}
		$select .= '</select>';
		return $select;	
	}
	
	public function hookAdminOrder($params)
	{	
	
		$order = new Order($params['id_order']);
		if ($order->id_carrier == Configuration::get('EMC_CARRIER'))
		{
			$return = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'envoimoinscher WHERE id_order = \''.(int)($order->id).'\' LIMIT 1');
			if (isset($return[0]['shipping_number']))
			{
				$html = '<br><br><fieldset style="width: 400px;"><legend><img src="'.$this->_path.'logo.gif" alt="" /> '.$this->l('Envoimoinscher').'</legend>';
				$html .= '<b>'.$this->l('Delivery Information').' : </b><br>			
				'.(isset($return[0]['shipping_number']) ? '<p>--> <a href="http://www.envoimoinscher.com/suivre_vos_envois.html?reference='.$return[0]['shipping_number'].'" style="text-decoration:underline">
				'.$this->l('Follow shipping').'</a>' : '<p style="color:red">'.$this->l('No shipping number')).'</p>'
				.(isset($return[0]['shipping_number']) ?' <p>--> <a href="http://www.envoimoinscher.com/documents?type=bordereau&envoi='.$return[0]['shipping_number'].'" style="text-decoration:underline">
				'.$this->l('Print delivery slips').'</a>' : '<p style="color:red">'.$this->l('No delivery slips number')).'</p>
				</fieldset>';
				return $html;
			}
		}
	}
	
	public function lang($str)
	{
		switch($str)
		{
		case 'No order to export':
			return $this->l('No order to export');
		break;
		case 'Please configure this module in order':
			return $this->l('Please configure this module in order');
		break;
		case 'Change configuration':
			return $this->l('Change configuration');
		break;
		case 'ID':
			return $this->l('ID');
		break;
		case 'Name':
			return $this->l('Name');
		break;
		case 'Total Cost':
			return $this->l('Total Cost');
		break;
		case 'Total shipment':
			return $this->l('Total shipment');
		break;
		case 'Date':
			return $this->l('Date');
		break;
		case 'Packaging':
			return $this->l('Packaging');
		break;
		case 'Nature of contents':
			return $this->l('Nature of contents');
		break;
		case 'Detail':
			return $this->l('Detail');
		break;
		case 'View':
			return $this->l('View');
		break;
		case 'Send':
			return $this->l('Send');
		break;
		case 'List of orders to export':
			return $this->l('List of orders to export');
		break;
		}
	}	
}

