<?php
/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminThemesControllerCore extends AdminController
{
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
					 * accepted attribute value if value doesnt match, prestashop configuration value must have thoses values
					*/
					'check_if_not_valid' => array(
						'PS_CSS_THEME_CACHE' => 0,
						'PS_JS_THEME_CACHE' => 0,
						'PS_HTML_THEME_COMPRESSION' => 0,
						'PS_JS_HTML_THEME_COMPRESSION' => 0,
					),
				),
			),
			'error' => 'This theme may not correctly use "combine, compress and cache"',
			'tab' => 'AdminPerformance',
		),
		'guest_checkout' => array(
			'attributes' => array(
				'available' => array(
				'value' => 'true',
				'check_if_not_valid' => array('PS_GUEST_CHECKOUT_ENABLED' => 0)
				),
			),
			'error' => 'This theme may not correctly use "guest checkout"',
			'tab' => 'AdminPreferences',
		),
		'one_page_checkout' => array(
			'attributes' => array(
				'available' => array(
					'value' => 'true',
					'check_if_not_valid' => array('PS_ORDER_PROCESS_TYPE' => 0),
				),
			),
			'error' => 'This theme may not correctly use "one page checkout"',
			'tab' => 'AdminPreferences',
		),
		'store_locator' => array(
			'attributes' => array(
				'available' => array(
				'value' => 'true',
				'check_if_not_valid' => array('PS_STORES_SIMPLIFIED' => 0,'PS_STORES_DISPLAY_FOOTER' => 0),
				)
			),
			'error' => 'This theme may not correctly use "display store location"',
			'tab' => 'AdminStores',
		)
	);
	
	public $className = 'Theme';
	public $table = 'theme';
	protected $toolbar_scroll = false;
	private $imgError;

	public function init()
	{

		define('MAX_NAME_LENGTH', 128);
		// No cache for auto-refresh uploaded logo
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

		parent::init();
		$this->can_display_themes = (!Shop::isFeatureActive() || Shop::getContext() == Shop::CONTEXT_SHOP) ? true : false;

		$this->fields_options = array(
			'theme' => array(
				'title' => sprintf($this->l('Select a theme for shop %s'), $this->context->shop->name),
				'description' => (!$this->can_display_themes) ? $this->l('You must select a shop from the above list if you wish to choose a theme.') : '',
				'fields' => array(
					'theme_for_shop' => array(
						'type' => 'theme',
						'themes' => Theme::getThemes(),
						'id_theme' => $this->context->shop->id_theme,
						'can_display_themes' => $this->can_display_themes,
						'no_multishop_checkbox' => true,
					),
				),
			),
			'appearance' => array(
				'title' =>	$this->l('Appearance'),
				'icon' =>	'icon-html5',
				'fields' =>	array(
					'PS_LOGO' => array(
						'title' => $this->l('Header logo'),
						'hint' => $this->l('Will appear on main page. Recommended height: 52px. Maximum height on default theme: 65px.'),
						'type' => 'file',
						'name' => 'PS_LOGO',
						'thumb' => _PS_IMG_.Configuration::get('PS_LOGO').'?date='.time()
					),
					'PS_LOGO_MOBILE' => array(
						'title' => $this->l('Header logo for mobile'),
						'desc' => 
							((Configuration::get('PS_LOGO_MOBILE') === false) ? '<span class="light-warning">'.$this->l('Warning: No mobile logo has been defined. The header logo will be used instead.').'</span><br />' : '').
							$this->l('Will appear on the main page of your mobile template. If left undefined, the header logo will be used.'),
						'type' => 'file',
						'name' => 'PS_LOGO_MOBILE',
						'thumb' => (Configuration::get('PS_LOGO_MOBILE') !== false && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO_MOBILE'))) ? _PS_IMG_.Configuration::get('PS_LOGO_MOBILE').'?date='.time() : _PS_IMG_.Configuration::get('PS_LOGO').'?date='.time()
					),
					'PS_LOGO_MAIL' => array(
						'title' => $this->l('Mail logo'),
						'desc' => 
							((Configuration::get('PS_LOGO_MAIL') === false) ? '<span class="light-warning">'.$this->l('Warning: No email logo has been indentified. The header logo will be used instead.').'</span><br />' : '').
							$this->l('Will appear on email headers. If undefined, the header logo will be used.'),
						'type' => 'file',
						'name' => 'PS_LOGO_MAIL',
						'thumb' => (Configuration::get('PS_LOGO_MAIL') !== false && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO_MAIL'))) ? _PS_IMG_.Configuration::get('PS_LOGO_MAIL').'?date='.time() : _PS_IMG_.Configuration::get('PS_LOGO').'?date='.time()
					),
					'PS_LOGO_INVOICE' => array(
						'title' => $this->l('Invoice logo'),
						'desc' => 
							((Configuration::get('PS_LOGO_INVOICE') === false) ? '<span class="light-warning">'.$this->l('Warning: No invoice logo has been defined. The header logo will be used instead.').'</span><br />' : '').
							$this->l('Will appear on invoice headers. If undefined, the header logo will be used.'),
						'type' => 'file',
						'name' => 'PS_LOGO_INVOICE',
						'thumb' => (Configuration::get('PS_LOGO_INVOICE') !== false && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO_INVOICE'))) ? _PS_IMG_.Configuration::get('PS_LOGO_INVOICE').'?date='.time() : _PS_IMG_.Configuration::get('PS_LOGO').'?date='.time()
					),
					'PS_FAVICON' => array(
						'title' => $this->l('Favicon'),
						'hint' => $this->l('Only ICO format allowed'),
						'hint' => $this->l('Will appear in the address bar of your web browser.'),
						'type' => 'file',
						'name' => 'PS_FAVICON',
						'thumb' => _PS_IMG_.Configuration::get('PS_FAVICON').'?date='.time()
					),
					'PS_STORES_ICON' => array(
						'title' => $this->l('Store icon'),
						'hint' => $this->l('Only GIF format allowed.'),
						'hint' => $this->l('Will appear on the store locator (inside Google Maps).').'<br />'.$this->l('Suggested size: 30x30, Transparent GIF'),
						'type' => 'file',
						'name' => 'PS_STORES_ICON',
						'thumb' => _PS_IMG_.Configuration::get('PS_STORES_ICON').'?date='.time()
					),
					'PS_NAVIGATION_PIPE' => array(
						'title' => $this->l('Navigation pipe'),
						'hint' => $this->l('Used for the navigation path inside categories/product.'),
						'cast' => 'strval',
						'type' => 'text',
						'size' => 20
					),
					'PS_ALLOW_MOBILE_DEVICE' => array(
						'title' => $this->l('Enable the mobile theme.'),
						'hint' => $this->l('Allows visitors browsing on mobile devices to view a lighter version of your website.'),
						'type' => 'radio',
						'required' => true,
						'validation' => 'isGenericName',
						'choices' => array(
							0 => $this->l('I\'d like to disable it, please. '),
							1 => $this->l('I\'d like to enable it only on smart phones.'),
							2 => $this->l('I\'d like to enable it only on tablets.'),
							3 => $this->l('I\'d like to enable it on both smart phones and tablets.')
						)
					),
					'PS_MAIL_COLOR' => array(
						'title' => $this->l('Mail color'),
						'hint' => $this->l('Your mail will be highlighted in this color. HTML colors only, please (e.g.').' "lightblue", "#CC6600")',
						'type' => 'color',
						'name' => 'PS_MAIL_COLOR',
						'size' => 30,					
						'value' => Configuration::get('PS_MAIL_COLOR')
					)
				),
				'submit' => array('title' => $this->l('Save'), 'class' => 'button')
			)
		);

		$this->fields_list = array(
			'id_theme' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'class' => 'fixed-width-xs'
			),
			'name' => array(
				'title' => $this->l('Name'),
			),
			'directory' => array(
				'title' => $this->l('Directory'),
			),
		);
	}

	protected function checkMobileNeeds()
	{
		$allow_mobile = (bool)Configuration::get('PS_ALLOW_MOBILE_DEVICE');
		if (!$allow_mobile && Context::getContext()->shop->getTheme() == 'default')
			return;
		
		$iso_code = Country::getIsoById((int)Configuration::get('PS_COUNTRY_DEFAULT'));
		$paypal_installed = (bool)Module::isInstalled('paypal');
		$paypal_countries = array('ES', 'FR', 'PL', 'IT');
		
		if (!$paypal_installed && in_array($iso_code, $paypal_countries))
		{
			if (!$this->isXmlHttpRequest())
				$this->warnings[] = $this->l('At this time, the mobile theme only works with PayPal\'s payment module. Please activate and configure the PayPal module to enable mobile payments.')
					.'<br>'.
					$this->l('In order to use the mobile theme, you must install and configure the PayPal module.');
		}
	}

	public function renderForm()
	{
		$get_available_themes = Theme::getAvailable(false);
		$available_theme_dir = array();
		$selected_theme_dir = null;
		if ($this->object)
			$selected_theme_dir = $this->object->directory;
		
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
					'label' => $this->l('Name of the theme:'),
					'name' => 'name',
					'required' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}',
				),
			),
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'button'
			)
		);
		// adding a new theme, you can create a directory, and copy from an existing theme
		if ($this->display == 'add' || !$this->object->id)
		{
			$this->fields_form['input'][] = array(
					'type' => 'text',
					'label' => $this->l('Name of the theme\'s directory:'),
					'name' => 'directory',
					'required' => true,
					'hint' => $this->l('If the directory does not exists, it will be created.'),
				);

			$theme_query = Theme::getThemes();
			$this->fields_form['input'][] = array(
				'type' => 'select',
				'name' => 'based_on',
				'label' => $this->l('Copy missing files from existing theme:'),
				'hint' => $this->l('If you create a new theme, it\'s recommended that you use default theme files.'),
				'options' => array(
					'id' => 'id', 'name' => 'name', 
					'default' => array('value' => 0, 'label' => '&nbsp;-&nbsp;'),
					'query' => $theme_query,
				)
			);
		}
		else
			$this->fields_form['input'][] = array(
					'type' => 'radio',
					'label' => $this->l('Directory:'),
					'name' => 'directory',
					'required' => true,
					'br' => true,
					'values' => $available_theme_dir,
					'selected' => $selected_theme_dir,
					'hint' => $this->l('Please select a valid theme directory.'),
				);

		return parent::renderForm();
	}

	public function renderList()
	{
		$this->addRowAction('edit');
		$this->addRowAction('delete');

		return parent::renderList();
	}
	
	/**
	 * copy $base_theme_dir into $target_theme_dir.
	 *
	 * @param string $base_theme_dir relative path to base dir 
	 * @param string $target_theme_dir relative path to target dir
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
		
		return $res;
	}

	public function processAdd()
	{
		$new_dir = Tools::getValue('directory');
		$res = true;

		if ($new_dir != '')
		{
			if (Validate::isDirName($new_dir) && !is_dir(_PS_ALL_THEMES_DIR_.$new_dir))
			{
				$res &= mkdir(_PS_ALL_THEMES_DIR_.$new_dir, Theme::$access_rights);
				if ($res)
					$this->confirmations[] = $this->l('The directory was successfully created.');
			}

			if (0 !== $id_based = (int)Tools::getValue('based_on'))
			{
				$base_theme = new Theme($id_based);
				$res = $this->copyTheme($base_theme->directory, $new_dir);
				$base_theme = new Theme((int)Tools::getValue('based_on'));
			}
		}

		return parent::processAdd();
	}

	public function processDelete()
	{
		$obj = $this->loadObject();

		if ($obj && is_dir(_PS_ALL_THEMES_DIR_.$obj->directory))
			Tools::deleteDirectory(_PS_ALL_THEMES_DIR_.$obj->directory.'/');

		if ($obj && $obj->isUsed())
		{
			$this->errors[] = $this->l('The theme is already being used by at least one shop. Please choose another theme before continuing.');
			return false;
		}

		return parent::processDelete();
	}

	public function initPageHeaderToolbar()
	{
		if (empty($this->display))
		{
			$this->page_header_toolbar_btn['new_theme'] = array(
				'href' => self::$currentIndex.'&amp;addtheme&amp;token='.$this->token,
				'desc' => $this->l('Add new theme'),
				'icon' => 'process-icon-new'
			);
			$this->page_header_toolbar_btn['import_theme'] = array(
				'href' => self::$currentIndex . '&amp;action=importtheme&amp;token=' . $this->token,
				'desc' => $this->l('import new theme'),
				'icon' => 'process-icon-upload'
			);
			$this->page_header_toolbar_btn['export_theme'] = array(
				'href' => self::$currentIndex . '&amp;action=exporttheme&amp;token=' . $this->token,
				'desc' => $this->l('export theme'),
				'icon' => 'process-icon-download'
			);
		}
		parent::initPageHeaderToolbar();
	}

	private function checkParentClass($name)
	{
		if (!$obj = Module::getInstanceByName($name))
			return false;
		if (is_callable(array($obj, 'validateOrder')))
			return false;
		if (is_callable(array($obj, 'getDateBetween')))
			return false;
		if (is_callable(array($obj, 'getGridEngines')))
			return false;
		if (is_callable(array($obj, 'getGraphEngines')))
			return false;
		if (is_callable(array($obj, 'hookAdminStatsModules')))
			return false;
		else
			return true;
		return false;
	}

	private function checkNames()
	{
		$author = Tools::getValue('name');
		$themeName = Tools::getValue('theme_name');
		$count = 0;

		if (!$author || !Validate::isGenericName($author) || strlen($author) > MAX_NAME_LENGTH)
			$this->errors[] = $this->l('Please enter a valid author name');
		elseif (!$themeName || !Validate::isGenericName($themeName) || strlen($themeName) > MAX_NAME_LENGTH)
			$this->errors[] = $this->l('Please enter a valid theme name');

		if (count($this->errors) > 0)
			return false;
		return true;
	}

	private function checkDocumentation()
	{
		$extensions = array('.pdf', '.txt');

		if (isset($_FILES['documentation']))
		{
			$extension = strrchr($_FILES['documentation']['name'], '.');
			$name = Tools::getValue('documentationName');

			if (!in_array($extension, $extensions))
				$this->errors[] = $this->l('File extension must be .txt or .pdf');
			elseif ($_FILES['documentation']['error'] > 0 || $_FILES['documentation']['size'] > 1048576)
				$this->errors[] = $this->l('An error occurred during documentation upload');
			elseif (!$name || !Validate::isGenericName($name) || strlen($name) > MAX_NAME_LENGTH)
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
			version_compare(Tools::getValue('compa_from'), Tools::getValue('compa_to')) == 1)
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
				if ($row != '.' && $row != '..')
					$this->archiveThisFile($obj, $row, $server_path.$file.'/', $archive_path.$file.'/');
		}
		elseif (!$obj->addFile($server_path.$file, $archive_path.$file))
			$this->error = true;
	}

	private function generateArchive()
	{
		$zip = new ZipArchive();
		$zip_file_name = md5(time()).'.zip';
		if ($zip->open(_PS_ROOT_DIR_.$zip_file_name, ZipArchive::OVERWRITE) === true)
		{
			if (!$zip->addFromString('Config.xml', $this->xml_file))
				$this->errors[] = $this->l('Cant create config file');

			if (isset($_FILES['documentation']))
				if (!$zip->addFile($_FILES['documentation']['tmp_name'], 'doc/'.$_FILES['documentation']['name']))
					$this->error = $this->l('Cant copy documentation.');

			$this->archiveThisFile($zip, Tools::getValue('theme_directory'), _PS_ALL_THEMES_DIR_, 'themes/');

			foreach ($this->to_export as $row)
			{
				if (!in_array($row, $this->native_modules))
					$this->archiveThisFile($zip, $row, dirname(__FILE__).'/../../modules/', 'modules/');
			}
			$zip->close();
			if (!$this->errors)
			{
				if (ob_get_length() > 0)
					ob_end_clean();
				header('Content-Type: multipart/x-zip');
				header('Content-Disposition:attachment;filename="'.$zip_file_name.'"');
				readfile(_PS_ROOT_DIR_.$zip_file_name);
				unlink(_PS_ROOT_DIR_.$zip_file_name);
				die;
			}
		}
		$this->errors[] = $this->l('An error occurred during the archive generation');
	}

	private function generateXML()
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

		$modules = $theme->addChild('modules');
		if (isset($this->to_export))
			foreach ($this->to_export as $row)
				if (!in_array($row, $this->native_modules))
				{
					$module = $modules->addChild('module');
					$module->addAttribute('action', 'install');
					$module->addAttribute('name', $row);
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
				$name     = Tools::htmlentitiesUTF8(Tools::getValue('documentationName'));
				$this->user_doc = array($name . '¤doc/' . $filename);


				$table = Db::getInstance()->executeS('
			SELECT name, width, height, products, categories, manufacturers, suppliers, scenes
			FROM `' . _DB_PREFIX_ . 'image_type`');

				$this->image_list = array();
				foreach ($table as $row)
					$this->image_list[] = $row['name'] . ';' . $row['width'] . ';' . $row['height'] . ';' .
						($row['products'] == 1 ? 'true' : 'false') . ';' .
						($row['categories'] == 1 ? 'true' : 'false') . ';' .
						($row['manufacturers'] == 1 ? 'true' : 'false') . ';' .
						($row['suppliers'] == 1 ? 'true' : 'false') . ';' .
						($row['scenes'] == 1 ? 'true' : 'false');

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
					$row['exceptions'] = trim(preg_replace('/(,,+)/', ',', $row['exceptions']), ',');

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
						if (in_array($str, $tmp))
						{
							$flag = 1;
							break;
						}
					if ($flag == 0)
						$this->to_disable[] = $str;
				}

				foreach($_POST as $key => $value)
				{
					if (strncmp($key, 'modulesToExport_module', strlen('modulesToExport_module')) == 0)
					{
						$this->to_export[] = $value;
					}
				}

				if ($this->to_install)
					foreach ($this->to_install as $string)
						foreach ($this->hook_list as $tmp)
							if ($tmp['name_module'] == $string)
								$this->to_hook[] = $string.';'.$tmp['name_hook'].';'.$tmp['position'].';'.$tmp['exceptions'];
				if ($this->to_enable)
					foreach ($this->to_enable as $string)
						foreach ($this->hook_list as $tmp)
							if ($tmp['name_module'] == $string)
								$this->to_hook[] = $string.';'.$tmp['name_hook'].';'.$tmp['position'].';'.$tmp['exceptions'];


				$this->generateXML();
				$this->generateArchive();

			} else
				$this->display = 'exporttheme';
		} else
			$this->display = 'exporttheme';
	}

	private function renderExportTheme1()
	{
		$module_list = Db::getInstance()->executeS('
				SELECT m.`id_module`, m.`name`, m.`active`, ms.`id_shop`
				FROM `' . _DB_PREFIX_ . 'module` m
				LEFT JOIN `' . _DB_PREFIX_ . 'module_shop` ms On (m.`id_module` = ms.`id_module`)
				WHERE ms.`id_shop` = ' . (int)$this->context->shop->id . '
			');

		// Select the list of hook for this shop
		$hook_list = Db::getInstance()->executeS('
				SELECT h.`id_hook`, h.`name` as name_hook, hm.`position`, hm.`id_module`, m.`name` as name_module, GROUP_CONCAT(hme.`file_name`, ",") as exceptions
				FROM `' . _DB_PREFIX_ . 'hook` h
				LEFT JOIN `' . _DB_PREFIX_ . 'hook_module` hm ON hm.`id_hook` = h.`id_hook`
				LEFT JOIN `' . _DB_PREFIX_ . 'module` m ON hm.`id_module` = m.`id_module`
				LEFT OUTER JOIN `' . _DB_PREFIX_ . 'hook_module_exceptions` hme ON (hme.`id_module` = hm.`id_module` AND hme.`id_hook` = h.`id_hook`)
				WHERE hm.`id_shop` = ' . (int)$this->context->shop->id . '
				GROUP BY `id_module`, `id_hook`
				ORDER BY `name_module`
			');

		foreach ($hook_list as &$row)
			$row['exceptions'] = trim(preg_replace('/(,,+)/', ',', $row['exceptions']), ',');

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
			} elseif ($array['active'] == 1)
				$to_install[] = $array['name'];
		}
		foreach ($native_modules as $str)
		{
			$flag = 0;
			if (!$this->checkParentClass($str))
				continue;
			foreach ($module_list as $tmp)
				if (in_array($str, $tmp))
				{
					$flag = 1;
					break;
				}
			if ($flag == 0)
				$to_disable[] = $str;
		}

		$employee = $this->context->employee;
		$mail     = Tools::getValue('email') ? Tools::htmlentitiesUTF8(Tools::getValue('email')) : Tools::htmlentitiesUTF8($employee->email);
		$author   = Tools::getValue('author_name') ? Tools::htmlentitiesUTF8(Tools::getValue('author_name')) : Tools::htmlentitiesUTF8(($employee->firstname) . ' ' . $employee->lastname);
		$website  = Tools::getValue('website') ? Tools::htmlentitiesUTF8(Tools::getValue('website')) : Tools::getHttpHost(true);

		$this->formatHelperArray($to_install);

		$theme = New Theme(Tools::getValue('id_theme_export'));

		$fields_form = array(
			'form' => array(
				'tinymce' => false,
				'legend'  => array(
					'title' => $this->l('Theme'),
					'icon'  => 'icon-picture'
				),
				'input' => array(
					array(
						'type' => 'hidden',
						'name' => 'id_theme_export'
					),
					array(
						'type'  => 'text',
						'name'  => 'name',
						'label' => $this->l('Name'),
					),
					array(
						'type'  => 'text',
						'name'  => 'email',
						'label' => $this->l('Email'),
					),
					array(
						'type'  => 'text',
						'name'  => 'website',
						'label' => $this->l('Website'),
					),
					array(
						'type'   => 'checkbox',
						'label'  => $this->l('Select the theme\'s modules you wish to export:'),
						'values' => array(
							'query' => $this->formatHelperArray($to_install),
							'id'    => 'id',
							'name'  => 'name'
						),
						'name'   => 'modulesToExport',
					),
					array(
						'type'  => 'text',
						'name'  => 'theme_name',
						'label' => $this->l('Theme name'),
					),
					array(
						'type'  => 'text',
						'name'  => 'theme_directory',
						'label' => $this->l('Theme directory'),
					),
					array(
						'type'  => 'text',
						'name'  => 'body_title',

						'lang'  => true,
						'label' => $this->l('Description'),
					),
					array(
						'type'  => 'text',
						'name'  => 'theme_version',
						'label' => $this->l('Theme version'),
					),
					array(
						'type'  => 'text',
						'name'  => 'compa_from',
						'label' => $this->l('Compatible from'),
					),
					array(
						'type'  => 'text',
						'name'  => 'compa_to',
						'label' => $this->l('Compatible to'),
					),
					array(
						'type'     => 'file',
						'name'     => 'documentation',
						'label'    => $this->l('Documentation'),
					),
					array(
						'type'  => 'text',
						'name'  => 'documentationName',
						'label' => $this->l('Documentation name'),
					),
				),
				'submit'  => array(
					'title' => $this->l('Save'),
					'class' => 'button'
				))
		);

		$default_language = (int)$this->context->language->id;
		$languages = Language::getLanguages();

		foreach($languages as $language)
			$fields_value['body_title'][$language['id_lang']] = '';

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
		{
			return $this->renderExportTheme1();
		}
		$theme_list = Theme::getThemes();



		$fields_form = array(
			'form' => array(
				'tinymce' => false,
				'legend'  => array(
					'title' => $this->l('Theme'),
					'icon'  => 'icon-picture'
				),
				'input' => array(
					array(
						'type' => 'select',
						'name' => 'id_theme_export',
						'label' => $this->l('Choose the theme to export:'),
						'options' => array(
							'id' => 'id', 'name' => 'name',
							'query' => $theme_list,
						)

					),
				),
				'submit'  => array(
					'title' => $this->l('Save'),
					'class' => 'button'
				))
		);

		$toolbar_btn['save'] = array(
			'href' => '#',
			'desc' => $this->l('Save')
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
			if (!$val['name'] || !$val['directory'] || !$val['from'] || !$val['to'])
				return false;
		foreach ($xml->modules->module as $val)
			if (!$val['action'] || !$val['name'])
				return false;
		foreach ($xml->modules->hooks->hook as $val)
			if (!$val['module'] || !$val['hook'] || !$val['position'])
				return false;
		return true;
	}

	private function recurseCopy($src, $dst)
	{
		if (!$dir = opendir($src))
			return;
		if (!file_exists($dst))
			mkdir($dst);
		while (($file = readdir($dir)) !== false)
			if (strncmp($file, '.', 1) != 0)
			{
				if (is_dir($src.'/'.$file))
					self::recurseCopy($src.'/'.$file, $dst.'/'.$file);
				elseif (is_readable($src.'/'.$file) && $file != 'Thumbs.db' && $file != '.DS_Store' && substr($file, -1) != '~')
					copy($src.'/'.$file, $dst.'/'.$file);
			}
		closedir($dir);
	}

	public function processImportTheme()
	{
		$this->display = "importtheme";

		if (isset($_FILES['themearchive']) && isset($_POST['filename']))
		{
			$uniqid = uniqid();
			$sandbox = _PS_CACHE_DIR_.'sandbox'.DIRECTORY_SEPARATOR.$uniqid.DIRECTORY_SEPARATOR;
			mkdir($sandbox);
			$archive_uploaded = false;

			if (Tools::getValue('filename') != '')
			{
				if ($_FILES['themearchive']['error'] || !file_exists($_FILES['themearchive']['tmp_name']))
					$this->errors[] = sprintf($this->l('An error has occurred during the file upload (%s)'), $_FILES['themearchive']['error']);
				elseif (substr($_FILES['themearchive']['name'], -4) != '.zip')
					$this->errors[] = $this->l('Only zip files are allowed');
				elseif (!move_uploaded_file($_FILES['themearchive']['tmp_name'], $sandbox . 'uploaded.zip'))
					$this->errors[] = $this->l('An error has occurred during the file copy.');
				elseif (Tools::ZipTest($sandbox . 'uploaded.zip'))
					$archive_uploaded = true;
				else
					$this->errors[] = $this->l('Zip file seems to be broken');

			}
			elseif(Tools::getValue('themearchiveUrl') != '')
			{
				if (!Validate::isModuleUrl($url = Tools::getValue('themearchiveUrl'), $this->errors)) // $tmp is not used, because we don't care about the error output of isModuleUrl
					$this->errors[] = $this->l('Only zip files are allowed');
				elseif (!move_uploaded_file($url, $sandbox.'uploaded.zip'))
					$this->errors[] = $this->l('Error during the file download');
				elseif (Tools::ZipTest($sandbox.'uploaded.zip'))
					$archive_uploaded = true;
				else
					$this->errors[] = $this->l('Zip file seems to be broken');
			}
			else
				$this->errors[] = $this->l('You must upload or enter a location of your zip');

			if ($archive_uploaded)
			{

				if (!Tools::ZipExtract($sandbox.'/uploaded.zip', $sandbox.'uploaded/'))
					$this->errors[] = $this->l('Error during zip extraction');
				else
				{
					if (!$this->checkXmlFields($sandbox))
						$this->errors[] = $this->l('Bad configuration file');
					else
					{
						$xml = simplexml_load_file($sandbox.'uploaded/Config.xml');
						$this->xml = $xml;

						$theme_directory = strval($xml->variations->variation[0]['directory']);

						$themes = Theme::getThemes();

						$name = strval($xml->variations->variation[0]['name']);

						foreach($themes as $theme_object)
							if ($theme_object->name == $name)
								$this->errors[] = $this->l('Theme already installed.');

						if (!count($this->errors))
						{
							if (!copy($sandbox . 'uploaded/Config.xml', _PS_ROOT_DIR_ . '/config/xml/' . $theme_directory . '.xml'))
								$this->errors[] = $this->l('Can\'t copy configuration file');

							$new_theme       = new Theme();
							$new_theme->name = $name;

							$new_theme->directory = $theme_directory;

							$new_theme->add();

							$target_dir = _PS_ALL_THEMES_DIR_ . $theme_directory;

							$themeDocDir = $target_dir.'/docs/';
							if (file_exists($themeDocDir))
								Tools::deleteDirectory($themeDocDir, true);

							$this->recurseCopy($sandbox . 'uploaded/themes/' . $theme_directory, $target_dir);
							$this->recurseCopy($sandbox . 'uploaded/doc/', $themeDocDir);
							$this->recurseCopy($sandbox . 'uploaded/modules/', _PS_MODULE_DIR_);
						}
					}
				}

			}
			Tools::deleteDirectory($sandbox, true);
			if (count($this->errors)>0)
				$this->display = 'importtheme';
			else
				Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminThemes').'&conf=18');
		}
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
				'legend'  => array(
					'title' => $this->l('Theme'),
					'icon'  => 'icon-picture'
				),
				'input' => array(
					array(
						'type'     => 'text',
						'label'    => $this->l('Archive URL:'),
						'name'     => 'themearchiveUrl'
					),
				),
				'submit'  => array(
					'title' => $this->l('Save'),
					'class' => 'button'
				)),
		);

		$fields_form[1] = array(
			'form' => array(
				'tinymce' => false,
				'legend'  => array(
					'title' => $this->l('Theme'),
					'icon'  => 'icon-picture'
				),
				'input' => array(
					array(
						'type'     => 'file',
						'label'    => $this->l('Zip of the theme:'),
						'name'     => 'themearchive'
					),
				),
				'submit'  => array(
					'id' => 'zip',
					'title' => $this->l('Save'),
					'class' => 'button hide'
				)),
		);

		$helper = new HelperForm();

		$helper->currentIndex = $this->context->link->getAdminLink('AdminThemes', false).'&action=importtheme';
		$helper->token = Tools::getAdminTokenLite('AdminThemes');
		$helper->show_toolbar = true;
		$helper->toolbar_btn = $toolbar_btn;
		$helper->fields_value['themearchiveUrl']='';
		$helper->multiple_fieldsets = true;

		$helper->override_folder = $this->tpl_folder;

		return $helper->generateForm($fields_form);
	}

	public function initContent()
	{
		$this->checkMobileNeeds();
		
		$themes = array();
		foreach (Theme::getThemes() as $theme)
			$themes[] = $theme->directory;

		foreach (scandir(_PS_ALL_THEMES_DIR_) as $theme_dir)
			if ($theme_dir[0] != '.' && Validate::isDirName($theme_dir) && is_dir(_PS_ALL_THEMES_DIR_.$theme_dir) && file_exists(_PS_ALL_THEMES_DIR_.$theme_dir.'/preview.jpg') && !in_array($theme_dir, $themes))
			{
				$theme = new Theme();
				$theme->name = $theme->directory = $theme_dir;
				$theme->add();
			}
	
		$content = '';
		if (file_exists(_PS_IMG_DIR_.'logo.jpg'))
		{
			list($width, $height, $type, $attr) = getimagesize(_PS_IMG_DIR_.Configuration::get('PS_LOGO'));
			Configuration::updateValue('SHOP_LOGO_HEIGHT', (int)round($height));
			Configuration::updateValue('SHOP_LOGO_WIDTH', (int)round($width));
		}
		if (file_exists(_PS_IMG_DIR_.'logo_mobile.jpg'))
		{
			list($width, $height, $type, $attr) = getimagesize(_PS_IMG_DIR_.Configuration::get('PS_LOGO_MOBILE'));
			Configuration::updateValue('SHOP_LOGO_MOBILE_HEIGHT', (int)round($height));
			Configuration::updateValue('SHOP_LOGO_MOBILE_WIDTH', (int)round($width));
		}

		$this->content .= $content;
		return parent::initContent();
	}

	public function ajaxProcessGetAddonsThemes()
	{
		// notice : readfile should be replaced by something else
		/*if (@fsockopen('addons.prestashop.com', 80, $errno, $errst, 3))
			@readfile('http://addons.prestashop.com/adminthemes.php?lang='.$this->context->language->iso_code);*/

		die(Tools::file_get_contents('http://addons.prestashop.com/adminthemes.php?lang='.$this->context->language->iso_code));
	}

	/**
	 * This function checks if the theme designer has thunk to make his theme compatible 1.4,
	 * and noticed it on the $theme_dir/config.xml file. If not, some new functionnalities has
	 * to be desactivated
	 *
	 * @since 1.4
	 * @param string $theme_dir theme directory
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
					foreach ($arrConfigToCheck['attributes'] as $attr => $v)
						if (!isset($version[$codeFeature]) || !isset($version[$codeFeature][$attr]) || $version[$codeFeature][$attr] != $v['value'])
							if (!$this->_checkConfigForFeatures($codeFeature, $attr)) // feature missing in config.xml file, or wrong attribute value
								$return = false;
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
	 * @return error message, or null if disabled
	 */
	protected function _checkConfigForFeatures($arrFeatures, $configItem = array())
	{
		$return = true;
		if (is_array($configItem))
		{
			foreach ($arrFeatures as $feature)
				if (!count($configItem))
					$configItem = array_keys(AdminThemes::$check_features[$feature]['attributes']);
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
						?' <a href="?tab='.AdminThemes::$check_features[$feature]['tab'].'&amp;token='
						.Tools::getAdminTokenLite(AdminThemes::$check_features[$feature]['tab']).'" ><u>'
						.Tools::displayError('You can disable this function.')
						.'</u></a>':''
					).'<br/>';
					$return = false;
					break; // break for this attributes
				}
			}
		}
		return $return;
	}


	private function getNativeModule()
	{
		$xml = simplexml_load_string(Tools::file_get_contents('http://api.prestashop.com/xml/modules_list_15.xml'));

		if ($xml)
		{
			$natives = array();
			foreach ($xml->modules as $row)
				foreach ($row->module as $row2)
					$natives[] = (string)$row2['name'];

			if (count($natives > 0))
				return $natives;
		}

		return array('addsharethis', 'bankwire', 'blockadvertising', 'blockbanner',
			'blockbestsellers', 'blockcart', 'blockcategories', 'blockcms', 'blockcmsinfo',
			'blockcontact', 'blockcontactinfos', 'blockcurrencies', 'blockcustomerprivacy',
			'blockfacebook', 'blocklanguages', 'blocklayered', 'blocklink', 'blockmanufacturer',
			'blockmyaccount', 'blockmyaccountfooter', 'blocknewproducts', 'blocknewsletter',
			'blockpaymentlogo', 'blockpermanentlinks', 'blockreinsurance', 'blockrss',
			'blocksearch', 'blocksharefb', 'blocksocial', 'blockspecials', 'blockstore',
			'blocksupplier', 'blocktags', 'blocktopmenu', 'blockuserinfo', 'blockviewed',
			'blockwishlist', 'carriercompare', 'cashondelivery', 'cheque', 'crossselling',
			'dashactivity', 'dashgoals', 'dashproducts', 'dashtrends', 'dateofdelivery',
			'editorial', 'favoriteproducts', 'feeder', 'followup', 'gapi', 'graphnvd3',
			'gridhtml', 'homefeatured', 'homeslider', 'loyalty', 'mailalerts', 'newsletter',
			'pagesnotfound', 'productcomments', 'productpaymentlogos', 'productscategory',
			'producttooltip', 'pscleaner', 'referralprogram', 'sekeywords', 'sendtoafriend',
			'statsbestcategories', 'statsbestcustomers', 'statsbestmanufacturers',
			'statsbestproducts', 'statsbestsuppliers', 'statsbestvouchers',
			'statscarrier', 'statscatalog', 'statscheckup', 'statsdata',
			'statsequipment', 'statsforecast', 'statslive', 'statsnewsletter',
			'statsorigin', 'statspersonalinfos', 'statsproduct', 'statsregistrations',
			'statssales', 'statssearch', 'statsstock', 'statsvisits',
			'themeconfigurator', 'trackingfront', 'vatnumber', 'watermark'
		);
	}

	private function getModules($xml)
	{
		$native_modules = $this->getNativeModule();
		$theme_module = array();
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
		foreach($origin_arr as $module)
		{
			$name = $module;

			if (!class_exists($module) && file_exists(_PS_MODULE_DIR_.$module.'/'.$module.'.php'))
				require(_PS_MODULE_DIR_.$module.'/'.$module.'.php');

			if (class_exists($module))
			{
				$module_class = New $module;
				$name        = $module_class->displayName;
			}

			$tmp = array();
			$tmp['id'] = 'module'.$module;
			$tmp['val'] = $module;
			$tmp['name'] = $name;
			$fmt_arr[] = $tmp;
		}
		return $fmt_arr;
	}

	private function formatHelperValuesArray($originArr)
	{
		$fmtArr = array();
		foreach($originArr as $key => $type)
		{
			foreach($type as $module)
				$fmtArr[$key.'_module'.$module] = true;
		}
		return $fmtArr;
	}

	public function renderChooseThemeModule()
	{
		$theme = New Theme((int)Tools::getValue('id_theme'));

		$xml = false;
		if (file_exists(_PS_ROOT_DIR_ . '/config/xml/' . $theme->directory . '.xml'))
			$xml = simplexml_load_file(_PS_ROOT_DIR_ . '/config/xml/' . $theme->directory . '.xml');
		elseif (file_exists(_PS_ROOT_DIR_ . '/config/xml/default.xml'))
			$xml = simplexml_load_file(_PS_ROOT_DIR_ . '/config/xml/default.xml');

		if ($xml)
		{
			$theme_module = $this->getModules($xml);

			$toolbar_btn['save'] = array(
				'href' => '#',
				'desc' => $this->l('Save')
			);

			$to_install = $this->formatHelperArray($theme_module['to_install']);
			$to_enable  = $this->formatHelperArray($theme_module['to_enable']);
			$to_disable = $this->formatHelperArray($theme_module['to_disable']);

			$fields_form              = array(
				'form' => array(
					'tinymce' => false,
					'legend'  => array(
						'title' => $this->l('Modules to install'),
						'icon'  => 'icon-picture'
					),
					'input'   => array(
						array('type'  => 'shop',
							  'label' => $this->l('Shop association:'),
							  'name'  => 'checkBoxShopAsso'),
						array(
							'type' => 'hidden',
							'name' => 'id_theme',
						),
						array(
							'type'   => 'checkbox',
							'label'  => $this->l('Select the theme\'s modules you wish to install:'),
							'values' => array(
								'query' => $to_install,
								'id'    => 'id',
								'name'  => 'name'
							),
							'name'   => 'to_install',
						),
						array(
							'type'   => 'checkbox',
							'label'  => $this->l('Select the theme\'s modules you wish to enable:'),
							'values' => array(
								'query' => $to_enable,
								'id'    => 'id',
								'name'  => 'name'
							),
							'name'   => 'to_enable',
						),
						array(
							'type'   => 'checkbox',
							'label'  => $this->l('Select the theme\'s modules you wish to disable:'),
							'values' => array(
								'query' => $to_disable,
								'id'    => 'id',
								'name'  => 'name'
							),
							'name'   => 'to_disable',
						)
					),
					'submit'  => array(
						'title' => $this->l('Save'),
						'class' => 'button'
					))
			);

			$shops = array();
			$shop = New Shop(Configuration::get('PS_SHOP_DEFAULT'));
			$tmp['id_shop'] = $shop->id;
			$tmp['id_theme'] = $shop->id_theme;
			$shops[] = $tmp;

			if (Shop::isFeatureActive())
				$shops = Shop::getShops();

			$current_shop = Context::getContext()->shop->id;

			foreach($shops as $shop)
			{
				$shop_theme = New Theme((int)$shop['id_theme']);
				if ((int)Tools::getValue('id_theme') == (int)$shop['id_theme'])
					continue;

				if (file_exists(_PS_ROOT_DIR_ . '/config/xml/' . $shop_theme->directory . '.xml'))
				{
					$shop_xml = simplexml_load_file(_PS_ROOT_DIR_ . '/config/xml/' . $shop_theme->directory . '.xml');
					$theme_shop_module = $this->getModules($shop_xml);
					$to_shop_uninstall = $this->formatHelperArray($theme_shop_module['to_install']);

					$class = '';
					if ($shop['id_shop'] == $current_shop)
						$theme_module['to_disable_shop'.$shop['id_shop']] = $theme_shop_module['to_install'];
					else
						$class = 'hide';

					$fields_form['form']['input'][] = array('type'   => 'checkbox',
															'label'  => sprintf($this->l('Select the old %1s theme\'s modules you wish to disable:'), $shop_theme->directory),
															'formGroupClass' => $class,
															'values' => array(
																'query' => $to_shop_uninstall,
																'id'    => 'id',
																'name'  => 'name'
															),
															'name'   => 'to_disable_shop'.$shop['id_shop']
					);


				}
			}

			$fields_value = $this->formatHelperValuesArray($theme_module);

			$fields_value['id_theme'] = (int)Tools::getValue('id_theme');

			$helper = new HelperForm();

			$helper->currentIndex = $this->context->link->getAdminLink('AdminThemes', false) . '&action=ThemeInstall';
			$helper->token        = Tools::getAdminTokenLite('AdminThemes');
			$helper->show_toolbar = true;
			$helper->toolbar_btn  = $toolbar_btn;
			$helper->fields_value = $fields_value;


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
				if ($result = (bool)Db::getInstance()->executes(sprintf('SELECT * FROM `'._DB_PREFIX_.'image_type` WHERE `name` = \'%s\' ', pSQL($row['name']))))
				{
						$return['error'][] = array(
							'name' => strval($row['name']),
							'width' => (int)$row['width'],
							'height' => (int)$row['height']
						);
				}
				else
				{
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
			}

		return $return;
	}

	private function hookModule($id_module, $module_hooks, $shop)
	{

			Db::getInstance()->execute('INSERT IGNORE INTO ' . _DB_PREFIX_ . 'module_shop (id_module, id_shop) VALUES(' . $id_module . ', ' . (int)$shop . ')');

			Db::getInstance()->execute($sql = 'DELETE FROM `'._DB_PREFIX_.'hook_module` WHERE `id_module` = '.pSQL($id_module).' AND id_shop = '.(int)$shop);

			foreach ($module_hooks as $hooks)
			{
				foreach ($hooks as $hook)
				{

					$sql_hook_module = 'INSERT INTO `' . _DB_PREFIX_ . 'hook_module` (`id_module`, `id_shop`, `id_hook`, `position`)
									VALUES (' . (int)$id_module . ', ' . (int)$shop . ', ' . (int)Hook::getIdByName($hook['hook']) . ', ' . (int)$hook['position'] . ')';

					if (count($hook['exceptions'])>0)
					{
						foreach($hook['exceptions'] as $exception)
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
		if (tools::isSubmit('checkBoxShopAsso_theme'))
			$shops = Tools::getValue('checkBoxShopAsso_theme');

		$xml = false;
		if (file_exists(_PS_ROOT_DIR_ . '/config/xml/' . $theme->directory . '.xml'))
		{
			$xml = simplexml_load_file(_PS_ROOT_DIR_ . '/config/xml/' . $theme->directory . '.xml');
		}
		elseif(file_exists(_PS_ROOT_DIR_ . '/config/xml/default.xml'))
		{
			$xml = simplexml_load_file(_PS_ROOT_DIR_ . '/config/xml/default.xml');
		}

		if ($xml)
		{
			$module_hook = array();

			foreach ($xml->modules->hooks->hook as $row)
			{
				$name = strval($row['module']);

				$exceptions = (isset($row['exceptions']) ? explode(',', strval($row['exceptions'])) : array());

				$module_hook[$name]['hook'][] = array('hook'=>strval($row['hook']), 'position'=>strval($row['position']), 'exceptions'=>$exceptions);
			}

			$this->imgError = $this->updateImages($xml);

			foreach ($shops as $shop)
			{

				foreach ($_POST as $key => $value)
				{
					if (strncmp($key, 'to_install', strlen('to_install')) == 0)
					{
						if (file_exists(_PS_MODULE_DIR_ . $value))
						{
							if (!class_exists($value) && file_exists(_PS_MODULE_DIR_ . $value . '/' . $value . '.php'))
								require(_PS_MODULE_DIR_ . $value . '/' . $value . '.php');

							if (class_exists($value))
							{
								$module = Module::getInstanceByName($value);
								if (!Module::isInstalled($module->name))
									$module->install();
								else
									$module->enable();

								if ((int)$module->id > 0 && isset($module_hook[$module->name]))
									$this->hookModule($module->id, $module_hook[$module->name], $shop);
							}
							unset($module_hook[$module->name]);
						}

					} else if (strncmp($key, 'to_enable', strlen('to_enable')) == 0)
					{
						if (file_exists(_PS_MODULE_DIR_ . $value))
						{
							if (!class_exists($value) && file_exists(_PS_MODULE_DIR_ . $value . '/' . $value . '.php'))
								require(_PS_MODULE_DIR_ . $value . '/' . $value . '.php');

							if (class_exists($value))
							{
								$module = Module::getInstanceByName($value);

								if (!Module::isInstalled($module->name))
									$module->install();
								else if (!Module::isEnabled($module->name))
									$module->enable();

								if ((int)$module->id > 0 && isset($module_hook[$module->name]))
									$this->hookModule($module->id, $module_hook[$module->name], $shop);

								unset($module_hook[$module->name]);
							}
						}
					} else if (strncmp($key, 'to_disable', strlen('to_disable')) == 0)
					{
						$id_shop = (int)substr($key, 15, 1);

						if ((int)$id_shop>0 && $id_shop != (int)$shop)
							continue;

						if (file_exists(_PS_MODULE_DIR_ . $value))
						{
							if (!class_exists($value))
								require(_PS_MODULE_DIR_ . $value . '/' . $value . '.php');

							if (class_exists($value))
							{
								$module = new $value;
								if (Module::isEnabled($module->name))
								{
									$module->disable();
								}
							}
							unset($module_hook[$module->name]);
						}
					}
				}

				$shop = New Shop((int)$shop);
				$shop->id_theme = (int)Tools::getValue('id_theme');
				$shop->save();
			}

			$this->doc = array();

			foreach ($xml->docs->doc as $row)
			{
				$this->doc[strval($row['name'])] = '../themes/'.$theme->directory.'/docs/'.basename(strval($row['path']));
			}
		}

		Tools::clearCache($this->context->smarty);

		$this->themeName = $theme->name;

		$this->display='view';
	}

	public function renderView()
	{
		$this->tpl_view_vars = array(
			'doc' => $this->doc,
			'themeName' => $this->themeName,
			'imgError' => $this->imgError,
			'back_link' => Context::getContext()->link->getAdminLink('AdminThemes')
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

		if (Tools::isSubmit('id_theme') && !Tools::isSubmit('deletetheme') && Tools::getValue('action') != 'ThemeInstall')
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
	 * @return bool
	 */
	protected function updateLogo($field_name, $logo_prefix)
	{
		$id_shop = Context::getContext()->shop->id;
		if (isset($_FILES[$field_name]['tmp_name']) && $_FILES[$field_name]['tmp_name'])
		{
			if ($error = ImageManager::validateUpload($_FILES[$field_name], 300000))
				$this->errors[] = $error;

			$tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
			if (!$tmp_name || !move_uploaded_file($_FILES[$field_name]['tmp_name'], $tmp_name))
				return false;

			$ext = ($field_name == 'PS_STORES_ICON') ? '.gif' : '.jpg';
			$logo_name = $logo_prefix.'-'.(int)$id_shop.$ext;
			if (Context::getContext()->shop->getContext() == Shop::CONTEXT_ALL || $id_shop == 0 || Shop::isFeatureActive() == false)
				$logo_name = $logo_prefix.$ext;

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

			Configuration::updateValue($field_name, $logo_name);
			$this->fields_options['appearance']['fields'][$field_name]['thumb'] = _PS_IMG_.$logo_name.'?date='.time();

			unlink($tmp_name);
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
		parent::initProcess();
		// This is a composite page, we don't want the "options" display mode
		if ($this->display == 'options')
			$this->display = '';
	}

	/**
	 * Function used to render the options for this controller
	 */
	public function renderOptions()
	{
		if ($this->fields_options && is_array($this->fields_options))
		{
			$helper = new HelperOptions($this);
			$this->setHelperDisplay($helper);
			$helper->toolbar_scroll = true;
			$helper->title = $this->l('Theme appearance');
			$helper->toolbar_btn = array('save' => array(
								'href' => '#',
								'desc' => $this->l('Save')
							));
			$helper->id = $this->id;
			$helper->tpl_vars = $this->tpl_option_vars;
			$options = $helper->generateOptions($this->fields_options);

			return $options;
		}
	}
}
