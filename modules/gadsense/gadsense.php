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

class GAdsense extends Module
{	
	function __construct()
	{
	 	$this->name = 'gadsense';
	 	$this->tab = 'advertising_marketing';
	 	$this->version = '1.1';
		$this->author = 'PrestaShop';
        $this->displayName = $this->l('Google Adsense');
		
	 	parent::__construct();
		
		if ($this->id AND !Configuration::get('GADSENSE_ID'))
			$this->warning = $this->l('You have not yet set your Google Adsense code');
    $this->description = $this->l('Integrate Google Adsense script into your shop');
		$this->confirmUninstall = $this->l('Are you sure you want to delete your details ?');
	}
	
    function install()
    {
        if (!parent::install() OR !$this->registerHook('home'))
			return false;
		return true;
    }
	
	function uninstall()
	{
		if (!Configuration::deleteByName('GADSENSE_ID') OR !parent::uninstall())
			return false;
		return true;
	}
	
	public function getContent()
	{
		$output = '<h2>'.$this->displayName.'</h2>';
		if (Tools::isSubmit('submitGAdsense') AND ($gai = Tools::getValue('gadsense_id')))
		{
			$gai = htmlentities($gai, ENT_COMPAT, 'UTF-8');
			Configuration::updateValue('GADSENSE_ID', $gai);
			$output .= '
			<div class="conf confirm">
				<img src="../img/admin/ok.gif" alt="" title="" />
				'.$this->l('Settings updated').'
			</div>';
		}
		return $output.$this->displayForm();
	}

	public function displayForm()
	{
		$output = '
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
			<fieldset><legend>'.$this->l('Settings').'</legend>
				<label>'.$this->l('Your code').'</label>
				<div class="margin-form">
					<textarea name="gadsense_id" cols="90" rows="10" />'.Tools::getValue('gadsense_id', Configuration::get('GADSENSE_ID')).'</textarea>
					<p class="clear">'.$this->l('Example:').' <br /><br /><img src="../modules/gadsense/adsense_script.gif"></p>
				</div>
				<center><input type="submit" name="submitGAdsense" value="'.$this->l('Update settings').'" class="button" /></center>			
			</fieldset>
		</form>';
		return $output;
	}

	function hookLeftColumn($params)
	{
		return $this->hookHome($params);
	}

	function hookRightColumn($params)
	{
		return $this->hookHome($params);
	}

	function hookTop($params)
	{
		return $this->hookHome($params);
	}

	function hookHome($params)
	{
		$output = html_entity_decode(Configuration::get('GADSENSE_ID'), ENT_COMPAT, 'UTF-8');
		return $output;
	}

}
