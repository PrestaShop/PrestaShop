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
		if (Tools::isSubmit('submitModule'))
		{	
			Configuration::updateValue('blocksocial_facebook', (($_POST['blocksocial_facebook'] != '') ? $_POST['blocksocial_facebook']: ''));
			Configuration::updateValue('blocksocial_twitter', (($_POST['blocksocial_twitter'] != '') ? $_POST['blocksocial_twitter']: ''));		
			Configuration::updateValue('blocksocial_rss', (($_POST['blocksocial_rss'] != '') ? $_POST['blocksocial_rss']: ''));				
			$this->_clearCache('blocksocial.tpl');
			$output .= $this->displayConfirmation($this->l('Configuration updated'));
		}
		
		return $output.$this->renderForm();
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
	
	public function renderForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('Facebook URL:'),
						'name' => 'blocksocial_facebook',
						'desc' => $this->l('Create a title for the block (default: \'RSS feed\')'),
					),
					array(
						'type' => 'text',
						'label' => $this->l('Twitter URL:'),
						'name' => 'blocksocial_twitter',
						'desc' => $this->l('Add the URL of the feed you want to use (sample: http://news.google.com/?output=rss)'),
					),
					array(
						'type' => 'text',
						'label' => $this->l('RSS URL:'),
						'name' => 'blocksocial_rss',
						'desc' => $this->l('Number of threads displayed by the block (default value: 5)'),
					),
				),
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'btn btn-default')
			),
		);
		
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table =  $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitModule';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}
	
	public function getConfigFieldsValues()
	{
		return array(
			'blocksocial_facebook' => Tools::getValue('blocksocial_facebook', Configuration::get('blocksocial_facebook')),
			'blocksocial_twitter' => Tools::getValue('blocksocial_twitter', Configuration::get('blocksocial_twitter')),
			'blocksocial_rss' => Tools::getValue('blocksocial_rss', Configuration::get('blocksocial_rss')),
		);
	}

}