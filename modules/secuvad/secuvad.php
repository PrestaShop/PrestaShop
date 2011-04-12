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

class Secuvad extends Module
{	
	private $_html = '';
  	private $_postErrors = array();
  	private $_allowed_modes = array('TEST', 'PROD');
	private $_secuvad_category = array();
	private $_secuvad_assoc_category = array();
	private $_secuvad_code_payment = array();
	private $_secuvad_carrier_type = array();
	private $_secuvad_carrier_delay = array();

	public function __construct()
	{
  		$this->name = 'secuvad';
  		$this->tab = 'payment_security';
  		$this->version = '2.0.1';
  		$this->currencies = NULL;
  		$this->currencies_mode = NULL;
		
		parent::__construct();
  		$this->displayName = $this->l('Secuvad module');
  		$this->description = $this->l('Solution fighting against online fraud');
  		$this->confirmUninstall = $this->l('Are you sure you want to delete this module?');
	}
	
	public function install()
	{
	  	if(!parent::install()
			|| !$this->registerHook('paymentConfirm')
			|| !$this->registerHook('adminOrder')
			|| !Configuration::updateValue('SECUVAD_CONTACT', 'prestashop@secuvad.com') 
			|| !Configuration::updateValue('SECUVAD_ACTIVATION', '0') 
			|| !Configuration::updateValue('SECUVAD_ID', '') 
			|| !Configuration::updateValue('SECUVAD_IP', '91.121.209.139,91.121.209.140') 
			|| !Configuration::updateValue('SECUVAD_IP_CONFIG', '91.213.82.241') 
			|| !Configuration::updateValue('SECUVAD_IP_TEST', '91.121.147.62') 
			|| !Configuration::updateValue('SECUVAD_NB_LOG_REPORTED', '100') 
			|| !Configuration::updateValue('SECUVAD_MAX_LOG_SIZE', '200') 
			|| !Configuration::updateValue('SECUVAD_LOG_SIZE', '100') 
			|| !Configuration::updateValue('SECUVAD_LOGIN', '') 
			|| !Configuration::updateValue('SECUVAD_MDP', '') 
			|| !Configuration::updateValue('SECUVAD_MODE', 'TEST') 
			|| !Configuration::updateValue('SECUVAD_RANDOM', 'b9ffecdde6169472ce33e354131cd26d34641358') 
			|| !Configuration::updateValue('SECUVAD_URL_PROD', 'www.secuvad.com/submission/index.php') 
			|| !Configuration::updateValue('SECUVAD_URL_TEST', 'www.secuvad-test.com/submission/index.php') 
			|| !Configuration::updateValue('SECUVAD_XML_ENCODING', 'utf-8'))
	    		return false;
		
		if (!file_exists(dirname(__FILE__).'/install.sql'))
			die(Tools::displayError('File install.sql is missing'));
		elseif (!$sql = file_get_contents(dirname(__FILE__).'/install.sql'))
			die(Tools::displayError('File install.sql is not readable'));
		$sql = str_replace(array('PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
		
		$sql = preg_split("/;\s*[\r\n]+/", $sql);
		foreach ($sql as $query)
			if ($query AND sizeof($query) AND !Db::getInstance()->Execute(trim($query)))
				return false;
		
		$langs = Language::getLanguages();
		$query = '
		INSERT IGNORE INTO '._DB_PREFIX_.'secuvad_category(`category_id`, `category_name`, `sort_num`, `id_lang`)
		VALUES ';
		foreach ($langs as $lang)
			if ($lang['iso_code'] == 'fr')
			{
				$query .= '
				(1, \'Informatique et Logiciels\', 101,'.(int)($lang['id_lang']).'),
				(2,\'Téléphonie, Télécommunications\',102,'.(int)($lang['id_lang']).'),
				(3,\'Matériels HiFi, Vidéo, Photo\',103,'.(int)($lang['id_lang']).'),
				(4,\'Voyages, Tourisme\',104,'.(int)($lang['id_lang']).'),
				(5,\'Vêtements, accessoires de mode\',105,'.(int)($lang['id_lang']).'),
				(6,\'Sport\',106,'.(int)($lang['id_lang']).'),
				(7,\'Electroménager\',107,'.(int)($lang['id_lang']).'),
				(8,\'Cadeaux, fleurs\',108,'.(int)($lang['id_lang']).'),
				(9,\'Accessoires de maison et jardin\',109,'.(int)($lang['id_lang']).'),
				(10,\'Auto, moto et accessoires\',110,'.(int)($lang['id_lang']).'),
				(11,\'Alimentation\',111,'.(int)($lang['id_lang']).'),
				(12,\'Culture, divertissements\',112,'.(int)($lang['id_lang']).'),
				(13,\'Beauté, santé\',113,'.(int)($lang['id_lang']).'),
				(14,\'Services\',114,'.(int)($lang['id_lang']).'),';
			}
			else
			{
				$query .= '
				(1,\'Computing and Softwares\',1,'.(int)($lang['id_lang']).'),
				(2,\'Phones, Telecommunications\',2,'.(int)($lang['id_lang']).'),
				(3,\'HiFi, Video, Photo\',3,'.(int)($lang['id_lang']).'),
				(4,\'Travels, Tourism\',4,'.(int)($lang['id_lang']).'),
				(5,\'Clothes, fashion accessories\',5,'.(int)($lang['id_lang']).'),
				(6,\'Sport\',6,'.(int)($lang['id_lang']).'),
				(7,\'Domestic appliances\',7,'.(int)($lang['id_lang']).'),
				(8,\'Gifts, flowers\',8,'.(int)($lang['id_lang']).'),
				(9,\'Home and garden goods\',9,'.(int)($lang['id_lang']).'),
				(10,\'Cars, motorbikes and accessories\',10,'.(int)($lang['id_lang']).'),
				(11,\'Food\',11,'.(int)($lang['id_lang']).'),
				(12,\'Culture, entertainement\',12,'.(int)($lang['id_lang']).'),
				(13,\'Beauty, healthcare\',13,'.(int)($lang['id_lang']).'),
				(14,\'Services\',14,'.(int)($lang['id_lang']).'),';
			}
		$query = rtrim($query, ',');
		Db::getInstance()->Execute($query);
		
		$query = '
		INSERT IGNORE INTO '._DB_PREFIX_.'secuvad_payment(`code`, `name`, `id_lang`)
		VALUES ';
		foreach ($langs as $lang)
			if ($lang['iso_code'] == 'fr')
			{
				$query .= '
				(\'carte\',\'Carte bancaire\','.(int)($lang['id_lang']).'),
				(\'cheque\',\'Chèque\','.(int)($lang['id_lang']).'),
				(\'virement\',\'Virement\','.(int)($lang['id_lang']).'),
				(\'paypal\',\'Tiers de paiement\','.(int)($lang['id_lang']).'),
				(\'cb en n fois\',\'Carte bancaire en plusieurs fois\','.(int)($lang['id_lang']).'),
				(\'contre-remboursement\',\'Contre-Remboursement\','.(int)($lang['id_lang']).'),';
			}
			else
			{
				$query .= '
				(\'carte\',\'Credit card\','.(int)($lang['id_lang']).'),
				(\'cheque\',\'Check\','.(int)($lang['id_lang']).'),
				(\'virement\',\'Transfer\','.(int)($lang['id_lang']).'),
				(\'paypal\',\'Third-party payment\','.(int)($lang['id_lang']).'),
				(\'cb en n fois\',\'Credit Card (n times)\','.(int)($lang['id_lang']).'),
				(\'contre-remboursement\',\'On delivery\','.(int)($lang['id_lang']).'),';
			}
		$query = rtrim($query, ',');
		Db::getInstance()->Execute($query);
		
		$query = '
		INSERT IGNORE INTO '._DB_PREFIX_.'secuvad_transport(`transport_id`, `transport_name`, `id_lang`)
		VALUES ';
		foreach ($langs as $lang)
			if ($lang['iso_code'] == 'fr')
			{
				$query .= '
				(1,\'Retrait chez le commerçant\','.(int)($lang['id_lang']).'),
				(2,\'Retrait dans un point de retrait tiers\','.(int)($lang['id_lang']).'),
				(3,\'Coliposte\','.(int)($lang['id_lang']).'),
				(4,\'Chronopost\','.(int)($lang['id_lang']).'),
				(5,\'Envoi remis sans signature\','.(int)($lang['id_lang']).'),
				(6,\'Bien/service immatériel\','.(int)($lang['id_lang']).'),';
			}
			else
			{
				$query .= '
				(1,\'Merchant Warehouse\','.(int)($lang['id_lang']).'),
				(2,\'Other withdrawal\','.(int)($lang['id_lang']).'),
				(3,\'Public conveyor with signature\','.(int)($lang['id_lang']).'),
				(4,\'Private conveyor with signature\','.(int)($lang['id_lang']).'),
				(5,\'Public conveyor without signature\','.(int)($lang['id_lang']).'),
				(6,\'Immaterial Good/Service\','.(int)($lang['id_lang']).'),';
			}
		$query = rtrim($query, ',');
		Db::getInstance()->Execute($query);		
		
		$query = '
		INSERT IGNORE INTO `'._DB_PREFIX_.'secuvad_transport_delay`(`transport_delay_id`, `transport_delay_name`, `id_lang`)
		VALUES ';
		foreach ($langs as $lang)
			$query .= '
			(1,\'express\','.(int)($lang['id_lang']).'),
			(2,\'standard\','.(int)($lang['id_lang']).'),';
		$query = rtrim($query, ',');
		Db::getInstance()->Execute($query);
		
		if (!file_exists(dirname(__FILE__).'/../../classes/PaymentCC.php'))
		{
			@copy(dirname(__FILE__).'/classes/PaymentCC.php', dirname(__FILE__).'/../../classes/PaymentCC.php');
			Db::getInstance()->Execute('
			INSERT INTO `'._DB_PREFIX_.'hook`(`name`, `title`, `position`) 
			VALUES (\'paymentCCAdded\', \'paymentCCAdded\', 0)');
			$this->registerHook('paymentCCAdded');
		}
		else
			$this->registerHook('paymentCCAdded');
		
		return true;
	}

	public function uninstall()
	{
		if(!parent::uninstall()
	    || !Configuration::deleteByName('SECUVAD_CONTACT')
	    || !Configuration::deleteByName('SECUVAD_ACTIVATION')
	    || !Configuration::deleteByName('SECUVAD_ID')
	    || !Configuration::deleteByName('SECUVAD_IP')
	    || !Configuration::deleteByName('SECUVAD_IP_CONFIG')
	    || !Configuration::deleteByName('SECUVAD_IP_TEST')
	    || !Configuration::deleteByName('SECUVAD_NB_LOG_REPORTED')
	    || !Configuration::deleteByName('SECUVAD_MAX_LOG_SIZE')
	    || !Configuration::deleteByName('SECUVAD_LOG_SIZE')
	    || !Configuration::deleteByName('SECUVAD_LOGIN')
	    || !Configuration::deleteByName('SECUVAD_MDP')
	    || !Configuration::deleteByName('SECUVAD_MODE')
	    || !Configuration::deleteByName('SECUVAD_RANDOM')
	    || !Configuration::deleteByName('SECUVAD_URL_PROD')
	    || !Configuration::deleteByName('SECUVAD_URL_TEST')
	    || !Configuration::deleteByName('SECUVAD_XML_ENCODING'))
	    return false;
		
		Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'secuvad_assoc_category`');
		Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'secuvad_assoc_payment`');
		Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'secuvad_assoc_transport`');
		Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'secuvad_category`');
		Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'secuvad_logs`');
		Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'secuvad_order`');
		Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'secuvad_payment`');
		Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'secuvad_transport`');
		Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'secuvad_transport_delay`');
		return true;
	}

	public function hookAdminOrder($params)
	{
		global $cookie, $currentIndex;
		
		if ($this->check_assoc() != '')
			return '
			<br />
			<fieldset style="width:400px;">
				<legend><img src="'._PS_BASE_URL_.__PS_BASE_URI__.'modules/'.$this->name.'/logo.gif" alt="" /> '.$this->l('Secuvad').'</legend>
				<p style="color:red;font-weight:bold;">'.$this->l('In order to use Secuvad protection, please configure your module.').'</p>
			</fieldset>
			';
		
		$secuvad_order = Db::getInstance()->getRow('
		SELECT * 
		FROM `'._DB_PREFIX_.'secuvad_order` 
		WHERE `id_secuvad_order` = '.(int)($params['id_order']).'
		');
		if (is_array($secuvad_order) AND sizeof($secuvad_order))
		{
			if (Tools::isSubmit('send_to_secuvad'))
				$this->_sendToSecuvad();
			elseif (Tools::isSubmit('report_fraud'))
				$this->_reportFraud();
			
			return '
			<br />
			<fieldset style="width:400px;">
				<legend><img src="'._PS_BASE_URL_.__PS_BASE_URI__.'modules/'.$this->name.'/logo.gif" alt="" /> '.$this->l('Secuvad').'</legend>
				<p>
					<b>'.$this->l('Secuvad status:').'</b> '.$this->_getSecuvadStatusHtml((int)($secuvad_order['secuvad_status'])).'
					'.($secuvad_order['is_fraud'] ? '<br /><b>'.$this->l('Unpaid transmitted:').'</b> '.$this->_getFraudStatusHtml((int)($secuvad_order['is_fraud'])) : '').'
				</p>
				
				<form method="POST" action="'.$currentIndex.'&id_order='.Tools::getValue('id_order').'&vieworder&token='.Tools::getAdminToken('AdminOrders'.(int)(Tab::getIdFromClassName('AdminOrders')).(int)($cookie->id_employee)).'">
					<p class="center">
						<input type="hidden" name="id_secuvad_order" value="'.(int)($params['id_order']).'" />
						<input type="submit" class="button" name="send_to_secuvad" value="'.$this->l('Update Secuvad status').'"> 
						<input type="submit" class="button" name="report_fraud" value="'.$this->l('Transmit an unpaid transaction').'" onclick="if(!confirm(\''.$this->l('Please note, by delivery of such unpaid SECUVAD, you certify that your claim is certain, liquid and payable, and that you entrust SECUVAD to EXCLUSIVE recovery. Do you wish to continue?').'\')) return false;">
					</p>
				</form>
			</fieldset>
			';
		}
	}
	
	public function hookPaymentConfirm($params)
	{
		$id_order = (int)($params['id_order']);
		$exists = Db::getInstance()->getValue('
		SELECT COUNT(*) 
		FROM `'._DB_PREFIX_.'secuvad_order` 
		WHERE `id_secuvad_order` = '.(int)($id_order));
		if (!$exists)
		{
			Db::getInstance()->Execute('
			INSERT INTO `'._DB_PREFIX_.'secuvad_order`(`id_secuvad_order`, `ip`, `ip_time`) 
			VALUES ('.(int)($id_order).', \''.pSQL($this->getRemoteIPaddress()).'\', \''.pSQL(date("Y-m-d H:i:s")).'\')
			');		
			if ($this->check_assoc() == '' AND Configuration::get('SECUVAD_ACTIVATION') == 1)
			{
				include_once (_PS_MODULE_DIR_.'/secuvad/classes/Secuvad_flux.php');
				include_once (_PS_MODULE_DIR_.'/secuvad/classes/Secuvad_connection.php');
				
				$xml_obj = new Secuvad_flux(Configuration::get('SECUVAD_ID'),Configuration::get('SECUVAD_XML_ENCODING'));
				$flux_xml = $xml_obj->get_flux_xml((int)($id_order));
				
				if (Configuration::get('SECUVAD_MODE') == 'TEST')
					$url = 'https://'.Configuration::get('SECUVAD_LOGIN').':'.Configuration::get('SECUVAD_MDP').'@'.Configuration::get('SECUVAD_URL_TEST');
				else
					$url = 'https://'.Configuration::get('SECUVAD_LOGIN').':'.Configuration::get('SECUVAD_MDP').'@'.Configuration::get('SECUVAD_URL_PROD');			
				
				$connection_obj = new Secuvad_connection($flux_xml, Configuration::get('SECUVAD_ID'), $url, $this);
				$connection_obj->send_transaction();
			}
		}
	}
	
	public function hookPaymentCCAdded($params)
	{
		$id_order = (int)($params['paymentCC']->id_order);
		$exists = Db::getInstance()->getValue('
		SELECT COUNT(*) 
		FROM `'._DB_PREFIX_.'secuvad_order` 
		WHERE `id_secuvad_order` = '.(int)($id_order));
		if (!$exists)
		{
			Db::getInstance()->Execute('
			INSERT INTO `'._DB_PREFIX_.'secuvad_order`(`id_secuvad_order`, `ip`, `ip_time`) 
			VALUES ('.(int)($id_order).', \''.pSQL($this->getRemoteIPaddress()).'\', \''.pSQL(date("Y-m-d H:i:s")).'\')
			');		
			if ($this->check_assoc() == '' AND Configuration::get('SECUVAD_ACTIVATION') == 1)
			{
				include_once (_PS_MODULE_DIR_.'/secuvad/classes/Secuvad_flux.php');
				include_once (_PS_MODULE_DIR_.'/secuvad/classes/Secuvad_connection.php');
				
				$xml_obj = new Secuvad_flux(Configuration::get('SECUVAD_ID'), Configuration::get('SECUVAD_XML_ENCODING'));
				$flux_xml = $xml_obj->get_flux_xml((int)($id_order));
				if (Configuration::get('SECUVAD_MODE') == 'TEST')
					$url = 'https://'.Configuration::get('SECUVAD_LOGIN').':'.Configuration::get('SECUVAD_MDP').'@'.Configuration::get('SECUVAD_URL_TEST');
				else
					$url = 'https://'.Configuration::get('SECUVAD_LOGIN').':'.Configuration::get('SECUVAD_MDP').'@'.Configuration::get('SECUVAD_URL_PROD');			
				
				$connection_obj = new Secuvad_connection($flux_xml, Configuration::get('SECUVAD_ID'), $url, $this);
				$connection_obj->send_transaction();
			}
		}
	}
	
	public function getContent()
	{
		global $cookie;
		
		$this->_html = '<h2>'.$this->l('Secuvad configuration').'</h2>';
		
		if (!$this->_isPaymentCCFilePresent())
			$this->_html .= $this->displayError($this->l('Payment CC file isn\'t present, please copy this file into classes directory of your Prestashop'));
		$lock = $this->_postProcess();
		if ($this->check_assoc() AND Configuration::get('SECUVAD_ACTIVATION'))
			$this->_html .= '<div class="warn">'.$this->l('Please configure all associations').'</div>';
		$this->_initSecuvadAssoc();
		if (Configuration::get('SECUVAD_ACTIVATION'))
			$this->_setFormConfigure();
		else
		{
			$this->_html .= '<h3>'.$this->l('If you have already an account').'</h3>';
			$this->_setFormConfigure();
			$this->_setFormRegister($lock);
		}
		
		return $this->_html;
	}

	private function _isPaymentCCFilePresent()
	{
		if (!file_exists(dirname(__FILE__).'/../../classes/PaymentCC.php'))
			return false;
		return true;
	}

	private function _initSecuvadAssoc()
	{
		$this->check_assoc();
		$this->_secuvad_category = $this->_getSecuvadCategories();
		$this->_secuvad_assoc_category = $this->_getSecuvadCategoryAssoc();
		$this->_secuvad_code_payment = $this->_getSecuvadCodePayment();
		$this->_secuvad_carrier_type = $this->_getSecuvadCarrierType();
		$this->_secuvad_carrier_delay = $this->_getSecuvadCarrierDelay();
	}

	private function _getSecuvadCategories()
	{
		global $cookie;
		
		return Db::getInstance()->ExecuteS('
		SELECT * 
		FROM `'._DB_PREFIX_.'secuvad_category` sc
		WHERE sc.`id_lang` = '.(int)($cookie->id_lang));
	}
	
	private function _getSecuvadCategoryAssoc()
	{
		$data = Db::getInstance()->ExecuteS('
		SELECT * 
		FROM `'._DB_PREFIX_.'secuvad_assoc_category`');
		
		if (!sizeof($data))
			return array();
		
		$assoc = array();
		foreach ($data as $d)
			$assoc[$d['id_category']] = $d['category_id'];
		return $assoc;
	}
	
	private function _getSecuvadPayment()
	{
		global $cookie;
		
		return Db::getInstance()->ExecuteS('
		SELECT IF(sp.name IS NULL, "Unknown", sp.name) AS `secuvad_name`, sp.`code`, m.`id_module`, m.`name` AS `module_name` 
		FROM `'._DB_PREFIX_.'secuvad_assoc_payment` sac
		JOIN `'._DB_PREFIX_.'module` m ON (m.`id_module` = sac.`id_module`)
	 	LEFT JOIN `'._DB_PREFIX_.'secuvad_payment` sp ON (sp.`code` = sac.`code` AND sp.`id_lang` = '.(int)($cookie->id_lang).')');
	}
	
	private function _getSecuvadCodePayment()
	{
		global $cookie;
		
		return Db::getInstance()->ExecuteS('
		SELECT * 
		FROM `'._DB_PREFIX_.'secuvad_payment` 
		WHERE `id_lang` = '.(int)($cookie->id_lang)
		);
	}
	
	private function _getSecuvadCarrier()
	{
		global $cookie;

		return Db::getInstance()->ExecuteS('
		SELECT * 
		FROM `'._DB_PREFIX_.'secuvad_assoc_transport` sat
		JOIN '._DB_PREFIX_.'carrier c ON (c.id_carrier = sat.id_carrier)
	 	LEFT JOIN '._DB_PREFIX_.'secuvad_transport st ON st.transport_id = sat.transport_id AND st.id_lang='.(int)($cookie->id_lang).'
		LEFT JOIN '._DB_PREFIX_.'secuvad_transport_delay std ON std.transport_delay_id = sat.transport_delay_id AND std.id_lang='.(int)($cookie->id_lang)
		);
	}
	
	private function _getSecuvadCarrierType()
	{
		global $cookie;
		
		return Db::getInstance()->ExecuteS('
		SELECT * 
		FROM `'._DB_PREFIX_.'secuvad_transport` 
		WHERE `id_lang` = '.(int)($cookie->id_lang)
		);
	}
	
	private function _getSecuvadCarrierDelay()
	{
		global $cookie;
		
		return Db::getInstance()->ExecuteS('
		SELECT * 
		FROM `'._DB_PREFIX_.'secuvad_transport_delay` 
		WHERE `id_lang` = '.(int)($cookie->id_lang)
		);
	}
	
	private function _postProcess()
	{
		$errors = array();
		if (Tools::isSubmit('submitSecuvadEdit'))
			return false;
		if (Tools::isSubmit('submitSecuvadConfiguration'))
		{
			if (Tools::getValue('forme') != 'SARL'
				AND Tools::getValue('forme') != 'SA'
				AND Tools::getValue('forme') != 'EURL'
				AND Tools::getValue('forme') != 'SAS'
				AND Tools::getValue('forme') != 'Entreprise individuelle'
				AND Tools::getValue('forme') != 'SNC')
				$errors[] = $this->l('Company type is invalid');
			if (Tools::getValue('societe') == NULL OR !Validate::isName(Tools::getValue('societe')))
				$errors[] = $this->l('Company name is invalid');
			if (Tools::getValue('capital') != NULL AND !Validate::isGenericName(Tools::getValue('capital')))
				$errors[] = $this->l('Capital is invalid');
			if (Tools::getValue('web_site') == NULL OR !Validate::isUrl(Tools::getValue('web_site')))
				$errors[] = $this->l('WebSite is invalid');
			if (Tools::getValue('address') != NULL AND !Validate::isAddress(Tools::getValue('address')))
				$errors[] = $this->l('Address is invalid');
			if (Tools::getValue('code_postal') != NULL AND !Validate::isPostCode(Tools::getValue('code_postal')))
				$errors[] = $this->l('Zip/ Postal Code is invalid');
			if (Tools::getValue('ville') != NULL AND !Validate::isCityName(Tools::getValue('ville')))
				$errors[] = $this->l('City is invalid');
			if (Tools::getValue('pays') != NULL AND !Validate::isCountryName(Tools::getValue('pays')))
				$errors[] = $this->l('Country is invalid');
			if (Tools::getValue('rcs') != NULL AND !Validate::isGenericName(Tools::getValue('rcs')))
				$errors[] = $this->l('RCS is invalid');
			if (Tools::getValue('siren') != NULL AND !Validate::isGenericName(Tools::getValue('siren')))
				$errors[] = $this->l('Siren is invalid');
			if (!is_array(Tools::getValue('categories')) OR !sizeof(Tools::getValue('categories')))
				$errors[] = $this->l('You must select at least one category.');
			if (Tools::getValue('civilite') != 'M'
				AND Tools::getValue('civilite') != 'Mme'
				AND Tools::getValue('civilite') != 'Mlle')
				$errors[] = $this->l('Title is invalid');
			if (Tools::getValue('nom') == NULL OR !Validate::isName(Tools::getValue('nom')))
				$errors[] = $this->l('Last name is invalid');
			if (Tools::getValue('prenom') == NULL OR !Validate::isName(Tools::getValue('prenom')))
				$errors[] = $this->l('First name is invalid');
			if (Tools::getValue('fonction') != NULL AND !Validate::isGenericName(Tools::getValue('fonction')))
				$errors[] = $this->l('Function name is invalid');
			if (Tools::getValue('email') == NULL OR !Validate::isEmail(Tools::getValue('email')))
				$errors[] = $this->l('E-mail name is invalid');
			if (Tools::getValue('telephone') == NULL OR !Validate::isPhoneNumber(Tools::getValue('telephone')))
				$errors[] = $this->l('Telephone is invalid');
			if (!sizeof($errors))
				return true;
			else
			{
				$this->_html .= $this->displayError(implode('<br />', $errors));
				return false;
			}
		} 
		
		if (Tools::isSubmit('submitSecuvadPostConfiguration')) 
		{
			$errors = array();
			if (!Validate::isGenericName(Tools::getValue('secuvad_login')))
				$errors[] = $this->l('Invalid login');
			if (!Validate::isGenericName(Tools::getValue('secuvad_password')))
				$errors[] = $this->l('Invalid password');
			if (!in_array(Tools::getValue('secuvad_mode'), $this->_allowed_modes))
				$errors[] = $this->l('Invalid Mode');
			if (!Validate::isInt(Tools::getValue('secuvad_id')))
				$errors[] = $this->l('Invalid ID');
			if (!sizeof($errors))
			{
				// update configuration
				Configuration::updateValue('SECUVAD_LOGIN',Tools::getValue('secuvad_login'));
				Configuration::updateValue('SECUVAD_MDP',Tools::getValue('secuvad_password'));
				Configuration::updateValue('SECUVAD_MODE',Tools::getValue('secuvad_mode'));
				Configuration::updateValue('SECUVAD_ID',Tools::getValue('secuvad_id'));	
				Configuration::updateValue('SECUVAD_ACTIVATION', 1);
				$this->_html .= $this->displayConfirmation($this->l('Settings are updated').'<img src="http://www.prestashop.com/modules/secuvad.png?id='.urlencode(Tools::getValue('secuvad_id')).'&login='.urlencode(Tools::getValue('secuvad_login')).'&mode='.(Tools::getValue('secuvad_mode') == 'TEST' ? 0 : 1).'" style="float:right" />');
			}
			else
				$this->_html .= $this->displayError(implode('<br />', $errors));
		}
		
		if (Tools::isSubmit('submitSecuvadCategory'))
		{
			Db::getInstance()->Execute('
			DELETE FROM `'._DB_PREFIX_.'secuvad_assoc_category`
			');
			$sql = 'INSERT INTO `'._DB_PREFIX_.'secuvad_assoc_category` VALUES';
			foreach ($_POST as $k => $category_id)
				if (preg_match('/secuvad_cat_([0-9]+)$/Ui', $k, $result))
				{
					$id_category = $result[1];
					$sql .= '(NULL, '.(int)($id_category).', '.(int)($category_id).'),';
				}
			$sql = rtrim($sql, ',');
			if (Db::getInstance()->Execute($sql))
				$this->_html .= $this->displayConfirmation($this->l('Settings are updated'));
			else
				$this->_html .= $this->displayError($this->l('Error during update'));
		}
		
		if (Tools::isSubmit('submitSecuvadPayment'))
		{
			Db::getInstance()->Execute('
			DELETE FROM `'._DB_PREFIX_.'secuvad_assoc_payment`
			');
			$sql = 'INSERT INTO `'._DB_PREFIX_.'secuvad_assoc_payment` VALUES';
			foreach ($_POST as $k => $code)
				if (preg_match('/secuvad_payment_([0-9]+)$/Ui', $k, $result))
				{
					$id_module = $result[1];
					$sql .= '(NULL, '.(int)($id_module).', \''.pSQL($code).'\'),';
				}
			$sql = rtrim($sql, ',');
			if (Db::getInstance()->Execute($sql))
				$this->_html .= $this->displayConfirmation($this->l('Settings are updated'));
			else
				$this->_html .= $this->displayError($this->l('Error during update'));
		}
		
		if (Tools::isSubmit('submitSecuvadCarrier'))
		{
			Db::getInstance()->Execute('
			DELETE FROM `'._DB_PREFIX_.'secuvad_assoc_transport`
			');
			$sql = 'INSERT INTO `'._DB_PREFIX_.'secuvad_assoc_transport` VALUES';
			foreach ($_POST as $k => $value)
				if (preg_match('/secuvad_carrier_type_([0-9]+)$/Ui', $k, $result))
				{
					$id_carrier = $result[1];
					$sql .= '(NULL, '.(int)($id_carrier).', '.(int)($value).', '.(int)($_POST['secuvad_carrier_delay_'.(int)($id_carrier)]).'),';
				}
			$sql = rtrim($sql, ',');
			if (Db::getInstance()->Execute($sql))
				$this->_html .= $this->displayConfirmation($this->l('Settings are updated'));
			else
				$this->_html .= $this->displayError($this->l('Error during update'));
		}
	}
	
	private function _getSecuvadRegisterURL()
	{
		return 'http://www.secuvad.com/contrat/';
	}
	
	private function _setFormConfigure()
	{
		global $cookie;
		
		$this->_html .= '
		<form method="POST" action="'.$_SERVER['REQUEST_URI'].'">
			<fieldset style="width:430px;margin-right:10px;margin-bottom:10px;">
				<legend><img src="'._PS_BASE_URL_.__PS_BASE_URI__.'/modules/'.$this->name.'/logo.gif" alt="" /> '.$this->l('Company').'</legend>
				
				<label for="secuvad_login">'.$this->l('Login:').'</label>
				<div class="margin-form">
					<input type="text" name="secuvad_login" id="secuvad_login" value="'.htmlentities(Configuration::get('SECUVAD_LOGIN'), ENT_QUOTES, 'UTF-8').'" size="30" />
				</div>

				<label for="secuvad_password">'.$this->l('Password:').'</label>
				<div class="margin-form">
					<input type="password" name="secuvad_password" id="secuvad_password" value="'.htmlentities(Configuration::get('SECUVAD_MDP'), ENT_QUOTES, 'UTF-8').'"  size="30" />
				</div>
				
				<label for="secuvad_mode">'.$this->l('Mode:').'</label>
				<div class="margin-form">
					<select name="secuvad_mode">
						<option value="TEST" '.(Configuration::get('SECUVAD_MODE') == 'TEST' ? 'selected="selected"' : '').'>'.$this->l('Test').'</option>
						<option value="PROD" '.(Configuration::get('SECUVAD_MODE') == 'PROD' ? 'selected="selected"' : '').'>'.$this->l('Production').'</option>
					</select>
				</div>
				
				<label for="secuvad_id">'.$this->l('ID:').'</label>
				<div class="margin-form">
					<input type="text" name="secuvad_id" id="secuvad_id" value="'.htmlentities(Configuration::get('SECUVAD_ID'), ENT_QUOTES, 'UTF-8').'" size="30" />
				</div>								
			
				<p class="center"><input type="submit" class="button" name="submitSecuvadPostConfiguration" value="'.$this->l('Send').'" /></p> 
			</fieldset>
		</form>
		';
		
		if (Configuration::get('SECUVAD_ACTIVATION'))
		{
			$categories = Category::getCategories((int)($cookie->id_lang), false);
			$categories[1]['infos'] = array('name' => $this->l('Home'), 'id_parent' => 0, 'level_depth' => 0);
			
			$this->_html .= '
			<form method="POST" action="'.$_SERVER['REQUEST_URI'].'">
				<fieldset style="width:900px;margin-right:10px;margin-bottom:10px;">
					<legend><img src="'._PS_BASE_URL_.__PS_BASE_URI__.'/modules/'.$this->name.'/logo.gif" alt="" /> '.$this->l('Secuvad categories').'</legend>
					
					<table class="table" style="width:100%;" cellspacing="0" cellpadding="0">
						<tr>
							<th style="width:70%;">'.$this->l('Name').'</th>
							<th style="width:30%;">'.$this->l('Secuvad Category').'</th>
						</tr>
			';
			$this->recurseCategoryForInclude($categories, $categories[1]);
			$this->_html .= '
					</table>
					<p class="center"><input type="submit" class="button" name="submitSecuvadCategory" value="'.$this->l('Send').'" /></p>
				</fieldset>
			</form>
			';
			
			$this->_html .= '
			<form method="POST" action="'.$_SERVER['REQUEST_URI'].'">
				<fieldset style="width:430px;margin-right:10px;margin-bottom:10px;">
					<legend><img src="'._PS_BASE_URL_.__PS_BASE_URI__.'/modules/'.$this->name.'/logo.gif" alt="" /> '.$this->l('Secuvad Payment').'</legend>
					
					<table class="table" style="width:100%;" cellspacing="0" cellpadding="0">
						<tr>
							<th style="width:50%;">'.$this->l('Name').'</th>
							<th style="width:50%;">'.$this->l('Secuvad Payment type').'</th>
						</tr>
			';
			$secuvad_payements = $this->_getSecuvadPayment();
			foreach ($secuvad_payements as $payment)
			{
				$this->_html .= '
					<tr>
						<td>'.htmlentities($payment['module_name'], ENT_QUOTES, 'UTF-8').'</td>
						<td>
						<select name="secuvad_payment_'.(int)($payment['id_module']).'">
							<option>'.$this->l('Unknown').'</option>';
				foreach ($this->_secuvad_code_payment as $code)
					$this->_html .= '<option value="'.htmlentities($code['code'], ENT_QUOTES, 'UTF-8').'" '.(strtolower($payment['code']) == strtolower($code['code']) ? 'selected="selected"' : '').'>'.htmlentities($code['name'], ENT_QUOTES, 'UTF-8').'</option>';
				$this->_html .= '
						</select>
						</td>
					</tr>
				';
			}
			$this->_html .= '
					</table>
					<p class="center"><input type="submit" class="button" name="submitSecuvadPayment" value="'.$this->l('Send').'" /></p>
				</fieldset>
			</form>
			';
			
			$this->_html .= '
			<form method="POST" action="'.$_SERVER['REQUEST_URI'].'">
				<fieldset style="width:430px;margin-right:10px;margin-bottom:10px;">
					<legend><img src="'._PS_BASE_URL_.__PS_BASE_URI__.'/modules/'.$this->name.'/logo.gif" alt="" /> '.$this->l('Secuvad Carrier').'</legend>
					
					<table class="table" style="width:100%;" cellspacing="0" cellpadding="0">
						<tr>
							<th style="width:33%;">'.$this->l('Name').'</th>
							<th style="width:34%;">'.$this->l('Secuvad Carrier').'</th>
							<th style="width:3%;">'.$this->l('Secuvad Carrier Delay').'</th>
						</tr>
			';
			$secuvad_carriers = $this->_getSecuvadCarrier();
			foreach ($secuvad_carriers as $carrier)
			{
				$this->_html .= '
					<tr>
						<td>'.(!preg_match('/^0$/Ui', $carrier['name']) ? htmlentities($carrier['name'], ENT_QUOTES, 'UTF-8') : Configuration::get('PS_SHOP_NAME')).'</td>
						<td>
						<select name="secuvad_carrier_type_'.(int)($carrier['id_carrier']).'">
							<option>---</option>';
				foreach ($this->_secuvad_carrier_type as $carrier_type)
					$this->_html .= '<option value="'.(int)($carrier_type['transport_id']).'" '.($carrier['transport_id'] == $carrier_type['transport_id'] ? 'selected="selected"' : '').'>'.htmlentities($carrier_type['transport_name'], ENT_QUOTES, 'UTF-8').'</option>';
				$this->_html .= '
						</select>
						</td>
						<td>
						<select name="secuvad_carrier_delay_'.(int)($carrier['id_carrier']).'">
							<option>---</option>';
				foreach ($this->_secuvad_carrier_delay as $carrier_delay)
					$this->_html .= '<option value="'.(int)($carrier_delay['transport_delay_id']).'" '.($carrier['transport_delay_id'] == $carrier_delay['transport_delay_id'] ? 'selected="selected"' : '').'>'.htmlentities($carrier_delay['transport_delay_name'], ENT_QUOTES, 'UTF-8').'</option>';
				$this->_html .= '
						</select>
						</td>
					</tr>
				';
			}
			$this->_html .= '
					</table>
					<p class="center"><input type="submit" class="button" name="submitSecuvadCarrier" value="'.$this->l('Send').'" /></p>
				</fieldset>
			</form>
			';
		}
	}
	
	private function recurseCategoryForInclude($categories, $current, $id_category = 1, $has_suite = array())
	{
		global $done, $cookie;
		static $irow;
		
		if (!isset($done[$current['infos']['id_parent']]))
			$done[$current['infos']['id_parent']] = 0;
		$done[$current['infos']['id_parent']] += 1;
		
		$todo = sizeof($categories[$current['infos']['id_parent']]);
		$doneC = $done[$current['infos']['id_parent']];

		$level = $current['infos']['level_depth'] + 1;	
		$this->_html .= '<tr class="'.($irow++ % 2 ? 'alt_row' : '').'">
				<td>';
		for ($i = 2; $i < $level; $i++)
				$this->_html .= '<img src="../img/admin/lvl_'.$has_suite[$i - 2].'.gif" alt="" style="vertical-align: middle;"/>';
		$this->_html .= '<img src="../img/admin/'.($level == 1 ? 'lv1.gif' : 'lv2_'.($todo == $doneC ? 'f' : 'b').'.gif').'" style="vertical-align:middle" /> &nbsp;<label for="categoryBox_'.$id_category.'" class="t">'.stripslashes($current['infos']['name']).'</label>';
		$this->_html .= '
				</td>
				<td>
					<select name="secuvad_cat_'.$id_category.'">
		';
		$this->_html .= '<option>---</option>';
		foreach ($this->_secuvad_category as $category)
			$this->_html .= '<option value="'.$category['category_id'].'" '.((isset($this->_secuvad_assoc_category[$id_category]) AND $this->_secuvad_assoc_category[$id_category] == $category['category_id'])? 'selected="selected"' : '').'>'.htmlentities($category['category_name'], ENT_QUOTES, 'UTF-8').'</option>';
		$this->_html .= '</select>
				</td>
			</tr>
		';
		if ($level > 1)
			$has_suite[] = ($todo == $doneC ? 0 : 1);
		if (isset($categories[$id_category]))
			foreach ($categories[$id_category] AS $key => $row)
				if ($key != 'infos')
					$this->recurseCategoryForInclude($categories, $categories[$id_category][$key], $key, $has_suite);
	}
	
	private function _setFormRegister($lock = false)
	{
		$this->_html .= '
		<h3>'.$this->l('In order to use the Secuvad module, please fill in this form, then click "Register".').'</h3>
		<form method="POST" action="'.($lock ? $this->_getSecuvadRegisterURL() : $_SERVER['REQUEST_URI']).'">';
		if ($lock)
		{
			foreach (Tools::getValue('categories') as $category)
				$this->_html .= '<input type="hidden" name="cat'.$category['category_id'].'" value="true" />';
		}
		$this->_html .= '
			<input type="hidden" name="flag" value="true" />
			<input type="hidden" name="contrat" value="prestashop" />
			<fieldset style="float:left;width:430px;margin-right:10px;margin-bottom:10px;">
				<legend><img src="'._PS_BASE_URL_.__PS_BASE_URI__.'/modules/'.$this->name.'/logo.gif" alt="" /> '.$this->l('Company').'</legend>
				
				<label for="forme">'.$this->l('Company type:').'</label>
				<div class="margin-form">
					<select name="'.($lock ? '' : 'forme').'" id="forme" '.($lock ? 'disabled="disabled"' : '').'>
						<option value="SARL" '.(Tools::getValue('forme') == 'SARL' ? 'selected' : '').'>'.$this->l('SARL').'</option>
						<option value="SA" '.(Tools::getValue('forme') == 'SA' ? 'selected' : '').'>'.$this->l('SA').'</option>
						<option value="EURL"'.(Tools::getValue('forme') == 'EURL' ? 'selected' : '').'>'.$this->l('EURL').'</option>
						<option value="SAS"'.(Tools::getValue('forme') == 'SAS' ? 'selected' : '').'>'.$this->l('SAS').'</option>
						<option value="Entreprise individuelle"'.(Tools::getValue('forme') == 'Entreprise individuelle' ? 'selected' : '').'>'.$this->l('Individual Company').'</option>
						<option value="SNC"'.(Tools::getValue('forme') == 'SNC' ? 'selected' : '').'>'.$this->l('SNC').'</option>
					</select>
					'.($lock ? '<input type="hidden" name="forme" value="'.htmlentities(Tools::getValue('forme'), ENT_QUOTES, 'UTF-8').'" />' : '').'
				</div>
				
				<label for="societe">'.$this->l('Company name:').' <sup>*</sup></label>
				<div class="margin-form">
					<input type="text" name="societe" id="societe" value="'.htmlentities(Tools::getValue('societe'), ENT_QUOTES, 'UTF-8').'" size="30" '.($lock ? 'readonly="readonly"' : '').'/>
				</div>
				
				<label for="capital">'.$this->l('Capital:').'</label>
				<div class="margin-form">
					<input type="text" name="capital" id="capital" value="'.htmlentities(Tools::getValue('capital'), ENT_QUOTES, 'UTF-8').'" '.($lock ? 'readonly="readonly"' : '').'/>
				</div>
				
				<label for="web_site">'.$this->l('Website:').' <sup>*</sup></label>
				<div class="margin-form">
					<input type="text" name="web_site" id="web_site" value="'.htmlentities(Tools::getValue('web_site', 'http://'), ENT_QUOTES, 'UTF-8').'" size="30" '.($lock ? 'readonly="readonly"' : '').'/>
				</div>
				
				<label for="address">'.$this->l('Address:').'</label>
				<div class="margin-form">
					<input type="text" name="address" id="address" value="'.htmlentities(Tools::getValue('address', Configuration::get('PS_SHOP_ADDR1').' '.Configuration::get('PS_SHOP_ADDR2')), ENT_QUOTES, 'UTF-8').'" size="30" '.($lock ? 'readonly="readonly"' : '').'/>
				</div>
				
				<label for="code_postal">'.$this->l('Zip/ Postal Code').'</label>
				<div class="margin-form">
					<input type="text" name="code_postal" id="code_postal" value="'.htmlentities(Tools::getValue('code_postal', Configuration::get('PS_SHOP_CODE')), ENT_QUOTES, 'UTF-8').'" size="5" '.($lock ? 'readonly="readonly"' : '').'/>
				</div>
				
				<label for="pays">'.$this->l('City:').'</label>
				<div class="margin-form">
					<input type="text" name="ville" id="ville" value="'.htmlentities(Tools::getValue('ville', Configuration::get('PS_SHOP_CITY')), ENT_QUOTES, 'UTF-8').'" '.($lock ? 'readonly="readonly"' : '').'/>
				</div>
				
				<label for="pays">'.$this->l('Country:').'</label>
				<div class="margin-form">
					<input type="text" name="pays" id="pays" value="'.htmlentities(Tools::getValue('pays', Configuration::get('PS_SHOP_COUNTRY')), ENT_QUOTES, 'UTF-8').'" '.($lock ? 'readonly="readonly"' : '').'/>
				</div>
				
				<label for="rcs">'.$this->l('RCS:').'</label>
				<div class="margin-form">
					<input type="text" name="rcs" id="rcs" value="'.htmlentities(Tools::getValue('rcs'), ENT_QUOTES, 'UTF-8').'" '.($lock ? 'readonly="readonly"' : '').'/>
				</div>
				
				<label for="siren">'.$this->l('Siren:').'</label>
				<div class="margin-form">
					<input type="text" name="siren" id="siren" value="'.htmlentities(Tools::getValue('siren'), ENT_QUOTES, 'UTF-8').'" '.($lock ? 'readonly="readonly"' : '').'/>
				</div>
				<p style="font-size:12px;font-weight:bold;"><sup>*</sup> '.$this->l('Required fields').'</p>
			</fieldset>
			<fieldset style="float:left;width:420px;">
				<legend><img src="'._PS_BASE_URL_.__PS_BASE_URI__.'/modules/'.$this->name.'/logo.gif" alt="" /> '.$this->l('Product types').'</legend>
				
				<label>'.$this->l('Categories of your products:').'</label>
				<div class="margin-form">
					<ul style="list-style-type:none;margin:0;padding:0;">
				';
			
			foreach ($this->_getSecuvadCategories() as $category)
				$this->_html .= '
				<li>
					<input type="checkbox" name="'.($lock ? '' : 'categories[]').'" id="cat'.(int)($category['category_id']).'" value="'.(int)($category['category_id']).'" '.((is_array(Tools::getValue('categories')) AND in_array((int)($category['category_id']), Tools::getValue('categories'))) ? 'checked="checked"' : '').' '.($lock ? 'disabled="disabled"' : '').'/> <label for="cat'.(int)($category['category_id']).'" class="t">'.htmlentities($category['category_name'], ENT_QUOTES, 'UTF-8').'</label>
					'.(($lock AND is_array(Tools::getValue('categories')) AND in_array((int)($category['category_id']), Tools::getValue('categories'))) ? '<input type="hidden" name="categories[]" id="cat'.(int)($category['category_id']).'" value="'.(int)($category['category_id']).'"': '' ).'
				</li>';
			$this->_html .= '
					</ul>
				</div>
			</fieldset>
			<div class="clear"></div>
			<fieldset style="width:430px;">
				<legend><img src="'._PS_BASE_URL_.__PS_BASE_URI__.'/modules/'.$this->name.'/logo.gif" alt="" /> '.$this->l('Company contact').'</legend>
				
				<label for="civilite">'.$this->l('Title:').'</label>
				<div class="margin-form">
					<select name="civilite" id="'.($lock ? '' : 'civilite').'" '.($lock ? 'disabled="disabled"' : '').'>
						<option value="M" '.(Tools::getValue('civilite') == 'M' ? 'selected' : '').'>'.$this->l('Mr').'</option>
						<option value="Mme" '.(Tools::getValue('civilite') == 'Mme' ? 'selected' : '').'>'.$this->l('Mrs').'</option>
						<option value="Mlle" '.(Tools::getValue('civilite') == 'Mlle' ? 'selected' : '').'>'.$this->l('Miss').'</option>
					</select>
					'.($lock ? '<input type="hidden" name="civilite" value="'.htmlentities(Tools::getValue('civilite'), ENT_QUOTES, 'UTF-8').'" />' : '').'
				</div>
				
				<label for="nom">'.$this->l('Last name:').' <sup>*</sup></label>
				<div class="margin-form">
					<input type="text" name="nom" id="nom" value="'.htmlentities(Tools::getValue('nom'), ENT_QUOTES, 'UTF-8').'" '.($lock ? 'readonly="readonly"' : '').'/>
				</div>
				
				<label for="prenom">'.$this->l('First name:').' <sup>*</sup></label>
				<div class="margin-form">
					<input type="text" name="prenom" id="prenom" value="'.htmlentities(Tools::getValue('prenom'), ENT_QUOTES, 'UTF-8').'" '.($lock ? 'readonly="readonly"' : '').'/>
				</div>
				
				<label for="fonction">'.$this->l('Function:').'</label>
				<div class="margin-form">
					<input type="text" name="fonction" id="fonction" value="'.htmlentities(Tools::getValue('fonction'), ENT_QUOTES, 'UTF-8').'" '.($lock ? 'readonly="readonly"' : '').'/>
				</div>
				
				<label for="email">'.$this->l('E-Mail:').' <sup>*</sup></label>
				<div class="margin-form">
					<input type="text" name="email" id="email" value="'.htmlentities(Tools::getValue('email'), ENT_QUOTES, 'UTF-8').'" '.($lock ? 'readonly="readonly"' : '').'/>
				</div>
				
				<label for="telephone">'.$this->l('Phone:').' <sup>*</sup></label>
				<div class="margin-form">
					<input type="text" name="telephone" id="telephone" value="'.htmlentities(Tools::getValue('telephone'), ENT_QUOTES, 'UTF-8').'" '.($lock ? 'readonly="readonly"' : '').'/>
				</div>
				<p style="font-size:12px;font-weight:bold;"><sup>*</sup> '.$this->l('Required fields').'</p>
			</fieldset>
			'.($lock ? 
			'<p class="center"><input type="submit" class="button" name="submitSecuvadEdit" value="'.$this->l('Edit').'" /> <input type="submit" class="button" name="submitSecuvadConfirmation" value="'.$this->l('Send').'" /></p>' : 
			'<p class="center"><input type="submit" class="button" name="submitSecuvadConfiguration" value="'.$this->l('Register').'" /></p>').'
		</form>
		';
	}

	private function installModuleTab($tabClass, $tabName, $idTabParent)
	{
		@copy(_PS_IMG_DIR_.'t/AdminAccess.gif', _PS_MODULE_DIR_.'/'.$this->name.'/'.$tabClass.'.gif');
		@copy(_PS_IMG_DIR_.'t/AdminAccess.gif', _PS_IMG_DIR_.'t/'.$tabClass.'.gif');
		$tab = new Tab();
		$tab->name = $tabName;
		$tab->class_name = $tabClass;
		$tab->module = $this->name;
		$tab->id_parent = (int)($idTabParent);
		if(!$tab->save())
		return false;
		return true;
	}

	private function uninstallModuleTab($tabClass)
	{
		$idTab = Tab::getIdFromClassName($tabClass);
		if($idTab != 0)
		{
			$tab = new Tab($idTab);
			$tab->delete();
			return true;
		}
		return false;
	}

	public function getRemoteIPaddress()
	{
		if (method_exists('Tools', 'getRemoteAddr'))
			return Tools::getRemoteAddr();
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND $_SERVER['HTTP_X_FORWARDED_FOR'])
		{
			if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ','))
			{
				$ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
				return $ips[0];
			}
			else
				return $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		return $_SERVER['REMOTE_ADDR'];
	}
	
	private function send_error_report($probleme,$mail)
	{
		$report = 'idsecuvad='.get_secuvad_id()."\n\n";
		$report .= $this->l('Mail:').$mail."\n\n";
		$report .= $this->l('Issue description:')."\n".$probleme."\n\n";
		$report .= $this->l('Log files:')."\n";
		$res = Db::getInstance()->ExecuteS('
		SELECT `message`, `date` 
		FROM `'._DB_PREFIX_.'secuvad_logs` 
		ORDER BY `date` DESC 
		LIMIT '.(int)($this->get_secuvad_nb_log_reportred()));
		foreach($res as $msg)
			$report .= $msg['date']." : ".$msg['message']."\n\n";
		mail($this->get_secuvad_contact(), $this->l('Error report').' idsecuvad='.$this->get_secuvad_id(), $report);
	}
	
	public function sent_to_secuvad($id_secuvad_order)
	{
		$secuvad_status = Db::getInstance()->getValue('
		SELECT `secuvad_status` 
		FROM `'._DB_PREFIX_.'secuvad_order` 
		WHERE `id_secuvad_order` = '.(int)($id_secuvad_order));
		
		if($secuvad_status == 0)
			return false;
		return true;
	}

	public function check_assoc()
	{
		$result = '';
		if(!$this->check_payment())
			$result .= 'payment';
		if(!$this->check_transport())
			$result .= 'transport';
		if(!$this->check_category())
			$result .= 'category';
		return $result;
	}
	
	private function check_payment()
	{
		$result = true;
		Db::getInstance()->Execute('
		DELETE FROM `'._DB_PREFIX_.'secuvad_assoc_payment` 
		WHERE `id_module` NOT IN 
		(
			SELECT m.`id_module` 
			FROM `'._DB_PREFIX_.'hook` h 
			JOIN `'._DB_PREFIX_.'hook_module` hm ON (hm.`id_hook` = h.`id_hook`) 
			JOIN `'._DB_PREFIX_.'module` m ON (m.`id_module` = hm.`id_module`) 
			WHERE h.`name` = \'payment\' 
		)
		');
		
		Db::getInstance()->Execute('
		INSERT INTO `'._DB_PREFIX_.'secuvad_assoc_payment` (`id_module`) 
		(
			SELECT m.`id_module` FROM `'._DB_PREFIX_.'hook` h 
			JOIN `'._DB_PREFIX_.'hook_module` hm ON (hm.`id_hook` = h.`id_hook`) 
			JOIN `'._DB_PREFIX_.'module` m ON (m.`id_module` = hm.`id_module`)
			LEFT JOIN `'._DB_PREFIX_.'secuvad_assoc_payment` sap ON (sap.`id_module` = m.`id_module`)
			WHERE h.`name` = "payment" 
			AND sap.`id_module` IS NULL
		)');
	
		$module_not_assoc = Db::getInstance()->ExecuteS('
		SELECT m.`name`, m.`id_module` 
		FROM `'._DB_PREFIX_.'hook` h 
		JOIN `'._DB_PREFIX_.'hook_module` hm ON (hm.`id_hook` = h.`id_hook`) 
		JOIN `'._DB_PREFIX_.'module` m ON (m.`id_module` = hm.`id_module`)
		JOIN `'._DB_PREFIX_.'secuvad_assoc_payment` sap ON (sap.`id_module` = m.`id_module`)
		WHERE h.`name` = "payment" AND sap.`code` IS NULL
		');
		if (sizeof($module_not_assoc) > 0)
		{
			$message = $this->l('Following payment modules are not associated:');
			foreach($module_not_assoc as $mod)
				$message .= "\n\t".$mod['id_module']."->".$mod['name'];
			$this->secuvad_log($message);
			$result = false;
		}
		
		return $result;
	}
	
	private function check_transport()
	{
		global $cookie;
		
		$result = true;
		Db::getInstance()->Execute('
		DELETE FROM `'._DB_PREFIX_.'secuvad_assoc_transport` 
		WHERE `id_carrier` NOT IN 
		(
			SELECT c.`id_carrier` FROM `'._DB_PREFIX_.'carrier` c
			WHERE c.`deleted` = 0 
		)
		');
		
		Db::getInstance()->Execute('
		INSERT INTO `'._DB_PREFIX_.'secuvad_assoc_transport`(id_carrier) 
		(
			SELECT c.`id_carrier` 
			FROM `'._DB_PREFIX_.'carrier` c 
			LEFT JOIN `'._DB_PREFIX_.'secuvad_assoc_transport` sat ON (sat.`id_carrier` = c.`id_carrier`)
			WHERE sat.`id_carrier` IS NULL 
			AND c.`deleted` = 0
		)
		');
			
		$module_not_assoc = Db::getInstance()->ExecuteS('
		SELECT c.`name`, c.`id_carrier` 
		FROM `'._DB_PREFIX_.'carrier` c 
		JOIN `'._DB_PREFIX_.'secuvad_assoc_transport` sat ON (sat.`id_carrier` = c.`id_carrier`) 
		LEFT JOIN `'._DB_PREFIX_.'secuvad_transport` st ON (st.`transport_id` = sat.`transport_id` AND st.`id_lang` = '.(int)($cookie->id_lang).')
		LEFT JOIN `'._DB_PREFIX_.'secuvad_transport_delay` std ON (std.`transport_delay_id` = sat.`transport_delay_id` AND std.`id_lang`='.(int)($cookie->id_lang).')
		WHERE (st.`transport_id` IS NULL OR std.`transport_delay_id` IS NULL)
		AND c.`deleted` = 0
		AND c.`active` = 1
		');
		if (count($module_not_assoc) > 0)
		{
			$message = $this->l('Following shipping methods are not associated:');
			foreach($module_not_assoc as $mod)
				$message .= "\n\t".$mod['id_carrier']."->".$mod['name'];
			$this->secuvad_log($message);
			$result = false;
		}
		
		return($result);
	}
	
	private function check_category()
	{
		global $cookie;
		
		$result = true;
		Db::getInstance()->Execute('
		DELETE FROM `'._DB_PREFIX_.'secuvad_assoc_category` 
		WHERE `id_category` NOT IN 
		(
			SELECT c.`id_category` 
			FROM `'._DB_PREFIX_.'category` c
		)
		');
		
		Db::getInstance()->Execute('
		INSERT INTO `'._DB_PREFIX_.'secuvad_assoc_category`(id_category) 
		(
			SELECT c.`id_category` 
			FROM `'._DB_PREFIX_.'category` c 
			LEFT JOIN `'._DB_PREFIX_.'secuvad_assoc_category` sac ON (sac.`id_category` = c.`id_category`) 
			WHERE sac.`id_category` IS NULL
		)
		');
		
		$module_not_assoc = Db::getInstance()->ExecuteS('
		SELECT cl.`name`, c.`id_category` 
		FROM `'._DB_PREFIX_.'category` c 
		JOIN `'._DB_PREFIX_.'category_lang` cl ON (cl.`id_category` = c.`id_category`) 
		JOIN `'._DB_PREFIX_.'lang` l ON (l.`id_lang` = cl.`id_lang` AND l.`iso_code` = \'en\') 
		JOIN `'._DB_PREFIX_.'secuvad_assoc_category` sac ON (sac.`id_category` = c.`id_category`)
		LEFT JOIN `'._DB_PREFIX_.'secuvad_category` sc ON (sc.`category_id` = sac.`category_id` AND sc.`id_lang` = '.(int)($cookie->id_lang).')
		WHERE sc.`category_id` IS NULL
		');
		if(count($module_not_assoc)>0)
		{
			$message = $this->l('Following categories are not associated:');
			foreach($module_not_assoc as $mod)
				$message .= "\n\t".$mod['id_category']."->".$mod['name'];
			$this->secuvad_log($message);
			$result = false;
		}
		
		return($result);
	}
	
	private function _sendToSecuvad()
	{	
		global $cookie;
		
		if ($this->check_assoc() != '' || Configuration::get('SECUVAD_ACTIVATION') != 1)
		{
			$this->secuvad_log('AdminOrders.php : '.$this->l('Error during activation'));
			return 0;
		}
		include_once(_PS_MODULE_DIR_.'/secuvad/classes/Secuvad_flux.php');
		include_once(_PS_MODULE_DIR_.'/secuvad/classes/Secuvad_connection.php');
		if (Tools::isSubmit('id_secuvad_order'))
		{
			$xml_obj = new Secuvad_flux(Configuration::get('SECUVAD_ID'), Configuration::get('SECUVAD_XML_ENCODING'));
			$flux_xml = $xml_obj->get_flux_xml(Tools::getValue('id_secuvad_order'));
			if (Configuration::get('SECUVAD_MODE') == 'TEST')
				$url = 'https://'.Configuration::get('SECUVAD_LOGIN').':'.Configuration::get('SECUVAD_MDP').'@'.Configuration::get('SECUVAD_URL_TEST');
			else
				$url = 'https://'.Configuration::get('SECUVAD_LOGIN').':'.Configuration::get('SECUVAD_MDP').'@'.Configuration::get('SECUVAD_URL_PROD');			
			$connection_obj = new Secuvad_connection($flux_xml, Configuration::get('SECUVAD_ID'),$url, $this);
			$connection_obj->send_transaction();
			Tools::redirectAdmin('index.php?tab=AdminOrders&confirm=1&id_order='.Tools::getValue('id_secuvad_order').'&vieworder&token='.Tools::getAdminToken('AdminOrders'.(int)(Tab::getIdFromClassName('AdminOrders')).(int)($cookie->id_employee)));
		}
	}
	
	private function _reportFraud()
	{		
		global $cookie;
		
		if ($this->check_assoc() != '' || Configuration::get('SECUVAD_ACTIVATION') != 1)
		{
			$this->secuvad_log('AdminOrders.php : '.$this->l('Error during activation'));
			return 0;
		}
		include_once (_PS_MODULE_DIR_.'/secuvad/classes/Secuvad_flux.php');
		include_once (_PS_MODULE_DIR_.'/secuvad/classes/Secuvad_connection.php');
		
		if (Tools::isSubmit('id_secuvad_order'))
		{	
			if (!$this->sent_to_secuvad(Tools::getValue('id_secuvad_order')))
			{
				echo '<div class="alert error" style="width:400px;">
				<h3>'.$this->l('Error').'</h3>
				<ol>
					<li>'.$this->l('Impossible to report fraud before submitting the associated order').'</li>
				</ol>
			  	</div>';
				return 0;
			}
		
			$xml_obj = new Secuvad_flux(Configuration::get('SECUVAD_ID'), Configuration::get('SECUVAD_XML_ENCODING'));
			$flux_xml = $xml_obj->get_flux_xml_fraud(Tools::getValue('id_secuvad_order'));
			
			if(Configuration::get('SECUVAD_MODE') == 'TEST')
				$url = 'https://'.Configuration::get('SECUVAD_LOGIN').':'.Configuration::get('SECUVAD_MDP').'@'.Configuration::get('SECUVAD_URL_TEST');
			else
				$url = 'https://'.Configuration::get('SECUVAD_LOGIN').':'.Configuration::get('SECUVAD_MDP').'@'.Configuration::get('SECUVAD_URL_PROD');			
			$connection_obj = new Secuvad_connection($flux_xml, Configuration::get('SECUVAD_ID'),$url ,$this);
			$result = $connection_obj->report_fraud('impaye','impaye_report');
			
			if ($result == "true")
				Tools::redirectAdmin('index.php?tab=AdminOrders&confirm=2&id_order='.Tools::getValue('id_secuvad_order').'&vieworder&token='.Tools::getAdminToken('AdminOrders'.(int)(Tab::getIdFromClassName('AdminOrders')).(int)($cookie->id_employee)));
			else
			{
				if ($result == "Erreur de connexion")
					$result = $this->l('Connection error');
				echo '<br /><div class="alert error" style="width:400px;">
				<h3>'.$this->l('Error').'</h3>
				<ol><li>'.$result.'</li>
				</ol>
			  </div>';
			}
		}
	}
	
	public function secuvad_log($message)
	{
		$res = Db::getInstance()->getValue('
		SELECT COUNT(0) nb 
		FROM `'._DB_PREFIX_.'secuvad_logs`');
		if($res > $this->get_secuvad_max_log_size())
			Db::getInstance()->Execute('
			DELETE FROM `'._DB_PREFIX_.'secuvad_logs` 
			ORDER BY `date` 
			LIMIT '.(int)($this->get_secuvad_log_size()));
		Db::getInstance()->Execute('
		INSERT INTO `'._DB_PREFIX_.'secuvad_logs` (`message`) 
		VALUES (\''.pSQL($message).'\')
		');
	}
	
	public function get_secuvad_ip()
	{
		if (Configuration::get('SECUVAD_MODE') == 'TEST')
			return explode(',', Configuration::get('SECUVAD_IP_TEST'));
		else
			return explode(',', Configuration::get('SECUVAD_IP'));
	}

	public function get_secuvad_ip_config()
	{
		return explode(',', Configuration::get('SECUVAD_IP_CONFIG'));
	}

	public function get_secuvad_random()
	{
		return(Configuration::get('SECUVAD_RANDOM'));
	}

	public function get_secuvad_id()
	{
		return(Configuration::get('SECUVAD_ID'));
	}

	public function get_secuvad_contact()
	{
		return(Configuration::get('SECUVAD_CONTACT'));
	}

	public function get_secuvad_nb_log_reportred()
	{
		return(Configuration::get('SECUVAD_NB_LOG_REPORTED'));
	}

	public function get_secuvad_max_log_size()
	{
		return(Configuration::get('SECUVAD_MAX_LOG_SIZE'));
	}

	public function get_secuvad_log_size()
	{
		return(Configuration::get('SECUVAD_LOG_SIZE'));
	}
	
	public function _getFraudStatusHtml($is_fraud)
	{
		if ($is_fraud == 0)
			return '<img src="../img/admin/blank.gif" alt="'.$this->l('Is not a fraud').'" /> '.$this->l('No');
		elseif ($is_fraud == 1)
			return '<img src="../img/admin/disabled.gif" alt="'.$this->l('Is a fraud').'" /> '.$this->l('Yes');
	}
	
	public function _getSecuvadStatusHtml($secuvad_status)
	{
		switch ($secuvad_status)
		{
			case 0 : 	
				return '<img src="../img/admin/blank.gif" alt="'.$this->l('Not sent to Secuvad').'" /> '.$this->l('Not sent to Secuvad');
				break;
			case 1 : 	
				return '<img src="../img/admin/ok.gif" alt="'.$this->l('Validated by Secuvad').'" /> '.$this->l('Validated by Secuvad');
				break;
			case 2 : 	
				return '<img src="../img/admin/manufacturers.gif" alt="'.$this->l('Analyzing').'" /> '.$this->l('Analyzing');
				break;
			case 3 : 	
				return '<img src="../img/admin/forbbiden.gif" alt="'.$this->l('Suspect order').'" /> '.$this->l('Suspect order');
				break;
			case 4 : 	
				return '<img src="../img/admin/warning.gif" alt="'.$this->l('Error').'" /> '.$this->l('Error');
				break;	
			case 6 : 	
				return '<img src="../img/admin/manufacturers.gif" alt="'.$this->l('To be checked by CUSTOMER').'" /> '.$this->l('To be checked by CUSTOMER');
				break;
			case 7 : 	
				return '<img src="../img/admin/manufacturers.gif" alt="'.$this->l('To be checked by Secuvad').'" /> '.$this->l('To be checked by Secuvad');
				break;
			default:	
				return '<img src="../img/admin/blank.gif" alt="'.$this->l('Not sent to Secuvad').'" /> '.$this->l('Not sent to Secuvad');
		}
	}
}


