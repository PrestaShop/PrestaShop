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
	
class Blockcontact extends Module
{
	public function __construct()
	{
		$this->name = 'blockcontact';
		$this->tab = 'front_office_features';
		$this->version = '1.0';

		parent::__construct();

		$this->displayName = $this->l('Contact Block');
		$this->description = $this->l('Allows you to add additional information about your store\'s customer service.');
	}
	
	public function install()
	{
		return parent::install()
			&& Configuration::updateValue('blockcontact_telnumber', '')
			&& Configuration::updateValue('blockcontact_email', '')
			&& $this->registerHook('displayRightColumn')
			&& $this->registerHook('displayHeader');
	}
	
	public function uninstall()
	{
		// Delete configuration
		return Configuration::deleteByName('blockcontact_telnumber') && Configuration::deleteByName('blockcontact_email') && parent::uninstall();
	}
	
	public function getContent()
	{
		$html = '';
		// If we try to update the settings
		if (Tools::isSubmit('submitModule'))
		{				
			Configuration::updateValue('blockcontact_telnumber', Tools::getValue('telnumber'));
			Configuration::updateValue('blockcontact_email', Tools::getValue('email'));
			$this->_clearCache('blockcontact.tpl');
			$html .= '<div class="conf confirm">'.$this->l('Configuration updated').'</div>';
		}

		$html .= '
		<h2>'.$this->displayName.'</h2>
		<form action="'.Tools::htmlentitiesutf8($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset>			
				<label for="telnumber">'.$this->l('Telephone number:').'</label>
				<input type="text" id="telnumber" name="telnumber" value="'.((Configuration::get('blockcontact_telnumber') != '') ? Tools::safeOutput(Configuration::get('blockcontact_telnumber')) : '').'" />
				<div class="clear">&nbsp;</div>
				<label for="email">'.$this->l('Email').'</label>
				<input type="text" id="email" name="email" value="'.((Configuration::get('blockcontact_email') != '') ? Tools::safeOutput(Configuration::get('blockcontact_email')) : '').'" />
				<div class="clear">&nbsp;</div>
				<div class="margin-form">
					<input type="submit" name="submitModule" value="'.$this->l('Update settings').'" class="button" /></center>
				</div>
			</fieldset>
		</form>';

		return $html;
	}

	public function hookDisplayHeader()
	{
		$this->context->controller->addCSS(($this->_path).'blockcontact.css', 'all');
	}
	
	public function hookDisplayRightColumn()
	{
		global $smarty;
		if (!$this->isCached('blockcontact.tpl', $this->getCacheId()))
			$smarty->assign(array(
				'telnumber' => Configuration::get('blockcontact_telnumber'),
				'email' => Configuration::get('blockcontact_email')
			));
		return $this->display(__FILE__, 'blockcontact.tpl', $this->getCacheId());
	}
	
	public function hookDisplayLeftColumn()
	{
		return $this->hookDisplayRightColumn();
	}
}
?>
