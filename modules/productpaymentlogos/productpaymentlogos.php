<?php
/*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class ProductPaymentLogos extends Module
{
	public function __construct()
	{
		$this->name = 'productpaymentlogos';
		$this->tab = 'front_office_features';
		$this->version = 1.0;
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();	

		$this->displayName = $this->l('Product payment logos block');
		$this->description = $this->l('Displays payment system logos at the product page.');
	}

	public function install()
	{
		Configuration::updateValue('PRODUCTPAYMENTLOGOS_IMG', 'payment-logo.png');
		Configuration::updateValue('PRODUCTPAYMENTLOGOS_LINK', '');
		Configuration::updateValue('PRODUCTPAYMENTLOGOS_TITLE', '');
		return parent::install() && $this->registerHook('displayProductButtons') && $this->registerHook('header');
	}

	public function uninstall()
	{
		Configuration::deleteByName('PRODUCTPAYMENTLOGOS_IMG');
		Configuration::deleteByName('PRODUCTPAYMENTLOGOS_LINK');
		Configuration::deleteByName('PRODUCTPAYMENTLOGOS_TITLE');
		return parent::uninstall();
	}

	public function hookDisplayProductButtons($params)
	{
		if (!$this->isCached('productpaymentlogos.tpl', $this->getCacheId()))
		{
			$this->smarty->assign('banner_img', Configuration::get('PRODUCTPAYMENTLOGOS_IMG'));
			$this->smarty->assign('banner_link', Configuration::get('PRODUCTPAYMENTLOGOS_LINK'));
			$this->smarty->assign('banner_title', Configuration::get('PRODUCTPAYMENTLOGOS_TITLE'));
			$sql = 'SELECT COUNT(*)
					FROM '._DB_PREFIX_.'store s'
					.Shop::addSqlAssociation('store', 's');
			$total = Db::getInstance()->getValue($sql);
			
				if ($total <= 0)
				return;
		}
		return $this->display(__FILE__, 'productpaymentlogos.tpl', $this->getCacheId());
	}

	public function hookHeader($params)
	{
		$this->context->controller->addCSS($this->_path.'productpaymentlogos.css', 'all');
	}

	public function postProcess()
	{
		if (Tools::isSubmit('submitStoreConf'))
		{
			Configuration::updateValue('PRODUCTPAYMENTLOGOS_LINK', Tools::getValue('PRODUCTPAYMENTLOGOS_LINK'));
			Configuration::updateValue('PRODUCTPAYMENTLOGOS_TITLE', Tools::getValue('PRODUCTPAYMENTLOGOS_TITLE'));
			if (isset($_FILES['PRODUCTPAYMENTLOGOS_IMG']) && isset($_FILES['PRODUCTPAYMENTLOGOS_IMG']['tmp_name']) && !empty($_FILES['PRODUCTPAYMENTLOGOS_IMG']['tmp_name']))
			{
				if ($error = ImageManager::validateUpload($_FILES['PRODUCTPAYMENTLOGOS_IMG'], 4000000))
					return $this->displayError($this->l('Invalid image'));
				else
				{
					$ext = substr($_FILES['PRODUCTPAYMENTLOGOS_IMG']['name'], strrpos($_FILES['PRODUCTPAYMENTLOGOS_IMG']['name'], '.') + 1);
					$file_name = md5($_FILES['PRODUCTPAYMENTLOGOS_IMG']['name']).'.'.$ext;
					if (!move_uploaded_file($_FILES['PRODUCTPAYMENTLOGOS_IMG']['tmp_name'], dirname(__FILE__).'/'.$file_name))
						return $this->displayError($this->l('An error occurred while attempting to upload the file.'));
					else
					{
						if (Configuration::hasContext('PRODUCTPAYMENTLOGOS_IMG', null, Shop::getContext()) && Configuration::get('PRODUCTPAYMENTLOGOS_IMG') != $file_name)
							@unlink(dirname(__FILE__).'/'.Configuration::get('PRODUCTPAYMENTLOGOS_IMG'));
						Configuration::updateValue('PRODUCTPAYMENTLOGOS_IMG', $file_name);
						$this->_clearCache('productpaymentlogos.tpl');
						return $this->displayConfirmation($this->l('The settings have been updated.'));
					}
				}
			}
			$this->_clearCache('productpaymentlogos.tpl');
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
						'type' => 'text',
						'label' => $this->l('Logos heading'),
						'name' => 'PRODUCTPAYMENTLOGOS_TITLE',
						'desc' => $this->l('Please input logos heading')
					),
					array(
						'type' => 'file',
						'label' => $this->l('Block image'),
						'name' => 'PRODUCTPAYMENTLOGOS_IMG',
						'desc' => $this->l('Please upload banner image'),
						'thumb' => '../modules/'.$this->name.'/'.Configuration::get('PRODUCTPAYMENTLOGOS_IMG'),
					),
					array(
						'type' => 'text',
						'label' => $this->l('Image Link'),
						'name' => 'PRODUCTPAYMENTLOGOS_LINK',
						'desc' => $this->l('Please input banner link')
					)
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)
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
			'PRODUCTPAYMENTLOGOS_IMG' => Tools::getValue('PRODUCTPAYMENTLOGOS_IMG', Configuration::get('PRODUCTPAYMENTLOGOS_IMG')),
			'PRODUCTPAYMENTLOGOS_LINK' => Tools::getValue('PRODUCTPAYMENTLOGOS_LINK', Configuration::get('PRODUCTPAYMENTLOGOS_LINK')),
			'PRODUCTPAYMENTLOGOS_TITLE' => Tools::getValue('PRODUCTPAYMENTLOGOS_TITLE', Configuration::get('PRODUCTPAYMENTLOGOS_TITLE')),
		);
	}
}