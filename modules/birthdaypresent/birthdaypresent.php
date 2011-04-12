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

if (!defined('_CAN_LOAD_FILES_'))
	exit;
  
class BirthdayPresent extends Module
{
    private $_html = '';

    function __construct()
    {
        $this->name = 'birthdaypresent';
        $this->tab = 'pricing_promotion';
        $this->version = 1.0;
		$this->author = 'PrestaShop';
		
		parent::__construct();
		
        $this->displayName = $this->l('Birthday Present');
        $this->description = $this->l('Offer your clients birthday presents automatically');
	}
		
	public function getContent()
	{
		global $cookie, $currentIndex;
		
		if (Tools::isSubmit('submitBirthday'))
		{
			Configuration::updateValue('BIRTHDAY_ACTIVE', (int)(Tools::getValue('bp_active')));
			Configuration::updateValue('BIRTHDAY_DISCOUNT_TYPE', (int)(Tools::getValue('id_discount_type')));
			Configuration::updateValue('BIRTHDAY_DISCOUNT_VALUE', (float)(Tools::getValue('discount_value')));
			Configuration::updateValue('BIRTHDAY_MINIMAL_ORDER', (float)(Tools::getValue('minimal_order')));
			Tools::redirectAdmin($currentIndex.'&configure=birthdaypresent&token='.Tools::getValue('token').'&conf=4');
		}
		
		$this->_html = '
		<fieldset class="width3"><legend><img src="../modules/'.$this->name.'/logo.gif" /> '.$this->displayName.'</legend>
			<p>'.$this->l('Create a voucher for customers celebrating their birthday and having at least one valid order').'</p>
			<form action="'.$_SERVER['REQUEST_URI'].'" method="post" style="margin-top: 15px;">
				<label>'.$this->l('Active').'</label>
				<div class="margin-form">
					<img src="../img/admin/enabled.gif" /> <input type="radio" name="bp_active" value="1"'.(Configuration::get('BIRTHDAY_ACTIVE') ? ' checked="checked"' : '').' />
					<img src="../img/admin/disabled.gif" /> <input type="radio" name="bp_active" value="0"'.(!Configuration::get('BIRTHDAY_ACTIVE') ? ' checked="checked"' : '').' />
					<p style="clear: both;">'.$this->l('Additionally, you have to set a CRON rule which calls the file').'<br />'.Tools::getProtocol().$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'modules/birthdaypresent/cron.php '.$this->l('every day').'</p>
				</div>
				<label>'.$this->l('Type').'</label>
				<div class="margin-form">
					<select name="id_discount_type">';
		$discountTypes = Discount::getDiscountTypes((int)($cookie->id_lang));
		foreach ($discountTypes AS $discountType)
			$this->_html .= '<option value="'.(int)($discountType['id_discount_type']).'"'.((Configuration::get('BIRTHDAY_DISCOUNT_TYPE') == $discountType['id_discount_type']) ? ' selected="selected"' : '').'>'.$discountType['name'].'</option>';
		$this->_html .= '
					</select>
				</div>
				<label>'.$this->l('Value').'</label>
				<div class="margin-form">
					<input type="text" size="15" name="discount_value" value="'.Configuration::get('BIRTHDAY_DISCOUNT_VALUE').'" onKeyUp="javascript:this.value = this.value.replace(/,/g, \'.\'); " />
					<p style="clear: both;">'.$this->l('Either the monetary amount or the %, depending on Type selected above').'</p>
				</div>
				<label>'.$this->l('Minimum order').'</label>
				<div class="margin-form">
					<input type="text" size="15" name="minimal_order" value="'.Configuration::get('BIRTHDAY_MINIMAL_ORDER').'" onKeyUp="javascript:this.value = this.value.replace(/,/g, \'.\'); " />
					<p style="clear: both;">'.$this->l('The minimum order amount needed to use the voucher').'</p>
				</div>
				<div class="clear center">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitBirthday" class="button" />
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
			</form>
		</fieldset><br />
		<fieldset class="width3"><legend><img src="../modules/'.$this->name.'/comment.gif" /> '.$this->l('Guide').'</legend>
			<h2>'.$this->l('Develop clients\' loyalty').'</h2>
			<p>'.$this->l('Offering a present to a client is a means of securing their loyalty.').'</p>
			<h3>'.$this->l('What should you do?').'</h3>
			<p>
				'.$this->l('Keeping a client is more profitable than capturing a new one. Thus, it is necessary to develop their loyalty, in other words to make them want to come back to your webshop.').' <br />
				'.$this->l('Word of mouth is also a means to get new satisfied clients; a dissatisfied one won\'t attract new clients.').'<br />
				'.$this->l('In order to achieve this goal you can organize: ').'
				<ul>
					<li>'.$this->l('Punctual operations: commercial rewards (personalized special offers, product or service offered), non commercial rewards (priority handling of an order or a product), pecuniary rewards (bonds, discount coupons, payback...).').'</li>
					<li>'.$this->l('Sustainable operations: loyalty or points cards, which not only justify communication between merchant and client, but also offer advantages to clients (private offers, discounts).').'</li>
				</ul>
				'.$this->l('These operations encourage clients to buy and also to come back to your webshop regularly.').' <br />
			</p>
		</fieldset>';
		return $this->_html;
	}
	
	public function createTodaysVouchers()
	{
		$users = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT DISTINCT c.id_customer, firstname, lastname, email
		FROM '._DB_PREFIX_.'customer c
		LEFT JOIN '._DB_PREFIX_.'orders o ON (c.id_customer = o.id_customer)
		WHERE o.valid = 1
		AND c.birthday LIKE \'%'.date('-m-d').'\'');

		foreach ($users as $user)
		{
			$voucher = new Discount();
			$voucher->id_customer = (int)($user['id_customer']);
			$voucher->id_discount_type = (int)(Configuration::get('BIRTHDAY_DISCOUNT_TYPE'));
			$voucher->name = 'BIRTHDAY-'.(int)($voucher->id_customer).'-'.date('Y');
			$voucher->description[(int)(Configuration::get('PS_LANG_DEFAULT'))] = $this->l('Your birthday present !');
			$voucher->value = Configuration::get('BIRTHDAY_DISCOUNT_VALUE');
			$voucher->quantity = 1;
			$voucher->quantity_per_user = 1;
			$voucher->cumulable = 1;
			$voucher->cumulable_reduction = 1;
			$voucher->date_from = date('Y-m-d');
			$voucher->date_to = strftime('%Y-%m-%d', strtotime('+1 month'));
			$voucher->minimal = Configuration::get('BIRTHDAY_MINIMAL_ORDER');
			$voucher->active = true;
			if ($voucher->add())
				Mail::Send((int)(Configuration::get('PS_LANG_DEFAULT')), 'birthday', Mail::l('Happy birthday!'), array('{firstname}' => $user['firstname'], '{lastname}' => $user['lastname']), $user['email'], NULL, strval(Configuration::get('PS_SHOP_EMAIL')), strval(Configuration::get('PS_SHOP_NAME')), NULL, NULL, dirname(__FILE__).'/mails/');
			else
				echo Db::getInstance()->getMsgError();
		}
	}
}

