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

if (!defined('_PS_VERSION_'))
	exit;

class BlockBanner extends Module
{
	public function __construct()
	{
		$this->name = 'blockbanner';
		$this->tab = 'other';
		$this->version = 1.0;
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();	

		$this->displayName = $this->l('Banner block');
		$this->description = $this->l('Displays banner at the top of the store.');
	}

	public function install()
	{
		Configuration::updateValue('BLOCKBANNER_IMG', 'store.jpg');
		Configuration::updateValue('BLOCKBANNER_LINK', '');
		Configuration::updateValue('BLOCKBANNER_DESC', '');
		return parent::install() && $this->registerHook('displayTop') && $this->registerHook('header');
	}

	public function uninstall()
	{
		Configuration::deleteByName('BLOCKBANNER_IMG');
		Configuration::deleteByName('BLOCKBANNER_LINK');
		Configuration::deleteByName('BLOCKBANNER_DESC');
		return parent::uninstall();
	}

	public function hookDisplayTop($params)
	{
		if (!$this->isCached('blockbanner.tpl', $this->getCacheId()))
		{
			if (file_exists(_PS_MODULE_DIR_.'blockbanner'.DIRECTORY_SEPARATOR.Configuration::get('BLOCKBANNER_IMG')))
				$this->smarty->assign('banner_img', Configuration::get('BLOCKBANNER_IMG'));
			$this->smarty->assign('banner_link', Configuration::get('BLOCKBANNER_LINK'));
			$this->smarty->assign('banner_desc', Configuration::get('BLOCKBANNER_DESC'));
			$sql = 'SELECT COUNT(*)
					FROM '._DB_PREFIX_.'store s'
					.Shop::addSqlAssociation('store', 's');
			$total = Db::getInstance()->getValue($sql);
			
			if ($total <= 0)
				return;
		}
		return $this->display(__FILE__, 'blockbanner.tpl', $this->getCacheId());
	}

	public function hookHeader($params)
	{
		$this->context->controller->addCSS($this->_path.'blockbanner.css', 'all');
	}

	public function postProcess()
	{
		if (Tools::isSubmit('submitStoreConf'))
		{
			if (isset($_FILES['BLOCKBANNER_IMG']) && isset($_FILES['BLOCKBANNER_IMG']['tmp_name']) && !empty($_FILES['BLOCKBANNER_IMG']['tmp_name']))
			{
				if ($error = ImageManager::validateUpload($_FILES['BLOCKBANNER_IMG'], 4000000))
					return $this->displayError($this->l('Invalid image'));
				else
				{
					$ext = substr($_FILES['BLOCKBANNER_IMG']['name'], strrpos($_FILES['BLOCKBANNER_IMG']['name'], '.') + 1);
					$file_name = md5($_FILES['BLOCKBANNER_IMG']['name']).'.'.$ext;
					if (!move_uploaded_file($_FILES['BLOCKBANNER_IMG']['tmp_name'], dirname(__FILE__).'/'.$file_name))
						return $this->displayError($this->l('An error occurred while attempting to upload the file.'));
					else
					{
						if (Configuration::hasContext('BLOCKBANNER_IMG', null, Shop::getContext()) && Configuration::get('BLOCKBANNER_IMG') != $file_name)
							@unlink(dirname(__FILE__).'/'.Configuration::get('BLOCKBANNER_IMG'));
						Configuration::updateValue('BLOCKBANNER_IMG', $file_name);
						$this->_clearCache('blockbanner.tpl');
						return $this->displayConfirmation($this->l('The settings have been updated.'));
					}
				}
			}
			Configuration::updateValue('BLOCKBANNER_LINK', Tools::getValue('BLOCKBANNER_LINK'));
			Configuration::updateValue('BLOCKBANNER_DESC', Tools::getValue('BLOCKBANNER_DESC'));
		}
		return '';
	}

	public function getContent()
	{
		return $this->postProcess().$this->renderForm();
		return $output;
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
						'type' => 'file',
						'label' => $this->l('Block image'),
						'name' => 'BLOCKBANNER_IMG',
						'desc' => $this->l('Please upload banner image'),
						'thumb' => '../modules/'.$this->name.'/'.Configuration::get('BLOCKBANNER_IMG'),
					),
					array(
						'type' => 'text',
						'label' => $this->l('Image Link'),
						'name' => 'BLOCKBANNER_LINK',
						'desc' => $this->l('Please input banner link')
					),			
					array(
						'type' => 'text',
						'label' => $this->l('Image description'),
						'name' => 'BLOCKBANNER_DESC',
						'desc' => $this->l('Please input banner image description')
					)
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
		$helper->submit_action = 'submitStoreConf';
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
			'BLOCKBANNER_IMG' => Tools::getValue('BLOCKBANNER_IMG', Configuration::get('BLOCKBANNER_IMG')),
			'BLOCKBANNER_LINK' => Tools::getValue('BLOCKBANNER_LINK', Configuration::get('BLOCKBANNER_LINK')),
			'BLOCKBANNER_DESC' => Tools::getValue('BLOCKBANNER_DESC', Configuration::get('BLOCKBANNER_DESC')),
		);
	}
}