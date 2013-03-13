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
	
class blocksocial extends Module
{
	public function __construct()
	{
		$this->name = 'blocksocial';
		$this->tab = 'front_office_features';
		$this->version = '1.0';

		parent::__construct();

		$this->displayName = $this->l('Social networking block');
		$this->description = $this->l('Allows you to add information about your brand\'s social networking sites.');
	}
	
	public function install()
	{
		return (parent::install() AND Configuration::updateValue('blocksocial_facebook', '') && Configuration::updateValue('blocksocial_twitter', '') && Configuration::updateValue('blocksocial_rss', '') && $this->registerHook('displayHeader') && $this->registerHook('displayFooter'));
	}
	
	public function uninstall()
	{
		//Delete configuration			
		return (Configuration::deleteByName('blocksocial_facebook') AND Configuration::deleteByName('blocksocial_twitter') AND Configuration::deleteByName('blocksocial_rss') AND parent::uninstall());
	}
	
	public function getContent()
	{
		// If we try to update the settings
		$output = '';
		if (isset($_POST['submitModule']))
		{	
			Configuration::updateValue('blocksocial_facebook', (($_POST['facebook_url'] != '') ? $_POST['facebook_url']: ''));
			Configuration::updateValue('blocksocial_twitter', (($_POST['twitter_url'] != '') ? $_POST['twitter_url']: ''));		
			Configuration::updateValue('blocksocial_rss', (($_POST['rss_url'] != '') ? $_POST['rss_url']: ''));				
			$this->_clearCache('blocksocial.tpl');
			$output = '<div class="conf confirm">'.$this->l('Configuration updated').'</div>';
		}
		
		return '
		<h2>'.$this->displayName.'</h2>
		'.$output.'
		<form action="'.Tools::htmlentitiesutf8($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset class="width2">				
				<label for="facebook_url">'.$this->l('Facebook URL: ').'</label>
				<input type="text" id="facebook_url" name="facebook_url" value="'.Tools::safeOutput((Configuration::get('blocksocial_facebook') != "") ? Configuration::get('blocksocial_facebook') : "").'" />
				<div class="clear">&nbsp;</div>		
				<label for="twitter_url">'.$this->l('Twitter URL: ').'</label>
				<input type="text" id="twitter_url" name="twitter_url" value="'.Tools::safeOutput((Configuration::get('blocksocial_twitter') != "") ? Configuration::get('blocksocial_twitter') : "").'" />
				<div class="clear">&nbsp;</div>		
				<label for="rss_url">'.$this->l('RSS URL: ').'</label>
				<input type="text" id="rss_url" name="rss_url" value="'.Tools::safeOutput((Configuration::get('blocksocial_rss') != "") ? Configuration::get('blocksocial_rss') : "").'" />
				<div class="clear">&nbsp;</div>						
				<br /><center><input type="submit" name="submitModule" value="'.$this->l('Update settings').'" class="button" /></center>
			</fieldset>
		</form>';
	}
	
	public function hookDisplayHeader()
	{
		$this->context->controller->addCSS(($this->_path).'blocksocial.css', 'all');
	}
		
	public function hookDisplayFooter()
	{
		if (!$this->isCached('blocksocial.tpl', $this->getCacheId()))
			$this->smarty->assign(array(
				'facebook_url' => Configuration::get('blocksocial_facebook'),
				'twitter_url' => Configuration::get('blocksocial_twitter'),
				'rss_url' => Configuration::get('blocksocial_rss')
			));

		return $this->display(__FILE__, 'blocksocial.tpl', $this->getCacheId());
	}
}
?>
