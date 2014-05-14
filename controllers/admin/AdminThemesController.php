<?php

/*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminThemesControllerCore extends AdminController
{
	const MAX_NAME_LENGTH = 128;

	public function __construct()
	{
		$this->bootstrap = true;
		parent::__construct();
	}

	/** This value is used in isThemeCompatible method. only version node with an
	 * higher version number will be used in [theme]/config.xml
	 * @since 1.4.0.11, check theme compatibility 1.4
	 * @static
	 */
	public static $check_features_version = '1.4';

	/** $check_features is a multidimensional array used to check [theme]/config.xml values,
	 * and also checks prestashop current configuration if not match.
	 * @static
	 */
	public static $check_features = array(
		'ccc' => array(
			'attributes' => array(
				'available' => array(
					'value' => 'true',
					/*
					 * accepted attribute value if value doesn't match, prestashop configuration value must have those values
					*/
					'check_if_not_valid' => array(
						'PS_CSS_THEME_CACHE' => 0,
						'PS_JS_THEME_CACHE' => 0,
						'PS_HTML_THEME_COMPRESSION' => 0,
						'PS_JS_HTML_THEME_COMPRESSION' => 0,
					),
				),
			),
			'error' => 'This theme may not correctly use "combine, compress and cache" options.',
			'tab' => 'AdminPerformance',
		),
		'guest_checkout' => array(
			'attributes' => array(
				'available' => array(
					'value' => 'true',
					'check_if_not_valid' => array('PS_GUEST_CHECKOUT_ENABLED' => 0)
				),
			),
			'error' => 'This theme may not correctly use "guest checkout" option.',
			'tab' => 'AdminPreferences',
		),
		'one_page_checkout' => array(
			'attributes' => array(
				'available' => array(
					'value' => 'true',
					'check_if_not_valid' => array('PS_ORDER_PROCESS_TYPE' => 0),
				),
			),
			'error' => 'This theme may not correctly use "one-page checkout" option.',
			'tab' => 'AdminPreferences',
		),
		'store_locator' => array(
			'attributes' => array(
				'available' => array(
					'value' => 'true',
					'check_if_not_valid' => array(
						'PS_STORES_SIMPLIFIED' => 0,
						'PS_STORES_DISPLAY_FOOTER' => 0
					),
				)
			),
			'error' => 'This theme may not correctly use "display store location" option.',
			'tab' => 'AdminStores',
		)
	);

	public $className = 'Theme';
	public $table = 'theme';
	protected $toolbar_scroll = false;
	private $img_error;

	public function init()
	{
		// No cache for auto-refresh uploaded logo
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

		parent::init();
		$this->can_display_themes = (!Shop::isFeatureActive() || Shop::getContext() == Shop::CONTEXT_SHOP) ? true : false;

		libxml_use_internal_errors(true);

		//get addons themes
		if ($this->logged_on_addons)
		{
			if (!$this->isFresh(Theme::CACHE_FILE_CUSTOMER_THEMES_LIST, 86400))
				file_put_contents(_PS_ROOT_DIR_.Theme::CACHE_FILE_CUSTOMER_THEMES_LIST, Tools::addonsRequest('customer_themes'));
			
			$customer_themes_list = file_get_contents(_PS_ROOT_DIR_.Theme::CACHE_FILE_CUSTOMER_THEMES_LIST);
			if (!empty($customer_themes_list) && $customer_themes_list_xml = simplexml_load_string($customer_themes_list))
			{			
				foreach ($customer_themes_list_xml->theme as $addons_theme)
				{
					//get addons theme if folder does not exist
					$ids_themes = unserialize(Configuration::get('PS_ADDONS_THEMES_IDS'));

					if (!is_array($ids_themes) || (is_array($ids_themes) && !in_array((string)$addons_theme->id, $ids_themes)))
					{
						$zip_content = Tools::addonsRequest('module', array(
							'id_module' => pSQL($addons_theme->id), 
							'username_addons' => pSQL(trim($this->context->cookie->username_addons)), 
							'password_addons' => pSQL(trim($this->context->cookie->password_addons)))
							);
						
						$uniqid = uniqid();
						$sandbox = _PS_CACHE_DIR_.'sandbox'.DIRECTORY_SEPARATOR.$uniqid.DIRECTORY_SEPARATOR;
						mkdir($sandbox);

						file_put_contents($sandbox.(string)$addons_theme->name.'.zip', $zip_content);
						
						if ($theme_directory = $this->extractTheme($sandbox.(string)$addons_theme->name.'.zip', $sandbox))
							$ids_themes[$theme_directory] = (string)$addons_theme->id;

						Tools::deleteDirectory($sandbox);
					}
					Configuration::updateValue('PS_ADDONS_THEMES_IDS', serialize($ids_themes));
				}
			}
		}

		$all_themes = Theme::getThemes();
		$themes = array();
		$other_themes = array();
		foreach ($all_themes as $theme)
		{
			if (file_exists(_PS_ALL_THEMES_DIR_.$theme->directory.'/preview.jpg'))
			{
				$themes[] = array('id' => $theme->id, 'name' => $theme->name, 'preview' => '../themes/'.$theme->directory.'/preview.jpg');
				if ($theme->id == $this->context->shop->id_theme)
				{
					if (file_exists(_PS_ROOT_DIR_.'/config/xml/themes/'.$theme->directory.'.xml'))
						$config_file = _PS_ROOT_DIR_.'/config/xml/themes/'.$theme->directory.'.xml';
					else
						$config_file = _PS_ROOT_DIR_.'/config/xml/themes/default.xml';

					if ($config_file)
					{
						$cur_theme = array();
						$cur_theme['theme_id'] = $theme->id;
						$xml_theme = @simplexml_load_file($config_file);
						foreach ($xml_theme->attributes() as $key => $value)
							$cur_theme['theme_'.$key] = (string)$value;
						foreach ($xml_theme->author->attributes() as $key => $value)
							$cur_theme['author_'.$key] = (string)$value;

						if ($cur_theme['theme_name'] == 'default-bootstrap')
						{
							$cur_theme['tc'] = Module::isEnabled('themeconfigurator');
						}
					}
				}
				else
					$other_themes[] = array('id' => $theme->id, 'name' => $theme->name, 'preview' => '../themes/'.$theme->directory.'/preview.jpg');
			}
			$themes_directory[] = $theme->directory;
		}

		foreach (scandir(_PS_ALL_THEMES_DIR_) as $theme_dir)
		{
			if ($theme_dir[0] != '.' && Validate::isDirName($theme_dir)
				&& is_dir(_PS_ALL_THEMES_DIR_.$theme_dir)
				&& file_exists(_PS_ALL_THEMES_DIR_.$theme_dir.'/preview.jpg')
				&& !in_array($theme_dir, $themes_directory)
			)
			{

				$config_file = false;
				$default_config = _PS_ROOT_DIR_.'/config/xml/themes/default.xml';
				$theme_config = _PS_ROOT_DIR_.'/config/xml/themes/'.$theme_dir.'.xml';

				if (file_exists($theme_config))
					$config_file = $theme_config;
				elseif (file_exists($default_config))
					$config_file = $default_config;

				if ($config_file)
				{
					$theme_installed = $this->importThemeXmlConfig(simplexml_load_file($config_file), $theme_dir);
					foreach ($theme_installed as $item)
						if (Validate::isLoadedObject($item) && file_exists(_PS_ALL_THEMES_DIR_.$item->directory.'/preview.jpg'))
							$themes[] = array('id' => $item->id, 'name' => $item->name, 'preview' => '../themes/'.$item->directory.'/preview.jpg');
				}
			}
		}

		// Employee languages used for link and utm_source
		$lang = new Language($this->context->language->id);
		$iso_lang_uc = strtoupper($lang->iso_code);

		$this->fields_options = array(
			'appearance' => array(
				'title' => $this->l('Your current theme'),
				'icon' => 'icon-html5',
				'tabs' => array(
					'logo' => $this->l('Logo'),
					'logo2' => $this->l('Invoice & Email Logos'),
					'icons' => $this->l('Icons'),
					'mobile' => $this->l('Mobile'),
					),
				'fields' => array(
					'PS_LOGO' => array(
						'title' => $this->l('Header logo'),
						'hint' => $this->l('Will appear on main page. Recommended height: 52px. Maximum height on default theme: 65px.'),
						'type' => 'file',
						'name' => 'PS_LOGO',
						'tab' => 'logo',
						'thumb' => _PS_IMG_.Configuration::get('PS_LOGO')
					),
					'PS_LOGO_MOBILE' => array(
						'title' => $this->l('Header logo for mobile'),
						'desc' =>
							((Configuration::get('PS_LOGO_MOBILE') === false) ? '<span class="light-warning">'.$this->l('Warning: No mobile logo has been defined. The header logo will be used instead.').'</span><br />' : '').
							$this->l('Will appear on the main page of your mobile template. If left undefined, the header logo will be used.'),
						'type' => 'file',
						'name' => 'PS_LOGO_MOBILE',
						'tab' => 'mobile',
						'thumb' => (Configuration::get('PS_LOGO_MOBILE') !== false && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO_MOBILE'))) ? _PS_IMG_.Configuration::get('PS_LOGO_MOBILE') : _PS_IMG_.Configuration::get('PS_LOGO')
					),
					'PS_LOGO_MAIL' => array(
						'title' => $this->l('Mail logo'),
						'desc' =>
							((Configuration::get('PS_LOGO_MAIL') === false) ? '<span class="light-warning">'.$this->l('Warning: No email logo has been identified. The header logo will be used instead.').'</span><br />' : '').
							$this->l('Will appear on email headers. If undefined, the header logo will be used.'),
						'type' => 'file',
						'name' => 'PS_LOGO_MAIL',
						'tab' => 'logo2',
						'thumb' => (Configuration::get('PS_LOGO_MAIL') !== false && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO_MAIL'))) ? _PS_IMG_.Configuration::get('PS_LOGO_MAIL') : _PS_IMG_.Configuration::get('PS_LOGO')
					),
					'PS_LOGO_INVOICE' => array(
						'title' => $this->l('Invoice logo'),
						'desc' =>
							((Configuration::get('PS_LOGO_INVOICE') === false) ? '<span class="light-warning">'.$this->l('Warning: No invoice logo has been defined. The header logo will be used instead.').'</span><br />' : '').
							$this->l('Will appear on invoice headers.').' '.$this->l('Warning: you can use a PNG file for transparency, but it can take up to 1 second per page for processing. Please consider using JPG instead.'),
						'type' => 'file',
						'name' => 'PS_LOGO_INVOICE',
						'tab' => 'logo2',
						'thumb' => (Configuration::get('PS_LOGO_INVOICE') !== false && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO_INVOICE'))) ? _PS_IMG_.Configuration::get('PS_LOGO_INVOICE') : _PS_IMG_.Configuration::get('PS_LOGO')
					),
					'PS_FAVICON' => array(
						'title' => $this->l('Favicon'),
						'hint' => $this->l('Will appear in the address bar of your web browser.'),
						'type' => 'file',
						'name' => 'PS_FAVICON',
						'tab' => 'icons',
						'thumb' => _PS_IMG_.Configuration::get('PS_FAVICON')
					),
					'PS_STORES_ICON' => array(
						'title' => $this->l('Store icon'),
						'hint' => $this->l('Will appear on the store locator (inside Google Maps).').'<br />'.$this->l('Suggested size: 30x30, transparent GIF.'),
						'type' => 'file',
						'name' => 'PS_STORES_ICON',
						'tab' => 'icons',
						'thumb' => _PS_IMG_.Configuration::get('PS_STORES_ICON')
					),
					'PS_ALLOW_MOBILE_DEVICE' => array(
						'title' => $this->l('Enable the mobile theme'),
						'hint' => $this->l('Allows visitors browsing on mobile devices to view a lighter version of your website.'),
						'type' => 'radio',
						'required' => true,
						'validation' => 'isGenericName',
						'tab' => 'mobile',
						'choices' => array(
							0 => $this->l('I\'d like to disable it, please.'),
							1 => $this->l('I\'d like to enable it only on smart phones.'),
							2 => $this->l('I\'d like to enable it only on tablets.'),
							3 => $this->l('I\'d like to enable it on both smart phones and tablets.')
						)
					),
				),
				'after_tabs' => array(
					'cur_theme' => $cur_theme,
				),
				'submit' => array('title' => $this->l('Save'))
			),
			'theme' => array(
				'title' => sprintf($this->l('Select a theme for shop %s'), $this->context->shop->name),
				'description' => (!$this->can_display_themes) ? $this->l('You must select a shop from the above list if you wish to choose a theme.') : '',
				'fields' => array(
					'theme_for_shop' => array(
						'type' => 'theme',
						'themes' => $other_themes,
						'id_theme' => $this->context->shop->id_theme,
						'can_display_themes' => $this->can_display_themes,
						'no_multishop_checkbox' => true,
						'addons_link' => 'http://addons.prestashop.com/en/3-templates-prestashop?utm_source=back-office&utm_medium=theme-button&utm_campaign=back-office-'.$iso_lang_uc,
					),
				),
			),
		);
	}

	public function renderForm()
	{
		$get_available_themes = Theme::getAvailable(false);
		$available_theme_dir = array();
		$selected_theme_dir = null;
		$metas = Meta::getMetas();
		$formated_metas = array();

		foreach ($metas as $meta)
		{
			$meta_object = New Meta($meta['id_meta']);

			$title = $meta['page'];
			if (isset($meta_object->title[(int)$this->context->language->id]) && $meta_object->title[(int)$this->context->language->id] != '')
				$title = $meta_object->title[(int)$this->context->language->id];

			$formated_metas[$meta['id_meta']] = array(
				'title' => $title,
				'left' => 0,
				'right' => 0,
			);
		}

		$image_url = false;
		if ($this->object)
		{
			if ((int)$this->object->id > 0)
			{
				$theme = New Theme((int)$this->object->id);
				$theme_metas = $theme->getMetas();

				// if no theme_meta are found, we must create them
				if (empty($theme_metas))
				{
					$metas = Db::getInstance()->executeS('SELECT id_meta FROM '._DB_PREFIX_.'meta');
					$metas_default = array();
					foreach ($metas as $meta)
					{
						$tmp_meta['id_meta'] = (int)$meta['id_meta'];
						$tmp_meta['left'] = 1;
						$tmp_meta['right'] = 1;
						$metas_default[] = $tmp_meta;
					}
					$theme->updateMetas($metas_default);
					$theme_metas = $theme->getMetas();
				}

				$image_url = '<img alt="preview" src="'.__PS_BASE_URI__.'themes/'.$theme->directory.'/preview.jpg">';
				foreach ($theme_metas as $theme_meta)
				{
					$formated_metas[$theme_meta['id_meta']]['id_theme_meta'] = (int)$theme_meta['id_theme_meta'];
					$formated_metas[$theme_meta['id_meta']]['id_meta'] = (int)$theme_meta['id_meta'];
					$formated_metas[$theme_meta['id_meta']]['left'] = (int)$theme_meta['left_column'];
					$formated_metas[$theme_meta['id_meta']]['right'] = (int)$theme_meta['right_column'];
				}
			}
			$selected_theme_dir = $this->object->directory;
		}

		foreach ($get_available_themes as $k => $dirname)
		{
			$available_theme_dir[$k]['value'] = $dirname;
			$available_theme_dir[$k]['label'] = $dirname;
			$available_theme_dir[$k]['id'] = $dirname;
		};

		$this->fields_form = array(
			'tinymce' => false,
			'legend' => array(
				'title' => $this->l('Theme'),
				'icon' => 'icon-picture'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Name of the theme'),
					'name' => 'name',
					'required' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}',
				),
				array(
					'type' => 'file',
					'label' => $this->l('Preview image for the theme'),
					'name' => 'image_preview',
					'display_image' => true,
					'hint' => sprintf($this->l('Max image size: %1s'), Tools::formatBytes(Tools::getMaxUploadSize())),
					'image' => $image_url,
				),
				array(
					'type' => 'switch',
					'label' => $this->l('Default left column'),
					'name' => 'default_left_column',
					'hint' => $this->l('Choose a default behavior when displaying the column in a new page added by you or by a module.'),
					'values' => array(
						array(
							'id' => 'default_left_column_on',
							'value' => 1,
							'label' => $this->l('Yes')
						),
						array(
							'id' => 'default_left_column_off',
							'value' => 0,
							'label' => $this->l('No')
						)
					),
				),
				array(
					'type' => 'switch',
					'label' => $this->l('Default right column'),
					'name' => 'default_right_column',
					'hint' => $this->l('Choose a default behavior when displaying the column in a new page added by you or by a module.'),
					'values' => array(
						array(
							'id' => 'default_right_column_on',
							'value' => 1,
							'label' => $this->l('Yes')
						),
						array(
							'id' => 'default_right_column_off',
							'value' => 0,
							'label' => $this->l('No')
						)
					),
				),
				array(
					'type' => 'text',
					'label' => $this->l('Number of products per page'),
					'name' => 'product_per_page',
					'hint' => $this->l('This value will be used when activating the theme.'),
				)
			),
			'submit' => array(
				'title' => $this->l('Save'),
			)
		);
		// adding a new theme, you can create a directory, and copy from an existing theme
		if ($this->display == 'add' || !Validate::isLoadedObject($this->object))
		{
			$this->fields_form['input'][] = array(
				'type' => 'text',
				'label' => $this->l('Name of the theme\'s directory'),
				'name' => 'directory',
				'required' => true,
				'hint' => $this->l('If the directory does not exists, it will be created.'),
			);

			$theme_query = Theme::getThemes();
			$this->fields_form['input'][] = array(
				'type' => 'select',
				'name' => 'based_on',
				'label' => $this->l('Copy missing files from existing theme'),
				'hint' => $this->l('If you create a new theme, it\'s recommended that you use the default theme files.'),
				'options' => array(
					'id' => 'id',
					'name' => 'name',
					'default' => array(
						'value' => 0,
						'label' => '-'
					),
					'query' => $theme_query,
				)
			);

			$this->fields_form['input'][] = array(
				'type' => 'switch',
				'label' => $this->l('Responsive'),
				'name' => 'responsive',
				'hint' => $this->l('Please indicate if the theme is adapted to all screen sizes (mobile, tablet, desktop).'),
				'values' => array(
					array(
						'id' => 'responsive_on',
						'value' => 1,
						'label' => $this->l('Yes')
					),
					array(
						'id' => 'responsive_off',
						'value' => 0,
						'label' => $this->l('No')
					)
				),
			);
		}
		else
			$this->fields_form['input'][] = array(
				'type' => 'radio',
				'label' => $this->l('Directory'),
				'name' => 'directory',
				'required' => true,
				'br' => true,
				'values' => $available_theme_dir,
				'selected' => $selected_theme_dir,
				'hint' => $this->l('Please select a valid theme directory.'),
			);

		$list = '';
		if (Tools::getIsset('update'.$this->table))
		{
			$fields_list = array(
				'title' => array(
					'title' => $this->l('Meta'),
					'align' => 'center',
					'width' => 'auto'
				),
				'left' => array(
					'title' => $this->l('Left column'),
					'active' => 'left',
					'type' => 'bool',
					'ajax' => true
				),
				'right' => array(
					'title' => $this->l('Right column'),
					'active' => 'right',
					'type' => 'bool',
					'ajax' => true
				),
			);
			$helper_list = New HelperList();
			$helper_list->tpl_vars = array('icon' => 'icon-columns');
			$helper_list->title = $this->l('Appearance of columns');
			$helper_list->no_link = true;
			$helper_list->shopLinkType = '';
			$helper_list->identifier = 'id_theme_meta';
			$helper_list->table = 'meta';
			$helper_list->tpl_vars['show_filters'] = false;
			$helper_list->currentIndex = $this->context->link->getAdminLink('AdminThemes', false);
			$helper_list->token = Tools::getAdminTokenLite('AdminThemes');
			$list = $helper_list->generateList($formated_metas, $fields_list);
		}

		return parent::renderForm().$list;
	}

	public function renderList()
	{

		return parent::renderList();
	}

	/**
	 * copy $base_theme_dir into $target_theme_dir.
	 *
	 * @param string $base_theme_dir   relative path to base dir
	 * @param string $target_theme_dir relative path to target dir
	 *
	 * @return boolean true if success
	 */
	protected static function copyTheme($base_theme_dir, $target_theme_dir)
	{
		$res = true;
		$base_theme_dir = rtrim($base_theme_dir, '/').'/';
		$base_dir = _PS_ALL_THEMES_DIR_.$base_theme_dir;
		$target_theme_dir = rtrim($target_theme_dir, '/').'/';
		$target_dir = _PS_ALL_THEMES_DIR_.$target_theme_dir;
		$files = scandir($base_dir);

		foreach ($files as $file)
		{
			if (!in_array($file[0], array('.', '..', '.svn')))
			{
				if (is_dir($base_dir.$file))
				{
					if (!is_dir($target_dir.$file))
						mkdir($target_dir.$file, Theme::$access_rights);

					$res &= AdminThemesController::copyTheme($base_theme_dir.$file, $target_theme_dir.$file);
				}
				elseif (!file_exists($target_theme_dir.$file))
					$res &= copy($base_dir.$file, $target_dir.$file);
			}
		}

		return $res;
	}

	public function processAdd()
	{
		if (Tools::getValue('directory') == '' || Tools::getValue('name') == '')
		{
			$this->errors[] = $this->l('Form invalid');
			$this->display = 'form';
			return false;
		}
		if (($new_dir = Tools::getValue('directory')) != '')
		{
			if (!Validate::isDirName($new_dir))
			{
				$this->display = 'add';

				return !($this->errors[] = sprintf(Tools::displayError('"%s" is not a valid directory name'), $new_dir));
			}
			if (Theme::getByDirectory($new_dir))
			{
				$this->display = 'add';

				return !($this->errors[] = Tools::displayError('A directory with this name already exist'));
			}

			if (mkdir(_PS_ALL_THEMES_DIR_.$new_dir, Theme::$access_rights))
				$this->confirmations[] = $this->l('The directory was successfully created.');

			if (0 !== $id_based = (int)Tools::getValue('based_on'))
			{
				$base_theme = new Theme($id_based);
				$this->copyTheme($base_theme->directory, $new_dir);
				$base_theme = new Theme((int)Tools::getValue('based_on'));
			}

			if (isset($_FILES['image_preview']) && $_FILES['image_preview']['error'] == 0)
			{
				if (@getimagesize($_FILES['image_preview']['tmp_name']) && !ImageManager::validateUpload($_FILES['image_preview'], Tools::getMaxUploadSize()))
				{
					move_uploaded_file($_FILES['image_preview']['tmp_name'], _PS_ALL_THEMES_DIR_.$new_dir.'/preview.jpg');
				}
				else
				{
					$this->errors[] = $this->l('Image not valid');
					$this->display = 'form';

					return false;
				}
			}
		}

		$theme = parent::processAdd();
		if ((int)$theme->product_per_page == 0)
		{
			$theme->product_per_page = 1;
			$theme->save();
		}
		if (is_object($theme) && (int)$theme->id > 0)
		{

			$metas = Meta::getMetas();

			foreach ($metas as &$meta)
			{
				$meta['left'] = $theme->default_left_column;
				$meta['right'] = $theme->default_right_column;

			}
			$theme->updateMetas($metas, true);
		}

		return $theme;
	}

	public function processUpdate()
	{
		if (Tools::getIsset('id_theme') && Tools::getIsset('name') && Tools::getIsset('directory'))
		{

			$theme = New Theme((int)Tools::getValue('id_theme'));
			$theme->name = Tools::getValue('name');
			$theme->directory = Tools::getValue('directory');
			$theme->default_left_column = Tools::getValue('default_left_column');
			$theme->default_right_column = Tools::getValue('default_right_column');
			$nb_product_per_page = (int)Tools::getValue('product_per_page');
			if ($nb_product_per_page == 0)
				$nb_product_per_page = 1;

			$theme->product_per_page = $nb_product_per_page;

			if ($this->context->shop->id_theme == (int)Tools::getValue('id_theme'))
				Configuration::updateValue('PS_PRODUCTS_PER_PAGE', $nb_product_per_page);

			if (isset($_FILES['image_preview']) && $_FILES['image_preview']['error'] == 0)
			{
				if (@getimagesize($_FILES['image_preview']['tmp_name']) && !ImageManager::validateUpload($_FILES['image_preview'], 300000))
				{
					move_uploaded_file($_FILES['image_preview']['tmp_name'], _PS_ALL_THEMES_DIR_.$theme->directory.'/preview.jpg');
				}
				else
				{
					$this->errors[] = $this->l('Image not valid');
					$this->display = 'form';

					return false;
				}
			}
			$theme->update();
		}
		Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminThemes').'&conf=29');
	}

	protected function processUpdateOptions()
	{
		parent::processUpdateOptions();

		if (!count($this->errors))
			Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminThemes').'&conf=6');
	}

	public function processDelete()
	{
		$obj = $this->loadObject();
		if ($obj)
		{

			if ($obj->isUsed())
			{
				$this->errors[] = $this->l('The theme is being used by at least one shop. Please choose another theme before continuing.');

				return false;
			}
			$themes = array();
			foreach (Theme::getThemes() as $theme)
			{
				if ($theme->id != $obj->id)
					$themes[] = $theme->directory;
			}

			if (is_dir(_PS_ALL_THEMES_DIR_.$obj->directory) && !in_array($obj->directory, $themes))
				Tools::deleteDirectory(_PS_ALL_THEMES_DIR_.$obj->directory.'/');
			
			$ids_themes = unserialize(Configuration::get('PS_ADDONS_THEMES_IDS'));
			if (array_key_exists($obj->directory, $ids_themes))
				unset($ids_themes[$obj->directory]);
			
			$obj->removeMetas();
		}

		return parent::processDelete();
	}

	public function initPageHeaderToolbar()
	{
		parent::initPageHeaderToolbar();

		if (empty($this->display))
		{
			if (!defined('_PS_HOST_MODE_'))
				$this->page_header_toolbar_btn['import_theme'] = array(
					'href' => self::$currentIndex.'&action=importtheme&token='.$this->token,
					'desc' => $this->l('Add new theme', null, null, false),
					'icon' => 'process-icon-new'
				);
			if (defined('_PS_HOST_MODE_'))
				unset($this->toolbar_btn['new']);
			$this->page_header_toolbar_btn['export_theme'] = array(
				'href' => self::$currentIndex.'&action=exporttheme&token='.$this->token,
				'desc' => $this->l('Export theme', null, null, false),
				'icon' => 'process-icon-export'
			);
		}

		if ($this->display == 'importtheme')
			$this->toolbar_title[] = $this->l('Import theme');
		elseif ($this->display == 'exporttheme')
			$this->toolbar_title[] = $this->l('Export theme');
		else
			$this->toolbar_title[] = $this->l('Theme');

		$title = implode(' '.Configuration::get('PS_NAVIGATION_PIPE').' ', $this->toolbar_title);
		$this->page_header_toolbar_title = $title;
	}

	private function checkParentClass($name)
	{
		if (!$obj = Module::getInstanceByName($name))
			return false;
		if (is_callable(array(
			$obj,
			'validateOrder'
		))
		)
			return false;
		if (is_callable(array(
			$obj,
			'getDateBetween'
		))
		)
			return false;
		if (is_callable(array(
			$obj,
			'getGridEngines'
		))
		)
			return false;
		if (is_callable(array(
			$obj,
			'getGraphEngines'
		))
		)
			return false;
		if (is_callable(array(
			$obj,
			'hookAdminStatsModules'
		))
		)
			return false;
		else
			return true;
	}

	private function checkNames()
	{
		$author = Tools::getValue('name');
		$theme_name = Tools::getValue('theme_name');

		if (!$author || !Validate::isGenericName($author) || strlen($author) > self::MAX_NAME_LENGTH)
			$this->errors[] = $this->l('Please enter a valid author name');
		elseif (!$theme_name || !Validate::isGenericName($theme_name) || strlen($theme_name) > self::MAX_NAME_LENGTH)
			$this->errors[] = $this->l('Please enter a valid theme name');

		if (count($this->errors) > 0)
			return false;

		return true;
	}

	private function checkDocumentation()
	{
		$extensions = array(
			'.pdf',
			'.txt'
		);

		if (isset($_FILES['documentation']) && $_FILES['documentation']['name'] != '')
		{
			$extension = strrchr($_FILES['documentation']['name'], '.');
			$name = Tools::getValue('documentationName');

			if (!in_array($extension, $extensions))
				$this->errors[] = $this->l('File extension must be .txt or .pdf');
			elseif ($_FILES['documentation']['error'] > 0 || $_FILES['documentation']['size'] > 1048576)
				$this->errors[] = $this->l('An error occurred during documentation upload');
			elseif (!$name || !Validate::isGenericName($name) || strlen($name) > self::MAX_NAME_LENGTH)
				$this->errors[] = $this->l('Please enter a valid documentation name');
		}

		if (count($this->errors) > 0)
			return false;

		return true;
	}

	private function checkVersionsAndCompatibility()
	{
		$exp = '#^[0-9]+[.]+[0-9.]*[0-9]$#';

		if (!preg_match('#^[0-9][.][0-9]$#', Tools::getValue('theme_version')) ||
			!preg_match($exp, Tools::getValue('compa_from')) || !preg_match($exp, Tools::getValue('compa_to')) ||
			version_compare(Tools::getValue('compa_from'), Tools::getValue('compa_to')) == 1
		)
			$this->errors[] = $this->l('Syntax error on version field. Only digits and points are allowed and the compatibility should be increasing or equal.');

		if (count($this->errors) > 0)
			return false;

		return true;
	}

	private function checkPostedDatas()
	{
		$mail = Tools::getValue('email');
		$website = Tools::getValue('website');

		if ($mail && !preg_match('#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#', $mail))
			$this->errors[] = $this->l('There is an error in your e-mail syntax!');
		elseif ($website && (!Validate::isURL($website) || !Validate::isAbsoluteUrl($website)))
			$this->errors[] = $this->l('There is an error in your URL syntax!');
		elseif (!$this->checkVersionsAndCompatibility() || !$this->checkNames() || !$this->checkDocumentation())
			return false;
		else
			return true;

		return false;
	}

	private function archiveThisFile($obj, $file, $server_path, $archive_path)
	{
		if (is_dir($server_path.$file))
		{
			$dir = scandir($server_path.$file);
			foreach ($dir as $row)
			{
				if ($row != '.' && $row != '..')
					$this->archiveThisFile($obj, $row, $server_path.$file.'/', $archive_path.$file.'/');
			}
		}
		elseif (!$obj->addFile($server_path.$file, $archive_path.$file))
			$this->error = true;
	}

	private function generateArchive()
	{
		$zip = new ZipArchive();
		$zip_file_name = md5(time()).'.zip';
		if ($zip->open(_PS_CACHE_DIR_.$zip_file_name, ZipArchive::OVERWRITE) === true)
		{
			if (!$zip->addFromString('Config.xml', $this->xml_file))
				$this->errors[] = $this->l('Can\'t create config file.');

			if (isset($_FILES['documentation']))
				if (!$zip->addFile($_FILES['documentation']['tmp_name'], 'doc/'.$_POST['documentation']['name']))
					$this->error = $this->l('Can\'t copy documentation.');

			$this->archiveThisFile($zip, Tools::getValue('theme_directory'), _PS_ALL_THEMES_DIR_, 'themes/');

			foreach ($this->to_export as $row)
			{
				if (!in_array($row, $this->native_modules))
					$this->archiveThisFile($zip, $row, _PS_ROOT_DIR_.'/modules/', 'modules/');
			}

			$zip->close();

			if (!$this->errors)
			{
				if (ob_get_length() > 0)
					ob_end_clean();

				ob_start();
				header('Pragma: public');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Cache-Control: public');
				header('Content-Description: File Transfer');
				header('Content-type: application/octet-stream');
				header('Content-Disposition: attachment; filename="'.$zip_file_name.'"');
				header('Content-Transfer-Encoding: binary');
				ob_end_flush();
				readfile(_PS_CACHE_DIR_.$zip_file_name);
				@unlink(_PS_CACHE_DIR_.$zip_file_name);
				exit;
			}
		}

		$this->errors[] = $this->l('An error occurred during the archive generation');
	}

	private function generateXML($theme_to_export, $metas)
	{
		$theme = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><!-- Copyright Prestashop --><theme></theme>');
		$theme->addAttribute('version', Tools::getValue('theme_version'));
		$theme->addAttribute('name', Tools::htmlentitiesUTF8(Tools::getValue('theme_name')));
		$theme->addAttribute('directory', Tools::htmlentitiesUTF8(Tools::getValue('theme_directory')));
		$author = $theme->addChild('author');
		$author->addAttribute('name', Tools::htmlentitiesUTF8(Tools::getValue('name')));
		$author->addAttribute('email', Tools::htmlentitiesUTF8(Tools::getValue('email')));
		$author->addAttribute('url', Tools::htmlentitiesUTF8(Tools::getValue('website')));

		$descriptions = $theme->addChild('descriptions');
		$languages = Language::getLanguages();
		foreach ($languages as $language)
		{
			$val = Tools::htmlentitiesUTF8(Tools::getValue('body_title_'.$language['id_lang']));
			$description = $descriptions->addChild('description', Tools::htmlentitiesUTF8($val));
			$description->addAttribute('iso', $language['iso_code']);
		}


		$variations = $theme->addChild('variations');

		$variation = $variations->addChild('variation');
		$variation->addAttribute('name', Tools::htmlentitiesUTF8(Tools::getValue('theme_name')));
		$variation->addAttribute('directory', Tools::getValue('theme_directory'));
		$variation->addAttribute('responsive', $theme_to_export->responsive);
		$variation->addAttribute('default_left_column', $theme_to_export->default_left_column);
		$variation->addAttribute('default_right_column', $theme_to_export->default_right_column);
		$variation->addAttribute('product_per_page', $theme_to_export->product_per_page);
		$variation->addAttribute('from', Tools::getValue('compa_from'));
		$variation->addAttribute('to', Tools::getValue('compa_to'));

		$docs = $theme->addChild('docs');
		if (isset($this->user_doc))
			foreach ($this->user_doc as $row)
			{
				$array = explode('¤', $row);
				$doc = $docs->addChild('doc');
				$doc->addAttribute('name', $array[0]);
				$doc->addAttribute('path', $array[1]);
			}

		$metas_xml = $theme->addChild('metas');

		foreach ($metas as $row)
		{
			$meta_obj = New Meta((int)$row['id_meta']);

			$meta_xml = $metas_xml->addChild('meta');
			$meta_xml->addAttribute('meta_page', $meta_obj->page);
			$meta_xml->addAttribute('left', $row['left_column']);
			$meta_xml->addAttribute('right', $row['right_column']);
		}
		$modules = $theme->addChild('modules');
		if (isset($this->to_export))
			foreach ($this->to_export as $row)
			{
				if (!in_array($row, $this->native_modules))
				{
					$module = $modules->addChild('module');
					$module->addAttribute('action', 'install');
					$module->addAttribute('name', $row);
				}
			}
		foreach ($this->to_enable as $row)
		{
			$module = $modules->addChild('module');
			$module->addAttribute('action', 'enable');
			$module->addAttribute('name', $row);
		}
		foreach ($this->to_disable as $row)
		{
			$module = $modules->addChild('module');
			$module->addAttribute('action', 'disable');
			$module->addAttribute('name', $row);
		}

		$hooks = $modules->addChild('hooks');
		foreach ($this->to_hook as $row)
		{
			$array = explode(';', $row);
			$hook = $hooks->addChild('hook');
			$hook->addAttribute('module', $array[0]);
			$hook->addAttribute('hook', $array[1]);
			$hook->addAttribute('position', $array[2]);
			if (!empty($array[3]))
				$hook->addAttribute('exceptions', $array[3]);
		}

		$images = $theme->addChild('images');
		foreach ($this->image_list as $row)
		{
			$array = explode(';', $row);
			$image = $images->addChild('image');
			$image->addAttribute('name', Tools::htmlentitiesUTF8($array[0]));
			$image->addAttribute('width', $array[1]);
			$image->addAttribute('height', $array[2]);
			$image->addAttribute('products', $array[3]);
			$image->addAttribute('categories', $array[4]);
			$image->addAttribute('manufacturers', $array[5]);
			$image->addAttribute('suppliers', $array[6]);
			$image->addAttribute('scenes', $array[7]);
		}
		$this->xml_file = $theme->asXML();
	}

	public function processExportTheme()
	{
		if (Tools::isSubmit('name'))
		{
			if ($this->checkPostedDatas())
			{
				$filename = Tools::htmlentitiesUTF8($_FILES['documentation']['name']);
				$name = Tools::htmlentitiesUTF8(Tools::getValue('documentationName'));
				$this->user_doc = array($name.'¤doc/'.$filename);


				$table = Db::getInstance()->executeS('
			SELECT name, width, height, products, categories, manufacturers, suppliers, scenes
			FROM `'._DB_PREFIX_.'image_type`');

				$this->image_list = array();
				foreach ($table as $row)
				{
					$this->image_list[] = $row['name'].';'.$row['width'].';'.$row['height'].';'.
						($row['products'] == 1 ? 'true' : 'false').';'.
						($row['categories'] == 1 ? 'true' : 'false').';'.
						($row['manufacturers'] == 1 ? 'true' : 'false').';'.
						($row['suppliers'] == 1 ? 'true' : 'false').';'.
						($row['scenes'] == 1 ? 'true' : 'false');
				}

				$id_shop = Db::getInstance()->getValue('SELECT `id_shop` FROM `'._DB_PREFIX_.'shop` WHERE `id_theme` = '.(int)Tools::getValue('id_theme_export'));

				// Select the list of module for this shop
				$this->module_list = Db::getInstance()->executeS('
				SELECT m.`id_module`, m.`name`, m.`active`, ms.`id_shop`
				FROM `'._DB_PREFIX_.'module` m
				LEFT JOIN `'._DB_PREFIX_.'module_shop` ms On (m.`id_module` = ms.`id_module`)
				WHERE ms.`id_shop` = '.(int)$id_shop.'
			');

				// Select the list of hook for this shop
				$this->hook_list = Db::getInstance()->executeS('
				SELECT h.`id_hook`, h.`name` as name_hook, hm.`position`, hm.`id_module`, m.`name` as name_module, GROUP_CONCAT(hme.`file_name`, ",") as exceptions
				FROM `'._DB_PREFIX_.'hook` h
				LEFT JOIN `'._DB_PREFIX_.'hook_module` hm ON hm.`id_hook` = h.`id_hook`
				LEFT JOIN `'._DB_PREFIX_.'module` m ON hm.`id_module` = m.`id_module`
				LEFT OUTER JOIN `'._DB_PREFIX_.'hook_module_exceptions` hme ON (hme.`id_module` = hm.`id_module` AND hme.`id_hook` = h.`id_hook`)
				WHERE hm.`id_shop` = '.(int)$id_shop.'
				GROUP BY `id_module`, `id_hook`
				ORDER BY `name_module`
			');

				$this->native_modules = $this->getNativeModule();

				foreach ($this->hook_list as &$row)
				{
					$row['exceptions'] = trim(preg_replace('/(,,+)/', ',', $row['exceptions']), ',');
				}

				$this->to_install = array();
				$this->to_enable = array();
				$this->to_hook = array();

				foreach ($this->module_list as $array)
				{
					if (!self::checkParentClass($array['name']))
						continue;
					if (in_array($array['name'], $this->native_modules))
					{
						if ($array['active'] == 1)
							$this->to_enable[] = $array['name'];
						else
							$this->to_disable[] = $array['name'];
					}
					elseif ($array['active'] == 1)
						$this->to_install[] = $array['name'];
				}
				foreach ($this->native_modules as $str)
				{
					$flag = 0;
					if (!self::checkParentClass($str))
						continue;
					foreach ($this->module_list as $tmp)
					{
						if (in_array($str, $tmp))
						{
							$flag = 1;
							break;
						}
					}
					if ($flag == 0)
						$this->to_disable[] = $str;
				}

				foreach ($_POST as $key => $value)
				{
					if (strncmp($key, 'modulesToExport_module', strlen('modulesToExport_module')) == 0)
					{
						$this->to_export[] = $value;
					}
				}

				if ($this->to_install)
					foreach ($this->to_install as $string)
					{
						foreach ($this->hook_list as $tmp)
						{
							if ($tmp['name_module'] == $string)
								$this->to_hook[] = $string.';'.$tmp['name_hook'].';'.$tmp['position'].';'.$tmp['exceptions'];
						}
					}
				if ($this->to_enable)
					foreach ($this->to_enable as $string)
					{
						foreach ($this->hook_list as $tmp)
						{
							if ($tmp['name_module'] == $string)
								$this->to_hook[] = $string.';'.$tmp['name_hook'].';'.$tmp['position'].';'.$tmp['exceptions'];
						}
					}

				$theme_to_export = New Theme((int)Tools::getValue('id_theme_export'));
				$metas = $theme_to_export->getMetas();

				$this->generateXML($theme_to_export, $metas);
				$this->generateArchive();
			}
			else
				$this->display = 'exporttheme';
		}
		else
			$this->display = 'exporttheme';
	}

	private function renderExportTheme1()
	{
		$to_install = array();

		$module_list = Db::getInstance()->executeS('
			SELECT m.`id_module`, m.`name`, m.`active`, ms.`id_shop`
			FROM `'._DB_PREFIX_.'module` m
			LEFT JOIN `'._DB_PREFIX_.'module_shop` ms On (m.`id_module` = ms.`id_module`)
			WHERE ms.`id_shop` = '.(int)$this->context->shop->id.'
		');

		// Select the list of hook for this shop
		$hook_list = Db::getInstance()->executeS('
			SELECT h.`id_hook`, h.`name` as name_hook, hm.`position`, hm.`id_module`, m.`name` as name_module, GROUP_CONCAT(hme.`file_name`, ",") as exceptions
			FROM `'._DB_PREFIX_.'hook` h
			LEFT JOIN `'._DB_PREFIX_.'hook_module` hm ON hm.`id_hook` = h.`id_hook`
			LEFT JOIN `'._DB_PREFIX_.'module` m ON hm.`id_module` = m.`id_module`
			LEFT OUTER JOIN `'._DB_PREFIX_.'hook_module_exceptions` hme ON (hme.`id_module` = hm.`id_module` AND hme.`id_hook` = h.`id_hook`)
			WHERE hm.`id_shop` = '.(int)$this->context->shop->id.'
			GROUP BY `id_module`, `id_hook`
			ORDER BY `name_module`
		');

		foreach ($hook_list as &$row)
		{
			$row['exceptions'] = trim(preg_replace('/(,,+)/', ',', $row['exceptions']), ',');
		}

		$native_modules = $this->getNativeModule();

		foreach ($module_list as $array)
		{
			if (!self::checkParentClass($array['name']))
				continue;
			if (in_array($array['name'], $native_modules))
			{
				if ($array['active'] == 1)
					$to_enable[] = $array['name'];
				else
					$to_disable[] = $array['name'];
			}
			elseif ($array['active'] == 1)
				$to_install[] = $array['name'];
		}
		foreach ($native_modules as $str)
		{
			$flag = 0;
			if (!$this->checkParentClass($str))
				continue;
			foreach ($module_list as $tmp)
			{
				if (in_array($str, $tmp))
				{
					$flag = 1;
					break;
				}
			}
			if ($flag == 0)
				$to_disable[] = $str;
		}

		$employee = $this->context->employee;
		$mail = Tools::getValue('email') ? Tools::getValue('email') : $employee->email;
		$author = Tools::getValue('author_name') ? Tools::getValue('author_name') : $employee->firstname.' '.$employee->lastname;
		$website = Tools::getValue('website') ? Tools::getValue('website') : Tools::getHttpHost(true);

		$this->formatHelperArray($to_install);

		$theme = New Theme(Tools::getValue('id_theme_export'));

		$fields_form = array(
			'form' => array(
				'tinymce' => false,
				'legend' => array(
					'title' => $this->l('Theme configuration'),
					'icon' => 'icon-picture'
				),
				'input' => array(
					array(
						'type' => 'hidden',
						'name' => 'id_theme_export'
					),
					array(
						'type' => 'text',
						'name' => 'name',
						'label' => $this->l('Name'),
					),
					array(
						'type' => 'text',
						'name' => 'email',
						'label' => $this->l('Email'),
					),
					array(
						'type' => 'text',
						'name' => 'website',
						'label' => $this->l('Website'),
					),
					array(
						'type' => 'text',
						'name' => 'theme_name',
						'label' => $this->l('Theme name'),
					),
					array(
						'type' => 'text',
						'name' => 'theme_directory',
						'label' => $this->l('Theme directory'),
					),
					array(
						'type' => 'text',
						'name' => 'body_title',
						'lang' => true,
						'label' => $this->l('Description'),
					),
					array(
						'type' => 'text',
						'name' => 'theme_version',
						'label' => $this->l('Theme version'),
					),
					array(
						'type' => 'text',
						'name' => 'compa_from',
						'label' => $this->l('Compatible from'),
					),
					array(
						'type' => 'text',
						'name' => 'compa_to',
						'label' => $this->l('Compatible to'),
					),
					array(
						'type' => 'file',
						'name' => 'documentation',
						'label' => $this->l('Documentation'),
					),
					array(
						'type' => 'text',
						'name' => 'documentationName',
						'label' => $this->l('Documentation name'),
					),
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)
			)
		);

		if (count($to_install) > 0)
		{
			$fields_form['form']['input'][] = array(
				'type' => 'checkbox',
				'label' => $this->l('Select the theme\'s modules you wish to export'),
				'values' => array(
					'query' => $this->formatHelperArray($to_install),
					'id' => 'id',
					'name' => 'name'
				),
				'name' => 'modulesToExport',
			);
		}

		$default_language = (int)$this->context->language->id;
		$languages = $this->getLanguages();

		foreach ($languages as $language)
		{
			$fields_value['body_title'][$language['id_lang']] = '';
		}

		$helper = new HelperForm();
		$helper->languages = $languages;
		$helper->default_form_language = $default_language;
		$fields_value['name'] = $author;
		$fields_value['email'] = $mail;
		$fields_value['website'] = $website;
		$fields_value['theme_name'] = $theme->name;
		$fields_value['theme_directory'] = $theme->directory;
		$fields_value['theme_version'] = '1.0';
		$fields_value['compa_from'] = _PS_VERSION_;
		$fields_value['compa_to'] = _PS_VERSION_;
		$fields_value['id_theme_export'] = Tools::getValue('id_theme_export');
		$fields_value['documentationName'] = $this->l('documentation');

		$toolbar_btn['save'] = array(
			'href' => '',
			'desc' => $this->l('Save')
		);

		$helper->currentIndex = $this->context->link->getAdminLink('AdminThemes', false).'&action=exporttheme';
		$helper->token = Tools::getAdminTokenLite('AdminThemes');
		$helper->show_toolbar = true;
		$helper->fields_value = $fields_value;
		$helper->toolbar_btn = $toolbar_btn;
		$helper->override_folder = $this->tpl_folder;

		return $helper->generateForm(array($fields_form));
	}

	public function renderExportTheme()
	{
		if (Tools::getIsset('id_theme_export') && (int)Tools::getValue('id_theme_export') > 0)
			return $this->renderExportTheme1();

		$theme_list = Theme::getThemes();
		$fields_form = array(
			'form' => array(
				'tinymce' => false,
				'legend' => array(
					'title' => $this->l('Theme'),
					'icon' => 'icon-picture'
				),
				'input' => array(
					array(
						'type' => 'select',
						'name' => 'id_theme_export',
						'label' => $this->l('Choose the theme you want to export'),
						'options' => array(
							'id' => 'id',
							'name' => 'name',
							'query' => $theme_list,
						)

					),
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)
			)
		);

		$toolbar_btn['save'] = array(
			'href' => '#',
			'desc' => $this->l('Export')
		);

		$fields_value['id_theme_export'] = array();
		$helper = new HelperForm();

		$helper->currentIndex = $this->context->link->getAdminLink('AdminThemes', false).'&action=exporttheme';
		$helper->token = Tools::getAdminTokenLite('AdminThemes');
		$helper->show_toolbar = true;
		$helper->fields_value = $fields_value;
		$helper->toolbar_btn = $toolbar_btn;
		$helper->override_folder = $this->tpl_folder;

		return $helper->generateForm(array($fields_form));
	}

	private function checkXmlFields($sandbox)
	{
		if (!file_exists($sandbox.'uploaded/Config.xml') || !$xml = simplexml_load_file($sandbox.'uploaded/Config.xml'))
			return false;
		if (!$xml['version'] || !$xml['name'])
			return false;
		foreach ($xml->variations->variation as $val)
		{
			if (!$val['name'] || !$val['directory'] || !$val['from'] || !$val['to'])
				return false;
		}
		foreach ($xml->modules->module as $val)
		{
			if (!$val['action'] || !$val['name'])
				return false;
		}
		foreach ($xml->modules->hooks->hook as $val)
		{
			if (!$val['module'] || !$val['hook'] || !$val['position'])
				return false;
		}

		return true;
	}

	private function recurseCopy($src, $dst)
	{
		if (!$dir = opendir($src))
			return;
		if (!file_exists($dst))
			mkdir($dst);
		while (($file = readdir($dir)) !== false)
		{
			if (strncmp($file, '.', 1) != 0)
			{
				if (is_dir($src.'/'.$file))
					self::recurseCopy($src.'/'.$file, $dst.'/'.$file);
				elseif (is_readable($src.'/'.$file) && $file != 'Thumbs.db' && $file != '.DS_Store' && substr($file, -1) != '~')
					copy($src.'/'.$file, $dst.'/'.$file);
			}
		}
		closedir($dir);
	}

	public function processImportTheme()
	{
		$this->display = 'importtheme';
		if (defined('_PS_HOST_MODE_'))
			return true;
		if (isset($_FILES['themearchive']) && isset($_POST['filename']) && Tools::isSubmit('theme_archive_server'))
		{
			$uniqid = uniqid();
			$sandbox = _PS_CACHE_DIR_.'sandbox'.DIRECTORY_SEPARATOR.$uniqid.DIRECTORY_SEPARATOR;
			mkdir($sandbox);
			$archive_uploaded = false;

			if (Tools::getValue('filename') != '')
			{
				$uploader = new Uploader('themearchive');
				$uploader->setAcceptTypes(array('zip'));
				$uploader->setSavePath($sandbox);
				$file = $uploader->process('uploaded.zip');

				if ($file[0]['error'] === 0)
				{
					if (Tools::ZipTest($sandbox.'uploaded.zip'))
						$archive_uploaded = true;
					else
						$this->errors[] = $this->l('Zip file seems to be broken');
				}
				else
					$this->errors[] = $file[0]['error'];

			}
			elseif (Tools::getValue('themearchiveUrl') != '')
			{
				if (!Validate::isModuleUrl($url = Tools::getValue('themearchiveUrl'), $this->errors))
					$this->errors[] = $this->l('Only zip files are allowed');
				elseif (!move_uploaded_file($url, $sandbox.'uploaded.zip'))
					$this->errors[] = $this->l('Error during the file download');
				elseif (Tools::ZipTest($sandbox.'uploaded.zip'))
					$archive_uploaded = true;
				else
					$this->errors[] = $this->l('Zip file seems to be broken');
			}
			elseif (Tools::getValue('theme_archive_server') != '')
			{
				$filename = _PS_ALL_THEMES_DIR_.Tools::getValue('theme_archive_server');
				if (substr($filename, -4) != '.zip')
					$this->errors[] = $this->l('Only zip files are allowed');
				elseif (!copy($filename, $sandbox.'uploaded.zip'))
					$this->errors[] = $this->l('An error has occurred during the file copy.');
				elseif (Tools::ZipTest($sandbox.'uploaded.zip'))
					$archive_uploaded = true;
				else
					$this->errors[] = $this->l('Zip file seems to be broken');
			}
			else
				$this->errors[] = $this->l('You must upload or enter a location of your zip');

			if ($archive_uploaded)
				$this->extractTheme($sandbox.'uploaded.zip', $sandbox);

			Tools::deleteDirectory($sandbox);

			if (count($this->errors) > 0)
				$this->display = 'importtheme';
			else
				Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminThemes').'&conf=18');
		}
	}
	
	protected function extractTheme($theme_zip_file, $sandbox)
	{
		if (!Tools::ZipExtract($theme_zip_file, $sandbox.'uploaded/'))
			$this->errors[] = $this->l('Error during zip extraction');
		else
		{
			if (!$this->checkXmlFields($sandbox))
				$this->errors[] = $this->l('Bad configuration file');
			else
			{
				$imported_theme = $this->importThemeXmlConfig(simplexml_load_file($sandbox.'uploaded/Config.xml'));
				foreach ($imported_theme as $theme)
				{
					if (Validate::isLoadedObject($theme))
					{
						if (!copy($sandbox.'uploaded/Config.xml', _PS_ROOT_DIR_.'/config/xml/themes/'.$theme->directory.'.xml'))
							$this->errors[] = $this->l('Can\'t copy configuration file');

						$target_dir = _PS_ALL_THEMES_DIR_.$theme->directory;

						$theme_doc_dir = $target_dir.'/docs/';
						if (file_exists($theme_doc_dir))
							Tools::deleteDirectory($theme_doc_dir);

						Tools::recurseCopy($sandbox.'uploaded/themes/'.$theme->directory, $target_dir);
						Tools::recurseCopy($sandbox.'uploaded/doc/', $theme_doc_dir);
						Tools::recurseCopy($sandbox.'uploaded/modules/', _PS_MODULE_DIR_);
					}
					else
						$this->errors[] = $theme;
				}
			}
		}
		if (!count($this->errors))
			return $theme->directory;
		return false;
	}

	protected function isThemeInstalled($theme_name)
	{
		$themes = Theme::getThemes();

		foreach ($themes as $theme_object)
		{
			if ($theme_object->name == $theme_name)
				return true;
		}
		return false;
	}

	/**
	 * @param SimpleXMLElement	$xml
	 * @param bool 				$theme_dir only used if the theme directory to import is already located on the shop
	 *
	 * @return array|string		return array of themes on success, otherwise the error as a string is returned
	 */
	protected function importThemeXmlConfig(SimpleXMLElement $xml, $theme_dir = false)
	{
		$new_theme_array = array();
		foreach ($xml->variations->variation as $variation)
		{
			$name = strval($variation['name']);

			$new_theme = new Theme();
			$new_theme->name = $name;

			$new_theme->directory = strval($variation['directory']);

			if ($theme_dir)
			{
				$new_theme->name = $theme_dir;
				$new_theme->directory = $theme_dir;
			}

			if ($this->isThemeInstalled($new_theme->name))
			{
				$new_theme_array[] = sprintf($this->l('Theme %s already installed.'), $new_theme->name);
				continue;
			}

			$new_theme->product_per_page = Configuration::get('PS_PRODUCTS_PER_PAGE');

			if (isset($variation['product_per_page']))
				$new_theme->product_per_page = intval($variation['product_per_page']);

			$new_theme->responsive = false;
			if (isset($variation['responsive']))
				$new_theme->responsive = (bool)strval($variation['responsive']);

			$new_theme->default_left_column = true;
			$new_theme->default_right_column = true;

			if (isset($variation['default_left_column']))
				$new_theme->default_left_column = (bool)strval($variation['default_left_column']);

			if (isset($variation['default_right_column']))
				$new_theme->default_right_column = (bool)strval($variation['default_right_column']);

			$fill_default_meta = true;
			$metas_xml = array();
			if ($xml->metas->meta)
			{
				foreach ($xml->metas->meta as $meta)
				{
					$meta_id = Db::getInstance()->getValue('SELECT id_meta FROM '._DB_PREFIX_.'meta WHERE page=\''.pSQL($meta['meta_page']).'\'');
					if ((int)$meta_id > 0)
					{
						$tmp_meta = array();
						$tmp_meta['id_meta'] = (int)$meta_id;
						$tmp_meta['left'] = intval($meta['left']);
						$tmp_meta['right'] = intval($meta['right']);
						$metas_xml[(int)$meta_id] = $tmp_meta;
					}
				}
				$fill_default_meta = false;
				if (count($xml->metas->meta) < (int)Db::getInstance()->getValue('SELECT count(*) FROM '._DB_PREFIX_.'meta'))
					$fill_default_meta = true;

			}

			if ($fill_default_meta == true)
			{
				$metas = Db::getInstance()->executeS('SELECT id_meta FROM '._DB_PREFIX_.'meta');
				foreach ($metas as $meta)
				{
					if (!isset($metas_xml[(int)$meta['id_meta']]))
					{
						$tmp_meta['id_meta'] = (int)$meta['id_meta'];
						$tmp_meta['left'] = $new_theme->default_left_column;
						$tmp_meta['right'] = $new_theme->default_right_column;
						$metas_xml[(int)$meta['id_meta']] = $tmp_meta;
					}
				}
			}
			if (!is_dir(_PS_ALL_THEMES_DIR_.$new_theme->directory))
				mkdir(_PS_ALL_THEMES_DIR_.$new_theme->directory);

			$new_theme->add();

			if ($new_theme->id > 0)
			{
				$new_theme->updateMetas($metas_xml);
				$new_theme_array[] = $new_theme;
			}
			else
				$new_theme_array[] = sprintf($this->l('Error while installing theme %s'), $new_theme->name);

		}

		return $new_theme_array;
	}

	public function renderImportTheme()
	{
		$toolbar_btn['save'] = array(
			'href' => '#',
			'desc' => $this->l('Save')
		);

		$fields_form[0] = array(
			'form' => array(
				'tinymce' => false,
				'legend' => array(
					'title' => $this->l('Import from your computer'),
					'icon' => 'icon-picture'
				),
				'input' => array(
					array(
						'type' => 'file',
						'label' => $this->l('Zip file'),
						'desc' => $this->l('Browse your computer files and select the Zip file for your new theme.'),
						'name' => 'themearchive'
					),
				),
				'submit' => array(
					'id' => 'zip',
					'title' => $this->l('Save'),
				)
			),
		);

		$fields_form[1] = array(
			'form' => array(
				'tinymce' => false,
				'legend' => array(
					'title' => $this->l('Import from the web'),
					'icon' => 'icon-picture'
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('Archive URL'),
						'desc' => $this->l('Indicate the complete URL to an online Zip file that contains your new theme. For instance, "http://example.com/files/theme.zip".'),
						'name' => 'themearchiveUrl'
					),
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)
			),
		);

		$theme_archive_server = array();
		$files = scandir(_PS_ALL_THEMES_DIR_);
		$theme_archive_server[] = '-';

		foreach ($files as $file)
		{
			if (is_file(_PS_ALL_THEMES_DIR_.$file) && substr(_PS_ALL_THEMES_DIR_.$file, -4) == '.zip')
			{
				$theme_archive_server[] = array(
					'id' => basename(_PS_ALL_THEMES_DIR_.$file),
					'name' => basename(_PS_ALL_THEMES_DIR_.$file)
				);
			}
		}

		$fields_form[2] = array(
			'form' => array(
				'tinymce' => false,
				'legend' => array(
					'title' => $this->l('Import from FTP'),
					'icon' => 'icon-picture'
				),
				'input' => array(
					array(
						'type' => 'select',
						'label' => $this->l('Select the archive'),
						'name' => 'theme_archive_server',
						'desc' => $this->l('This selector lists the Zip files that you uploaded in the \'/themes\' folder.'),
						'options' => array(
							'id' => 'id',
							'name' => 'name',
							'query' => $theme_archive_server,
						)
					),
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)
			),
		);

		$this->context->smarty->assign(
			array(
				'add_new_theme_href' => self::$currentIndex.'&addtheme&token='.$this->token,
				'add_new_theme_label' => $this->l('Create new theme')
			)
		);

		$create_new_theme_panel = $this->context->smarty->fetch('controllers/themes/helpers/view/importtheme_view.tpl');

		$helper = new HelperForm();

		$helper->currentIndex = $this->context->link->getAdminLink('AdminThemes', false).'&action=importtheme';
		$helper->token = Tools::getAdminTokenLite('AdminThemes');
		$helper->show_toolbar = true;
		$helper->toolbar_btn = $toolbar_btn;
		$helper->fields_value['themearchiveUrl'] = '';
		$helper->fields_value['theme_archive_server'] = array();
		$helper->multiple_fieldsets = true;
		$helper->override_folder = $this->tpl_folder;
		$helper->languages = $this->getLanguages();
		$helper->default_form_language = (int)$this->context->language->id;

		return $helper->generateForm($fields_form).$create_new_theme_panel;
	}

	public function initContent()
	{
		if ($this->display == 'list')
			$this->display = '';
		if (isset($this->display) && method_exists($this, 'render'.$this->display))
		{
			$this->content .= $this->initPageHeaderToolbar();

			$this->content .= $this->{'render'.$this->display}();
			$this->context->smarty->assign(array(
				'content' => $this->content,
				'show_page_header_toolbar' => $this->show_page_header_toolbar,
				'page_header_toolbar_title' => $this->page_header_toolbar_title,
				'page_header_toolbar_btn' => $this->page_header_toolbar_btn
			));
		}
		else
		{

			$content = '';
			if (Configuration::hasKey('PS_LOGO') && trim(Configuration::get('PS_LOGO')) != ''
				&& file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO')) && filesize(_PS_IMG_DIR_.Configuration::get('PS_LOGO')))
			{
				list($width, $height, $type, $attr) = getimagesize(_PS_IMG_DIR_.Configuration::get('PS_LOGO'));
				Configuration::updateValue('SHOP_LOGO_HEIGHT', (int)round($height));
				Configuration::updateValue('SHOP_LOGO_WIDTH', (int)round($width));
			}
			if (Configuration::get('PS_LOGO_MOBILE') && trim(Configuration::get('PS_LOGO_MOBILE')) != '' 
				&& file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO_MOBILE')) && filesize(_PS_IMG_DIR_.Configuration::get('PS_LOGO_MOBILE')))
			{
				list($width, $height, $type, $attr) = getimagesize(_PS_IMG_DIR_.Configuration::get('PS_LOGO_MOBILE'));
				Configuration::updateValue('SHOP_LOGO_MOBILE_HEIGHT', (int)round($height));
				Configuration::updateValue('SHOP_LOGO_MOBILE_WIDTH', (int)round($width));
			}

			$this->content .= $content;

			return parent::initContent();
		}
	}

	public function ajaxProcessGetAddonsThemes()
	{
		$parent_domain = Tools::getHttpHost(true).substr($_SERVER['REQUEST_URI'], 0, -1 * strlen(basename($_SERVER['REQUEST_URI'])));
		$iso_lang = $this->context->language->iso_code;
		$iso_currency = $this->context->currency->iso_code;
		$iso_country = $this->context->country->iso_code;
		$activity = Configuration::get('PS_SHOP_ACTIVITY');
		$addons_url = 'http://addons.prestashop.com/iframe/search-1.6.php?psVersion='._PS_VERSION_.'&onlyThemes=1&isoLang='.$iso_lang.'&isoCurrency='.$iso_currency.'&isoCountry='.$iso_country.'&activity='.(int)$activity.'&parentUrl='.$parent_domain;

		die(Tools::file_get_contents($addons_url));
	}

	/**
	 * This function checks if the theme designer has thunk to make his theme compatible 1.4,
	 * and noticed it on the $theme_dir/config.xml file. If not, some new functionnalities has
	 * to be desactivated
	 *
	 * @since 1.4
	 *
	 * @param string $theme_dir theme directory
	 *
	 * @return boolean Validity is ok or not
	 */
	protected function _isThemeCompatible($theme_dir)
	{
		$return = true;
		$check_version = AdminThemes::$check_features_version;

		if (!is_file(_PS_ALL_THEMES_DIR_.$theme_dir.'/config.xml'))
		{
			$this->errors[] = Tools::displayError('config.xml is missing in your theme path.').'<br/>';
			$xml = null;
		}
		else
		{
			$xml = @simplexml_load_file(_PS_ALL_THEMES_DIR_.$theme_dir.'/config.xml');
			if (!$xml)
				$this->errors[] = Tools::displayError('config.xml is not a valid xml file in your theme path.').'<br/>';
		}
		// will be set to false if any version node in xml is correct
		$xml_version_too_old = true;

		// foreach version in xml file,
		// node means feature, attributes has to match
		// the corresponding value in AdminThemes::$check_features[feature] array
		$xmlArray = simpleXMLToArray($xml);
		foreach ($xmlArray as $version)
		{
			if (isset($version['value']) && version_compare($version['value'], $check_version) >= 0)
			{
				foreach (AdminThemes::$check_features as $codeFeature => $arrConfigToCheck)
				{
					foreach ($arrConfigToCheck['attributes'] as $attr => $v)
					{
						if (!isset($version[$codeFeature]) || !isset($version[$codeFeature][$attr]) || $version[$codeFeature][$attr] != $v['value'])
							if (!$this->_checkConfigForFeatures($codeFeature, $attr)) // feature missing in config.xml file, or wrong attribute value
								$return = false;
					}
				}
				$xml_version_too_old = false;
			}
		}
		if ($xml_version_too_old && !$this->_checkConfigForFeatures(array_keys(AdminThemes::$check_features)))
		{
			$this->errors[] .= Tools::displayError('config.xml theme file has not been created for this version of PrestaShop.');
			$return = false;
		}

		return $return;
	}

	/**
	 * _checkConfigForFeatures
	 *
	 * @param array $arrFeature array of feature code to check
	 * @param mixed $configItem will precise the attribute which not matches. If empty, will check every attributes
	 *
	 * @return error message, or null if disabled
	 */
	protected function _checkConfigForFeatures($arrFeatures, $configItem = array())
	{
		$return = true;
		if (is_array($configItem))
		{
			foreach ($arrFeatures as $feature)
			{
				if (!count($configItem))
					$configItem = array_keys(AdminThemes::$check_features[$feature]['attributes']);
			}
			foreach ($configItem as $attr)
			{
				$check = $this->_checkConfigForFeatures($arrFeatures, $attr);
				if ($check == false)
					$return = false;
			}

			return $return;
		}

		$return = true;
		if (!is_array($arrFeatures))
			$arrFeatures = array($arrFeatures);

		foreach ($arrFeatures as $feature)
		{
			$arrConfigToCheck = AdminThemes::$check_features[$feature]['attributes'][$configItem]['check_if_not_valid'];
			foreach ($arrConfigToCheck as $config_key => $config_val)
			{
				$config_get = Configuration::get($config_key);
				if ($config_get != $config_val)
				{
					$this->errors[] = Tools::displayError(AdminThemes::$check_features[$feature]['error']).'.'
						.(!empty(AdminThemes::$check_features[$feature]['tab'])
							? ' <a href="?tab='.AdminThemes::$check_features[$feature]['tab'].'&amp;token='
							.Tools::getAdminTokenLite(AdminThemes::$check_features[$feature]['tab']).'" ><u>'
							.Tools::displayError('You can disable this function.')
							.'</u></a>' : ''
						).'<br/>';
					$return = false;
					break; // break for this attributes
				}
			}
		}

		return $return;
	}

	/**
	 *
	 * @param int $type
	 * $type = 0 both native & partner (default)
	 * $type = 1 native
	 * $type = 2 partner
	 *
	 *
	 * @return array
	 */
	private function getNativeModule($type = 0)
	{
		$xml = simplexml_load_string(Tools::file_get_contents('http://api.prestashop.com/xml/modules_list_16.xml'));

		if ($xml)
		{
			$natives = array();

			switch ($type)
			{
				case 0:
					foreach ($xml->modules as $row)
						foreach ($row->module as $row2)
							$natives[] = (string)$row2['name'];
					break;
				case 1:
					foreach ($xml->modules as $row)
						if ($row['type'] == 'native')
							foreach ($row->module as $row2)
								$natives[] = (string)$row2['name'];
					break;
				case 2:
					foreach ($xml->modules as $row)
						if ($row['type'] == 'partner')
							foreach ($row->module as $row2)
								$natives[] = (string)$row2['name'];
					break;
			}

			if (count($natives > 0))
				return $natives;
		}

		return array(
			'addsharethis',
			'bankwire',
			'blockadvertising',
			'blockbanner',
			'blockbestsellers',
			'blockcart',
			'blockcategories',
			'blockcms',
			'blockcmsinfo',
			'blockcontact',
			'blockcontactinfos',
			'blockcurrencies',
			'blockcustomerprivacy',
			'blockfacebook',
			'blocklanguages',
			'blocklayered',
			'blocklink',
			'blockmanufacturer',
			'blockmyaccount',
			'blockmyaccountfooter',
			'blocknewproducts',
			'blocknewsletter',
			'blockpaymentlogo',
			'blockpermanentlinks',
			'blockreinsurance',
			'blockrss',
			'blocksearch',
			'blocksharefb',
			'blocksocial',
			'blockspecials',
			'blockstore',
			'blocksupplier',
			'blocktags',
			'blocktopmenu',
			'blockuserinfo',
			'blockviewed',
			'blockwishlist',
			'carriercompare',
			'cashondelivery',
			'cheque',
			'crossselling',
			'dashactivity',
			'dashgoals',
			'dashproducts',
			'dashtrends',
			'dateofdelivery',
			'editorial',
			'favoriteproducts',
			'feeder',
			'followup',
			'gapi',
			'graphnvd3',
			'gridhtml',
			'homefeatured',
			'homeslider',
			'loyalty',
			'mailalerts',
			'newsletter',
			'pagesnotfound',
			'productcomments',
			'productpaymentlogos',
			'productscategory',
			'producttooltip',
			'pscleaner',
			'referralprogram',
			'sekeywords',
			'sendtoafriend',
			'socialsharing',
			'statsbestcategories',
			'statsbestcustomers',
			'statsbestmanufacturers',
			'statsbestproducts',
			'statsbestsuppliers',
			'statsbestvouchers',
			'statscarrier',
			'statscatalog',
			'statscheckup',
			'statsdata',
			'statsequipment',
			'statsforecast',
			'statslive',
			'statsnewsletter',
			'statsorigin',
			'statspersonalinfos',
			'statsproduct',
			'statsregistrations',
			'statssales',
			'statssearch',
			'statsstock',
			'statsvisits',
			'themeconfigurator',
			'trackingfront',
			'vatnumber',
			'watermark'
		);
	}

	private function getModules($xml)
	{
		$native_modules = $this->getNativeModule();
		$theme_module = array();

		$theme_module['to_install'] = array();
		$theme_module['to_enable'] = array();
		$theme_module['to_disable'] = array();
		foreach ($xml->modules->module as $row)
		{
			if (strval($row['action']) == 'install' && !in_array(strval($row['name']), $native_modules))
				$theme_module['to_install'][] = strval($row['name']);
			elseif (strval($row['action']) == 'enable')
				$theme_module['to_enable'][] = strval($row['name']);
			elseif (strval($row['action']) == 'disable')
				$theme_module['to_disable'][] = strval($row['name']);
		}

		return $theme_module;
	}

	private function formatHelperArray($origin_arr)
	{
		$fmt_arr = array();
		foreach ($origin_arr as $module)
		{
			$display_name = $module;

			$module_obj = Module::getInstanceByName($module);
			if (Validate::isLoadedObject($module_obj))
				$display_name = $module_obj->displayName;

			$tmp = array();
			$tmp['id'] = 'module'.$module;
			$tmp['val'] = $module;
			$tmp['name'] = $display_name;
			$fmt_arr[] = $tmp;
		}

		return $fmt_arr;
	}

	private function formatHelperValuesArray($originArr)
	{
		$fmtArr = array();
		foreach ($originArr as $key => $type)
			foreach ($type as $module)
				$fmtArr[$key.'_module'.$module] = true;

		return $fmtArr;
	}

	public function renderChooseThemeModule()
	{
		$theme = New Theme((int)Tools::getValue('id_theme'));

		$xml = false;
		if (file_exists(_PS_ROOT_DIR_.'/config/xml/themes/'.$theme->directory.'.xml'))
			$xml = simplexml_load_file(_PS_ROOT_DIR_.'/config/xml/themes/'.$theme->directory.'.xml');
		elseif (file_exists(_PS_ROOT_DIR_.'/config/xml/themes/default.xml'))
			$xml = simplexml_load_file(_PS_ROOT_DIR_.'/config/xml/themes/default.xml');

		if ($xml)
		{
			$theme_module = $this->getModules($xml);

			$toolbar_btn['save'] = array(
				'href' => '#',
				'desc' => $this->l('Save')
			);

			$to_install = array();
			$to_enable = array();
			$to_disable = array();

			if (isset($theme_module['to_install']))
				$to_install = $this->formatHelperArray($theme_module['to_install']);
			if (isset($theme_module['to_enable']))
				$to_enable = $this->formatHelperArray($theme_module['to_enable']);
			if (isset($theme_module['to_disable']))
				$to_disable = $this->formatHelperArray($theme_module['to_disable']);

			$fields_form = array(
				'form' => array(
					'tinymce' => false,
					'legend' => array(
						'title' => $this->l('Modules to install'),
						'icon' => 'icon-picture'
					),
					'description' => $this->l('The themes include their own modules in order to work properly. This option determines which module should be enabled or disabled. If you are unsure of what to do next, just press the "Save" button and proceed to the next step.'),
					'input' => array(
						array(
							'type' => 'shop',
							'label' => $this->l('Shop association'),
							'name' => 'checkBoxShopAsso_theme'
						),
						array(
							'type' => 'hidden',
							'name' => 'id_theme',
						),
					),
					'submit' => array(
						'title' => $this->l('Save'),
					),					
				)
			);


			if (count($to_install) > 0)
				$fields_form['form']['input'][] = array(
					'type' => 'checkbox',
					'label' => $this->l('Select the theme\'s modules you wish to install'),
					'values' => array(
						'query' => $to_install,
						'id' => 'id',
						'name' => 'name'
					),
					'name' => 'to_install',
					'expand' => array(
						'print_total' => count($to_install),
						'default' => 'show',
						'show' => array('text' => $this->l('Show'), 'icon' => 'plus-sign-alt'),
						'hide' => array('text' => $this->l('Hide'), 'icon' => 'minus-sign-alt')
					),
				);
			if (count($to_enable) > 0)
				$fields_form['form']['input'][] = array(
					'type' => 'checkbox',
					'label' => $this->l('Select the theme\'s modules you wish to enable'),
					'values' => array(
						'query' => $to_enable,
						'id' => 'id',
						'name' => 'name'
					),
					'name' => 'to_enable',
					'expand' => array(
						'print_total' => count($to_enable),
						'default' => 'show',
						'show' => array('text' => $this->l('Show'), 'icon' => 'plus-sign-alt'),
						'hide' => array('text' => $this->l('Hide'), 'icon' => 'minus-sign-alt')
					),
				);
			if (count($to_disable) > 0)
				$fields_form['form']['input'][] = array(
					'type' => 'checkbox',
					'label' => $this->l('Select the theme\'s modules you wish to disable'),
					'values' => array(
						'query' => $to_disable,
						'id' => 'id',
						'name' => 'name'
					),
					'name' => 'to_disable',
					'expand' => array(
						'print_total' => count($to_disable),
						'default' => 'show',
						'show' => array('text' => $this->l('Show'), 'icon' => 'plus-sign-alt'),
						'hide' => array('text' => $this->l('Hide'), 'icon' => 'minus-sign-alt')
					),
				);
			$shops = array();
			$shop = New Shop(Configuration::get('PS_SHOP_DEFAULT'));
			$tmp['id_shop'] = $shop->id;
			$tmp['id_theme'] = $shop->id_theme;
			$shops[] = $tmp;

			if (Shop::isFeatureActive())
				$shops = Shop::getShops();

			$current_shop = Context::getContext()->shop->id;

			foreach ($shops as $shop)
			{
				$shop_theme = New Theme((int)$shop['id_theme']);
				if ((int)Tools::getValue('id_theme') == (int)$shop['id_theme'])
					continue;

				$old_xml_name = 'default.xml';
				if (file_exists(_PS_ROOT_DIR_.'/config/xml/themes/'.$shop_theme->directory.'.xml'))
					$old_xml_name = $shop_theme->directory.'.xml';

				$shop_xml = simplexml_load_file(_PS_ROOT_DIR_.'/config/xml/themes/'.$old_xml_name);
				$theme_shop_module = $this->getModules($shop_xml);

				$to_shop_uninstall = array_merge($theme_shop_module['to_install'], $theme_shop_module['to_enable']);

				$to_shop_uninstall = preg_grep('/dash/', $to_shop_uninstall, PREG_GREP_INVERT);

				$to_shop_uninstall_clean = array_diff($to_shop_uninstall, $theme_module['to_enable']);

				$to_shop_uninstall_formated = $this->formatHelperArray($to_shop_uninstall_clean);

				if (count($to_shop_uninstall_formated) == 0)
					continue;

				$class = '';
				if ($shop['id_shop'] == $current_shop)
					$theme_module['to_disable_shop'.$shop['id_shop']] = array_merge($theme_shop_module['to_install'], $to_shop_uninstall_clean);
				else
					$class = 'hide';

				$fields_form['form']['input'][] = array(
					'type' => 'checkbox',
					'label' => sprintf($this->l('Select the modules from the old %1s theme that you wish to disable'), $shop_theme->directory),
					'form_group_class' => $class,
					'values' => array(
						'query' => $to_shop_uninstall_formated,
						'id' => 'id',
						'name' => 'name'
					),
					'expand' => array(
						'print_total' => count($to_shop_uninstall_formated),
						'default' => 'show',
						'show' => array('text' => $this->l('Show'), 'icon' => 'plus-sign-alt'),
						'hide' => array('text' => $this->l('Hide'), 'icon' => 'minus-sign-alt')
					),
					'name' => 'to_disable_shop'.$shop['id_shop']
				);

			}

			$fields_value = $this->formatHelperValuesArray($theme_module);

			$fields_value['id_theme'] = (int)Tools::getValue('id_theme');

			$helper = new HelperForm();

			$helper->currentIndex = $this->context->link->getAdminLink('AdminThemes', false).'&action=ThemeInstall';
			$helper->token = Tools::getAdminTokenLite('AdminThemes');
			$helper->submit_action = '';
			$helper->show_toolbar = true;
			$helper->toolbar_btn = $toolbar_btn;
			$helper->fields_value = $fields_value;
			$helper->languages = $this->getLanguages();
			$helper->default_form_language = (int)$this->context->language->id;
			$helper->table = 'theme';

			$helper->override_folder = $this->tpl_folder;


			return $helper->generateForm(array($fields_form));
		}

		Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminThemes'));
	}

	private function updateImages($xml)
	{
		$return = array();

		if (isset($xml->images->image))
			foreach ($xml->images->image as $row)
			{
				Db::getInstance()->delete('image_type', '`name` = \''.pSQL($row['name']).'\'');
					Db::getInstance()->execute('
					INSERT INTO `'._DB_PREFIX_.'image_type` (`name`, `width`, `height`, `products`, `categories`, `manufacturers`, `suppliers`, `scenes`)
					VALUES (\''.pSQL($row['name']).'\',
						'.(int)$row['width'].',
						'.(int)$row['height'].',
						'.($row['products'] == 'true' ? 1 : 0).',
						'.($row['categories'] == 'true' ? 1 : 0).',
						'.($row['manufacturers'] == 'true' ? 1 : 0).',
						'.($row['suppliers'] == 'true' ? 1 : 0).',
						'.($row['scenes'] == 'true' ? 1 : 0).')');

					$return['ok'][] = array(
						'name' => strval($row['name']),
						'width' => (int)$row['width'],
						'height' => (int)$row['height']
					);
			}

		return $return;
	}

	private function hookModule($id_module, $module_hooks, $shop)
	{

		Db::getInstance()->execute('INSERT IGNORE INTO '._DB_PREFIX_.'module_shop (id_module, id_shop) VALUES('.$id_module.', '.(int)$shop.')');

		Db::getInstance()->execute($sql = 'DELETE FROM `'._DB_PREFIX_.'hook_module` WHERE `id_module` = '.pSQL($id_module).' AND id_shop = '.(int)$shop);

		foreach ($module_hooks as $hooks)
		{
			foreach ($hooks as $hook)
			{
				$sql_hook_module = 'INSERT INTO `'._DB_PREFIX_.'hook_module` (`id_module`, `id_shop`, `id_hook`, `position`)
									VALUES ('.(int)$id_module.', '.(int)$shop.', '.(int)Hook::getIdByName($hook['hook']).', '.(int)$hook['position'].')';

				if (count($hook['exceptions']) > 0)
				{
					foreach ($hook['exceptions'] as $exception)
					{
						$sql_hook_module_except = 'INSERT INTO `'._DB_PREFIX_.'hook_module_exceptions` (`id_module`, `id_hook`, `file_name`) VALUES ('.(int)$id_module.', '.(int)Hook::getIdByName($hook['hook']).', "'.pSQL($exception).'")';

						Db::getInstance()->execute($sql_hook_module_except);
					}
				}
				Db::getInstance()->execute($sql_hook_module);
			}
		}
	}

	public function processThemeInstall()
	{
		if (Shop::isFeatureActive() && !Tools::getIsset('checkBoxShopAsso_theme'))
		{
			$this->errors[] = $this->l('You must choose at least one shop.');
			$this->display = 'ChooseThemeModule';

			return;
		}

		$theme = New Theme((int)Tools::getValue('id_theme'));

		$shops = array(Configuration::get('PS_SHOP_DEFAULT'));
		if (Tools::isSubmit('checkBoxShopAsso_theme'))
			$shops = Tools::getValue('checkBoxShopAsso_theme');

		$xml = false;
		if (file_exists(_PS_ROOT_DIR_.'/config/xml/themes/'.$theme->directory.'.xml'))
			$xml = simplexml_load_file(_PS_ROOT_DIR_.'/config/xml/themes/'.$theme->directory.'.xml');
		elseif (file_exists(_PS_ROOT_DIR_.'/config/xml/themes/default.xml'))
			$xml = simplexml_load_file(_PS_ROOT_DIR_.'/config/xml/themes/default.xml');

		if ($xml)
		{
			$module_hook = array();
			foreach ($xml->modules->hooks->hook as $row)
			{
				$name = strval($row['module']);

				$exceptions = (isset($row['exceptions']) ? explode(',', strval($row['exceptions'])) : array());

				if (Hook::getIdByName(strval($row['hook'])))
					$module_hook[$name]['hook'][] = array(
						'hook' => strval($row['hook']),
						'position' => strval($row['position']),
						'exceptions' => $exceptions
					);
			}

			$this->img_error = $this->updateImages($xml);

			$this->modules_errors = array();
			foreach ($shops as $id_shop)
			{
				foreach ($_POST as $key => $value)
				{
					if (strncmp($key, 'to_install', strlen('to_install')) == 0)
					{
						$module = Module::getInstanceByName($value);
						if ($module)
						{
							$is_installed_success = true;
							if (!Module::isInstalled($module->name))
								$is_installed_success = $module->install();
							if ($is_installed_success)
							{
								if (!Module::isEnabled($module->name))
									$module->enable();

								if ((int)$module->id > 0 && isset($module_hook[$module->name]))
									$this->hookModule($module->id, $module_hook[$module->name], $id_shop);
							}
							else
								$this->modules_errors[] = array('module_name' => $module->name, 'errors' => $module->getErrors());

							unset($module_hook[$module->name]);
						}

					}
					else if (strncmp($key, 'to_enable', strlen('to_enable')) == 0)
					{
						$module = Module::getInstanceByName($value);
						if ($module)
						{
							$is_installed_success = true;
							if (!Module::isInstalled($module->name))
								$is_installed_success = $module->install();

							if ($is_installed_success)
							{
								if (!Module::isEnabled($module->name))
									$module->enable();

								if ((int)$module->id > 0 && isset($module_hook[$module->name]))
									$this->hookModule($module->id, $module_hook[$module->name], $id_shop);
							}
							else
								$this->modules_errors[] = array('module_name' => $module->name, 'errors' => $module->getErrors());

							unset($module_hook[$module->name]);
						}

					}
					else if (strncmp($key, 'to_disable', strlen('to_disable')) == 0)
					{
						$key_exploded = explode('_', $key);
						$id_shop_module = (int)substr($key_exploded[2], 4);

						if ((int)$id_shop_module > 0 && $id_shop_module != (int)$id_shop)
							continue;

						$module_obj = Module::getInstanceByName($value);
						if (Validate::isLoadedObject($module_obj))
						{
							if (Module::isEnabled($module_obj->name))
								$module_obj->disable();

							unset($module_hook[$module_obj->name]);
						}
					}
				}
				$shop = New Shop((int)$id_shop);
				$shop->id_theme = (int)Tools::getValue('id_theme');
				$this->context->shop->id_theme = $shop->id_theme;
				$this->context->shop->update();
				$shop->save();

				if (Shop::isFeatureActive())
					Configuration::updateValue('PS_PRODUCTS_PER_PAGE', (int)$theme->product_per_page, false, null, (int)$id_shop);
				else
					Configuration::updateValue('PS_PRODUCTS_PER_PAGE', (int)$theme->product_per_page);
			}

			$this->doc = array();
			foreach ($xml->docs->doc as $row)
				$this->doc[strval($row['name'])] = __PS_BASE_URI__.'themes/'.$theme->directory.'/docs/'.basename(strval($row['path']));
		}

		Tools::clearCache($this->context->smarty);
		$this->theme_name = $theme->name;
		$this->display = 'view';
	}

	public function renderView()
	{
		$this->tpl_view_vars = array(
			'doc' => $this->doc,
			'theme_name' => $this->theme_name,
			'img_error' => $this->img_error,
			'modules_errors' => $this->modules_errors,
			'back_link' => Context::getContext()->link->getAdminLink('AdminThemes'),
			'image_link' => Context::getContext()->link->getAdminLink('AdminImages')
		);

		return parent::renderView();
	}

	/**
	 * This functions make checks about AdminThemes configuration edition only.
	 *
	 * @since 1.4
	 */
	public function postProcess()
	{
		if (Tools::isSubmit('submitOptionstheme') && Tools::isSubmit('id_theme') && !Tools::isSubmit('deletetheme') && Tools::getValue('action') != 'ThemeInstall' && $this->context->shop->id_theme != Tools::getValue('id_theme'))
			$this->display = "ChooseThemeModule";
		else
		{
			// new check compatibility theme feature (1.4) :
			$val = Tools::getValue('PS_THEME');
			Configuration::updateValue('PS_IMG_UPDATE_TIME', time());
			if (!empty($val) && !$this->_isThemeCompatible($val)) // don't submit if errors
				unset($_POST['submitThemes'.$this->table]);
			Tools::clearCache($this->context->smarty);

			return parent::postProcess();
		}
	}

	/**
	 * Update PS_LOGO
	 */
	public function updateOptionPsLogo()
	{
		$this->updateLogo('PS_LOGO', 'logo');
	}

	/**
	 * Update PS_LOGO_MOBILE
	 */
	public function updateOptionPsLogoMobile()
	{
		$this->updateLogo('PS_LOGO_MOBILE', 'logo_mobile');
	}

	/**
	 * Update PS_LOGO_MAIL
	 */
	public function updateOptionPsLogoMail()
	{
		$this->updateLogo('PS_LOGO_MAIL', 'logo_mail');
	}

	/**
	 * Update PS_LOGO_INVOICE
	 */
	public function updateOptionPsLogoInvoice()
	{
		$this->updateLogo('PS_LOGO_INVOICE', 'logo_invoice');
	}

	/**
	 * Update PS_STORES_ICON
	 */
	public function updateOptionPsStoresIcon()
	{
		$this->updateLogo('PS_STORES_ICON', 'logo_stores');
	}

	/**
	 * Generic function which allows logo upload
	 *
	 * @param $field_name
	 * @param $logo_prefix
	 *
	 * @return bool
	 */
	protected function updateLogo($field_name, $logo_prefix)
	{
		$id_shop = Context::getContext()->shop->id;
		if (isset($_FILES[$field_name]['tmp_name']) && $_FILES[$field_name]['tmp_name'])
		{
			if ($error = ImageManager::validateUpload($_FILES[$field_name], Tools::getMaxUploadSize()))
			{
				$this->errors[] = $error;
				return false;
			}

			$tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
			if (!$tmp_name || !move_uploaded_file($_FILES[$field_name]['tmp_name'], $tmp_name))
				return false;

			$ext = ($field_name == 'PS_STORES_ICON') ? '.gif' : '.jpg';
			$logo_name = Tools::link_rewrite(Context::getContext()->shop->name).'-'
				.Configuration::get('PS_IMG_UPDATE_TIME').'-'.(int)$id_shop.$ext;

			if (Context::getContext()->shop->getContext() == Shop::CONTEXT_ALL || $id_shop == 0
				|| Shop::isFeatureActive() == false)
				$logo_name = Tools::link_rewrite(Context::getContext()->shop->name).'-'
					.Configuration::get('PS_IMG_UPDATE_TIME').$ext;

			if ($field_name == 'PS_STORES_ICON')
			{
				if (!@ImageManager::resize($tmp_name, _PS_IMG_DIR_.$logo_name, null, null, 'gif', true))
					$this->errors[] = Tools::displayError('An error occurred while attempting to copy your logo.');
			}
			else
			{
				if (!@ImageManager::resize($tmp_name, _PS_IMG_DIR_.$logo_name))
					$this->errors[] = Tools::displayError('An error occurred while attempting to copy your logo.');
			}

			if (!count($this->errors) && @filemtime(_PS_IMG_DIR_.Configuration::get($field_name)))
				@unlink(_PS_IMG_DIR_.Configuration::get($field_name));

			Configuration::updateValue($field_name, $logo_name);
			@unlink($tmp_name);
		}
	}

	/**
	 * Update PS_FAVICON
	 */
	public function updateOptionPsFavicon()
	{
		$id_shop = Context::getContext()->shop->id;
		if ($id_shop == Configuration::get('PS_SHOP_DEFAULT'))
			$this->uploadIco('PS_FAVICON', _PS_IMG_DIR_.'favicon.ico');
		if ($this->uploadIco('PS_FAVICON', _PS_IMG_DIR_.'favicon-'.(int)$id_shop.'.ico'))
			Configuration::updateValue('PS_FAVICON', 'favicon-'.(int)$id_shop.'.ico');

		Configuration::updateGlobalValue('PS_FAVICON', 'favicon.ico');
		$this->redirect_after = self::$currentIndex.'&token='.$this->token;
	}

	/**
	 * Update theme for current shop
	 */
	public function updateOptionThemeForShop()
	{
		if (!$this->can_display_themes)
			return;

		$id_theme = (int)Tools::getValue('id_theme');
		if ($id_theme && $this->context->shop->id_theme != $id_theme)
		{
			$this->context->shop->id_theme = $id_theme;
			$this->context->shop->update();
			$this->redirect_after = self::$currentIndex.'&token='.$this->token;
		}
	}

	protected function uploadIco($name, $dest)
	{
		if (isset($_FILES[$name]['tmp_name']) && !empty($_FILES[$name]['tmp_name']))
		{
			// Check ico validity
			if ($error = ImageManager::validateIconUpload($_FILES[$name]))
				$this->errors[] = $error;

			// Copy new ico
			elseif (!copy($_FILES[$name]['tmp_name'], $dest))
				$this->errors[] = sprintf(Tools::displayError('An error occurred while uploading favicon: %s to %s'), $_FILES[$name]['tmp_name'], $dest);
		}

		return !count($this->errors) ? true : false;
	}

	public function initProcess()
	{
		if ((isset($_GET['responsive'.$this->table]) || isset($_GET['responsive'])) && Tools::getValue($this->identifier))
		{
			if ($this->tabAccess['edit'] === '1')
				$this->action = 'responsive';
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit this.');
		}
		else if ((isset($_GET['default_left_column'.$this->table]) || isset($_GET['default_left_column'])) && Tools::getValue($this->identifier))
		{
			if ($this->tabAccess['edit'] === '1')
				$this->action = 'defaultleftcolumn';
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit this.');
		}
		else if ((isset($_GET['default_right_column'.$this->table]) || isset($_GET['default_right_column'])) && Tools::getValue($this->identifier))
		{
			if ($this->tabAccess['edit'] === '1')
				$this->action = 'defaultrightcolumn';
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit this.');
		}
		else if (Tools::getIsset('id_theme_meta') && Tools::getIsset('leftmeta'))
		{
			if ($this->tabAccess['edit'] === '1')
				$this->action = 'leftmeta';
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit this.');
		}
		else if (Tools::getIsset('id_theme_meta') && Tools::getIsset('rightmeta'))
		{
			if ($this->tabAccess['edit'] === '1')
				$this->action = 'rightmeta';
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit this.');
		}

		parent::initProcess();
		// This is a composite page, we don't want the "options" display mode
		if ($this->display == 'options' || $this->display == 'list')
			$this->display = '';
	}

	public function printResponsiveIcon($value)
	{
		return ($value ? '<span class="list-action-enable  action-enabled"><i class="icon-check"></i></span>' : '<span class="list-action-enable  action-disabled"><i class="icon-remove"></i></span>');
	}

	public function processResponsive()
	{
		if (Validate::isLoadedObject($object = $this->loadObject()))
		{
			if ($object->toggleResponsive())
				$this->redirect_after = self::$currentIndex.'&conf=5&token='.$this->token;
			else
				$this->errors[] = Tools::displayError('An error occurred while updating responsive status.');
		}
		else
			$this->errors[] = Tools::displayError('An error occurred while updating the responsive status for this object.').
				' <b>'.$this->table.'</b> '.
				Tools::displayError('(cannot load object)');

		return $object;
	}

	public function processDefaultLeftColumn()
	{
		if (Validate::isLoadedObject($object = $this->loadObject()))
		{
			if ($object->toggleDefaultLeftColumn())
				$this->redirect_after = self::$currentIndex.'&conf=5&token='.$this->token;
			else
				$this->errors[] = Tools::displayError('An error occurred while updating default left column status.');
		}
		else
			$this->errors[] = Tools::displayError('An error occurred while updating the default left column status for this object.').
				' <b>'.$this->table.'</b> '.
				Tools::displayError('(cannot load object)');

		return $object;
	}

	public function processDefaultRightColumn()
	{
		if (Validate::isLoadedObject($object = $this->loadObject()))
		{
			if ($object->toggleDefaultRightColumn())
				$this->redirect_after = self::$currentIndex.'&conf=5&token='.$this->token;
			else
				$this->errors[] = Tools::displayError('An error occurred while updating default right column status.');
		}
		else
			$this->errors[] = Tools::displayError('An error occurred while updating the default right column status for this object.').
				' <b>'.$this->table.'</b> '.
				Tools::displayError('(cannot load object)');

		return $object;
	}

	public function ajaxProcessLeftMeta()
	{
		$theme_meta = Db::getInstance()->getRow(
			'SELECT * FROM '._DB_PREFIX_.'theme_meta WHERE id_theme_meta = '.(int)Tools::getValue('id_theme_meta')
		);

		$result = false;
		if ($theme_meta)
		{
			$sql = 'UPDATE '._DB_PREFIX_.'theme_meta SET left_column='.(int)!(bool)$theme_meta['left_column'].' WHERE id_theme_meta='.(int)Tools::getValue('id_theme_meta');
			$result = Db::getInstance()->execute($sql);
		}

		if ($result)
			echo json_encode(array('success' => 1, 'text' => $this->l('The status has been updated successfully.')));
		else
			echo json_encode(array('success' => 0, 'text' => $this->l('An error occurred while updating this meta.')));
	}

	public function processLeftMeta()
	{
		$theme_meta = Db::getInstance()->getRow(
			'SELECT * FROM '._DB_PREFIX_.'theme_meta WHERE id_theme_meta = '.(int)Tools::getValue('id_theme_meta')
		);

		$result = false;
		if ($theme_meta)
		{
			$sql = 'UPDATE '._DB_PREFIX_.'theme_meta SET left_column='.(int)!(bool)$theme_meta['left_column'].' WHERE id_theme_meta='.(int)Tools::getValue('id_theme_meta');
			$result = Db::getInstance()->execute($sql);
		}

		if ($result)
			$this->redirect_after = self::$currentIndex.'&updatetheme&id_theme='.$theme_meta['id_theme'].'&conf=5&token='.$this->token;
		else
			$this->errors[] = Tools::displayError('An error occurred while updating this meta.');
	}

	public function ajaxProcessRightMeta()
	{
		$theme_meta = Db::getInstance()->getRow(
			'SELECT * FROM '._DB_PREFIX_.'theme_meta WHERE id_theme_meta = '.(int)Tools::getValue('id_theme_meta')
		);

		$result = false;
		if ($theme_meta)
		{
			$sql = 'UPDATE '._DB_PREFIX_.'theme_meta SET right_column='.(int)!(bool)$theme_meta['right_column'].' WHERE id_theme_meta='.(int)Tools::getValue('id_theme_meta');
			$result = Db::getInstance()->execute($sql);
		}

		if ($result)
			echo json_encode(array('success' => 1, 'text' => $this->l('The status has been updated successfully.')));
		else
			echo json_encode(array('success' => 0, 'text' => $this->l('An error occurred while updating this meta.')));
	}

	public function processRightMeta()
	{
		$theme_meta = Db::getInstance()->getRow(
			'SELECT * FROM '._DB_PREFIX_.'theme_meta WHERE id_theme_meta = '.(int)Tools::getValue('id_theme_meta')
		);

		$result = false;
		if ($theme_meta)
		{
			$sql = 'UPDATE '._DB_PREFIX_.'theme_meta SET right_column='.(int)!(bool)$theme_meta['right_column'].' WHERE id_theme_meta='.(int)Tools::getValue('id_theme_meta');
			$result = Db::getInstance()->execute($sql);
		}

		if ($result)
			$this->redirect_after = self::$currentIndex.'&updatetheme&id_theme='.$theme_meta['id_theme'].'&conf=5&token='.$this->token;
		else
			$this->errors[] = Tools::displayError('An error occurred while updating this meta.');
	}


	/**
	 * Function used to render the options for this controller
	 */
	public function renderOptions()
	{
		if (isset($this->display) && method_exists($this, 'render'.$this->display))
			return $this->{'render'.$this->display}();
		if ($this->fields_options && is_array($this->fields_options))
		{
			$helper = new HelperOptions($this);
			$this->setHelperDisplay($helper);
			$helper->toolbar_scroll = true;
			$helper->title = $this->l('Theme appearance');
			$helper->toolbar_btn = array(
				'save' => array(
					'href' => '#',
					'desc' => $this->l('Save')
				)
			);
			$helper->id = $this->id;
			$helper->tpl_vars = $this->tpl_option_vars;
			$options = $helper->generateOptions($this->fields_options);

			return $options;
		}
	}
}
