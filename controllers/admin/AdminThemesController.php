<?php
/*
* 2007-2011 PrestaShop
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
*  @copyright  2007-2011 PrestaShop SA
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
		'ccc' => array( // feature key name
			'attributes' => array(
				'available' => array(
					'value' => 'true', // accepted attribute value
					// if value doesnt match,
					// prestashop configuration value must have thoses values
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

	public function __construct()
	{
		$this->className = 'Theme';
		$this->table = 'theme';

		parent::__construct();

		// Thumbnails filenames depend on multishop activation
		if (Shop::isFeatureActive())
			$shop_suffix = '-'.(int)Context::getContext()->shop->getID();
		else
			$shop_suffix = '';

		$this->options = array(
			'appearance' => array(
				'title' =>	$this->l('Appearance'),
				'icon' =>	'email',
				'class' => 'width3',
				'fields' =>	array(
					'PS_LOGO' => array('title' => $this->l('Header logo:'), 'desc' => $this->l('Will appear on main page'), 'type' => 'file', 'thumb' => _PS_IMG_.'logo'.$shop_suffix.'.jpg?date='.time()),
					'PS_LOGO_MAIL' => array('title' => $this->l('Mail logo:'), 'desc' => $this->l('Will appear on e-mail headers, if undefined the Header logo will be used'), 'type' => 'file', 'thumb' => (file_exists(_PS_IMG_DIR_.'logo_mail'.$shop_suffix.'.jpg')) ? _PS_IMG_.'logo_mail'.$shop_suffix.'.jpg?date='.time() : _PS_IMG_.'logo'.$shop_suffix.'.jpg?date='.time()),
					'PS_LOGO_INVOICE' => array('title' => $this->l('Invoice logo:'), 'desc' => $this->l('Will appear on invoices headers, if undefined the Header logo will be used'), 'type' => 'file', 'thumb' => file_exists(_PS_IMG_DIR_.'logo_invoice'.$shop_suffix.'.jpg') ? _PS_IMG_.'logo_invoice'.$shop_suffix.'.jpg?date='.time() : _PS_IMG_.'logo'.$shop_suffix.'.jpg?date='.time()),
					'PS_FAVICON' => array('title' => $this->l('Favicon:'), 'desc' => $this->l('Will appear in the address bar of your web browser'), 'type' => 'file', 'thumb' => _PS_IMG_.'favicon'.$shop_suffix.'.ico?date='.time()),
					'PS_STORES_ICON' => array('title' => $this->l('Store icon:'), 'desc' => $this->l('Will appear on the store locator (inside Google Maps)').'<br />'.$this->l('Suggested size: 30x30, Transparent GIF'), 'type' => 'file', 'thumb' => _PS_IMG_.'logo_stores'.$shop_suffix.'.gif?date='.time()),
					'PS_NAVIGATION_PIPE' => array('title' => $this->l('Navigation pipe:'), 'desc' => $this->l('Used for navigation path inside categories/product'), 'cast' => 'strval', 'type' => 'text', 'size' => 20),
				),
				'submit' => array('title' => $this->l('   Save   '), 'class' => 'button')
 			),
 		);

		$this->fieldsDisplay = array(
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

		$getAvailableThemes = Theme::getAvailable(false);
		$available_theme_directories = array();
		
		$selected_theme_directory = null;
		if ($this->loadObject(true))
			$selected_theme_directory = $this->object->directory;

		foreach($getAvailableThemes as $k => $dirname)
		{
			$available_theme_directories[$k]['value'] = $dirname;
			$available_theme_directories[$k]['label'] = $dirname;
			$available_theme_directories[$k]['id'] = $dirname;
		};
		$this->fields_form = array(
			'tinymce' => false,
			'legend' => array(
				'title' => $this->l('Theme'),
				'image' => '../img/admin/tab-themes.gif'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Name:'),
					'name' => 'name',
					'size' => 48,
					'required' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}',
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Directory:'),
					'name' => 'directory',
					'required' => true,
					'br' => true,
					'class' => 't',
					'values' => $available_theme_directories,
					'selected' => $selected_theme_directory,
					'desc' => $this->l('Note: only the existence of the directory is checked. Please be sure to select a valid theme directory.'),
				),
			),
			'submit' => array(
				'title' => $this->l('   Save   '),
				'class' => 'button'
			)
		);
	}

	public function renderList(){
		$this->addRowAction('edit');
		$this->addRowAction('delete');

	//	$this->_filter .= ' AND `id_parent` = '.(int)$this->_category->id.' ';
	//	$this->_select = 'position ';

		return parent::renderList();
	}

	public function processDelete($token){
		$obj = $this->loadObject();
		if ($obj && $obj->isUsed())
		{
			$this->_errors[] = $this->l('This theme is used by at least one shop. Please choose another theme first.');
			return false;
		}

		return parent::processDelete($token);
	}

	public function initContent()
	{
		$content = '';
		if (file_exists(_PS_IMG_DIR_.'logo.jpg'))
		{
			list($width, $height, $type, $attr) = getimagesize(_PS_IMG_DIR_.'logo.jpg');
			Configuration::updateValue('SHOP_LOGO_WIDTH', (int)round($width));
			Configuration::updateValue('SHOP_LOGO_HEIGHT', (int)round($height));
		}
		// No cache for auto-refresh uploaded logo
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		//	$this->displayOptionsList();

/*		if (@ini_get('allow_url_fopen') AND @fsockopen('addons.prestashop.com', 80, $errno, $errst, 3))
			$content .= '<script type="text/javascript">
				$.post("'.dirname(self::$currentIndex).'/ajax.php",{page:"themes"},function(a){getE("prestastore-content").innerHTML="<legend><img src=\"../img/admin/prestastore.gif\" class=\"middle\" /> '.$this->l('Live from PrestaShop Addons!').'</legend>"+a;});
			</script>
			<fieldset id="prestastore-content" class="width3">ZZZZZ</fieldset>';
		else
			$content .= '<a href="http://addons.prestashop.com/3-prestashop-themes">'.$this->l('Find new themes on PrestaShop Addons!').'</a>';
			*/
		$this->content .= $content;
		return parent::initContent();
	}

	public function ajaxProcessGetAddonsThemes()
	{
		// notice : readfile should be replaced by something else
		if (@fsockopen('addons.prestashop.com', 80, $errno, $errst, 3))
			readfile('http://addons.prestashop.com/adminmodules.php?lang='.$this->context->language->iso_code);
		$this->content = '';
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
	private function _isThemeCompatible($theme_dir)
	{
		$all_errors='';
		$return=true;
		$check_version=AdminThemes::$check_features_version;

		if (!is_file(_PS_ALL_THEMES_DIR_ . $theme_dir . '/config.xml'))
		{
			$this->_errors[] = Tools::displayError('config.xml is missing in your theme path.').'<br/>';
			$xml=null;
		}
		else
		{
			$xml=@simplexml_load_file(_PS_ALL_THEMES_DIR_.$theme_dir.'/config.xml');
			if (!$xml)
			{
				$this->_errors[] = Tools::displayError('config.xml is not a valid xml file in your theme path.').'<br/>';
			}
		}
		// will be set to false if any version node in xml is correct
		$xml_version_too_old = true;

		// foreach version in xml file,
		// node means feature, attributes has to match
		// the corresponding value in AdminThemes::$check_features[feature] array
		$xmlArray = simpleXMLToArray($xml);
		foreach($xmlArray AS $version)
		{
			if (isset($version['value']) AND version_compare($version['value'], $check_version) >= 0)
			{
				$checkedFeature = array();
				foreach (AdminThemes::$check_features AS $codeFeature => $arrConfigToCheck)
					foreach ($arrConfigToCheck['attributes'] AS $attr => $v)
						if (!isset($version[$codeFeature]) OR !isset($version[$codeFeature][$attr]) OR $version[$codeFeature][$attr] != $v['value'])
							if (!$this->_checkConfigForFeatures($codeFeature, $attr)) // feature missing in config.xml file, or wrong attribute value
								$return = false;
				$xml_version_too_old = false;
			}
		}
		if ($xml_version_too_old AND !$this->_checkConfigForFeatures(array_keys(AdminThemes::$check_features)))
		{
			$this->_errors[] .= Tools::displayError('config.xml theme file has not been created for this version of prestashop.');
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
	private function _checkConfigForFeatures($arrFeatures, $configItem = array())
	{
		$return = true;
		if (is_array($configItem))
		{
			foreach ($arrFeatures as $feature)
				if (!sizeof($configItem))
					$configItem = array_keys(AdminThemes::$check_features[$feature]['attributes']);
			foreach ($configItem as $attr)
			{
				$check = $this->_checkConfigForFeatures($arrFeatures,$attr);
				if($check == false)
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
					$this->_errors[] = Tools::displayError(AdminThemes::$check_features[$feature]['error']).'.'
					.(!empty(AdminThemes::$check_features[$feature]['tab'])
						?' <a href="?tab='.AdminThemes::$check_features[$feature]['tab'].'&amp;token='
						.Tools::getAdminTokenLite(AdminThemes::$check_features[$feature]['tab']).'" ><u>'
						.Tools::displayError('You can disable this function on this page')
						.'</u></a>':''
					).'<br/>' ;
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
		if (!empty($val) AND !$this->_isThemeCompatible($val)) // don't submit if errors
			unset($_POST['submitThemes'.$this->table]);
		Tools::clearCache($this->context->smarty);

		parent::postProcess();
	}

	/**
	 * Update PS_LOGO
	 */
	public function updateOptionPsLogo()
	{
		$id_shop = Context::getContext()->shop->getID();
		if (isset($_FILES['PS_LOGO']['tmp_name']) AND $_FILES['PS_LOGO']['tmp_name'])
		{
			if ($error = checkImage($_FILES['PS_LOGO'], 300000))
				$this->_errors[] = $error;
			if (!$tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS') OR !move_uploaded_file($_FILES['PS_LOGO']['tmp_name'], $tmpName))
				return false;
			if ($id_shop == Configuration::get('PS_SHOP_DEFAULT') && !@imageResize($tmpName, _PS_IMG_DIR_.'logo.jpg'))
				$this->_errors[] = 'an error occurred during logo copy';
			if (!@imageResize($tmpName, _PS_IMG_DIR_.'logo-'.(int)$id_shop.'.jpg'))
				$this->_errors[] = 'an error occurred during logo copy';

			unlink($tmpName);
		}
	}

	/**
	 * Update PS_LOGO_MAIL
	 */
	public function updateOptionPsLogoMail()
	{
		$id_shop = Context::getContext()->shop->getID();
		if (isset($_FILES['PS_LOGO_MAIL']['tmp_name']) AND $_FILES['PS_LOGO_MAIL']['tmp_name'])
		{
			if ($error = checkImage($_FILES['PS_LOGO_MAIL'], 300000))
				$this->_errors[] = $error;
			if (!$tmpName == tempnam(_PS_TMP_IMG_DIR_, 'PS_MAIL') OR !move_uploaded_file($_FILES['PS_LOGO_MAIL']['tmp_name'], $tmpName))
				return false;
			if ($id_shop == Configuration::get('PS_SHOP_DEFAULT') && !@imageResize($tmpName, _PS_IMG_DIR_.'logo_mail.jpg'))
				$this->_errors[] = 'an error occurred during logo copy';
			if (!@imageResize($tmpName, _PS_IMG_DIR_.'logo_mail-'.(int)$id_shop.'.jpg'))
				$this->_errors[] = 'an error occurred during logo copy';
			unlink($tmpName);
		}
	}

	/**
	 * Update PS_LOGO_INVOICE
	 */
	public function updateOptionPsLogoInvoice()
	{
		$id_shop = Context::getContext()->shop->getID();
		if (isset($_FILES['PS_LOGO_INVOICE']['tmp_name']) AND $_FILES['PS_LOGO_INVOICE']['tmp_name'])
		{
			if ($error = checkImage($_FILES['PS_LOGO_INVOICE'], 300000))
				$this->_errors[] = $error;
			if (!$tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS_INVOICE') OR !move_uploaded_file($_FILES['PS_LOGO_INVOICE']['tmp_name'], $tmpName))
				return false;
			if ($id_shop == Configuration::get('PS_SHOP_DEFAULT') && !@imageResize($tmpName, _PS_IMG_DIR_.'logo_invoice.jpg'))
				$this->_errors[] = 'an error occurred during logo copy';
			if (!@imageResize($tmpName, _PS_IMG_DIR_.'logo_invoice-'.(int)$id_shop.'.jpg'))
				$this->_errors[] = 'an error occurred during logo copy';

			unlink($tmpName);
		}
	}

	/**
	 * Update PS_STORES_ICON
	 */
	public function updateOptionPsStoresIcon()
	{
		$id_shop = Context::getContext()->shop->getID();
		if (isset($_FILES['PS_STORES_ICON']['tmp_name']) AND $_FILES['PS_STORES_ICON']['tmp_name'])
		{
			if ($error = checkImage($_FILES['PS_STORES_ICON'], 300000))
				$this->_errors[] = $error;
			if (!$tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS_STORES_ICON') OR !move_uploaded_file($_FILES['PS_STORES_ICON']['tmp_name'], $tmpName))
				return false;
			if ($id_shop = Configuration::get('PS_SHOP_DEFAULT') && !@imageResize($tmpName, _PS_IMG_DIR_.'logo_stores.gif'))
				$this->_errors[] = 'an error occurred during logo copy';
			if (!@imageResize($tmpName, _PS_IMG_DIR_.'logo_stores-'.(int)$id_shop.'.gif'))
				$this->_errors[] = 'an error occurred during logo copy';
			unlink($tmpName);
		}

		if ($id_shop = Configuration::get('PS_SHOP_DEFAULT'))
			$this->uploadIco('PS_FAVICON', _PS_IMG_DIR_.'favicon.ico');
		$this->uploadIco('PS_FAVICON', _PS_IMG_DIR_.'favicon-'.(int)$id_shop.'.ico');
	}

	protected function uploadIco($name, $dest)
	{

		if (isset($_FILES[$name]['tmp_name']) && !empty($_FILES[$name]['tmp_name']))
		{
			/* Check ico validity */
			if ($error = checkIco($_FILES[$name]))
				$this->_errors[] = $error;

			/* Copy new ico */
			elseif(!copy($_FILES[$name]['tmp_name'], $dest))
				$this->_errors[] = Tools::displayError('an error occurred while uploading favicon: '.$_FILES[$name]['tmp_name'].' to '.$dest);
		}
		return !count($this->_errors) ? true : false;
	}
}
