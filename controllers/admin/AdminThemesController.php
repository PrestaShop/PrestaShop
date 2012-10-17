<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 7346 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminThemesControllerCore extends AdminController
{
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
						'PS_HIGH_HTML_THEME_COMPRESSION' => 0,
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

	public function init()
	{
		// No cache for auto-refresh uploaded logo
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

		parent::init();

		$this->can_display_themes = (!Shop::isFeatureActive() || Shop::getContext() == Shop::CONTEXT_SHOP) ? true : false;

		$this->fields_options = array(
			'theme' => array(
				'title' => sprintf($this->l('Select theme for shop %s'), $this->context->shop->name),
				'description' => (!$this->can_display_themes) ? $this->l('You must select a shop from the above list if you want to select a theme') : '',
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
				'icon' =>	'email',
				'fields' =>	array(
					'PS_LOGO' => array(
						'title' => $this->l('Header logo'),
						'desc' => $this->l('Will appear on main page'),
						'type' => 'file',
						'thumb' => _PS_IMG_.Configuration::get('PS_LOGO').'?date='.time()
					),
					'PS_LOGO_MAIL' => array(
						'title' => $this->l('Mail logo'),
						'desc' => 
							((Configuration::get('PS_LOGO_MAIL') === false) ? '<span class="light-warning">'.$this->l('Warning: No e-mail logo defined, the header logo is used instead.').'</span><br />' : '').
							$this->l('Will appear on e-mail headers. If undefined, the Header logo will be used'),
						'type' => 'file',
						'thumb' => (Configuration::get('PS_LOGO_MAIL') !== false && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO_MAIL'))) ? _PS_IMG_.Configuration::get('PS_LOGO_MAIL').'?date='.time() : _PS_IMG_.Configuration::get('PS_LOGO').'?date='.time()
					),
					'PS_LOGO_INVOICE' => array(
						'title' => $this->l('Invoice logo'),
						'desc' => 
							((Configuration::get('PS_LOGO_INVOICE') === false) ? '<span class="light-warning">'.$this->l('Warning: No invoice logo defined, the header logo is used instead.').'</span><br />' : '').
							$this->l('Will appear on invoice headers. If undefined, the Header logo will be used'),
						'type' => 'file',
						'thumb' => (Configuration::get('PS_LOGO_INVOICE') !== false && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO_INVOICE'))) ? _PS_IMG_.Configuration::get('PS_LOGO_INVOICE').'?date='.time() : _PS_IMG_.Configuration::get('PS_LOGO').'?date='.time()
					),
					'PS_FAVICON' => array(
						'title' => $this->l('Favicon'),
						'hint' => $this->l('Only ICO format allowed'),
						'desc' => $this->l('Will appear in the address bar of your web browser'),
						'type' => 'file',
						'thumb' => _PS_IMG_.Configuration::get('PS_FAVICON').'?date='.time()
					),
					'PS_STORES_ICON' => array(
						'title' => $this->l('Store icon'),
						'hint' => $this->l('Only GIF format allowed'),
						'desc' => $this->l('Will appear on the store locator (inside Google Maps)').'<br />'.$this->l('Suggested size: 30x30, Transparent GIF'),
						'type' => 'file',
						'thumb' => _PS_IMG_.Configuration::get('PS_STORES_ICON').'?date='.time()
					),
					'PS_NAVIGATION_PIPE' => array(
						'title' => $this->l('Navigation pipe'),
						'desc' => $this->l('Used for navigation path inside categories/product'),
						'cast' => 'strval',
						'type' => 'text',
						'size' => 20
					),
					'PS_ALLOW_MOBILE_DEVICE' => array(
						'title' => $this->l('Enable mobile theme'),
						'desc' => $this->l('Allows visitors browsing on a mobile device or on a touchpad, to have a light version of website'),
						'type' => 'radio',
						'required' => true,
						'validation' => 'isGenericName',
						'choices' => array(
							0 => $this->l('I want to disable it'),
							1 => $this->l('I want to enable it only on mobiles devices'),
							2 => $this->l('I want to enable it only on touchpads'),
							3 => $this->l('I want to enable it on mobile and touchpad devices')
						)
					)
				),
				'submit' => array('title' => $this->l('Save'), 'class' => 'button')
			)
		);

		$this->fields_list = array(
			'id_theme' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'width' => 20,
			),
			'name' => array(
				'title' => $this->l('Name'),
				'width' => 'auto',
			),
			'directory' => array(
				'title' => $this->l('Directory'),
				'width' => 'auto',
			),
		);

	}

	public function renderForm()
	{
		$getAvailableThemes = Theme::getAvailable(false);
		$available_theme_dir = array();
		$selected_theme_dir = null;
		if ($this->object)
			$selected_theme_dir = $this->object->directory;
		
		foreach ($getAvailableThemes as $k => $dirname)
		{
			$available_theme_dir[$k]['value'] = $dirname;
			$available_theme_dir[$k]['label'] = $dirname;
			$available_theme_dir[$k]['id'] = $dirname;
		};

		$this->fields_form = array(
			'tinymce' => false,
			'legend' => array(
				'title' => $this->l('Theme'),
				'image' => '../img/admin/themes.gif'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Name of the theme:'),
					'name' => 'name',
					'size' => 48,
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
					'desc' => $this->l('If the directory does not exists, it will be created.'),
				);

			$theme_query = Theme::getThemes();
			$this->fields_form['input'][] = array(
				'type' => 'select',
				'name' => 'based_on',
				'label' => $this->l('Copy missing files from existing theme:'),
				'desc' => $this->l('If you create a new theme, it\'s recommended to use default theme files.'),
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
					'class' => 't',
					'values' => $available_theme_dir,
					'selected' => $selected_theme_dir,
					'desc' => $this->l('Please select a valid theme directory.'),
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
					$this->confirmations[] = $this->l('Directory successfully created');
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
			$this->errors[] = $this->l('This theme is already used by at least one shop. Please choose another theme first.');
			return false;
		}

		return parent::processDelete();
	}

	public function initContent()
	{
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

		$this->content .= $content;
		return parent::initContent();
	}

	public function ajaxProcessGetAddonsThemes()
	{
		// notice : readfile should be replaced by something else
		if (@fsockopen('addons.prestashop.com', 80, $errno, $errst, 3))
			@readfile('http://addons.prestashop.com/adminthemes.php?lang='.$this->context->language->iso_code);
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
			$this->errors[] .= Tools::displayError('config.xml theme file has not been created for this version of prestashop.');
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
						.Tools::displayError('You can disable this function on this page')
						.'</u></a>':''
					).'<br/>';
					$return = false;
					break; // break for this attributes
				}
			}
		}
		return $return;
	}

	/**
	 * This functions make checks about AdminThemes configuration edition only.
	 *
	 * @since 1.4
	 */
	public function postProcess()
	{
		// new check compatibility theme feature (1.4) :
		$val = Tools::getValue('PS_THEME');
		Configuration::updateValue('PS_IMG_UPDATE_TIME', time());
		if (!empty($val) && !$this->_isThemeCompatible($val)) // don't submit if errors
			unset($_POST['submitThemes'.$this->table]);
		Tools::clearCache($this->context->smarty);

		parent::postProcess();
	}

	/**
	 * Update PS_LOGO
	 */
	public function updateOptionPsLogo()
	{
		$this->updateLogo('PS_LOGO', 'logo');
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
			if (Context::getContext()->shop->getContext() == Shop::CONTEXT_ALL || $id_shop == 0)
				$logo_name = $logo_prefix.$ext;

			if ($field_name == 'PS_STORES_ICON')
			{
				if (!@ImageManager::resize($tmp_name, _PS_IMG_DIR_.$logo_name, null, null, 'gif', true))
					$this->errors[] = Tools::displayError('An error occurred during logo copy.');
			}
			else
			{
				if (!@ImageManager::resize($tmp_name, _PS_IMG_DIR_.$logo_name))
					$this->errors[] = Tools::displayError('An error occurred during logo copy.');
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
