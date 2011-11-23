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
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_CAN_LOAD_FILES_'))
	exit;
	
class blockcontact extends Module
{
	public function __construct()
	{
		$this->name = 'blockcontact';
		$this->tab = 'front_office_features';
		$this->version = '1.0';

		parent::__construct();

		$this->displayName = $this->l('Block contact');
		$this->description = $this->l('Allows you to add extra information about customer service');
	}
	
	public function install()
	{
		return (parent::install() AND Configuration::updateValue('blockcontact_telnumber', '') AND Configuration::updateValue('blockcontact_email', '') AND $this->registerHook('rightColumn'));
	}
	
	public function uninstall()
	{
		//Delete configuration			
		return (Configuration::deleteByName('blockcontact_telnumber') AND Configuration::deleteByName('blockcontact_email') AND parent::uninstall());
	}
	
	public function getContent()
	{
		// If we try to update the settings
		if (isset($_POST['submitModule']))
		{				
			Configuration::updateValue('blockcontact_telnumber', (preg_match('/^[0-9]+/', $_POST['telnumber']) ? $_POST['telnumber']: ''));
			Configuration::updateValue('blockcontact_email', (($_POST['email'] != '') ? $_POST['email']: ''));
			echo '<div class="conf confirm"><img src="../img/admin/ok.gif"/>'.$this->l('Configuration updated').'</div>';
		}
		
		return '
		<h2>'.$this->displayName.'</h2>
		<form action="'.Tools::htmlentitiesutf8($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset>			
				<label for="telnumber">'.$this->l('Telephone number : ').'</label>
				<input type="text" id="telnumber" name="telnumber" value="'.((Configuration::get('blockcontact_telnumber') != "") ? Configuration::get('blockcontact_telnumber') : "").'" />
				<div class="clear">&nbsp;</div>
				<label for="email">'.$this->l('Email : ').'</label>
				<input type="text" id="email" name="email" value="'.((Configuration::get('blockcontact_email') != "") ? Configuration::get('blockcontact_email') : "").'" />
				<div class="clear">&nbsp;</div>
				<div class="margin-form">
					<input type="submit" name="submitModule" value="'.$this->l('Update settings').'" class="button" /></center>
				</div>
			</fieldset>
		</form>
		';
	}
	
	public function hookRightColumn()
	{
		global $smarty;

		$smarty->assign(array(
			'telnumber' => Configuration::get('blockcontact_telnumber'),
			'email' => Configuration::get('blockcontact_email')
		));
		return $this->display(__FILE__, 'blockcontact.tpl');
	}
}
?>
