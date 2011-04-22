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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminPreferences extends AdminTab
{
	public function __construct()
	{
		global $cookie;

		$this->className = 'Configuration';
		$this->table = 'configuration';

		$timezones = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT name FROM '._DB_PREFIX_.'timezone');
		$taxes[] = array('id' => 0, 'name' => $this->l('None'));
		foreach (Tax::getTaxes((int)($cookie->id_lang)) as $tax)
			$taxes[] = array('id' => $tax['id_tax'], 'name' => $tax['name']);

		$order_process_type = array(
			array(
				'value' => PS_ORDER_PROCESS_STANDARD,
				'name' => $this->l('Standard (5 steps)')
			),
			array(
				'value' => PS_ORDER_PROCESS_OPC,
				'name' => $this->l('One page checkout')
			)
		);

		$round_mode = array(
			array(
				'value' => PS_ROUND_UP,
				'name' => $this->l('superior')
			),
			array(
				'value' => PS_ROUND_DOWN,
				'name' => $this->l('inferior')
			),
			array(
				'value' => PS_ROUND_HALF,
				'name' => $this->l('classical')
			)
		);

		$cms_tab = array(0 =>
			array(
				'id' => 0,
				'name' => $this->l('None')
			)
		);
		foreach (CMS::listCms($cookie->id_lang) as $cms_file)
			$cms_tab[] = array('id' => $cms_file['id_cms'], 'name' => $cms_file['meta_title']);

		$this->_fieldsGeneral = array(
			'PS_SHOP_ENABLE' => array('title' => $this->l('Enable Shop'), 'desc' => $this->l('Activate or deactivate your shop. Deactivate your shop while you perform maintenance on it. Please note that the webservice will not be disabled'), 'validation' => 'isBool', 'cast' => 'intval', 'type' => 'bool'),
			'PS_MAINTENANCE_IP' => array('title' => $this->l('Maintenance IP'), 'desc' => $this->l('IP addresses allowed to access the Front Office even if shop is disabled. Use a comma to separate them (e.g., 42.24.4.2,127.0.0.1,99.98.97.96)'), 'validation' => 'isGenericName', 'type' => 'text', 'size' => 15, 'default' => ''),
			'PS_SSL_ENABLED' => array('title' => $this->l('Enable SSL'), 'desc' => $this->l('If your hosting provider allows SSL, you can activate SSL encryption (https://) for customer account identification and order processing'), 'validation' => 'isBool', 'cast' => 'intval', 'type' => 'bool', 'default' => '0'),
			'PS_COOKIE_CHECKIP' => array('title' => $this->l('Check IP on the cookie'), 'desc' => $this->l('Check the IP address of the cookie in order to avoid your cookie being stolen'), 'validation' => 'isBool', 'cast' => 'intval', 'type' => 'bool', 'default' => '0'),
			'PS_TOKEN_ENABLE' => array('title' => $this->l('Increase Front Office security'), 'desc' => $this->l('Enable or disable token on the Front Office in order to improve PrestaShop security'), 'validation' => 'isBool', 'cast' => 'intval', 'type' => 'bool', 'default' => '0'),
			'PS_HELPBOX' => array('title' => $this->l('Back Office help boxes'), 'desc' => $this->l('Enable yellow help boxes which are displayed under form fields in the Back Office'), 'validation' => 'isBool', 'cast' => 'intval', 'type' => 'bool'),
			'PS_ORDER_PROCESS_TYPE' => array('title' => $this->l('Order process type'), 'desc' => $this->l('You can choose the order process type as either standard (5 steps) or One Page Checkout'), 'validation' => 'isInt', 'cast' => 'intval', 'type' => 'select', 'list' => $order_process_type, 'identifier' => 'value'),
			'PS_GUEST_CHECKOUT_ENABLED' => array('title' => $this->l('Enable guest checkout'), 'desc' => $this->l('Your guest can make an order without registering'), 'validation' => 'isBool', 'cast' => 'intval', 'type' => 'bool'),
			'PS_CONDITIONS' => array('title' => $this->l('Terms of service'), 'desc' => $this->l('Require customers to accept or decline terms of service before processing the order'), 'validation' => 'isBool', 'cast' => 'intval', 'type' => 'bool', 'js' => array('on' => 'onchange="changeCMSActivationAuthorization()"', 'off' => 'onchange="changeCMSActivationAuthorization()"')),
			'PS_CONDITIONS_CMS_ID' => array('title' => $this->l('Conditions of use CMS page'), 'desc' => $this->l('Choose the Conditions of use CMS page'), 'validation' => 'isInt', 'type' => 'select', 'list' => $cms_tab, 'identifier' => 'id', 'cast' => 'intval'),
			'PS_GIFT_WRAPPING' => array('title' => $this->l('Offer gift-wrapping'), 'desc' => $this->l('Suggest gift-wrapping to customer and possibility of leaving a message'), 'validation' => 'isBool', 'cast' => 'intval', 'type' => 'bool'),
			'PS_GIFT_WRAPPING_PRICE' => array('title' => $this->l('Gift-wrapping price'), 'desc' => $this->l('Set a price for gift-wrapping'), 'validation' => 'isPrice', 'cast' => 'floatval', 'type' => 'price'),
			'PS_GIFT_WRAPPING_TAX' => array('title' => $this->l('Gift-wrapping tax'), 'desc' => $this->l('Set a tax for gift-wrapping'), 'validation' => 'isInt', 'cast' => 'intval', 'type' => 'select', 'list' => $taxes, 'identifier' => 'id'),
			'PS_ATTACHMENT_MAXIMUM_SIZE' => array('title' => $this->l('Attachment maximum size'), 'desc' => $this->l('Set the maximum size of attachment files (in MegaBytes)'), 'validation' => 'isInt', 'cast' => 'intval', 'type' => 'text'),
			'PS_RECYCLABLE_PACK' => array('title' => $this->l('Offer recycled packaging'), 'desc' => $this->l('Suggest recycled packaging to customer'), 'validation' => 'isBool', 'cast' => 'intval', 'type' => 'bool'),
			'PS_CART_FOLLOWING' => array('title' => $this->l('Cart re-display at login'), 'desc' => $this->l('After customer logs in, recall and display contents of his/her last shopping cart'), 'validation' => 'isBool', 'cast' => 'intval', 'type' => 'bool'),
			'PS_PRICE_ROUND_MODE' => array('title' => $this->l('Round mode'), 'desc' => $this->l('You can choose how to round prices: always round superior; always round inferior, or classic rounding'), 'validation' => 'isInt', 'cast' => 'intval', 'type' => 'select', 'list' => $round_mode, 'identifier' => 'value'),
			'PRESTASTORE_LIVE' => array('title' => $this->l('Automatically check for module updates'), 'desc' => $this->l('New modules and updates are displayed on the modules page'), 'validation' => 'isBool', 'cast' => 'intval', 'type' => 'bool'),
			'PS_HIDE_OPTIMIZATION_TIPS' => array('title' => $this->l('Hide optimization tips'), 'desc' => $this->l('Hide optimization tips on the back office homepage'), 'validation' => 'isBool', 'cast' => 'intval', 'type' => 'bool'),
			'PS_DISPLAY_SUPPLIERS' => array('title' => $this->l('Display suppliers and manufacturers'), 'desc' => $this->l('Display manufacturers and suppliers list even if corresponding blocks are disabled'), 'validation' => 'isBool', 'cast' => 'intval', 'type' => 'bool'),
			'PS_FORCE_SMARTY_2' => array('title' => $this->l('Use Smarty 2 instead of 3'), 'desc' => $this->l('Enable if your theme is incompatible with Smarty 3 (you should update your theme, since Smarty 2 will be unsupported from PrestaShop v1.5)'), 'validation' => 'isBool', 'cast' => 'intval', 'type' => 'bool'),
		);
			if (function_exists('date_default_timezone_set'))
				$this->_fieldsGeneral['PS_TIMEZONE'] = array('title' => $this->l('Time Zone:'), 'validation' => 'isAnything', 'type' => 'select', 'list' => $timezones, 'identifier' => 'name');


		parent::__construct();
	}

	public function display()
	{
		$this->_displayForm('general', $this->_fieldsGeneral, $this->l('General'), '', 'tab-preferences');
	}

	public function postProcess()
	{
		global $currentIndex;

		if (isset($_POST['submitGeneral'.$this->table]))
		{
			Module::hookExec('categoryUpdate'); // We call this hook, for regenerate cache of categories
			if (Tools::getValue('PS_CONDITIONS') == true AND (Tools::getValue('PS_CONDITIONS_CMS_ID') == 0 OR !Db::getInstance()->getValue('
			SELECT `id_cms` FROM `'._DB_PREFIX_.'cms`
			WHERE id_cms = '.(int)(Tools::getValue('PS_CONDITIONS_CMS_ID')))))
				$this->_errors[] = Tools::displayError('Assign a valid CMS page if you want it to be read.');
		 	if ($this->tabAccess['edit'] === '1')
				$this->_postConfig($this->_fieldsGeneral);
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		elseif (isset($_POST['submitShop'.$this->table]))
		{
		 	if ($this->tabAccess['edit'] === '1')
				$this->_postConfig($this->_fieldsShop);
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		elseif (isset($_POST['submitAppearance'.$this->table]))
		{
		 	if ($this->tabAccess['edit'] === '1')
				$this->_postConfig($this->_fieldsAppearance);
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		elseif (isset($_POST['submitThemes'.$this->table]))
		{
		 	if ($this->tabAccess['edit'] === '1')
		 	{
				if ($val = Tools::getValue('PS_THEME'))
				{
					if (rewriteSettingsFile(NULL, $val, NULL))
						Tools::redirectAdmin($currentIndex.'&conf=6'.'&token='.$this->token);
					else
						$this->_errors[] = Tools::displayError('Cannot access settings file.');
				}
				else
					$this->_errors[] = Tools::displayError('You must choose a graphical theme.');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		parent::postProcess();
	}

	/**
	  * Update settings in database and configuration files
	  *
	  * @params array $fields Fields settings
	  *
	  * @global string $currentIndex Current URL in order to keep current Tab
	  */
	protected function _postConfig($fields)
	{
		global $currentIndex, $smarty;

		$languages = Language::getLanguages(false);
		if (!Configuration::get('PS_FORCE_SMARTY_2'))
		{
			$files = scandir(_PS_THEME_DIR_);
			foreach ($files AS $file)
				if (!preg_match('/^\..*/', $file))
						$smarty->clearCache($file);
		}
		else
			$smarty->clear_all_cache();

		/* Check required fields */
		foreach ($fields AS $field => $values)
			if (isset($values['required']) AND $values['required'])
				if (isset($values['type']) AND $values['type'] == 'textLang')
				{
					foreach ($languages as $language)
						if (($value = Tools::getValue($field.'_'.$language['id_lang'])) == false AND (string)$value != '0')
							$this->_errors[] = Tools::displayError('field').' <b>'.$values['title'].'</b> '.Tools::displayError('is required.');
				}
				elseif (($value = Tools::getValue($field)) == false AND (string)$value != '0')
					$this->_errors[] = Tools::displayError('field').' <b>'.$values['title'].'</b> '.Tools::displayError('is required.');

		/* Check fields validity */
		foreach ($fields AS $field => $values)
			if (isset($values['type']) AND $values['type'] == 'textLang')
			{
				foreach ($languages as $language)
					if (Tools::getValue($field.'_'.$language['id_lang']) AND isset($values['validation']))
						if (!Validate::$values['validation'](Tools::getValue($field.'_'.$language['id_lang'])))
							$this->_errors[] = Tools::displayError('field').' <b>'.$values['title'].'</b> '.Tools::displayError('is invalid.');
			}
			elseif (Tools::getValue($field) AND isset($values['validation']))
				if (!Validate::$values['validation'](Tools::getValue($field)))
					$this->_errors[] = Tools::displayError('field').' <b>'.$values['title'].'</b> '.Tools::displayError('is invalid.');

		/* Default value if null */
		foreach ($fields AS $field => $values)
			if (!Tools::getValue($field) AND isset($values['default']))
				$_POST[$field] = $values['default'];

		/* Save process */
		if (!sizeof($this->_errors))
		{
			if (Tools::isSubmit('submitAppearanceconfiguration'))
			{
				if (isset($_FILES['PS_LOGO']['tmp_name']) AND $_FILES['PS_LOGO']['tmp_name'])
				{
					if ($error = checkImage($_FILES['PS_LOGO'], 300000))
						$this->_errors[] = $error;
					if (!$tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS') OR !move_uploaded_file($_FILES['PS_LOGO']['tmp_name'], $tmpName))
						return false;
					elseif (!@imageResize($tmpName, _PS_IMG_DIR_.'logo.jpg'))
						$this->_errors[] = 'an error occurred during logo copy';
					unlink($tmpName);
				}
				if (isset($_FILES['PS_LOGO_MAIL']['tmp_name']) AND $_FILES['PS_LOGO_MAIL']['tmp_name'])
				{
					if ($error = checkImage($_FILES['PS_LOGO_MAIL'], 300000))
						$this->_errors[] = $error;
					if (!$tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS_MAIL') OR !move_uploaded_file($_FILES['PS_LOGO_MAIL']['tmp_name'], $tmpName))
						return false;
					elseif (!@imageResize($tmpName, _PS_IMG_DIR_.'logo_mail.jpg'))
						$this->_errors[] = 'an error occurred during logo copy';
					unlink($tmpName);
				}
				if (isset($_FILES['PS_LOGO_INVOICE']['tmp_name']) AND $_FILES['PS_LOGO_INVOICE']['tmp_name'])
				{
					if ($error = checkImage($_FILES['PS_LOGO_INVOICE'], 300000))
						$this->_errors[] = $error;
					if (!$tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS_INVOICE') OR !move_uploaded_file($_FILES['PS_LOGO_INVOICE']['tmp_name'], $tmpName))
						return false;
					elseif (!@imageResize($tmpName, _PS_IMG_DIR_.'logo_invoice.jpg'))
						$this->_errors[] = 'an error occurred during logo copy';
					unlink($tmpName);
				}
				if (isset($_FILES['PS_STORES_ICON']['tmp_name']) AND $_FILES['PS_STORES_ICON']['tmp_name'])
				{
					if ($error = checkImage($_FILES['PS_STORES_ICON'], 300000))
						$this->_errors[] = $error;
					if (!$tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS_STORES_ICON') OR !move_uploaded_file($_FILES['PS_STORES_ICON']['tmp_name'], $tmpName))
						return false;
					elseif (!@imageResize($tmpName, _PS_IMG_DIR_.'logo_stores.gif'))
						$this->_errors[] = 'an error occurred during logo copy';
					unlink($tmpName);
				}
				$this->uploadIco('PS_FAVICON', _PS_IMG_DIR_.'favicon.ico');
			}

			/* Update settings in database */
			if (!sizeof($this->_errors))
			{
				foreach ($fields AS $field => $values)
				{
					unset($val);
					if (isset($values['type']) AND $values['type'] == 'textLang')
						foreach ($languages as $language)
							$val[$language['id_lang']] = isset($values['cast']) ? $values['cast'](Tools::getValue($field.'_'.$language['id_lang'])) : Tools::getValue($field.'_'.$language['id_lang']);
					else
						$val = isset($values['cast']) ? $values['cast'](Tools::getValue($field)) : Tools::getValue($field);

					Configuration::updateValue($field, $val);
				}
				Tools::redirectAdmin($currentIndex.'&conf=6'.'&token='.$this->token);
			}
		}
	}

	private function getVal($conf, $key)
	{
		return Tools::getValue($key, (isset($conf[$key]) ? $conf[$key] : ''));
	}

	private function getConf($fields, $languages)
	{
		foreach ($fields AS $key => $field)
		{
			if ($field['type'] == 'textLang')
				foreach ($languages as $language)
					$tab[$key.'_'.$language['id_lang']] = Tools::getValue($key.'_'.$language['id_lang'], Configuration::get($key, $language['id_lang']));
			else
				$tab[$key] =  Tools::getValue($key, Configuration::get($key));
		}
		$tab['__PS_BASE_URI__'] = __PS_BASE_URI__;
		$tab['_MEDIA_SERVER_1_'] = _MEDIA_SERVER_1_;
		$tab['_MEDIA_SERVER_2_'] = _MEDIA_SERVER_2_;
		$tab['_MEDIA_SERVER_3_'] = _MEDIA_SERVER_3_;
		$tab['PS_THEME'] = _THEME_NAME_;
		$tab['db_type'] = _DB_TYPE_;
		$tab['db_server'] = _DB_SERVER_;
		$tab['db_name'] = _DB_NAME_;
		$tab['db_prefix'] = _DB_PREFIX_;
		$tab['db_user'] = _DB_USER_;
		$tab['db_passwd'] = '';

		return $tab;
	}

	private function getDivLang($fields)
	{
		$tab = array();
		foreach ($fields AS $key => $field)
			if ($field['type'] == 'textLang' || $field['type'] == 'selectLang')
				$tab[] = $key;
		return implode('¤', $tab);
	}

	/**
	  * Display configuration form
	  *
	  * @params string $name Form name
	  * @params array $fields Fields settings
	  *
	  * @global string $currentIndex Current URL in order to keep current Tab
	  */
	protected function _displayForm($name, $fields, $tabname, $size, $icon)
	{
		global $currentIndex;

		$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
		$languages = Language::getLanguages(false);
		$confValues = $this->getConf($fields, $languages);
		$divLangName = $this->getDivLang($fields);
		$required = false;

		echo '
		<script type="text/javascript">
			id_language = Number('.$defaultLanguage.');
		</script>
		<form action="'.$currentIndex.'&submit'.$name.$this->table.'=1&token='.$this->token.'" method="post" enctype="multipart/form-data">
			<fieldset><legend><img src="../img/admin/'.strval($icon).'.gif" />'.$tabname.'</legend>';
		foreach ($fields AS $key => $field)
		{
			/* Specific line for e-mails settings */
			if (get_class($this) == 'Adminemails' AND $key == 'PS_MAIL_SERVER')
				echo '<div id="smtp" style="display: '.((isset($confValues['PS_MAIL_METHOD']) AND $confValues['PS_MAIL_METHOD'] == 2) ? 'block' : 'none').';">';
			if (isset($field['required']) AND $field['required'])
				$required = true;
			$val = $this->getVal($confValues, $key);

			if (!in_array($field['type'], array('image', 'radio', 'container', 'container_end')) OR isset($field['show']))
				echo '<div style="clear: both; padding-top:15px;">'.($field['title'] ? '<label >'.$field['title'].'</label>' : '').'<div class="margin-form" style="padding-top:5px;">';

			/* Display the appropriate input type for each field */
			switch ($field['type'])
			{
				case 'select':
					echo '
					<select name="'.$key.'"'.(isset($field['js']) === true ? ' onchange="'.$field['js'].'"' : '').' id="'.$key.'">';
					foreach ($field['list'] AS $k => $value)
						echo '<option value="'.(isset($value['cast']) ? $value['cast']($value[$field['identifier']]) : $value[$field['identifier']]).'"'.(($val == $value[$field['identifier']]) ? ' selected="selected"' : '').'>'.$value['name'].'</option>';
					echo '
					</select>';
					break;

				case 'selectLang':
					foreach ($languages as $language)
					{
						echo '
						<div id="'.$key.'_'.$language['id_lang'].'" style="margin-bottom:8px; display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left; vertical-align: top;">
							<select name="'.$key.'_'.strtoupper($language['iso_code']).'">';
							foreach ($field['list'] AS $k => $value)
								echo '<option value="'.(isset($value['cast']) ? $value['cast']($value[$field['identifier']]) : $value[$field['identifier']]).'"'.((htmlentities(Tools::getValue($key.'_'.strtoupper($language['iso_code']), (Configuration::get($key.'_'.strtoupper($language['iso_code'])) ? Configuration::get($key.'_'.strtoupper($language['iso_code'])) : '')), ENT_COMPAT, 'UTF-8') == $value[$field['identifier']]) ? ' selected="selected"' : '').'>'.$value['name'].'</option>';
							echo '
							</select>
						</div>';
					}
					$this->displayFlags($languages, $defaultLanguage, $divLangName, $key);
					break;

				case 'bool':
					echo '<label class="t" for="'.$key.'_on"><img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'" /></label>
					<input type="radio" name="'.$key.'" id="'.$key.'_on" value="1"'.($val ? ' checked="checked"' : '').(isset($field['js']['on']) ? $field['js']['on'] : '').' />
					<label class="t" for="'.$key.'_on"> '.$this->l('Yes').'</label>
					<label class="t" for="'.$key.'_off"><img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'" style="margin-left: 10px;" /></label>
					<input type="radio" name="'.$key.'" id="'.$key.'_off" value="0" '.(!$val ? 'checked="checked"' : '').(isset($field['js']['off']) ? $field['js']['off'] : '').'/>
					<label class="t" for="'.$key.'_off"> '.$this->l('No').'</label>';
					break;

				case 'radio':
					foreach ($field['choices'] AS $cValue => $cKey)
						echo '<input type="radio" name="'.$key.'" id="'.$key.$cValue.'_on" value="'.(int)($cValue).'"'.(($cValue == $val) ? ' checked="checked"' : '').(isset($field['js'][$cValue]) ? ' '.$field['js'][$cValue] : '').' /><label class="t" for="'.$key.$cValue.'_on"> '.$cKey.'</label><br />';
					echo '<br />';
					break;

				case 'image':
					echo '
					<table cellspacing="0" cellpadding="0">
						<tr>';
					if ($name == 'themes')
						echo '
						<td colspan="'.sizeof($field['list']).'">
							<b>'.$this->l('In order to use a new theme, please follow this steps:', get_class()).'</b>
							<ul>
								<li>'.$this->l('Import your theme with using this module:', get_class()).' <a href="index.php?tab=AdminModules&token='.Tools::getAdminTokenLite('AdminModules').'&filtername=themeinstallator" style="text-decoration: underline;">'.$this->l('Theme installator', get_class()).'</a></li>
								<li>'.$this->l('When your theme is imported, please select the theme in this page', get_class()).'</li>
							</ul>
						</td>
						</tr>
						<tr>
						';
					$i = 0;
					foreach ($field['list'] AS $theme)
					{
						echo '<td class="center" style="width: 180px; padding:0px 20px 20px 0px;">
						<input type="radio" name="'.$key.'" id="'.$key.'_'.$theme['name'].'_on" style="vertical-align: text-bottom;" value="'.$theme['name'].'"'.
						(_THEME_NAME_ == $theme['name'] ? 'checked="checked"' : '').' />
						<label class="t" for="'.$key.'_'.$theme['name'].'_on"> '.Tools::strtolower($theme['name']).'</label>
						<br />
						<label class="t" for="'.$key.'_'.$theme['name'].'_on">
							<img src="../themes/'.$theme['name'].'/preview.jpg" alt="'.Tools::strtolower($theme['name']).'">
						</label>
						</td>';
						if (isset($field['max']) AND ($i+1) % $field['max'] == 0)
							echo '</tr><tr>';
						$i++;
					}
					echo '</tr>
					</table>';
					break;

				case 'price':
					$default_currency = new Currency((int)(Configuration::get("PS_CURRENCY_DEFAULT")));
					echo $default_currency->getSign('left').'<input type="'.$field['type'].'" size="'.(isset($field['size']) ? (int)($field['size']) : 5).'" name="'.$key.'" value="'.($field['type'] == 'password' ? '' : htmlentities($val, ENT_COMPAT, 'UTF-8')).'" />'.$default_currency->getSign('right').' '.$this->l('(tax excl.)');
					break;

				case 'textLang':
					foreach ($languages as $language)
						echo '
						<div id="'.$key.'_'.$language['id_lang'].'" style="margin-bottom:8px; display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left; vertical-align: top;">
							<input type="text" size="'.(isset($field['size']) ? (int)($field['size']) : 5).'" name="'.$key.'_'.$language['id_lang'].'" value="'.htmlentities($this->getVal($confValues, $key.'_'.$language['id_lang']), ENT_COMPAT, 'UTF-8').'" />
						</div>';
					$this->displayFlags($languages, $defaultLanguage, $divLangName, $key);
					break;

				case 'file':
					if (isset($field['thumb']) AND $field['thumb'] AND $field['thumb']['pos'] == 'before')
						echo '<img src="'.$field['thumb']['file'].'" alt="'.$field['title'].'" title="'.$field['title'].'" /><br />';
					echo '<input type="file" name="'.$key.'" />';
					break;

				case 'textarea':
					echo '<textarea name='.$key.' cols="'.$field['cols'].'" rows="'.$field['rows'].'">'.htmlentities($val, ENT_COMPAT, 'UTF-8').'</textarea>';
					break;

				case 'container':
					echo '<div id="'.$key.'">';
				break;

				case 'container_end':
					echo (isset($field['content']) === true ? $field['content'] : '').'</div>';
				break;

				case 'text':
				default:
					echo '<input type="'.$field['type'].'"'.(isset($field['id']) === true ? ' id="'.$field['id'].'"' : '').' size="'.(isset($field['size']) ? (int)($field['size']) : 5).'" name="'.$key.'" value="'.($field['type'] == 'password' ? '' : htmlentities($val, ENT_COMPAT, 'UTF-8')).'" />'.(isset($field['next']) ? '&nbsp;'.strval($field['next']) : '');
			}
			echo ((isset($field['required']) AND $field['required'] AND !in_array($field['type'], array('image', 'radio')))  ? ' <sup>*</sup>' : '');
			echo (isset($field['desc']) ? '<p style="clear:both">'.((isset($field['thumb']) AND $field['thumb'] AND $field['thumb']['pos'] == 'after') ? '<img src="'.$field['thumb']['file'].'" alt="'.$field['title'].'" title="'.$field['title'].'" style="float:left;" />' : '' ).$field['desc'].'</p>' : '');
			if (!in_array($field['type'], array('image', 'radio', 'container', 'container_end')) OR isset($field['show']))
				echo '</div></div>';
		}

		/* End of specific div for e-mails settings */
		if (get_class($this) == 'Adminemails')
			echo '<script type="text/javascript">if (getE(\'PS_MAIL_METHOD2_on\').checked) getE(\'smtp\').style.display = \'block\'; else getE(\'smtp\').style.display = \'none\';</script></div>';

		if(!is_writable(PS_ADMIN_DIR.'/../config/settings.inc.php') AND $name == 'themes')
			echo '<p><img src="../img/admin/warning.gif" alt="" /> '.$this->l('if you change the theme, the settings.inc.php file must be writable (CHMOD 755 / 777)').'</p>';

		echo '	<div align="center" style="margin-top: 20px;">
					<input type="submit" value="'.$this->l('   Save   ', 'AdminPreferences').'" name="submit'.ucfirst($name).$this->table.'" class="button" />
				</div>
				'.($required ? '<div class="small"><sup>*</sup> '.$this->l('Required field', 'AdminPreferences').'</div>' : '').'
			</fieldset>
		</form>';

		if (get_class($this) == 'AdminPreferences')
			echo '<script type="text/javascript">changeCMSActivationAuthorization();</script>';
	}
}

