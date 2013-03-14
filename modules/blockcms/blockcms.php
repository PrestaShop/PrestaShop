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
 *  @version  Release: $Revision: 7060 $
 *  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_CAN_LOAD_FILES_'))
	exit;

include_once(dirname(__FILE__) . '/BlockCMSModel.php');

class BlockCms extends Module
{
	private $_html;
	private $_display;

	public function __construct()
	{
		$this->name = 'blockcms';
		$this->tab = 'front_office_features';
		$this->version = '1.1.1';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('CMS Block');
		$this->description = $this->l('Adds a block with several CMS links.');
		$this->secure_key = Tools::encrypt($this->name);
	}

	public function install()
	{
		if (!parent::install() ||
			!$this->registerHooks() ||
			!BlockCMSModel::createTables() ||
			!Configuration::updateValue('FOOTER_CMS', '') ||
			!Configuration::updateValue('FOOTER_BLOCK_ACTIVATION', 1) ||
			!Configuration::updateValue('FOOTER_POWEREDBY', 1))
		return false;

		// Install fixtures for blockcms
		$default = Db::getInstance()->insert('cms_block', array(
			'id_cms_category' =>	1,
			'location' =>			0,
			'position' =>			0,
		));

		if (!$default)
			return false;

		$result = true;
		$id_cms_block = Db::getInstance()->Insert_ID();
		$shops = Shop::getShops(true, null, true);

		foreach ($shops as $shop)
			$result &= Db::getInstance()->insert('cms_block_shop', array(
				'id_cms_block' =>	$id_cms_block,
				'id_shop' =>		$shop
			));

		$languages = Language::getLanguages(false);
		foreach ($languages  as $lang)
			$result &= Db::getInstance()->insert('cms_block_lang', array(
				'id_cms_block' =>	$id_cms_block,
				'id_lang' =>		$lang['id_lang'],
				'name' =>			$this->l('Information'),
			));

		$pages = CMS::getCMSPages(null, 1);
		foreach ($pages as $cms)
			$result &= Db::getInstance()->insert('cms_block_page', array(
				'id_cms_block' =>	$id_cms_block,
				'id_cms' =>			$cms['id_cms'],
				'is_category' =>	0,
			));

		return $result;
	}

	public function uninstall()
	{
		if (!parent::uninstall() ||
			!BlockCMSModel::DropTables() ||
			!Configuration::deleteByName('FOOTER_CMS') ||
			!Configuration::deleteByName('FOOTER_BLOCK_ACTIVATION') ||
			!Configuration::deleteByName('FOOTER_POWEREDBY'))
			return false;

		return true;
	}

	private function registerHooks()
	{
		return (
			$this->registerHook('leftColumn') && $this->registerHook('rightColumn') &&
			$this->registerHook('header') && $this->registerHook('footer') && $this->registerHook('actionShopDataDuplication'));
	}

	public function initToolbar()
	{
		$current_index = AdminController::$currentIndex;
		$token = Tools::getAdminTokenLite('AdminModules');

		$back = Tools::safeOutput(Tools::getValue('back', ''));

		if (!isset($back) || empty($back))
			$back = $current_index.'&amp;configure='.$this->name.'&token='.$token;

		switch ($this->_display)
		{
			case 'add':
				$this->toolbar_btn['cancel'] = array(
					'href' => $back,
					'desc' => $this->l('Cancel')
				);
				break;
			case 'edit':
				$this->toolbar_btn['cancel'] = array(
					'href' => $back,
					'desc' => $this->l('Cancel')
				);
				break;
			case 'index':
				$this->toolbar_btn['new'] = array(
					'href' => $current_index.'&amp;configure='.$this->name.'&amp;token='.$token.'&amp;addBlockCMS',
					'desc' => $this->l('Add new')
				);
				break;
			default:
				break;
		}

		$this->toolbar_btn['save'] = array(
			'href' => '#',
			'desc' => $this->l('Save')
		);

		return $this->toolbar_btn;
	}

	protected function displayForm()
	{
		$this->context->controller->addJqueryPlugin('tablednd');
		$this->context->controller->addJS(_PS_JS_DIR_.'admin-dnd.js');

		$this->_display = 'index';

		$this->fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('CMS block configuration'),
				'image' => _PS_ADMIN_IMG_.'information.png'
			),
			'input' => array(
				array(
					'type' => 'cms_blocks',
					'label' => $this->l('CMS Blocks:'),
					'name' => 'cms_blocks',
					'values' => array(
						0 => BlockCMSModel::getCMSBlocksByLocation(BlockCMSModel::LEFT_COLUMN, Shop::getContextShopID()),
						1 => BlockCMSModel::getCMSBlocksByLocation(BlockCMSModel::RIGHT_COLUMN, Shop::getContextShopID()))
				)
			)
		);

		$this->fields_form[1]['form'] = array(
			'tinymce' => true,
			'legend' => array(
				'title' => $this->l('Footer\'s various links Configuration'),
				'image' => _PS_ADMIN_IMG_.'information.png'
			),
			'input' => array(
				array(
					'type' => 'checkbox',
					'name' => 'cms_footer',
					'values' => array(
						'query' => array(
							array(
								'id' => 'on',
								'name' => $this->l('Display various links and information in the Footer'),
								'val' => '1'
							),
						),
						'id' => 'id',
						'name' => 'name'
					)
				),
				array(
					'type' => 'cms_pages',
					'label' => $this->l('Footer links:'),
					'name' => 'footerBox[]',
					'values' => BlockCMSModel::getAllCMSStructure(),
					'desc' => $this->l('Please mark every page that you want to display in the footer CMS block.')
				),
				array(
					'type' => 'textarea',
					'label' => $this->l('Footer informations:'),
					'name' => 'footer_text',
					'rows' => 5,
					'cols' => 60,
					'lang' => true
				),
				array(
					'type' => 'checkbox',
					'name' => 'cms_footer_powered_by',
					'values' => array(
						'query' => array(
							array(
								'id' => 'on',
								'name' => $this->l('Display "Powered by PrestaShop"'),
								'val' => '1'
							),
						),
						'id' => 'id',
						'name' => 'name'
					)
				)
			),
			'submit' => array(
				'name' => 'submitFooterCMS',
				'title' => $this->l('Save   '),
				'class' => 'button'
			)
		);

		$this->context->controller->getLanguages();
		$tmp = Configuration::get('FOOTER_CMS');
		$footer_boxes = explode('|', $tmp);

		if ($footer_boxes && is_array($footer_boxes))
			foreach ($footer_boxes as $key => $value)
				$this->fields_value[$value] = true;

		$this->fields_value['cms_footer_on'] = Configuration::get('FOOTER_BLOCK_ACTIVATION');
		$this->fields_value['cms_footer_powered_by_on'] = Configuration::get('FOOTER_POWEREDBY');

		foreach ($this->context->controller->_languages as $language)
		{
			$footer_text = Configuration::get('FOOTER_CMS_TEXT_'.$language['id_lang']);
			$this->fields_value['footer_text'][$language['id_lang']] = $footer_text;
		}

		$helper = $this->initForm();
		$helper->submit_action = '';
		$helper->title = $this->l('CMS Block configuration');

		$helper->fields_value = $this->fields_value;
		$this->_html .= $helper->generateForm($this->fields_form);

		return;
	}

	protected function displayAddForm()
	{
		if (Tools::isSubmit('editBlockCMS') && Tools::getValue('id_cms_block'))
		{
			$this->_display = 'edit';
			$id_cms_block = (int)Tools::getValue('id_cms_block');
			$cmsBlock = BlockCMSModel::getBlockCMS($id_cms_block);
			$cmsBlockCategories = BlockCMSModel::getCMSBlockPagesCategories($id_cms_block);
			$cmsBlockPages = BlockCMSModel::getCMSBlockPages(Tools::getValue('id_cms_block'));
		}
		else
			$this->_display = 'add';

		$this->fields_form[0]['form'] = array(
			'tinymce' => true,
			'legend' => array(
				'title' => isset($cmsBlock) ? $this->l('Edit the CMS block.') : $this->l('New CMS block.'),
				'image' => isset($cmsBlock) ? _PS_ADMIN_IMG_.'edit.gif' : _PS_ADMIN_IMG_.'add.gif'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Name of the CMS block :'),
					'name' => 'block_name',
					'lang' => true,
					'desc' => $this->l('If you leave this field empty, the block name will use the category name by default.')
				),
				array(
					'type' => 'select_category',
					'label' => $this->l('CMS category :'),
					'name' => 'id_category',
					'options' => array(
						'query' => BlockCMSModel::getCMSCategories(true),
						'id' => 'id_cms_category',
						'name' => 'name'
					)
				),
				array(
					'type' => 'select',
					'label' => $this->l('Location :'),
					'name' => 'block_location',
					'options' => array(
						'query' => array(
							array(
								'id' => BlockCMSModel::LEFT_COLUMN,
								'name' => $this->l('Left')),
							array(
								'id' => BlockCMSModel::RIGHT_COLUMN,
								'name' => $this->l('Right')),
						),
						'id' => 'id',
						'name' => 'name'
					)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Display stores :'),
					'name' => 'display_stores',
					'class' => 't',
					'required' => true,
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'display_stores_on',
							'value' => 1,
							'label' => $this->l('Yes')),
						array(
							'id' => 'display_stores_off',
							'value' => 0,
							'label' => $this->l('No')),
					),
					'desc' => $this->l('Display "our stores" at the end of the block.')
				),
				array(
					'type' => 'cms_pages',
					'label' => $this->l('CMS content:'),
					'name' => 'cmsBox[]',
					'values' => BlockCMSModel::getAllCMSStructure(),
					'desc' => $this->l('Please mark every page that you want to display in this block.')
				),
			),
			'submit' => array(
				'name' => 'submitBlockCMS',
				'title' => $this->l('Save   '),
				'class' => 'button'
			)
		);

		$this->context->controller->getLanguages();
		foreach ($this->context->controller->_languages as $language)
		{
			if (Tools::getValue('block_name_'.$language['id_lang']))
				$this->fields_value['block_name'][$language['id_lang']] = Tools::getValue('block_name_'.$language['id_lang']);
			else if (isset($cmsBlock) && isset($cmsBlock[$language['id_lang']]['name']))
				$this->fields_value['block_name'][$language['id_lang']] = $cmsBlock[$language['id_lang']]['name'];
			else
				$this->fields_value['block_name'][$language['id_lang']] = '';
		}

		if (Tools::getValue('display_stores'))
			$this->fields_value['display_stores'] = Tools::getValue('display_stores');
		else if (isset($cmsBlock) && isset($cmsBlock[1]['display_store']))
			$this->fields_value['display_stores'] = $cmsBlock[1]['display_store'];
		else
			$this->fields_value['display_stores'] = '';

		if (Tools::getValue('id_category'))
			$this->fields_value['id_category'] = (int)Tools::getValue('id_category');
		else if (isset($cmsBlock) && isset($cmsBlock[1]['id_cms_category']))
			$this->fields_value['id_category'] = $cmsBlock[1]['id_cms_category'];

		if (Tools::getValue('block_location'))
			$this->fields_value['block_location'] = Tools::getValue('block_location');
		else if (isset($cmsBlock) && isset($cmsBlock[1]['location']))
			$this->fields_value['block_location'] = $cmsBlock[1]['location'];
		else
			$this->fields_value['block_location'] = 0;

		if ($cmsBoxes = Tools::getValue('cmsBox'))
			foreach ($cmsBoxes as $key => $value)
				$this->fields_value[$value] = true;
		else
		{
			if (isset($cmsBlockPages) && is_array($cmsBlockPages) && count($cmsBlockPages) > 0)
				foreach ($cmsBlockPages as $item)
					$this->fields_value['0_'.$item['id_cms']] = true;
			if (isset($cmsBlockCategories) && is_array($cmsBlockCategories) && count($cmsBlockCategories) > 0)
				foreach ($cmsBlockCategories as $item)
					$this->fields_value['1_'.$item['id_cms']] = true;
		}

		$helper = $this->initForm();

		if (isset($id_cms_block))
		{
			$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name.'&id_cms_block='.$id_cms_block;
			$helper->submit_action = 'editBlockCMS';
		}
		else
			$helper->submit_action = 'addBlockCMS';

		$helper->fields_value = isset($this->fields_value) ? $this->fields_value : array();
		$this->_html .= $helper->generateForm($this->fields_form);

		return;
	}

	private function initForm()
	{
		$helper = new HelperForm();

		$helper->module = $this;
		$helper->name_controller = 'blockcms';
		$helper->identifier = $this->identifier;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->languages = $this->context->controller->_languages;
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		$helper->default_form_language = $this->context->controller->default_form_language;
		$helper->allow_employee_form_lang = $this->context->controller->allow_employee_form_lang;
		$helper->toolbar_scroll = true;
		$helper->toolbar_btn = $this->initToolbar();

		return $helper;
	}

	protected function changePosition()
	{
		if (!Validate::isInt(Tools::getValue('position')) ||
			(Tools::getValue('location') != BlockCMSModel::LEFT_COLUMN &&
			 Tools::getValue('location') != BlockCMSModel::RIGHT_COLUMN) ||
			(Tools::getValue('way') != 0 && Tools::getValue('way') != 1))
		Tools::displayError();

		$this->_html .= 'pos change!';
		$position = (int)Tools::getValue('position');
		$location = (int)Tools::getValue('location');
		$id_cms_block = (int)Tools::getValue('id_cms_block');

		if (Tools::getValue('way') == 0)
			$new_position = $position + 1;
		else if (Tools::getValue('way') == 1)
			$new_position = $position - 1;

		BlockCMSModel::updateCMSBlockPositions($id_cms_block, $position, $new_position, $location);
		Tools::redirectAdmin('index.php?tab=AdminModules&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
	}

	protected function _postValidation()
	{
		$this->_errors = array();

		if (Tools::isSubmit('submitBlockCMS'))
		{
			$this->context->controller->getLanguages();
			$cmsBoxes = Tools::getValue('cmsBox');

			if (!Validate::isInt(Tools::getValue('display_stores')) || (Tools::getValue('display_stores') != 0 && Tools::getValue('display_stores') != 1))
				$this->_errors[] = $this->l('Invalid store display value.');
			if (!Validate::isInt(Tools::getValue('block_location')) || (Tools::getValue('block_location') != BlockCMSModel::LEFT_COLUMN && Tools::getValue('block_location') != BlockCMSModel::RIGHT_COLUMN))
				$this->_errors[] = $this->l('Invalid block location.');
			if (!is_array($cmsBoxes))
				$this->_errors[] = $this->l('You must choose at least one page -- or subcategory -- in order to create a CMS block.');
			else
			{
				foreach ($cmsBoxes as $cmsBox)
					if (!preg_match('#^[01]_[0-9]+$#', $cmsBox))
						$this->_errors[] = $this->l('Invalid CMS page and/or category.');
				foreach ($this->context->controller->_languages as $language)
					if (strlen(Tools::getValue('block_name_'.$language['id_lang'])) > 40)
						$this->_errors[] = $this->l('The block name is too long.');
			}
		}
		else if (Tools::isSubmit('deleteBlockCMS') && !Validate::isInt(Tools::getValue('id_cms_block')))
		{
			$this->_errors[] = $this->l('Invalid id_cms_block');
		}
		else if (Tools::isSubmit('submitFooterCMS'))
		{
			if (Tools::getValue('footerBox') && is_array(Tools::getValue('footerBox')))
			{
				foreach (Tools::getValue('footerBox') as $cmsBox)
					if (!preg_match('#^[01]_[0-9]+$#', $cmsBox))
						$this->_errors[] = $this->l('Invalid CMS page and/or category.');
			}

			$empty_footer_text = true;
			$footer_text = array((int)Configuration::get('PS_LANG_DEFAULT') => Tools::getValue('footer_text_'.(int)Configuration::get('PS_LANG_DEFAULT')));

			$this->context->controller->getLanguages();
			foreach ($this->context->controller->_languages as $language)
			{
				if ($language['id_lang'] == (int)Configuration::get('PS_LANG_DEFAULT'))
					continue;

				$footer_text_value = Tools::getValue('footer_text_'.(int)$language['id_lang']);
				if (!empty($footer_text_value))
				{
					$empty_footer_text = false;
					$footer_text[(int)$language['id_lang']] = $footer_text_value;
				}
				else
					$footer_text[(int)$language['id_lang']] = $footer_text[(int)Configuration::get('PS_LANG_DEFAULT')];
			}

			if (!$empty_footer_text && empty($footer_text[(int)Configuration::get('PS_LANG_DEFAULT')]))
				$this->_errors[] = $this->l('Please provide footer text for the default language.');
			else
			{
				foreach ($this->context->controller->_languages as $language)
					Configuration::updateValue('FOOTER_CMS_TEXT_'.(int)$language['id_lang'], $footer_text[(int)$language['id_lang']], true);
			}

			if ((Tools::getValue('cms_footer_on') != 0) && (Tools::getValue('cms_footer_on') != 1))
				$this->_errors[] = $this->l('Invalid activation footer.');
		}
		if (count($this->_errors))
		{
			foreach ($this->_errors as $err)
				$this->_html .= '<div class="alert error">'.$err.'</div>';

			return false;
		}
		return true;
	}

	private function _postProcess()
	{
		if ($this->_postValidation() == false)
			return false;

		$this->_errors = array();
		if (Tools::isSubmit('submitBlockCMS'))
		{
			$this->context->controller->getLanguages();
			$id_cms_category = (int)Tools::getvalue('id_category');
			$display_store = (int)Tools::getValue('display_stores');
			$location = (int)Tools::getvalue('block_location');
			$position = BlockCMSModel::getMaxPosition($location);

			if (Tools::isSubmit('addBlockCMS'))
			{
				$id_cms_block = BlockCMSModel::insertCMSBlock($id_cms_category, $location, $position, $display_store);

				if ($id_cms_block !== false)
				{
					foreach ($this->context->controller->_languages as $language)
						BlockCMSModel::insertCMSBlockLang($id_cms_block, $language['id_lang']);

					$shops = Shop::getContextListShopID();

					foreach ($shops as $shop)
						BlockCMSModel::insertCMSBlockShop($id_cms_block, $shop);
				}

				$this->_errors[] = $this->l('A new block cannot be created!');
			}
			elseif (Tools::isSubmit('editBlockCMS'))
			{
				$id_cms_block = Tools::getvalue('id_cms_block');
				$old_block = BlockCMSModel::getBlockCMS($id_cms_block);

				BlockCMSModel::deleteCMSBlockPage($id_cms_block);

				if ($old_block[1]['location'] != (int)Tools::getvalue('block_location'))
					BlockCMSModel::updatePositions($old_block[1]['position'], $old_block[1]['position'] + 1, $old_block[1]['location']);

				BlockCMSModel::updateCMSBlock($id_cms_block, $id_cms_category, $position, $location, $display_store);

				foreach ($this->context->controller->_languages as $language)
				{
					$block_name = Tools::getValue('block_name_'.$language['id_lang']);
					BlockCMSModel::updateCMSBlockLang($id_cms_block, $block_name, $language['id_lang']);
				}
			}

			$cmsBoxes = Tools::getValue('cmsBox');
			if ($cmsBoxes)
			{
				foreach ($cmsBoxes as $cmsBox)
				{
					$cms_properties = explode('_', $cmsBox);
					BlockCMSModel::insertCMSBlockPage($id_cms_block, $cms_properties[1], $cms_properties[0]);
				}
			}

			if (Tools::isSubmit('addBlockCMS'))
				$redirect = 'addBlockCMSConfirmation';
			elseif (Tools::isSubmit('editBlockCMS'))
				$redirect = 'editBlockCMSConfirmation';

			Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&'.$redirect);
		}
		elseif (Tools::isSubmit('deleteBlockCMS') && Tools::getValue('id_cms_block'))
		{
			$id_cms_block = Tools::getvalue('id_cms_block');

			if ($id_cms_block)
			{
				BlockCMSModel::deleteCMSBlock((int)$id_cms_block);
				BlockCMSModel::deleteCMSBlockPage((int)$id_cms_block);

				Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&deleteBlockCMSConfirmation');
			}
			else
				$this->_html .= $this->displayError($this->l('Error: You are trying to delete a non-existing CMS blocks. '));
		}
		elseif (Tools::isSubmit('submitFooterCMS'))
		{
			$powered_by = Tools::getValue('cms_footer_powered_by_on') ? 1 : 0;
			$footer_boxes = Tools::getValue('footerBox') ? implode('|', Tools::getValue('footerBox')) : '';
			$block_activation = (Tools::getValue('cms_footer_on') == 1) ? 1 : 0;

			Configuration::updateValue('FOOTER_CMS', rtrim($footer_boxes, '|'));
			Configuration::updateValue('FOOTER_POWEREDBY', $powered_by);
			Configuration::updateValue('FOOTER_BLOCK_ACTIVATION', $block_activation);

			$this->_html .= $this->displayConfirmation($this->l('Update your footer\'s information.'));
		}
		elseif (Tools::isSubmit('addBlockCMSConfirmation'))
			$this->_html .= $this->displayConfirmation($this->l('CMS block added.'));
		elseif (Tools::isSubmit('editBlockCMSConfirmation'))
			$this->_html .= $this->displayConfirmation($this->l('CMS block edited.'));
		elseif (Tools::isSubmit('deleteBlockCMSConfirmation'))
			$this->_html .= $this->displayConfirmation($this->l('Deletion successful'));
		elseif (Tools::isSubmit('id_cms_block') && Tools::isSubmit('way') && Tools::isSubmit('position') && Tools::isSubmit('location'))
			$this->changePosition();
		elseif (Tools::isSubmit('updatePositions'))
			$this->updatePositionsDnd();
		if (count($this->_errors))
		{
			foreach ($this->_errors as $err)
				$this->_html .= '<div class="alert error">'.$err.'</div>';
		}
	}

	public function getContent()
	{
		$this->_html = '';
		$this->_postProcess();

		if (Tools::isSubmit('addBlockCMS') || Tools::isSubmit('editBlockCMS'))
			$this->displayAddForm();
		else
			$this->displayForm();

		return $this->_html;
	}

	public function displayBlockCMS($column)
	{
		$cms_titles = BlockCMSModel::getCMSTitles($column);

		$this->smarty->assign(array(
			'block' => 1,
			'cms_titles' => $cms_titles,
			'contact_url' => (_PS_VERSION_ >= 1.5) ? 'contact' : 'contact-form'
		));

		return $this->display(__FILE__, 'blockcms.tpl');
	}

	private function _prepareHook()
	{
		$block_activation = Configuration::get('FOOTER_BLOCK_ACTIVATION');

		if (!$block_activation)
			return false;

		$cms_titles = BlockCMSModel::getCMSTitlesFooter();
		$display_footer = Configuration::get('PS_STORES_DISPLAY_FOOTER');
		$display_poweredby = Configuration::get('FOOTER_POWEREDBY');
		$footer_text = Configuration::get('FOOTER_CMS_TEXT_'.(int)$this->context->language->id);

		$this->smarty->assign(
			array(
				'block' => 0,
				'contact_url' => 'contact',
				'cmslinks' => $cms_titles,
				'display_stores_footer' => $display_footer,
				'display_poweredby' => ((int)$display_poweredby === 1 || $display_poweredby === false),
				'footer_text' => $footer_text
			)
		);
		return true;
	}


	public function hookHeader($params)
	{
		$this->context->controller->addCSS(($this->_path).'blockcms.css', 'all');
	}

	public function hookLeftColumn()
	{
		return $this->displayBlockCMS(BlockCMSModel::LEFT_COLUMN);
	}

	public function hookRightColumn()
	{
		return $this->displayBlockCMS(BlockCMSModel::RIGHT_COLUMN);
	}

	public function hookFooter()
	{
		if (!$this->_prepareHook())
			return ;
		return $this->display(__FILE__, 'blockcms.tpl');
	}

	protected function updatePositionsDnd()
	{
		if (Tools::getValue('cms_block_0'))
			$positions = Tools::getValue('cms_block_0');
		elseif (Tools::getValue('cms_block_1'))
			$positions = Tools::getValue('cms_block_1');
		else
			$positions = array();

		foreach ($positions as $position => $value)
		{
			$pos = explode('_', $value);

			if (isset($pos[2]))
				BlockCMSModel::updateCMSBlockPosition($pos[2], $position);
		}
	}
	
	public function hookActionShopDataDuplication($params)
	{
		Db::getInstance()->execute('
		INSERT IGNORE INTO '._DB_PREFIX_.'cms_block_shop (id_cms_block, id_shop)
		SELECT id_cms_block, '.(int)$params['new_id_shop'].'
		FROM '._DB_PREFIX_.'cms_block_shop
		WHERE id_shop = '.(int)$params['old_id_shop']);
	}
}
