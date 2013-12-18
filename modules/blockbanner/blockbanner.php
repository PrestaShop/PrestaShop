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
		$this->version = 1.1;
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();	

		$this->displayName = $this->l('Banner block');
		$this->description = $this->l('Displays banner at the top of the store.');
	}

	public function install()
	{
		$languages = Language::getLanguages(false);
		$values = array();

		foreach ($languages as $lang)
		{
			$values['BLOCKBANNER_IMG'][$lang['id_lang']] = 'sale70.gif';
			$values['BLOCKBANNER_LINK'][$lang['id_lang']] = '';
			$values['BLOCKBANNER_DESC'][$lang['id_lang']] = '';
		}

		Configuration::updateValue('BLOCKBANNER_IMG', $values['BLOCKBANNER_IMG']);
		Configuration::updateValue('BLOCKBANNER_LINK', $values['BLOCKBANNER_LINK']);
		Configuration::updateValue('BLOCKBANNER_DESC', $values['BLOCKBANNER_DESC']);

		return parent::install() && $this->registerHook('displayBanner') && $this->registerHook('displayHeader');
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
			if (file_exists(_PS_MODULE_DIR_.'blockbanner'.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR
				.Configuration::get('BLOCKBANNER_IMG', $this->context->language->id)))
				$this->smarty->assign('banner_img', 'img/'.Configuration::get('BLOCKBANNER_IMG', $this->context->language->id));

			$this->smarty->assign('banner_link', Configuration::get('BLOCKBANNER_LINK', $this->context->language->id));
			$this->smarty->assign('banner_desc', Configuration::get('BLOCKBANNER_DESC', $this->context->language->id));
			$sql = 'SELECT COUNT(*)
					FROM '._DB_PREFIX_.'store s'
					.Shop::addSqlAssociation('store', 's');
			$total = Db::getInstance()->getValue($sql);

			if ($total <= 0)
				return;
		}

		return $this->display(__FILE__, 'blockbanner.tpl', $this->getCacheId());
	}

	public function hookDisplayBanner($params)
	{
		return $this->hookDisplayTop($params);
	}

	public function hookDisplayFooter($params)
	{
		return $this->hookDisplayTop($params);
	}

	public function hookDisplayHeader($params)
	{
		$this->context->controller->addCSS($this->_path.'blockbanner.css', 'all');
	}

	public function postProcess()
	{
		if (Tools::isSubmit('submitStoreConf'))
		{
			$languages = Language::getLanguages(false);
			$values = array();
			$update_images_values = false;

			foreach ($languages as $lang)
			{
				if (isset($_FILES['BLOCKBANNER_IMG_'.$lang['id_lang']])
					&& isset($_FILES['BLOCKBANNER_IMG_'.$lang['id_lang']]['tmp_name'])
					&& !empty($_FILES['BLOCKBANNER_IMG_'.$lang['id_lang']]['tmp_name']))
				{
					if ($error = ImageManager::validateUpload($_FILES['BLOCKBANNER_IMG_'.$lang['id_lang']], 4000000))
						return $this->displayError($this->l('Invalid image'));
					else
					{
						$ext = substr($_FILES['BLOCKBANNER_IMG_'.$lang['id_lang']]['name'], strrpos($_FILES['BLOCKBANNER_IMG_'.$lang['id_lang']]['name'], '.') + 1);
						$file_name = md5($_FILES['BLOCKBANNER_IMG_'.$lang['id_lang']]['name']).'.'.$ext;

						if (!move_uploaded_file($_FILES['BLOCKBANNER_IMG_'.$lang['id_lang']]['tmp_name'], dirname(__FILE__).DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.$file_name))
							return $this->displayError($this->l('An error occurred while attempting to upload the file.'));
						else
						{
							if (Configuration::hasContext('BLOCKBANNER_IMG', $lang['id_lang'], Shop::getContext())
								&& Configuration::get('BLOCKBANNER_IMG', $lang['id_lang']) != $file_name)
								@unlink(dirname(__FILE__).DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.Configuration::get('BLOCKBANNER_IMG', $lang['id_lang']));

							$values['BLOCKBANNER_IMG'][$lang['id_lang']] = $file_name;
						}
					}

					$update_images_values = true;
				}

				$values['BLOCKBANNER_LINK'][$lang['id_lang']] = Tools::getValue('BLOCKBANNER_LINK_'.$lang['id_lang']);
				$values['BLOCKBANNER_DESC'][$lang['id_lang']] = Tools::getValue('BLOCKBANNER_DESC_'.$lang['id_lang']);
			}

			if ($update_images_values)
				Configuration::updateValue('BLOCKBANNER_IMG', $values['BLOCKBANNER_IMG']);

			Configuration::updateValue('BLOCKBANNER_LINK', $values['BLOCKBANNER_LINK']);
			Configuration::updateValue('BLOCKBANNER_DESC', $values['BLOCKBANNER_DESC']);

			$this->_clearCache('blockbanner.tpl');
			return $this->displayConfirmation($this->l('The settings have been updated.'));
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
						'type' => 'file_lang',
						'label' => $this->l('Block image'),
						'name' => 'BLOCKBANNER_IMG',
						'desc' => $this->l('Please upload banner image'),
						'lang' => true,
					),
					array(
						'type' => 'text',
						'lang' => true,
						'label' => $this->l('Image Link'),
						'name' => 'BLOCKBANNER_LINK',
						'desc' => $this->l('Please input banner link')
					),			
					array(
						'type' => 'text',
						'lang' => true,
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
		$helper->module = $this;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitStoreConf';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'uri' => $this->getPathUri(),
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}
	
	public function getConfigFieldsValues()
	{
		$languages = Language::getLanguages(false);
		$fields = array();

		foreach ($languages as $lang)
		{
			$fields['BLOCKBANNER_IMG'][$lang['id_lang']] = Tools::getValue('BLOCKBANNER_IMG_'.$lang['id_lang'], Configuration::get('BLOCKBANNER_IMG', $lang['id_lang']));
			$fields['BLOCKBANNER_LINK'][$lang['id_lang']] = Tools::getValue('BLOCKBANNER_LINK_'.$lang['id_lang'], Configuration::get('BLOCKBANNER_LINK', $lang['id_lang']));
			$fields['BLOCKBANNER_DESC'][$lang['id_lang']] = Tools::getValue('BLOCKBANNER_DESC_'.$lang['id_lang'], Configuration::get('BLOCKBANNER_DESC', $lang['id_lang']));
		}

		return $fields;
	}
}