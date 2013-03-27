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

if (!defined('_CAN_LOAD_FILES_'))
	exit;
	
class Blockcontactinfos extends Module
{
	public function __construct()
	{
		$this->name = 'blockcontactinfos';
		if (version_compare(_PS_VERSION_, '1.4.0.0') >= 0)
			$this->tab = 'front_office_features';
		else
			$this->tab = 'Blocks';
		$this->version = '1.0';

		parent::__construct();

		$this->displayName = $this->l('Block contact info');
		$this->description = $this->l('This module will allow you to display your e-store\'s contact information in a customizable block.');
	}
	
	public function install()
	{
		return (parent::install() 
				&& Configuration::updateValue('blockcontactinfos_company', Configuration::get('PS_SHOP_NAME'))
				&& Configuration::updateValue('blockcontactinfos_address', '') && Configuration::updateValue('blockcontactinfos_phone', '')
				&& Configuration::updateValue('blockcontactinfos_email', Configuration::get('PS_SHOP_EMAIL'))
				&& $this->registerHook('header') && $this->registerHook('footer'));
	}
	
	public function uninstall()
	{
		//Delete configuration			
		return (Configuration::deleteByName('blockcontactinfos_company') 
				&& Configuration::deleteByName('blockcontactinfos_address') && Configuration::deleteByName('blockcontactinfos_phone')
				&& Configuration::deleteByName('blockcontactinfos_email') && parent::uninstall());
	}
	
	public function getContent()
	{
		$html = '';
		// If we try to update the settings
		if (isset($_POST['submitModule']))
		{	
			Configuration::updateValue('blockcontactinfos_company', ((isset($_POST['company']) && $_POST['company'] != '') ? $_POST['company'] : Configuration::get('PS_SHOP_NAME')));
			Configuration::updateValue('blockcontactinfos_address', ((isset($_POST['address']) && $_POST['address'] != '') ? $_POST['address'] : ''));
			Configuration::updateValue('blockcontactinfos_phone', ((isset($_POST['phone']) && $_POST['phone'] != '') ? $_POST['phone'] : ''));
			Configuration::updateValue('blockcontactinfos_email', ((isset($_POST['email']) && $_POST['email'] != '') ? $_POST['email'] : Configuration::get('PS_SHOP_EMAIL')));
			$this->_clearCache('blockcontactinfos.tpl');
			$html .= '<div class="conf confirm">'.$this->l('Configuration updated').'</div>';
		}

		$html .= '
		<h2>'.$this->displayName.'</h2>
		<form action="'.Tools::htmlentitiesutf8($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset>	
				<p><label for="company">'.$this->l('Company name').' :</label>
				<input type="text" id="company" name="company" value="'.Tools::safeOutput(Configuration::get('blockcontactinfos_company')).'" /></p>
				<p><label for="address">'.$this->l('Address').' :</label>
				<textarea id="address" name="address" cols="60" rows="4">'.Tools::safeOutput(Configuration::get('blockcontactinfos_address')).'</textarea></p>
				<p><label for="phone">'.$this->l('Phone number').' :</label>
				<input type="text" id="phone" name="phone" value="'.Tools::safeOutput(Configuration::get('blockcontactinfos_phone')).'" /></p>
				<p><label for="email">'.$this->l('Email').' :</label>
				<input type="text" id="email" name="email" value="'.Tools::safeOutput(Configuration::get('blockcontactinfos_email')).'" />	</p>
				<div class="margin-form">
					<input type="submit" name="submitModule" value="'.$this->l('Update settings').'" class="button" /></center>
				</div>
			</fieldset>
		</form>
		';
		
		return $html;
	}
	
	public function hookHeader()
	{
		$this->context->controller->addCSS(($this->_path).'blockcontactinfos.css', 'all');
	}
	
	public function hookFooter($params)
	{	
		if (!$this->isCached('blockcontactinfos.tpl', $this->getCacheId()))
			$this->smarty->assign(array(
				'blockcontactinfos_company' => Configuration::get('blockcontactinfos_company'),
				'blockcontactinfos_address' => Configuration::get('blockcontactinfos_address'),
				'blockcontactinfos_phone' => Configuration::get('blockcontactinfos_phone'),
				'blockcontactinfos_email' => Configuration::get('blockcontactinfos_email')
			));
		return $this->display(__FILE__, 'blockcontactinfos.tpl', $this->getCacheId());
	}
}
?>
