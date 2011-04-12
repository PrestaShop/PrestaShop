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

class LiveZilla extends Module
{
	public function __construct()
	{
		$this->name = 'livezilla';
		$this->tab = 'front_office_features';
		$this->version = 1.0;
		$this->author = 'PrestaShop';

		parent::__construct();
		
		$this->displayName = 'LiveZilla';
		$this->description = $this->l('Live support chat');
	}

	public function install()
	{
		if (!Configuration::get('LIVEZILLA_URL'))
			Configuration::updateValue('LIVEZILLA_URL', Tools::htmlentitiesUTF8($_SERVER['HTTP_HOST']).'/LiveZilla/');
		return (parent::install() AND $this->registerHook('rightColumn'));
	}

	public function getContent()
	{
		if (Tools::isSubmit('submitLiveZilla'))
		{
			Configuration::updateValue('LIVEZILLA_URL', Tools::getValue('LIVEZILLA_URL_TYPE').Tools::getValue('LIVEZILLA_URL'));
			Configuration::updateValue('LIVEZILLA_SCRIPT', Tools::getValue('LIVEZILLA_SCRIPT'), true);
			echo $this->displayConfirmation($this->l('Settings updated'));
		}
	
		$html = '<h2>'.$this->displayName.'</h2>
		<fieldset><legend><img src="../modules/'.$this->name.'/logo.gif" /> '.$this->l('How-to').'</legend>
			<img src="../modules/'.$this->name.'/lz_package.gif" style="float:right;margin-left:10px" />
			'.$this->l('LiveZilla is not a hosted solution, which means that LiveZilla needs to be installed on your local computer (step 1) and on your webserver (step 2) as well.').'
			'.$this->l('The LiveZilla installation on your webserver is called the LiveZilla Server.').'
			<br /><br />
			'.$this->l('Once you have finished step 1 & 2, you must fill in the URL of your LiveZilla installation below or directly copy / paste the script in the text area. This will integrates LiveZilla with your website (step 3).').'
			<br /><br />
			'.$this->l('The full installation guide is available on').' <a href="http://www.livezilla.net/installation/" style="text-decoration:underline">'.$this->l('the official LiveZilla website').'</a>.
			<br /><br />
			<a href="https://www.livezilla.net/downloads/" style="font-weight:700"><img src="../modules/'.$this->name.'/lz_download.gif" style="vertical-align:middle" /> '.$this->l('Download LiveZilla now!').'</a>
		</fieldset>
		<div class="clear">&nbsp;</div>
		<form action="'.Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset><legend><img src="../modules/'.$this->name.'/logo.gif" /> '.$this->l('Configuration').'</legend>
				<label>'.$this->l('Enter the URL to your LiveZilla installation').'</label>
				<div class="margin-form">
					<select name="LIVEZILLA_URL_TYPE">
						<option '.(Tools::getValue('LIVEZILLA_URL_TYPE') == 'http://' ? ' selected="selected" ' : '' ).' value="http://">http://</option>
						<option '.(Tools::getValue('LIVEZILLA_URL_TYPE') == 'https://' ? ' selected="selected" ' : '' ).' value="https://">https://</option>
					</select>
					<input type="text" name="LIVEZILLA_URL" style="width:300px" value="'.Tools::htmlentitiesUTF8(Tools::getValue('LIVEZILLA_URL', Configuration::get('LIVEZILLA_URL'))).'" />
					<p>'.$this->l('Absolute URL with the trailing slash, e.g.,').' '.Tools::getProtocol().Tools::htmlentitiesUTF8($_SERVER['HTTP_HOST']).'/LiveZilla/</p>
				</div>
				<div class="clear">&nbsp;</div>
				<div style="font-size:1.2em;font-weight:700;text-align:center">'.$this->l('-- OR --').'</div>
				<div class="clear">&nbsp;</div>
				<label>'.$this->l('Copy / paste the script given by LiveZilla').'</label>
				<div class="margin-form">
					<textarea name="LIVEZILLA_SCRIPT" style="width:600px;height:200px" />'.Tools::htmlentitiesUTF8(Tools::getValue('LIVEZILLA_SCRIPT', Configuration::get('LIVEZILLA_SCRIPT'))).'</textarea>
				</div>
				<div class="clear">&nbsp;</div>
				<input type="submit" name="submitLiveZilla" value="'.$this->l('Update settings').'" class="button" />
			</fieldset>
		</form>';
		return $html;
	}

	private function displayBlock()
	{
		global $smarty;
		
		
		if ($livezilla_script = Configuration::get('LIVEZILLA_SCRIPT'))
			$smarty->assign('LIVEZILLA_SCRIPT', $livezilla_script);
		elseif ($livezilla_url = Configuration::get('LIVEZILLA_URL'))
			$smarty->assign('LIVEZILLA_URL', $livezilla_url);
		else
			$smarty->assign('LIVEZILLA_UNDEFINED', 1);
		return $this->display(__FILE__, 'livezilla.tpl');
	}
	
	public function hookLeftColumn($params)
	{
		return $this->displayBlock();
	}
	
	public function hookRightColumn($params)
	{
		return $this->displayBlock();
	}
}