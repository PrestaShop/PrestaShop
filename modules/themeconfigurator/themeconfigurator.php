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

class ThemeConfigurator extends Module
{
	protected $max_image_size = 1048576;
	protected $default_language;
	protected $languages;

	public function __construct()
	{
		$this->name = 'themeconfigurator';
		$this->tab = 'front_office_features';
		$this->version = '0.2';
		$this->bootstrap = true;
		$this->secure_key = Tools::encrypt($this->name);
		$this->default_language = Language::getLanguage(Configuration::get('PS_LANG_DEFAULT'));
		$this->languages = Language::getLanguages();
		$this->author = 'PrestaShop';
		parent::__construct();
		$this->displayName = $this->l('Theme configurator');
		$this->description = $this->l('Configure elements of your theme');
		$this->module_path = _PS_MODULE_DIR_.$this->name.'/';
		$this->uploads_path = _PS_MODULE_DIR_.$this->name.'/img/';
		$this->admin_tpl_path = _PS_MODULE_DIR_.$this->name.'/views/templates/admin/';
		$this->hooks_tpl_path = _PS_MODULE_DIR_.$this->name.'/views/templates/hooks/';
	}

	public function install()
	{
		$themes_colors = array(
			'theme1',
			'theme2',
			'theme3',
			'theme4',
			'theme5',
			'theme6',
			'theme7',
			'theme8',
			'theme9'
		);
		$themes_fonts = array(
			'font1' => 'Open Sans',
			'font2' => 'Josefin Slab',
			'font3' => 'Arvo',
			'font4' => 'Lato',
			'font5' => 'Volkorn',
			'font6' => 'Abril Fatface',
			'font7' => 'Ubuntu',
			'font8' => 'PT Sans',
			'font9' => 'Old Standard TT',
			'font10' => 'Droid Sans'
		);

		if (!parent::install()
			|| !$this->installDB()
			|| !$this->installFixtures() ||
			!$this->registerHook('displayHeader') ||
			!$this->registerHook('displayTopColumn') ||
			!$this->registerHook('displayLeftColumn') ||
			!$this->registerHook('displayRightColumn') ||
			!$this->registerHook('displayHome') ||
			!$this->registerHook('displayFooter') ||
			!$this->registerHook('displayBackOfficeHeader') ||
			!Configuration::updateValue('PS_TC_THEMES', serialize($themes_colors)) ||
			!Configuration::updateValue('PS_TC_FONTS', serialize($themes_fonts)) ||
			!Configuration::updateValue('PS_TC_THEME', '') ||
			!Configuration::updateValue('PS_TC_FONT', '')
		)
			return false;

		return true;
	}

	private function installDB()
	{
		return (
			Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'themeconfigurator`') &&
			Db::getInstance()->Execute('
			CREATE TABLE `'._DB_PREFIX_.'themeconfigurator` (
					`id_item` int(10) unsigned NOT NULL AUTO_INCREMENT,
					`id_shop` int(10) unsigned NOT NULL,
					`id_lang` int(10) unsigned NOT NULL,
					`item_order` int(10) unsigned NOT NULL,
					`title` VARCHAR(100),
					`title_use` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
					`hook` VARCHAR(100),
					`url` VARCHAR(100),
					`target` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
					`image` VARCHAR(100),
					`image_w` VARCHAR(10),
					`image_h` VARCHAR(10),
					`html` TEXT,
					`active` tinyint(1) unsigned NOT NULL DEFAULT \'1\',
					PRIMARY KEY (`id_item`)
			) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;')
		);

	}

	public function installFixtures()
	{
		$result = true;

		for ($i = 1; $i < 6; $i++)
		{
			$sizes = @getimagesize((dirname(__FILE__).DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'banner-img'.(int)$i.'.jpg'));
			$width = (isset($sizes[0]) && $sizes[0])? (int)$sizes[0] : 0;
			$height = (isset($sizes[1]) && $sizes[1])? (int)$sizes[1] : 0;

			$result &= Db::getInstance()->Execute('
				INSERT INTO `'._DB_PREFIX_.'themeconfigurator` ( 
						`id_shop`, `id_lang`, `item_order`, `title`, `title_use`, `hook`, `url`, `target`, `image`, `image_w`, `image_h`, `html`, `active`
				) VALUES ( 
					\''.(int)$this->context->shop->id.'\',
					\''.(int)$this->context->language->id.'\',
					\''.(int)$i.'\',
					\'\',
					\'0\',
					\'home\',
					\'http://www.prestashop.com/\',
					\'0\',
					\'banner-img'.(int)$i.'.jpg\',
					'.$width.',
					'.$height.',
					\'\',
					1)
				');
		}

		for ($i = 6; $i < 8; $i++)
		{
			$sizes = @getimagesize((dirname(__FILE__).DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'banner-img'.(int)$i.'.jpg'));
			$width = (isset($sizes[0]) && $sizes[0]) ? (int)$sizes[0] : 0;
			$height = (isset($sizes[1]) && $sizes[1]) ? (int)$sizes[1] : 0;

			$result &= Db::getInstance()->Execute('
				INSERT INTO `'._DB_PREFIX_.'themeconfigurator` ( 
						`id_shop`, `id_lang`, `item_order`, `title`, `title_use`, `hook`, `url`, `target`, `image`, `image_w`, `image_h`, `html`, `active`
				) VALUES ( 
					\''.(int)$this->context->shop->id.'\',
					\''.(int)$this->context->language->id.'\',
					\''.(int)$i.'\',
					\'\',
					\'0\',
					\'top\',
					\'http://www.prestashop.com/\',
					\'0\',
					\'banner-img'.(int)$i.'.jpg\',
					'.$width.',
					'.$height.',
					\'\',
					1)
				');
		}

		return $result;
	}

	public function uninstall()
	{
		$images = Db::getInstance()->executeS('SELECT image FROM `'._DB_PREFIX_.'themeconfigurator`');
		foreach ($images as $image)
			$this->deleteImage($image['image']);

		if (!Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'themeconfigurator`') || !parent::uninstall())
			return false;

		return true;
	}

	public function hookDisplayBackOfficeHeader()
	{
		if (Tools::getValue('configure') != $this->name)
			return;

		$this->context->controller->addCSS($this->_path.'css/admin.css');
		$this->context->controller->addJquery();
		$this->context->controller->addJS($this->_path.'js/admin.js');
	}

	public function hookdisplayHeader()
	{
		$this->context->controller->addCss($this->_path.'css/hooks.css', 'all');

		if (Tools::getValue('live_configurator', 0) == 1 && Tools::getValue('live_configurator_token') == Tools::getAdminToken($this->name))
		{
			$this->context->controller->addCSS($this->_path.'css/live_configurator.css');
			$this->context->controller->addJS($this->_path.'js/live_configurator.js');

			if (Tools::getValue('theme'))
				$this->context->controller->addCss($this->_path.'css/'.Tools::getValue('theme').'.css', 'all');

			if (Tools::getValue('theme_font'))
				$this->context->controller->addCss($this->_path.'css/'.Tools::getValue('theme_font').'.css', 'all');
		}
		else
		{
			if (Configuration::get('PS_TC_THEME') != '')
				$this->context->controller->addCss($this->_path.'css/'.Configuration::get('PS_TC_THEME').'.css', 'all');

			if (Configuration::get('PS_TC_FONT') != '')
				$this->context->controller->addCss($this->_path.'css/'.Configuration::get('PS_TC_FONT').'.css', 'all');
		}
	}

	public function hookdisplayTopColumn()
	{
		return $this->hookdisplayTop();
	}

	public function hookdisplayTop()
	{
		$this->context->smarty->assign(array(
			'htmlitems' => $this->getItemsFromHook('top'),
			'hook' => 'top'
		));

		return $this->display(__FILE__, 'hook.tpl');
	}

	public function hookDisplayHome()
	{
		$this->context->smarty->assign(array(
			'htmlitems' => $this->getItemsFromHook('home'),
			'hook' => 'home'
		));

		return $this->display(__FILE__, 'hook.tpl');
	}

	public function hookDisplayLeftColumn()
	{
		$this->context->smarty->assign(array(
			'htmlitems' => $this->getItemsFromHook('left'),
			'hook' => 'left'
		));

		return $this->display(__FILE__, 'hook.tpl');
	}

	public function hookDisplayRightColumn()
	{
		$this->context->smarty->assign(array(
			'htmlitems' => $this->getItemsFromHook('right'),
			'hook' => 'right'
		));

		return $this->display(__FILE__, 'hook.tpl');
	}

	public function hookDisplayFooter()
	{
		$html = '';

		if (Tools::getValue('live_configurator', 0) == 1 && Tools::getValue('live_configurator_token') == Tools::getAdminToken($this->name))
		{
			if (Tools::isSubmit('submitLiveConfigurator'))
			{
				Configuration::updateValue('PS_TC_THEME', Tools::getValue('theme'));
				Configuration::updateValue('PS_TC_FONT', Tools::getValue('theme_font'));
				Tools::redirect('index.php');
			}

			$ad_image = $this->_path.'img/'.$this->context->language->iso_code.'/advertisement.png';

			if (!file_exists($ad_image))
				$ad_image = $this->_path.'img/en/advertisement.png';

			$this->smarty->assign(array(
				'themes' => unserialize(Configuration::get('PS_TC_THEMES')),
				'fonts' => unserialize(Configuration::get('PS_TC_FONTS')),
				'theme_font' => Tools::getValue('theme_font', Configuration::get('PS_TC_FONT')),
				'id_shop' => (int)$this->context->shop->id,
				'id_employee' => isset($this->context->employee) ? (int)$this->context->employee->id : 0,
				'live_configurator_token' => Tools::getValue('live_configurator_token', ''),
				'advertisement_image' => $ad_image,
				'advertisement_text' => $this->l('Over 500+ PrestaShop Premium Templates! Browse Now!')
			));

			$html .= $this->display(__FILE__, 'live_configurator.tpl');
		}

		$this->context->smarty->assign(array(
			'htmlitems' => $this->getItemsFromHook('footer'),
			'hook' => 'footer'
		));

		return $html.$this->display(__FILE__, 'hook.tpl');
	}

	protected function getItemsFromHook($hook)
	{
		if (!$hook)
			return false;

		return Db::getInstance()->ExecuteS('
		SELECT *
		FROM `'._DB_PREFIX_.'themeconfigurator`
		WHERE id_shop = '.(int)$this->context->shop->id.' AND id_lang = '.(int)$this->context->language->id.'
		AND hook = \''.pSQL($hook).'\' AND active = 1
		ORDER BY item_order ASC');
	}

	protected function deleteImage($image)
	{
		$file_name = $this->uploads_path.$image;

		if (realpath(dirname($file_name)) != realpath($this->uploads_path))
			Tools::dieOrLog(sprintf('Could not find upload directory'));

		if ($image != '' && is_file($file_name))
			unlink($file_name);
	}

	protected function removeItem()
	{
		$id_item = (int)Tools::getValue('item_id');

		if ($image = Db::getInstance()->getValue('SELECT image FROM `'._DB_PREFIX_.'themeconfigurator` WHERE id_item = '.(int)$id_item))
			$this->deleteImage($image);

		Db::getInstance()->delete(_DB_PREFIX_.'themeconfigurator', 'id_item = '.(int)$id_item);

		if (Db::getInstance()->Affected_Rows() == 1)
		{
			Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_.'themeconfigurator` 
				SET item_order = item_order-1 
				WHERE (
					item_order > '.(int)Tools::getValue('item_order').' AND 
					id_shop = '.(int)$this->context->shop->id.' AND
					hook = \''.pSQL(Tools::getValue('item_hook')).'\')'
			);

			$this->context->smarty->assign('confirmation', $this->l('Successful deletion.'));
		}
		else
			$this->context->smarty->assign('error', $this->l('Can\'t delete the slide.'));
	}

	protected function updateItem()
	{
		$id_item = (int)Tools::getValue('item_id');
		$title = Tools::getValue('item_title');
		$content = Tools::getValue('item_html');

		if (!Validate::isCleanHtml($title, (int)Configuration::get('PS_ALLOW_HTML_IFRAME')) || !Validate::isCleanHtml($content, (int)Configuration::get('PS_ALLOW_HTML_IFRAME')))
		{
			$this->context->smarty->assign('error', $this->l('Invalid content'));

			return false;
		}

		$new_image = '';
		$image_w = (is_numeric(Tools::getValue('item_img_w'))) ? (int)Tools::getValue('item_img_w') : '';
		$image_h = (is_numeric(Tools::getValue('item_img_h'))) ? (int)Tools::getValue('item_img_h') : '';

		if (!empty($_FILES['item_img']['name']))
		{
			if ($old_image = Db::getInstance()->getValue('SELECT image FROM `'._DB_PREFIX_.'themeconfigurator` WHERE id_item = '.(int)$id_item))
				if (file_exists(dirname(__FILE__).'/img/'.$old_image))
					@unlink(dirname(__FILE__).'/img/'.$old_image);

			if (!$image = $this->uploadImage($_FILES['item_img'], $image_w, $image_h))
				return false;

			$new_image = 'image = \''.pSQL($image).'\',';
		}

		if (!Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'themeconfigurator` SET 
					title = \''.pSQL($title).'\',
					title_use = '.(int)Tools::getValue('item_title_use').',
					hook = \''.pSQL(Tools::getValue('item_hook')).'\',
					url = \''.pSQL(Tools::getValue('item_url')).'\',
					target = '.(int)Tools::getValue('item_target').',
					'.$new_image.'
					image_w = '.(int)$image_w.',
					image_h = '.(int)$image_h.',
					active = '.(int)Tools::getValue('item_active').',
					html = \''.pSQL($content).'\'
			WHERE id_item = '.(int)Tools::getValue('item_id')
		)
		)
		{
			if ($image = Db::getInstance()->getValue('SELECT image FROM `'._DB_PREFIX_.'themeconfigurator` WHERE id_item = '.(int)Tools::getValue('item_id')))
				$this->deleteImage($image);

			$this->context->smarty->assign('error', $this->l('An error occured while saving data.'));

			return false;
		}

		$this->context->smarty->assign('confirmation', $this->l('Successfully updated.'));

		return true;
	}

	protected function uploadImage($image, $image_w = '', $image_h = '')
	{
		$res = false;
		if (is_array($image) && (ImageManager::validateUpload($image, $this->max_image_size) === false) && ($tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS')) && move_uploaded_file($image['tmp_name'], $tmp_name))
		{
			$type = Tools::strtolower(Tools::substr(strrchr($image['name'], '.'), 1));
			$img_name = Tools::encrypt($image['name'].sha1(microtime())).'.'.$type;
			Configuration::set('PS_IMAGE_QUALITY', 'png_all');
			if (ImageManager::resize($tmp_name, dirname(__FILE__).'/img/'.$img_name, $image_w, $image_h))
				$res = true;
		}

		if (!$res)
		{
			$this->context->smarty->assign('error', $this->l('An error occurred during the image upload.'));

			return false;
		}

		return $img_name;
	}

	public function getContent()
	{
		if (Tools::isSubmit('submitModule'))
		{
			Configuration::updateValue('PS_QUICK_VIEW', (int)Tools::getValue('quick_view'));
			foreach ($this->getConfigurableModules() as $module)
			{
				if (!isset($module['is_module']) || !$module['is_module'] || !Validate::isModuleName($module['name']) || !Tools::isSubmit($module['name']))
					continue;

				$module_instance = Module::getInstanceByName($module['name']);
				if ($module_instance === false || !is_object($module_instance))
					continue;

				$is_installed = (int)Validate::isLoadedObject($module_instance);
				if ($is_installed)
				{
					if (($active = (int)Tools::getValue($module['name'])) == $module_instance->active)
						continue;

					if ($active)
						$module_instance->enable();
					else
						$module_instance->disable();
				}
				else
					if ((int)Tools::getValue($module['name']))
						$module_instance->install();
			}
		}

		if (Tools::isSubmit('newItem'))
			$this->addItem();
		elseif (Tools::isSubmit('updateItem'))
			$this->updateItem();
		elseif (Tools::isSubmit('removeItem'))
			$this->removeItem();

		$html = $this->renderConfigurationForm();
		$html .= $this->renderThemeConfiguratorForm();

		return $html;
	}

	protected function addItem()
	{
		$title = Tools::getValue('item_title');
		$content = Tools::getValue('item_html');
		if (!Validate::isCleanHtml($title, (int)Configuration::get('PS_ALLOW_HTML_IFRAME')) || !Validate::isCleanHtml($content, (int)Configuration::get('PS_ALLOW_HTML_IFRAME')))
		{
			$this->context->smarty->assign('error', $this->l('Invalid content'));

			return false;
		}

		if (!$current_order = (int)Db::getInstance()->getValue('
			SELECT item_order + 1
			FROM `'._DB_PREFIX_.'themeconfigurator` 
			WHERE 
					id_shop = '.(int)$this->context->shop->id.' 
					AND id_lang = '.(int)Tools::getValue('id_lang').'
					AND hook = \''.pSQL(Tools::getValue('item_hook')).'\' 
					ORDER BY item_order DESC'
		)
		)
			$current_order = 1;

		$image_w = is_numeric(Tools::getValue('item_img_w')) ? (int)Tools::getValue('item_img_w') : '';
		$image_h = is_numeric(Tools::getValue('item_img_h')) ? (int)Tools::getValue('item_img_h') : '';

		if (!empty($_FILES['item_img']['name']))
		{
			if (!$image = $this->uploadImage($_FILES['item_img'], $image_w, $image_h))
				return false;
		}
		else
		{
			$image = '';
			$image_w = '';
			$image_h = '';
		}

		if (!Db::getInstance()->Execute('
			INSERT INTO `'._DB_PREFIX_.'themeconfigurator` ( 
					`id_shop`, `id_lang`, `item_order`, `title`, `title_use`, `hook`, `url`, `target`, `image`, `image_w`, `image_h`, `html`, `active`
			) VALUES ( 
					\''.(int)$this->context->shop->id.'\',
					\''.(int)Tools::getValue('id_lang').'\',
					\''.(int)$current_order.'\',
					\''.pSQL($title).'\',
					\''.(int)Tools::getValue('item_title_use').'\',
					\''.pSQL(Tools::getValue('item_hook')).'\',
					\''.pSQL(Tools::getValue('item_url')).'\',
					\''.(int)Tools::getValue('item_target').'\',
					\''.pSQL($image).'\',
					\''.pSQL($image_w).'\',
					\''.pSQL($image_h).'\',
					\''.pSQL($content).'\',
					1)
			')
		)
		{
			if (!Tools::isEmpty($image))
				$this->deleteImage($image);

			$this->context->smarty->assign('error', $this->l('An error occured while saving data.'));

			return false;
		}

		$this->context->smarty->assign('confirmation', $this->l('New item added successfull.'));

		return true;
	}

	public function renderConfigurationForm()
	{
		$inputs = array();
		foreach ($this->getConfigurableModules() as $module)
		{
			$desc = '';
			if (isset($module['is_module']) && $module['is_module'])
			{
				$module_instance = Module::getInstanceByName($module['name']);
				if (Validate::isLoadedObject($module_instance) && method_exists($module_instance, 'getContent'))
					$desc = '<a href="'.$this->context->link->getAdminLink('AdminModules', true).'&configure='.urlencode($module_instance->name).'&tab_module='.$module_instance->tab.'&module_name='.urlencode($module_instance->name).'">'.$this->l('Configure').'</a>';
			}
			if (!$desc && isset($module['desc']) && $module['desc'])
				$desc = $module['desc'];

			$inputs[] = array(
				'type' => 'switch',
				'label' => $module['label'],
				'name' => $module['name'],
				'desc' => $desc,
				'values' => array(
					array(
						'id' => 'active_on',
						'value' => 1,
						'label' => $this->l('Enabled')
					),
					array(
						'id' => 'active_off',
						'value' => 0,
						'label' => $this->l('Disabled')
					)
				),
			);
		}

		$inputs[] = array(
			'type' => 'free',
			'label' => $this->l('Live configurator'),
			'name' => 'live-conf'
		);

		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'input' => $inputs,
				'submit' => array(
					'title' => $this->l('Save'),
					'class' => 'btn btn-default pull-right'
				)
			),
		);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();

		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitModule';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => array_merge($this->getConfigFieldsValues(),
				array(
					'live-conf' => '<a href="'.$this->context->link->getPageLink('index')
						.'?live_configurator=1&id_employee='.(int)$this->context->employee->id
						.'&id_shop='.(int)$this->context->shop->id
						.'&live_configurator_token='.Tools::getAdminToken($this->name)
						.(Configuration::get('PS_TC_THEME') != '' ? '&theme='.Configuration::get('PS_TC_THEME') : '')
						.(Configuration::get('PS_TC_FONT') != '' ? '&theme_font='.Configuration::get('PS_TC_FONT') : '')
						.'" class="btn btn-default" onclick="return !window.open($(this).attr(\'href\'));"><i class="icon-cogs"></i> Live configurator</a>'
				)),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}

	protected function renderThemeConfiguratorForm()
	{
		$id_shop = (int)$this->context->shop->id;
		$items = array();
		$hooks = array();

		$this->context->smarty->assign('htmlcontent', array(
			'admin_tpl_path' => $this->admin_tpl_path,
			'hooks_tpl_path' => $this->hooks_tpl_path,

			'info' => array(
				'module' => $this->name,
				'name' => $this->displayName,
				'version' => $this->version,
				'psVersion' => _PS_VERSION_,
				'context' => (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') == 0) ? 1 : ($this->context->shop->getTotalShops() != 1) ? $this->context->shop->getContext() : 1
			)
		));

		foreach ($this->languages as $language)
		{
			$hooks[$language['id_lang']] = array(
				'home',
				'top',
				'left',
				'right',
				'footer'
			);

			foreach ($hooks[$language['id_lang']] as $hook)
				$items[$language['id_lang']][$hook] = Db::getInstance()->ExecuteS('
					SELECT * FROM `'._DB_PREFIX_.'themeconfigurator` 
					WHERE id_shop = '.(int)$id_shop.' 
					AND id_lang = '.(int)$language['id_lang'].' 
					AND hook = \''.pSQL($hook).'\' 
					ORDER BY item_order ASC'
				);
		}

		$this->context->smarty->assign('htmlitems', array(
			'items' => $items,
			'lang' => array(
				'default' => $this->default_language,
				'all' => $this->languages,
				'lang_dir' => _THEME_LANG_DIR_,
				'user' => $this->context->language->id
			),
			'postAction' => 'index.php?tab=AdminModules&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&tab_module=other&module_name='.$this->name.'',
			'id_shop' => $id_shop
		));

		return $this->display(__FILE__, 'views/templates/admin/admin.tpl');
	}

	protected function getConfigurableModules()
	{
		return array(
			array(
				'label' => $this->l('Display the social following links'),
				'name' => 'blocksocial',
				'value' => (int)Validate::isLoadedObject($module = Module::getInstanceByName('blocksocial')) && $module->isEnabledForShopContext(),
				'is_module' => true,
			),
			array(
				'label' => $this->l('Display contact information'),
				'name' => 'blockcontactinfos',
				'value' => (int)Validate::isLoadedObject($module = Module::getInstanceByName('blockcontactinfos')) && $module->isEnabledForShopContext(),
				'is_module' => true,
			),
			array(
				'label' => $this->l('Display social buttons on the products page'),
				'name' => 'socialsharing',
				'value' => (int)Validate::isLoadedObject($module = Module::getInstanceByName('socialsharing')) && $module->isEnabledForShopContext(),
				'is_module' => true,
			),
			array(
				'label' => $this->l('Display facebook block on the home page'),
				'name' => 'blockfacebook',
				'value' => (int)Validate::isLoadedObject($module = Module::getInstanceByName('blockfacebook')) && $module->isEnabledForShopContext(),
				'is_module' => true,
			),
			array(
				'label' => $this->l('Customer cms information block'),
				'name' => 'blockcmsinfo',
				'value' => (int)Validate::isLoadedObject($module = Module::getInstanceByName('blockcmsinfo')) && $module->isEnabledForShopContext(),
				'is_module' => true,
			),
			array(
				'label' => $this->l('Enable Quick view'),
				'name' => 'quick_view',
				'value' => (int)Tools::getValue('PS_QUICK_VIEW', Configuration::get('PS_QUICK_VIEW'))
			),
			array(
				'label' => $this->l('Enable top banner'),
				'name' => 'blockbanner',
				'value' => (int)Validate::isLoadedObject($module = Module::getInstanceByName('blockbanner')) && $module->isEnabledForShopContext(),
				'is_module' => true,
			),
			array(
				'label' => $this->l('Enable product payment logos'),
				'name' => 'productpaymentlogos',
				'value' => (int)Validate::isLoadedObject($module = Module::getInstanceByName('productpaymentlogos')) && $module->isEnabledForShopContext(),
				'is_module' => true,
			)
		);
	}

	public function getConfigFieldsValues()
	{
		$values = array();
		foreach ($this->getConfigurableModules() as $module)
			$values[$module['name']] = $module['value'];

		return $values;
	}
}
