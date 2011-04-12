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

class FianetSceau extends Module
{
	private $id_order;
	
	function __construct()
	{
	 	$this->name = 'fianetsceau';
	 	$this->tab = 'front_office_features';
	 	$this->version = '1.0';
		$this->limited_countries = array('fr');
		
	 	parent::__construct();
		
		$this->displayName = $this->l('FIA-NET Seal of Confidence');
		$this->description = $this->l('Turn your visitors into buyers by creating confidence in your site.');
		if (!Configuration::get('FIANET_SCEAU_PRIVATEKEY'))
			$this->warning = $this->l('Please enter your Private Key field.');
		if (!Configuration::get('FIANET_SCEAU_SITEID'))
			$this->warning = $this->l('Please enter your site ID.');
	}
	
	public function install()
	{
		return (parent::install() AND
		$this->registerHook('rightColumn') AND
		$this->registerHook('updateOrderStatus') AND
		Db::getInstance()->Execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'fianet_seal`(
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `id_order` int(10) unsigned NOT NULL,
			  `upload_success` int(10) unsigned NOT NULL,
			  `valid` int(10) unsigned NOT NULL,
			  `status` int(10) unsigned NOT NULL,
			  `date_upd` datetime NOT NULL,
			  `date_add` datetime NOT NULL,
			  PRIMARY KEY (`id`)
			)'));
	}

	private function getProcess()
	{
		if ((int)Tools::getValue('fia_net_mode') == 1)
			Configuration::updateValue('FIA_NET_SEAL_MODE', 1);
		else
			Configuration::updateValue('FIA_NET_SEAL_MODE', 0);

		if (!preg_match('#^[0-9]+$#', Tools::getValue('FIANET_SCEAU_SITEID')))
			return parent::displayError('Bad site id (numbers only)');
		else
			Configuration::updateValue('FIANET_SCEAU_SITEID', Tools::getValue('FIANET_SCEAU_SITEID'));
		Configuration::updateValue('FIANET_SCEAU_PRIVATEKEY', Tools::getValue('FIANET_SCEAU_PRIVATEKEY'));
		
		if ((int)Tools::getValue('fia_net_mode'))
			$dataSync = (($site_id = Configuration::get('FIANET_SCEAU_SITEID')) ? '<img src="http://www.prestashop.com/modules/fianetsceau.png?site_id='.urlencode($site_id).'" style="float:right" />' : 'toto');
		else
			$dataSync = '';
		return $this->_html .= $this->displayConfirmation($this->l('Configuration updated').$dataSync);	}
	
	public function getContent()
	{
		$output = '<h2>'.$this->displayName.'</h2>';
		if (!is_callable('curl_init'))
			$output .= parent::displayError('You need to enable Curl library to use this module');
		else if (Tools::isSubmit('submitFianet'))
			$output .= self::getProcess();
	
		$output .= '
		<fieldset style="width:80%"><legend>'.$this->displayName.'</legend>
			<img src="../modules/'.$this->name.'/logo.jpg" style="float:right;margin:5px 10px 5px 0" />
			<blockquote style="margin-left:5px"><b>« Le Sceau de Confiance FIA-NET, leader de la confiance sur le web, influence la décision d’achat de 83 % des internautes (*)</b></blockquote>
			<p style="margin-left:30px"><br />
			Le Sceau de Confiance FIA-NET, le plus connu en France, fait la preuve de vos performances. Il restitue les avis de vos clients grâce à l’envoi <b>de deux questionnaires de satisfaction</b> après l’achat et après la livraison.<br /><br />
			<b>L’extranet, un outil d’analyse de performance unique</b>, exploite les réponses de vos clients à ces questionnaires. Une aide inestimable qui vous permet de mieux connaitre vos clients et de piloter votre politique marketing et communication.<br />
			<br />
				<span style="font-size:0.8em;font-style:italic;">(*Etude FIA-NET – Novembre 2009 – 836 répondants)</span> »</p>
			<p>'.$this->l('To sign in, check out: ').' <u><a href="https://www.fia-net.com/marchands/devispartenaire.php?p=185" target="_blank">'.$this->l('Fia-net Website').'</a></u></p>
		</fieldset><p class="clear">&nbsp;</p>';
		$output .= '
		<form action="'.Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset class="width2">
				<legend><img src="'.$this->_path.'logo.gif" alt="" class="middle" />'.$this->l('Settings').'</legend>
				<label>'.$this->l('Your site ID').'</label>
				<div class="margin-form">
					<input type="text" name="FIANET_SCEAU_SITEID" value="'.Configuration::get('FIANET_SCEAU_SITEID').'" />
					<p class="clear">'.$this->l('Sample:').' site_id = \'<b>XXXXX</b>\' '.$this->l('(numbers only)').'</p>
				</div>
				<label>'.$this->l('Private Key').'</label>
				<div class="margin-form">
					<input type="text" name="FIANET_SCEAU_PRIVATEKEY" value="'.Configuration::get('FIANET_SCEAU_PRIVATEKEY').'" />
					<p class="clear">'.$this->l('Private key communicated by Fia-Net').'</p>
				</div>
				<label>'.$this->l('Mode').'</label>
				<div class="margin-form">
					<span style="display:block;float:left;margin-top:3px;">
					<input type="radio" id="test" name="fia_net_mode" value="0" style="vertical-align:middle;display:block;float:left;margin-top:2px;margin-right:3px;"
						'.(!Configuration::get('FIA_NET_SEAL_MODE') ? 'checked' : '').'/>
					<label for="test" style="color:#900;display:block;float:left;text-align:left;width:60px;">'.$this->l('Test').'</label>&nbsp;</span>
					<span style="display:block;float:left;margin-top:3px;">
					<input type="radio" id="production" name="fia_net_mode" value="1" style="vertical-align:middle;display:block;float:left;margin-top:2px;margin-right:3px;"
						'.(Configuration::get('FIA_NET_SEAL_MODE') ? 'checked' : '').'/>
					<label for="production" style="color:#080;display:block;float:left;text-align:left;width:85px;">'.$this->l('Production').'</label></span>
				</div>
				<p class="clear">&nbsp;</p>
				<input type="submit" name="submitFianet" value="'.$this->l('Update settings').'" class="button" />	
			</fieldset>
		</form>';

		return $output;
	}
	
	private function sendXML()
	{
		$SiteID = Configuration::get('FIANET_SCEAU_SITEID');
		$order = new Order($this->id_order);
		$customer = new Customer($order->id_customer);
		$currency = new Currency($order->id_currency);
		$control = new SimpleXMLElement('<?xml version="1.0" encoding="ISO-8859-1"?><control></control>');

		$user = $control->addChild('utilisateur');
		$name = $user->addChild('nom', $customer->lastname);
		if ($customer->id_gender == 1 OR $customer->id_gender == 2)
			$name->addAttribute('titre', 1);
		$user->addChild('prenom', $customer->firstname);
		$user->addChild('email', $customer->email);
		
		$info = $control->addChild('infocommande');
		$info->addChild('siteid', $SiteID);
		$info->addChild('refid', $order->id);
		
		$total = round($order->total_paid / $currency->conversion_rate, 2);
		$amount = $info->addChild('montant', $total);
		$amount->addAttribute('devise', 'EUR');

		$ip = long2ip(Db::getInstance()->getValue('
			SELECT ip_address
			FROM '._DB_PREFIX_.'connections a
			WHERE a.id_guest = (
				SELECT id_guest FROM '._DB_PREFIX_.'guest
				WHERE id_customer = '.$customer->id.'
				LIMIT 1)'));

		$order_date = Db::getInstance()->getValue('
			SELECT date_add
			FROM '._DB_PREFIX_.'orders
			WHERE id_order = '.(int)$order->id);
		$customer_ip = $info->addChild('ip', $ip);		
		$customer_ip->addAttribute('timestamp', $order_date);

		$cryptKey = md5(Configuration::get('FIANET_SCEAU_PRIVATEKEY').'_'.$order->id.'+'.$order_date.'='.$customer->email);
		$control->addChild('crypt', $cryptKey);
		
		$XMLInfo = $control->asXML();
		$CheckSum = md5($XMLInfo);

		$curl = curl_init('https://www.fia-net.com/engine/'.(!Configuration::get('FIA_NET_SEAL_MODE') ? 'preprod/' : '').'sendrating.cgi');
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, 'SiteID='.$SiteID.'&XMLInfo='.$XMLInfo.'&CheckSum='.$CheckSum);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		$result = new simpleXMLelement(curl_exec($curl));
		curl_close ($curl);

		if ($result['type'] == 'OK')
			return true;
		return false;
	}
	
	public function	hookUpdateOrderStatus($params)
	{
		$this->id_order = (int)$params['id_order'];
		$res = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'fianet_seal` a WHERE a.id_order = '.(int)$params['id_order']);
		$upload_success = false;
		if (!$res)
		{
			if (!($order = new Order($params['id_order'])))
				return;
			if ($params['newOrderStatus']->logable == 1)
				$upload_success = self::sendXML();
			Db::getInstance()->Execute('
				INSERT INTO '._DB_PREFIX_.'fianet_seal (id_order, upload_success, valid, status, date_upd, date_add)
				VALUES ('.(int)$order->id.', 0,'.(int)$params['newOrderStatus']->logable.', '.(int)$params['newOrderStatus']->id.', NOW(), NOW())');
		}
		else
		{
			if ($res['valid'] == 1 AND $res['upload_success'] == 1)
				return;
			if ($params['newOrderStatus']->logable == 1)
				$upload_success = self::sendXML();
			Db::getInstance()->Execute('
				UPDATE `'._DB_PREFIX_.'fianet_seal` a
				SET valid = '.(int)$params['newOrderStatus']->logable.', upload_success = '.(int)$upload_success.', status = '.(int)$params['newOrderStatus']->id.', date_upd = NOW()
				WHERE a.id = '.$res['id']);
		}
	}
	
	public function hookLeftColumn($params)
	{
		return $this->hookRightColumn($params);
	}
	
	public function hookRightColumn($params)
	{
		global $cookie;
		return '<a href="javascript:;" onclick="varwin=window.open(\'https://www.fia-net.com/certif/certificat.php?key='.Configuration::get('FIANET_SCEAU_SITEID').'&amp;lang='.Language::getIsoById((int)($cookie->id_lang)).'\', \'certificat\', \'width=650, height=510\', \'toolbar=no, location=no,directories=no, status=no, menubar=no, scrollbars=no, resizable=yes,dependent=yes\');"><img src="https://www.fia-net.com/img/logos/'.(($cookie->id_lang != 2 ) ? 'en/' : '' ).'rouge3bc.gif" title="Voir la fiche marchand sur Fia-net.com" alt="Voir la fiche marchand sur Fia-net.com" /></a>';
	}
}


