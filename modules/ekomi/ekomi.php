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

class Ekomi extends Module
{
	private $_html = '';
	private $_postErrors = array();

    function __construct()
    {
        $this->name = 'ekomi';
        $this->tab = 'advertising_marketing';
        $this->version = 0.8;

		parent::__construct();

		$this->displayName = $this->l('eKomi');
		$this->description = $this->l('Adds a eKomi block');
	}

	public function install()
	{
		return (parent::install() AND $this->registerHook('rightColumn')  AND $this->registerHook('newOrder'));
	}

	public function getContent()
	{
		$output = '<h2>'.$this->displayName.'</h2>';
		if (Tools::isSubmit('submitEkomi'))
		{
			$email = Tools::getValue('ekomi_email');
			Configuration::updateValue('PS_EKOMI_DISPLAY', Tools::getValue('ekomi_display'));
			Configuration::updateValue('PS_EKOMI_SCRIPT', htmlentities(str_replace(array("\r\n", "\n"), '', Tools::getValue('ekomi_script'))));
			if (!empty($email) && !Validate::isEmail($email))
				Configuration::updateValue('PS_EKOMI_EMAIL', '');
			else
				Configuration::updateValue('PS_EKOMI_EMAIL', Tools::getValue('ekomi_email'));
			$output .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Settings updated').'</div>';
		}
		return $output.$this->displayForm();
	}

	public function displayForm()
	{
		return '
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
			<fieldset>
				<legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Settings').'</legend>
				<label>'.$this->l('eKomi configuration').'</label>
				<div class="margin-form">
					<br class="clear"/>
					<label for="ekomi_email">'.$this->l('eKomi e-mail').'&nbsp;&nbsp;</label><input id="ekomi_email" type="text" name="ekomi_email" value="'.Configuration::get('PS_EKOMI_EMAIL').'" />
					<br class="clear"/><br />
					<label for="ekomi_script">'.$this->l('eKomi script').'&nbsp;&nbsp;</label><textarea id="ekomi_script" name="ekomi_script">'.stripslashes(html_entity_decode(Configuration::get('PS_EKOMI_SCRIPT'))).'</textarea>
					<br class="clear"/><br />
					<label>Display block</label>
					<div class="margin-form">
						<input type="radio" name="ekomi_display" id="ekomi_display_on" value="1" />
						<label class="t" for="ekomi_display_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
						<input type="radio" name="ekomi_display" id="ekomi_display_off" value="0" checked="checked" />
						<label class="t" for="ekomi_display_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
						<p class="clear">'.$this->l('Show or don\'t show the block (orders will be sent to eKomi either you choose to hide or display the block).').'</p>
					</div>
					<p class="clear">'.$this->l('Please, fill the form with the datas that eKomi gives you.').'</p>
				</div>
				<center><input type="submit" name="submitEkomi" value="'.$this->l('Save').'" class="button" /></center>
			</fieldset>
		</form>';
	}

	public function hookRightColumn($params)
	{
		if (!Configuration::get('PS_EKOMI_SCRIPT'))
			return ;
		if (!Configuration::get('PS_EKOMI_DISPLAY'))
			return ;
		return stripslashes(html_entity_decode(Configuration::get('PS_EKOMI_SCRIPT'))).'<br clear="left" /><br />';
	}
		
	public function hookLeftColumn($params)
	{
		return $this->hookRightColumn($params);
	}
		
	public function hookNewOrder($params)
	{
		global $cookie;
		if (!Configuration::get('PS_EKOMI_EMAIL'))
			return false;

		/* Email generation */
		$subject = '[Ekomi-Prestashop] '.Configuration::get('PS_SHOP_NAME');
		$templateVars = array(
			'{firstname}' => $params['customer']->firstname,
			'{lastname}' => $params['customer']->lastname,
			'{email}' => $params['customer']->email,
			'{id_order}' => $params['order']->id
		);

		/* Email sending */
		if (!Mail::Send((int)($cookie->id_lang), 'ekomi', $subject, $templateVars, Configuration::get('PS_EKOMI_EMAIL'), NULL, $params['customer']->email, Configuration::get('PS_SHOP_NAME'), NULL, NULL, dirname(__FILE__).'/mails/'))
			return false;
		return true;
	}
}


