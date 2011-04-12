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

class Ogone extends PaymentModule
{
	public function __construct()
	{
		$this->name = 'ogone';
		$this->tab = 'payments_gateways';
		$this->version = '2.0';

        parent::__construct();

        $this->displayName = 'Ogone';
        $this->description = '';
	}
	
	public function install()
	{
		return (parent::install() AND $this->registerHook('payment') AND $this->registerHook('orderConfirmation'));
	}
	
	public function getContent()
	{
		if (Tools::isSubmit('submitOgone'))
		{
			Configuration::updateValue('OGONE_PSPID', Tools::getValue('OGONE_PSPID'));
			Configuration::updateValue('OGONE_SHA_IN', Tools::getValue('OGONE_SHA_IN'));
			Configuration::updateValue('OGONE_SHA_OUT', Tools::getValue('OGONE_SHA_OUT'));
			Configuration::updateValue('OGONE_MODE', (int)Tools::getValue('OGONE_MODE'));
			$dataSync = (($pspid = Configuration::get('OGONE_PSPID'))
				? '<img src="http://www.prestashop.com/modules/ogone.png?pspid='.urlencode($pspid).'&mode='.(int)Tools::getValue('OGONE_MODE').'" style="float:right" />'
				: ''
			);
			echo $this->displayConfirmation($this->l('Configuration updated').$dataSync);
		}
		
		return '
		<fieldset><legend><img src="../modules/'.$this->name.'/logo.gif" /> '.$this->l('Help').'</legend>
			<p>'.$this->l('Please follow these steps:').'</p>
			<ol>
				<li>
					<h3>'.$this->l('PrestaShop side').'</h3>
					<ol>
						<li>'.$this->l('Fill in your Ogone ID (PSPID)').'</li>
						<li>'.$this->l('Fill in the signatures of your choice (you will have to copy them to Ogone back office).').'</li>
						<li>'.$this->l('Select test mode if you just created your test account on Ogone (it does not work with the demo account).').'</li>
					</ol>
				</li>
				<li>
					<h3>
						'.$this->l('Ogone Side').' /
						<a href="https://secure.ogone.com/ncol/test/admin_ogone.asp"><span style="text-decoration:underline;color:#383838">'.$this->l('test mode').'</span></a> /
						<a href="https://secure.ogone.com/ncol/prod/admin_ogone.asp"><span style="text-decoration:underline;color:#383838">'.$this->l('production mode').'</span></a>
					</h3>
					<ol>
						<li><a href="../modules/'.$this->name.'/docs/en1.png">'.$this->l('See the screenshot for step').' 1</a></li>
						<li><a href="../modules/'.$this->name.'/docs/en2.png">'.$this->l('See the screenshot for step').' 2</a></li>
						<li><a href="../modules/'.$this->name.'/docs/en3.png">'.$this->l('See the screenshot for step').' 3</a></li>
						<li><a href="../modules/'.$this->name.'/docs/en4.png">'.$this->l('See the screenshot for step').' 4</a></li>
					</ol>
				</li>
			</ol>
			<h3>'.$this->l('Test cards').'</h3>
			<ul>
				<li>Visa : 4111 1111 1111 1111</li>
				<li>Visa 3D : 4000 0000 0000 0002</li>
				<li>American Express : 3741 1111 1111 111</li>
				<li>MasterCard : 5399 9999 9999 9999</li>
				<li>Diners : 3625 5695 5800 17</li>
				<li>Bancontact/Mister : 67030000000000003</li>
				<li>Visa Purchasing : 4484 1200 0000 0029</li>
				<li>American Express : 3742 9101 9071 995</li>
			</ul>
			<div class="clear">&nbsp;</div>
		</fieldset>
		<div class="clear">&nbsp;</div>
		<form action="'.Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset><legend><img src="../img/admin/contact.gif" /> '.$this->l('Settings').'</legend>
				<label for="pspid">'.$this->l('PSPID').'</label>
				<div class="margin-form">
					<input type="text" id="pspid" size="20" name="OGONE_PSPID" value="'.Tools::getValue('OGONE_PSPID', Configuration::get('OGONE_PSPID')).'" />
				</div>
				<div class="clear">&nbsp;</div>
				<label for="sha-in">'.$this->l('SHA-in signature').'</label>
				<div class="margin-form">
					<input type="text" id="sha-in" size="20" name="OGONE_SHA_IN" value="'.Tools::getValue('OGONE_SHA_IN', Configuration::get('OGONE_SHA_IN')).'" />
				</div>
				<div class="clear">&nbsp;</div>
				<label for="sha-out">'.$this->l('SHA-out signature').'</label>
				<div class="margin-form">
					<input type="text" id="sha-out" size="20" name="OGONE_SHA_OUT" value="'.Tools::getValue('OGONE_SHA_OUT', Configuration::get('OGONE_SHA_OUT')).'" />
				</div>
				<div class="clear">&nbsp;</div>
				<label>'.$this->l('Mode').'</label>
				<div class="margin-form">
					<span style="display:block;float:left;margin-top:3px;"><input type="radio" id="test" name="OGONE_MODE" value="0" style="vertical-align:middle;display:block;float:left;margin-top:2px;margin-right:3px;"
						'.(!Tools::getValue('OGONE_MODE', Configuration::get('OGONE_MODE')) ? 'checked="checked"' : '').'
					/>
					<label for="test" style="color:#900;display:block;float:left;text-align:left;width:60px;">'.$this->l('Test').'</label>&nbsp;</span>
					<span style="display:block;float:left;margin-top:3px;">
					<input type="radio" id="production" name="OGONE_MODE" value="1" style="vertical-align:middle;display:block;float:left; margin-top:2px;margin-right:3px;"
						'.(Tools::getValue('OGONE_MODE', Configuration::get('OGONE_MODE')) ? 'checked="checked"' : '').'
					/>
					<label for="production" style="color:#080;display:block;float:left;text-align:left;width:85px;">'.$this->l('Production').'</label></span>
				</div>
				<div class="clear">&nbsp;</div>
				<input type="submit" name="submitOgone" value="'.$this->l('Update settings').'" class="button" />
			</fieldset>
		</form>
		<div class="clear">&nbsp;</div>';
	}
	
	public function hookPayment($params)
	{
		global $smarty;
		
		$currency = new Currency((int)($params['cart']->id_currency));
		$lang = new Language((int)($params['cart']->id_lang));
		$customer = new Customer((int)($params['cart']->id_customer));
		$address = new Address((int)($params['cart']->id_address_invoice));
		$country = new Country((int)($address->id_country), (int)($params['cart']->id_lang));
		
		$ogoneParams = array();
		$ogoneParams['PSPID'] = Configuration::get('OGONE_PSPID');
		$ogoneParams['OPERATION'] = 'SAL';
		$ogoneParams['ORDERID'] = pSQL($params['cart']->id);
		$ogoneParams['AMOUNT'] = number_format(Tools::convertPrice((float)(number_format($params['cart']->getOrderTotal(true, Cart::BOTH), 2, '.', '')), $currency), 2, '.', '') * 100;
		$ogoneParams['CURRENCY'] = $currency->iso_code;
		$ogoneParams['LANGUAGE'] = $lang->iso_code.'_'.strtoupper($lang->iso_code);
		$ogoneParams['CN'] = $customer->lastname;
		$ogoneParams['EMAIL'] = $customer->email;
		$ogoneParams['OWNERZIP'] = $address->postcode;
		$ogoneParams['OWNERADDRESS'] = ($address->address1);
		$ogoneParams['OWNERCTY'] = $country->iso_code;
		$ogoneParams['OWNERTOWN'] = $address->city;
		$ogoneParams['paramplus'] = 'secure_key='.$params['cart']->secure_key;
		if (!empty($address->phone))
			$ogoneParams['OWNERTELNO'] = $address->phone;

		ksort($ogoneParams);
		$shasign = '';
		foreach ($ogoneParams as $key => $value)
			$shasign .= strtoupper($key).'='.$value.Configuration::get('OGONE_SHA_IN');
		$ogoneParams['SHASign'] = strtoupper(sha1($shasign));
		
		$smarty->assign('ogone_params', $ogoneParams);
		$smarty->assign('OGONE_MODE', Configuration::get('OGONE_MODE'));
		
		return $this->display(__FILE__, 'ogone.tpl');
    }
	
	public function hookOrderConfirmation($params)
	{
		global $smarty, $cookie;
		
		if ($params['objOrder']->module != $this->name)
			return;
		
		if ($params['objOrder']->valid)
			$smarty->assign(array('status' => 'ok', 'id_order' => $params['objOrder']->id));
		else
			$smarty->assign('status', 'failed');
		$link = new Link();
		$smarty->assign('ogone_link', (method_exists($link, 'getPageLink') ? $link->getPageLink('contact-form.php', true) : Tools::getHttpHost(true).'contact-form.php'));
		return $this->display(__FILE__, 'hookorderconfirmation.tpl');
	}
	
	public function validate($id_cart, $id_order_state, $amount, $message = '', $secure_key)
	{
		$this->validateOrder((int)$id_cart, $id_order_state, $amount, $this->displayName, $message, NULL, NULL, true, pSQL($secure_key));
		if ($amount > 0 AND class_exists('PaymentCC'))
		{
			$pcc = new PaymentCC();
			$order = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'orders WHERE id_cart = '.(int)$secure_cart[0]);
			$pcc->id_order = (int)$order['id_order'];
			$pcc->id_currency = (int)$order['id_currency'];
			$pcc->amount = $amount;
			$pcc->transaction_id = Tools::getValue('PAYID');
			$pcc->card_number = Tools::getValue('CARDNO');
			$pcc->card_brand = Tools::getValue('BRAND');
			$pcc->card_expiration = Tools::getValue('ED');
			$pcc->card_holder = Tools::getValue('CN');
			$pcc->add();
		}		
	}
}


