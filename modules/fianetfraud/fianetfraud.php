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

if ((basename(__FILE__) == 'fianetfraud.php'))
	require_once(dirname(__FILE__).'/fianet/fianet.php');

class Fianetfraud extends Module
{
	const INSTALL_SQL_FILE = 'install.sql';

	private $_html;
	private $_product_type  = array(
		'1' => 'Alimentation & gastronomie',
		'2' => 'Auto & moto',
		'3' => 'Culture & divertissements',
		'4' => 'Maison & jardin',
		'5' => 'Electromenager',
		'6' => 'Enchers et achats group&eacute;s',
		'7' => 'Fleurs & cadeaux',
		'8' => 'Informatique & logiciels',
		'9' => 'Sant&eacute; & beaut&eacute;',
		'10' => 'Services aux particuliers',
		'11' => 'Services aux professionnels',
		'12' => 'Sport',
		'13' => 'Vetements & accessoires',
		'14' => 'Voyage & tourisme',
		'15' => 'Hifi, photo & videos',
		'16' => 'Telephonie & communication',
		'17' => 'Bijoux & Métaux précieux',
		'18' => 'Articles et Accessoires pour bébé',
		'19' => 'Sonorisation & Lumière'
	);

	private $_carrier_type = array(
		1 => 'Retrait de la marchandise chez le marchand',
		2 => 'Utilisation d\'un réseau de points-retrait tiers (type kiala, alveol, etc.)',
		3 => 'Retrait dans un aéroport, une gare ou une agence de voyage',
		4 => 'Transporteur (La Poste, Colissimo, UPS, DHL... ou tout transporteur privé)',
		5 => 'Emission d’un billet électronique, téléchargements'
	);
	
	private $_payement_type = array(
		1 => 'carte',
		2 => 'cheque',
		3 => 'contre-remboursement',
		4 => 'virement',
		5 => 'cb en n fois',
		6 => 'paypal',
		7 => '1euro.com'
	);

	public function __construct()
	{
		$this->name = 'fianetfraud';
	 	$this->tab = 'payment_security';
		$this->version = '1.1';
		$this->limited_countries = array('fr');

		parent::__construct();

		$this->displayName = 'FIA-NET - Système d\'Analyse des Commandes';
		$this->description = "Protégez vous contre la fraude à la carte bancaire sans perturber l'acte d'achat";
	}

	public function install()
	{
		if (!parent::install())
			return false;

		if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
			return false;
		elseif (!$sql = file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
			return false;
		$sql = str_replace('PREFIX_', _DB_PREFIX_, $sql);
		$sql = preg_split("/;\s*[\r\n]+/", $sql);
		foreach ($sql AS $query)
			if ($query AND sizeof($query) AND !Db::getInstance()->Execute(trim($query)))
				return false;
		$langs = Language::getLanguages();

		$orderState = new OrderState();
		foreach ($langs AS $lang)
			$orderState->name[$lang['id_lang']] = 'Waiting FIA-NET checking';
		$orderState->name[2] = 'Attente validation commande FIA-NET';
		$orderState->invoice = false;
		$orderState->send_email = false;
		$orderState->logable = false;
		$orderState->color = '#FF9999';
		$orderState->hidden = true;
		$orderState->save();
		Configuration::updateValue('SAC_ID_WAITING', (int)($orderState->id));

		$orderState = new OrderState();
		foreach ($langs AS $lang)
			$orderState->name[$lang['id_lang']] = 'Fraud Detected By FIA-NET';
		$orderState->name[2] = 'Fraude détectée par FIA-NET';
		$orderState->invoice = false;
		$orderState->send_email = false;
		$orderState->logable = false;
		$orderState->color = '#FF6666';
		$orderState->hidden = true;
		$orderState->save();
		Configuration::updateValue('SAC_ID_FRAUD', (int)($orderState->id));

		if (!$this->registerHook('updateCarrier'))
			return false;
		if (!Configuration::updateValue('SAC_SITEID', '') OR
			!Configuration::updateValue('SAC_LOGIN', '') OR
			!Configuration::updateValue('SAC_PASSWORD', '') OR
			!Configuration::updateValue('SAC_MINIMAL_ORDER', 0))
			return false;

		return ($this->registerHook('cart') AND
			$this->registerHook('newOrder') AND
			$this->registerHook('adminOrder') AND
			$this->registerHook('updateOrderStatus')
		);
	}

	public function uninstall()
	{
		$orderState = new OrderState((int)(Configuration::get('SAC_ID_FRAUD')), Configuration::get('PS_LANG_DEFAULT'));
		if (!$orderState->delete())
			return false;
		$orderState = new OrderState((int)(Configuration::get('SAC_ID_WAITING')), Configuration::get('PS_LANG_DEFAULT'));
		if (!$orderState->delete())
			return false;

		return parent::uninstall();
	}

	private function _postProcess()
	{	
		global $cookie;
		
		$error = false;
		
		Configuration::updateValue('SAC_PRODUCTION', ((Tools::getValue('fianetfraud_production') == 1 ) ? 1 : 0));
		Configuration::updateValue('SAC_LOGIN', Tools::getValue('fianetfraud_login'));
		Configuration::updateValue('SAC_PASSWORD', Tools::getValue('fianetfraud_password'));
		Configuration::updateValue('SAC_SITEID', Tools::getValue('fianetfraud_siteid'));
		Configuration::updateValue('SAC_DEFAULT_PRODUCT_TYPE', Tools::getValue('fianetfraud_product_type'));
		Configuration::updateValue('SAC_DEFAULT_CARRIER_TYPE', Tools::getValue('fianetfraud_default_carrier'));
		Configuration::updateValue('SAC_MINIMAL_ORDER', Tools::getValue('fianetfraud_minimal_order'));
		
		if (isset($_POST['payementBox']))
		{
			Configuration::updateValue('SAC_PAYMENT_MODULE', implode(',', $_POST['payementBox']));
			foreach ($_POST['payementBox'] as $payment) 
			 	Configuration::updateValue('SAC_PAYMENT_TYPE_'.$payment,Tools::getValue($payment));
		}
		
		$categories = Category::getSimpleCategories($cookie->id_lang);
		foreach ($categories AS $category)
			Configuration::updateValue('SAC_CATEGORY_TYPE_'.$category['id_category'],Tools::getValue('cat_'.$category['id_category']));
		
		$carriers = Carrier::getCarriers($cookie->id_lang);
		foreach ($carriers as $carrier) 
		{
			if (isset($_POST['carrier_'.$carrier['id_carrier']]))
				Configuration::updateValue('SAC_CARRIER_TYPE_'.$carrier['id_carrier'], $_POST['carrier_'.$carrier['id_carrier']]);
			else
			{
				$error = true;
				$this->_html .= '<div class="alert error">'.$this->l('Invalid carrier code').'</div>';
			}
		}
		
		if (!$error)
		{
			$dataSync = ((($site_id = Configuration::get('SAC_SITEID')) AND Configuration::get('SAC_PRODUCTION'))
				? '<img src="http://www.prestashop.com/modules/fianetfraud.png?site_id='.urlencode($site_id).'" style="float:right" />'
				: ''
			);
			$this->_html .= '<div class="conf confirm">'.$this->l('Settings are updated').$dataSync.'</div>';
		}
		
	}

	public function getContent()
	{
		if (isset($_POST['submitSettings']))
			$this->_postProcess();
		$id_lang = Configuration::get('PS_LANG_DEFAULT');
		$categories = Category::getSimpleCategories($id_lang);

		$carriers = Carrier::getCarriers($id_lang);
		$this->_html .= '
		<fieldset><legend>FIA-NET - Système d\'Analyse des Commandes</legend>
			<img src="../modules/'.$this->name.'/logo.jpg" style="float:right;margin:5px 10px 5px 0" />
			FIA-NET, le leader français de la lutte contre la fraude à la carte bancaire sur internet !<br /><br />
			Avec son réseau mutualisé de plus de 1 700 sites marchands, et sa base de données de 14 millions de cyber-acheteurs, le Système d’Analyse des Commandes vous offre une protection complète et unique contre le risque d’impayé.<br /><br />
			Le logiciel expert (SAC) score vos transactions en quasi temps réel à partir de plus de 200 critères pour valider plus de 92 % de vos transactions.<br />
			Le contrôle humain, prenant en charge les transactions les plus risqués, associé à l’assurance FIA-NET vous permet de valider et garantir jusqu’à 100 % de vos transactions.<br /><br />
			Ne restez pas isolé face à l’explosion des réseaux de fraudeurs !
			<p>'.$this->l('To sign in, check out: ').' <u><a href="https://www.fia-net.com/marchands/devispartenaire.php?p=185" target="_blank">'.$this->l('Fia-net Website').'</a></u></p>
		</fieldset><br />
		<form action="'.Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset>
				<legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Settings').'</legend>
				<label>'.$this->l('Login').'</label>
				<div class="margin-form">
					<input type="text" name="fianetfraud_login" value="'.Configuration::get('SAC_LOGIN').'"/>
				</div>
				<label>'.$this->l('Password').'</label>
				<div class="margin-form">
					<input type="text" name="fianetfraud_password" value="'.Configuration::get('SAC_PASSWORD').'"/>
				</div>
				<label>'.$this->l('Site ID').'</label>
				<div class="margin-form">
					<input type="text" name="fianetfraud_siteid" value="'.Configuration::get('SAC_SITEID').'"/>
				</div>
				<label>'.$this->l('Production mode').'</label>
				<div class="margin-form">
					<input type="checkbox" name="fianetfraud_production" id="activated_on" value="1" '.((Configuration::get('SAC_PRODUCTION') == 1) ? 'checked="checked" ' : '').'/>
				</div>
				<label>'.$this->l('Default Product Type').'</label>
				<div class="margin-form">
					<select name="fianetfraud_product_type">
						<option value="0">'.$this->l('-- Choose --').'</option>';
		foreach ($this->_product_type AS $k => $product_type)
			$this->_html .= '<option value="'.$k.'"'.(Configuration::get('SAC_DEFAULT_PRODUCT_TYPE') == $k ? ' selected="selected"' : '').'>'.$product_type.'</option>';
		$this->_html .= '</select>
				</div>
			</fieldset><br />
			<fieldset><legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Category Detail').'</legend>
			<label>'.$this->l('Category Detail').'</label>
			<div class="margin-form">
			<table cellspacing="0" cellpadding="0" class="table">
						<thead><tr><th>'.$this->l('Category').'</th><th>'.$this->l('Category Type').'</th></tr></thead><tbody>';
		foreach ($categories AS $category)
		{
			$this->_html .= '<tr><td>'.$category['name'].'</td><td>
			<select name="cat_'.$category['id_category'].'" id="cat_'.$category['id_category'].'">
				<option value="0">'.$this->l('Choose a category...').'</option>';
				foreach ($this->_product_type AS $id => $cat)
					$this->_html .= '<option value="'.$id.'" '.((Configuration::get('SAC_CATEGORY_TYPE_'.$category['id_category']) == $id) ? ' selected="true"' : '').'>'.$cat.'</option>';
			$this->_html .= '</select></td></tr>';
		}
		$this->_html .= '</tbody></table></div>
			</fieldset>
			<div class="clear">&nbsp;</div>
			<fieldset><legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Carrier Configuration').'</legend>
				<label>'.$this->l('Carrier Detail').'</label>
				<div class="margin-form">
					<table cellspacing="0" cellpadding="0" class="table">
						<thead><tr><th>'.$this->l('Carrier').'</th><th>'.$this->l('Carrier Type').'</th></tr></thead><tbody>';
		foreach ($carriers AS $carrier)
		{
			$this->_html .= '<tr><td>'.$carrier['name'].'</td><td><select name="carrier_'.$carrier['id_carrier'].'" id="cat_'.$carrier['id_carrier'].'">
			<option value="0">'.$this->l('Choose a carrier type...').'</option>';
			foreach ($this->_carrier_type AS $id => $type)
				$this->_html .= '<option value="'.$id.'"'.((Configuration::get('SAC_CARRIER_TYPE_'.$carrier['id_carrier']) == $id) ? ' selected="true"' : '').'>'.$type.'</option>';
			$this->_html .= '</select></td>';
		}
			$this->_html .= '</tbody></table></margin>
			</div>
			<div class="clear">&nbsp;</div>
			<label>'.$this->l('Default Carrier Type').'</label>
			<div class="margin-form">
				<select name="fianetfraud_default_carrier">';
		foreach ($this->_carrier_type AS $k => $type)
			$this->_html .= '<option value="'.$k.'"'.($k == Configuration::get('SAC_DEFAULT_CARRIER_TYPE') ? ' selected' : '').'>'.$type.'</option>';
		$this->_html .= '</select>
			</div>
			</fieldset><div class="clear">&nbsp;</div>';
		
		/* Get all modules then select only payment ones*/
		$modules = Module::getModulesOnDisk();
		$modules_is_fianet = explode(',', Configuration::get('SAC_PAYMENT_MODULE'));
		$this->paymentModules = array();
		foreach ($modules AS $module)
			if (method_exists($module, 'hookPayment'))
			{
				if($module->id)
				{
					$module->country = array();
					$countries = DB::getInstance()->ExecuteS('SELECT id_country FROM '._DB_PREFIX_.'module_country WHERE id_module = '.(int)($module->id));
					foreach ($countries as $country)
						$module->country[] = $country['id_country'];
						
					$module->currency = array();
					$currencies = DB::getInstance()->ExecuteS('SELECT id_currency FROM '._DB_PREFIX_.'module_currency WHERE id_module = '.(int)($module->id));
					foreach ($currencies as $currency)
						$module->currency[] = $currency['id_currency'];
						
					$module->group = array();
					$groups = DB::getInstance()->ExecuteS('SELECT id_group FROM '._DB_PREFIX_.'module_group WHERE id_module = '.(int)($module->id));
					foreach ($groups as $group)
						$module->group[] = $group['id_group'];
				}
				else
				{
					$module->country = NULL;
					$module->currency = NULL;
					$module->group = NULL;
				}
				$this->paymentModules[] = $module;
			}

			$this->_html .= '<fieldset><legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Payment Configuration').'</legend>
				<label>'.$this->l('Payment Detail').'</label>
				<div class="margin-form">
					<table cellspacing="0" cellpadding="0" class="table" ><thead><tr>
						<th><input type="checkbox" name="checkme" class="noborder" onclick="checkDelBoxes(this.form, \'payementBox[]\', this.checked)" /></th>	
						<th>'.$this->l('Payment Module').'</th><th>'.$this->l('Payment Type').'</th></tr></thead><tbody>';

			foreach ($this->paymentModules as $module)
			{
					$this->_html .= '<tr><td><input type="checkbox" class="noborder" value="'.substr($module->name,0,15).'" name="payementBox[]" ' .(in_array(substr($module->name,0,15), $modules_is_fianet) ? 'checked="checked"' : '').'></td>';
					$this->_html .= '<td><img src="'.__PS_BASE_URI__.'modules/'.$module->name.'/logo.gif" alt="'.$module->name.'" title="'.$module->displayName.'" />'.stripslashes($module->displayName).'</td><td><select name="'.substr($module->name,0,15).'">';
					$this->_html .= '<option value="0">'.$this->l('-- Choose --').'</option>';
					foreach ($this->_payement_type as $type)
						$this->_html .= '<option '.((Configuration::get('SAC_PAYMENT_TYPE_'.substr($module->name,0,15)) == $type) ? 'selected="true"' : '').'>'.$type.'</option>';			
					$this->_html .= '</select></tr>';
			}
			
		$this->_html .= '</tbody></table></margin></fieldset><br class="clear" /><br />
			<center><input type="submit" name="submitSettings" value="'.$this->l('Save').'" class="button" /></center>
		</form>
		<div class="clear">&nbsp;</div>';
		return $this->_html;
	}

	public function hookCart($params)
	{
		if ($_SERVER['REMOTE_ADDR'] == '0.0.0.0' OR $_SERVER['REMOTE_ADDR'] == '' OR $_SERVER['REMOTE_ADDR'] === false)
			return true;
		$res = Db::getInstance()->ExecuteS('
		SELECT `id_cart`
		FROM '._DB_PREFIX_.'fianet_fraud
		WHERE id_cart='.(int)($params['cart']->id));
		if (Db::getInstance()->NumRows() > 0)
			Db::getInstance()->Execute('
			UPDATE `'._DB_PREFIX_.'fianet_fraud`
			SET `ip_address` = '.ip2long($_SERVER['REMOTE_ADDR']).', `date` = \''.pSQL(date('Y-m-d H:i:s')).'\'
			WHERE `id_cart` = '.(int)($params['cart']->id).' LIMIT 1');
		else
			Db::getInstance()->Execute('
			INSERT INTO `'._DB_PREFIX_.'fianet_fraud` (`id_cart`, `ip_address`, `date`)
			VALUES ('.(int)($params['cart']->id).', '.ip2long($_SERVER['REMOTE_ADDR']).',\''.date('Y-m-d H:i:s').'\')');
		return true;
	}

	static private function getIpByCart($id_cart = false)
	{
		if ($id_cart == false)
			return false;
		return long2ip(Db::getInstance()->getValue('
		SELECT `ip_address`
		FROM '._DB_PREFIX_.'fianet_fraud
		WHERE id_cart = '.(int)($id_cart)));
	}

	public function hookUpdateOrderStatus($params)
	{
		$order_status = false;
		$conf = Configuration::getMultiple(array('SAC_PRODUCTION', 'PS_SAC_ID_FRAUD', 'SAC_SITEID', 'SAC_LOGIN', 'SAC_PASSWORD'));
		if ($params['newOrderStatus']->id  == Configuration::get('SAC_ID_FRAUD'))
			$order_status = 2;
		elseif ($params['newOrderStatus']->id  == _PS_OS_DELIVERED_)
			$order_status = 1;
		elseif ($params['newOrderStatus']->id == _PS_OS_CANCELED_)
			$order_status = 2;
		elseif ($params['newOrderStatus']->id == _PS_OS_REFUND_)
			$order_status = 6;
		if ($order_status != false)
			return file_get_contents('https://secure.fia-net.com/'.($conf['SAC_PRODUCTION'] ? 'fscreener' : 'pprod').'/engine/delivery.cgi?SiteID='.$conf['SAC_SITEID'].'&Pwd='.urlencode($conf['SAC_PASSWORD']).'&RefID='.(int)$params['id_order'].'&Status='.$order_status);
		else
			return true;
	}

	public function needCheck($id_module, $total_paid)
	{
		$modules = explode(',', Configuration::get('SAC_PAYMENT_MODULE'));
		if (!in_array($id_module, $modules))
			return false;
		if ($total_paid < Configuration::get('SAC_MINIMAL_ORDER'))
			return false;
		return true;
	}

	public function hookNewOrder($params)
	{
		if ($params['order']->total_paid <= 0)
			return;
			
		if (!$this->needCheck($params['order']->module, $params['order']->total_paid))
			return false;
			
		$address_delivery = new Address((int)($params['order']->id_address_delivery));
		$address_invoice = new Address((int)($params['order']->id_address_invoice));
		$customer = new Customer((int)($params['order']->id_customer));
		$orderFianet = new fianet_order_xml();
		$id_lang = Configuration::get('PS_LANG_DEFAULT');
		if($address_invoice->company == '')
			$orderFianet->billing_user->set_quality_nonprofessional();
		else
			$orderFianet->billing_user->set_quality_professional();

		$orderFianet->billing_user->titre = (($customer->id_gender == 1) ? $this->l('Mr.') : (($customer->id_gender == 2 ) ? $this->l('Mrs') : $this->l('Mr.')));
		$orderFianet->billing_user->nom = utf8_decode($address_invoice->lastname);
		$orderFianet->billing_user->prenom = utf8_decode($address_invoice->firstname);
		$orderFianet->billing_user->societe = utf8_decode($address_invoice->company);
		$orderFianet->billing_user->telhome = utf8_decode($address_invoice->phone);
		$orderFianet->billing_user->office = '';
		$orderFianet->billing_user->telmobile = utf8_decode($address_invoice->phone_mobile);
		$orderFianet->billing_user->telfax = '';
		$orderFianet->billing_user->email = $customer->email;

		$customer_stats = $customer->getStats();
		$all_orders = Order::getCustomerOrders((int)($customer->id));
		$orderFianet->billing_user->site_conso = new fianet_user_siteconso_xml();
		$orderFianet->billing_user->site_conso->ca = $customer_stats['total_orders'];
		$orderFianet->billing_user->site_conso->nb = $customer_stats['nb_orders'];
		$orderFianet->billing_user->site_conso->datepremcmd	= $all_orders[count($all_orders) - 1]['date_add'];
		if (count($all_orders) > 1)
			$orderFianet->billing_user->site_conso->datederncmd	= $all_orders[1]['date_add'];

		$orderFianet->billing_adress->rue1  = utf8_decode($address_invoice->address1);
		$orderFianet->billing_adress->rue2  = utf8_decode($address_invoice->address2);
		$orderFianet->billing_adress->cpostal = utf8_decode($address_invoice->postcode);
		$orderFianet->billing_adress->ville = utf8_decode($address_invoice->city);
		$country = new Country((int)($address_invoice->id_country));
		$orderFianet->billing_adress->pays = utf8_decode($country->name[$id_lang]);

		//delivery adresse not send if carrier id is 1 or 2
		$carrier_id = array(1,2);
		if (!in_array(Configuration::get('SAC_CARRIER_TYPE_'.(int)($params['cart']->id_carrier)),$carrier_id))
		{
			$orderFianet->delivery_user = new fianet_delivery_user_xml();
			$orderFianet->delivery_adress = new fianet_delivery_adress_xml();
		
			if ($address_delivery->company == '')
				$orderFianet->delivery_user->set_quality_nonprofessional();
			else
				$orderFianet->delivery_user->set_quality_professional();
				
			$orderFianet->delivery_user->titre = (($customer->id_gender == 1) ? $this->l('Mr.') : (($customer->id_gender == 2) ? $this->l('Mrs') : $this->l('Unknown')));
		
			$orderFianet->delivery_user->nom = utf8_decode($address_delivery->lastname);
			$orderFianet->delivery_user->prenom = utf8_decode($address_delivery->firstname);
			$orderFianet->delivery_user->societe = utf8_decode($address_delivery->company);
			$orderFianet->delivery_user->telhome = utf8_decode($address_delivery->phone);
			$orderFianet->delivery_user->office = '';
			$orderFianet->delivery_user->telmobile = utf8_decode($address_delivery->phone_mobile);
			$orderFianet->delivery_user->telfax = '';
			$orderFianet->delivery_user->email = $customer->email;
		
			$orderFianet->delivery_adress->rue1 = utf8_decode($address_delivery->address1);
			$orderFianet->delivery_adress->rue2 = utf8_decode($address_delivery->address2);
			$orderFianet->delivery_adress->cpostal = utf8_decode($address_delivery->postcode);
			$orderFianet->delivery_adress->ville = utf8_decode($address_delivery->city);
			$country =  new Country((int)($address_delivery->id_country));
			$orderFianet->delivery_adress->pays = utf8_decode($country->name[$id_lang]);
		}
		
		$orderFianet->info_commande->refid = ($params['order']->id);
		$orderFianet->info_commande->montant = $params['order']->total_paid;
		$currency = new Currency((int)($params['order']->id_currency));
		$orderFianet->info_commande->devise = $currency->iso_code;
		$orderFianet->info_commande->ip = self::getIpByCart((int)($params['cart']->id));
		$orderFianet->info_commande->timestamp	= date('Y-m-d H:i:s');

		$products = $params['cart']->getProducts();
		$default_product_type = Configuration::get('SAC_DEFAULT_PRODUCT_TYPE');
		foreach ($products AS $product)
		{
			$product_categories = Product::getIndexedCategories((int)($product['id_product']));
			$have_sac_cat = false;

			$produit = new fianet_product_xml();
			
			if(Configuration::get('SAC_CATEGORY_TYPE_'.$product['id_category_default']))
			{
				$produit->type = Configuration::get('SAC_CATEGORY_TYPE_'.$product['id_category_default']);
			}
			else
			$produit->type = $default_product_type;
			$produit->ref = utf8_decode((((isset($product['reference']) AND !empty($product['reference'])) ? $product['reference'] : ((isset($product['ean13']) AND !empty($product['ean13'])) ? $product['ean13'] : $product['name']))));
			$produit->nb = $product['cart_quantity'];
			$produit->prixunit = $product['price'];
			$produit->name = utf8_decode($product['name']);
			$orderFianet->info_commande->list->add_product($produit);
		}

		$carrier = new Carrier((int)($params['order']->id_carrier));
		$orderFianet->info_commande->transport->type = Configuration::get('SAC_CARRIER_TYPE_'.(int)($carrier->id));
		$orderFianet->info_commande->transport->nom = $carrier->name;
		$orderFianet->info_commande->transport->rapidite = self::getCarrierFastById((int)($carrier->id));
		$orderFianet->payment->type = Configuration::get('SAC_PAYMENT_TYPE_'.substr($params['order']->module,0,15));

		$xml = $orderFianet->get_xml();
		$sender = new fianet_sender();
		if (Configuration::get('SAC_PRODUCTION'))
			$sender->mode = 'production';
		else
			$sender->mode = 'test';
			
		$sender->add_order($orderFianet);
		$res = $sender->send_orders_stacking();
		Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'fianet_fraud_orders(id_order, date_add) VALUES('.(int)($params['order']->id).', \''.pSQL(date('Y-m-d H:i:s')).'\')');
		return true;
	}

	static public function checkWaitingOrders()
	{
		$orders = Db::getInstance()->ExecuteS('SELECT id_order FROM '._DB_PREFIX_.'fianet_fraud_orders WHERE `date_add` > \''.pSQL(strtotime('+5 minute')).'\'');
		foreach ($orders AS $order)
		{
			self::updateOrderHistory((int)($order['id_order']));
			Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'fianet_fraud_orders WHERE id_order='.(int)($order['id_order']));
		}
	}

	public function hookAdminOrder($params)
	{
		$conf = Configuration::get('SAC_PRODUCTION');
		$order = new Order((int)($params['id_order']));
		if (!self::needCheck($order->module, $order->total_paid))
			return null;

		if (isset($_POST['submitFianet']))
			$this->_postProcess();
		$html = '<br /><fieldset style="width:400px;"><legend>'.$this->l('Fianet Validation').'</legend>';
		$html .= '<a href="https://secure.fia-net.com/'.($conf ? 'fscreener' : 'pprod').'/BO/visucheck_detail.php?sid='.Configuration::get('SAC_SITEID').'&log='.Configuration::get('SAC_LOGIN').'&pwd='.urlencode(Configuration::get('SAC_PASSWORD')).'&rid='.$params['id_order'].'">'.$this->l('See Detail').'</a><br />';
		$html .= $this->l('Evaluate').': '.self::getEval((int)($order->id));
		$html .= '</fieldset>';

		return $html;
	}

	private static function getHCarriers($field)
	{
		$carriers = Carrier::getCarriers(Configuration::get('PS_LANG_DEFAULT'));
		$hcarrier = '<option value=""></option>';
		foreach ($carriers AS $carrier)
			$hcarrier .= '<option value="'.$carrier['id_carrier'].'"'.(($carrier['id_carrier'] == Configuration::get($field)) ? 'selected="selected"' : '').'>'.$carrier['name'].'</option>';
		return $hcarrier;
	}

	private static function updateOrderHistory($id_order)
	{
		if (self::getEval((int)($id_order)) > 0)
			return true;
		elseif (self::getEval((int)($id_order)) == 0)
		{
			$orderHistory = new OrderHistory();
			$orderHistory->id_order = (int)($id_order);
			$orderHistory->id_order_state = Configuration::get('SAC_ID_FRAUD');
			$orderHistory->save();
			return true;
		}
	}

	private static function getEval($id_order)
	{
		$sender = new fianet_sender();
		if (Configuration::get('SAC_PRODUCTION'))
			$sender->mode = 'production';
		$result = $sender->get_evaluation(array($id_order));
		return $result[0]['eval'];
	}

	public static function reEvaluateOrder()
	{
		$sender = new fianet_sender();
		if (Configuration::get('SAC_PRODUCTION'))
			$sender->mode = 'production';
		$result = $sender->get_reevaluated_order();

		foreach ($result AS $row)
			if ($row['eval'] > 0)
				if (OrderHistory::getLastOrderState($row['refid']) == Configuration::get('SAC_ID_WAITING'))
				{
					$orderHistory = new OrderHistory();
					$orderHistory->id_order = (int)($row['refid']);
					$orderHistory->id_order_state = _PS_OS_PAYMENT_;
					$orderHistory->save();
				}
		return true;
	}

	private static function getCarrierFastById($id_carrier)
	{
		return 2;
	}

	public function getSACCategories()
	{
		$categories = Db::getInstance()->ExecuteS('SELECT id_category, id_sac FROM '._DB_PREFIX_.'sac_categories');
		$sac_cat = array();
		if ($categories)
			foreach ($categories AS $category)
				$sac_cat[$category['id_category']] = $category['id_sac'];
		return $sac_cat;
	}
}

