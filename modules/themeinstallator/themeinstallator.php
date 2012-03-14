<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 7451 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class ThemeInstallator extends Module
{
	/*
	** Modules
	*/
	private $to_install = array();
	private	$to_enable = array();
	private	$to_disable = array();
	private $to_export = array();

	/*
	** index
	*/
	private $page;

	/*
	** Config File
	*/
	private $xml;

	public function __construct()
	{
		@set_time_limit(0);
		@ini_set('memory_limit', '2G');

		$this->name = 'themeinstallator';
		$this->version = '1.4';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;
		if (version_compare(_PS_VERSION_, 1.4) >= 0)
			$this->tab = 'administration';
		else
			$this->tab = 'Theme';
		parent::__construct();
		$this->displayName = $this->l('Import/export a theme');
		$this->description = $this->l('Export or Install a theme and its modules on your shop.');
	}

	private function getTheNativeModules()
	{
		$xml = simplexml_load_string(Tools::file_get_contents('http://api.prestashop.com/xml/modules_list.xml'));
		$natives = array();
		if ($xml)
			foreach ($xml->modules as $row)
				foreach ($row->module as $row2)
					$natives[] = (string)$row2['name'];

		if (count($natives > 0))
			return $natives;
		// use this list if we can't contact the prestashop.com server
		$natives = array('bankwire', 'blockadvertising', 'blockbestsellers', 'blockcart', 'blockcategories', 'blockcms',
		'blockcurrencies', 'blockinfos', 'blocklanguages', 'blocklink', 'blockmanufacturer', 'blockmyaccount', 'blocknewproducts',
		'blocknewsletter', 'blockpaymentlogo', 'blockpermanentlinks', 'blockrss', 'blocksearch', 'blockspecials', 'blocksupplier',
		'blocktags', 'blockuserinfo', 'blockvariouslinks', 'blockviewed', 'blockwishlist', 'canonicalurl', 'cashondelivery', 'cheque',
		'crossselling', 'dejala', 'editorial', 'feeder', 'followup', 'gadsense', 'ganalytics', 'gcheckout', 'graphartichow', 'graphgooglechart',
		'graphvisifire', 'graphxmlswfcharts', 'gridhtml', 'gsitemap', 'hipay', 'homefeatured', 'loyalty', 'mailalerts', 'moneybookers',
		'newsletter', 'pagesnotfound', 'paypal', 'paypalapi', 'productcomments', 'productscategory', 'producttooltip', 'referralprogram', 'reverso',
		'sekeywords', 'sendtoafriend', 'statsbestcategories', 'statsbestcustomers', 'statsbestproducts', 'statsbestsuppliers', 'statsbestvouchers',
		'statscarrier', 'statscatalog', 'statscheckup', 'statsdata', 'statsequipment', 'statsgeolocation', 'statshome', 'statslive', 'statsnewsletter',
		'statsorigin', 'statspersonalinfos', 'statsproduct', 'statsregistrations', 'statssales', 'statssearch', 'themeinstallator', 'statsvisits', 'tm4b',
		'trackingfront', 'watermark');
		return $natives;
	}

	private function deleteDirectory($dirname)
	{
		$files = scandir($dirname);
		foreach ($files as $file)
            if ($file != '.' && $file != '..')
			{
				if (is_dir($dirname.'/'.$file))
					self::deleteDirectory($dirname.'/'.$file);
				else if (file_exists($dirname.'/'.$file))
					unlink($dirname.'/'.$file);
				}
        rmdir($dirname);
	}

	private function recurseCopy($src, $dst)
	{
		if (!$dir = opendir($src))
			return;
		if (!file_exists($dst))
			mkdir($dst);
		while(($file = readdir($dir)) !== false)
			if (strncmp($file, '.', 1) != 0)
			{
				if (is_dir($src.'/'.$file))
					self::recurseCopy($src.'/'.$file, $dst.'/'.$file);
				else if (is_readable($src.'/'.$file) && $file != 'Thumbs.db' && $file != '.DS_Store' && substr($file, -1) != '~')
					copy($src.'/'.$file, $dst.'/'.$file);
			}
		closedir($dir);
	}

	/*
	** Checks if module is installed
	** Returns true if module is active
	** Also returns false if it's a payment or stat module
	*/
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

	private function deleteTmpFiles()
	{
		if (file_exists(_IMPORT_FOLDER_.'doc'))
			self::deleteDirectory(_IMPORT_FOLDER_.'doc');
		if (file_exists(_IMPORT_FOLDER_.XMLFILENAME))
			unlink(_IMPORT_FOLDER_.XMLFILENAME);
		if (file_exists(_IMPORT_FOLDER_.'modules'))
			self::deleteDirectory(_IMPORT_FOLDER_.'modules');
		if (file_exists(_IMPORT_FOLDER_.'themes'))
			self::deleteDirectory(_IMPORT_FOLDER_.'themes');
		if (file_exists(_EXPORT_FOLDER_.'archive.zip'))
			unlink(_EXPORT_FOLDER_.'archive.zip');
	}

	private function init_defines()
	{
		define('_EXPORT_FOLDER_', dirname(__FILE__).'/export/');
		define('_IMPORT_FOLDER_', dirname(__FILE__).'/import/');
		$this->page = 1;
		if (!file_exists(_EXPORT_FOLDER_) || !is_dir(_EXPORT_FOLDER_))
			mkdir(_EXPORT_FOLDER_, 0777);
		if (!file_exists(_IMPORT_FOLDER_) || !is_dir(_IMPORT_FOLDER_))
			mkdir(_IMPORT_FOLDER_, 0777);

		if (!Tools::isSubmit('cancelExport') && (Tools::isSubmit('exportTheme') || Tools::isSubmit('submitExport')))
			$this->page = 'exportPage';
		$this->_html = '<form action="'.AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::htmlentitiesUTF8(Tools::getValue('token')).'" method="POST" enctype=multipart/form-data>';

		if (Tools::isSubmit('modulesToExport') || Tools::isSubmit('submitModules'))
			$this->to_export = Tools::getValue('modulesToExport');
		if (Tools::isSubmit('submitThemes'))
			$this->selectedVariations = Tools::getValue('variation');
		$_POST = @array_map('trim', $_POST);
		define('DEFAULT_COMPATIBILITY_FROM', _PS_VERSION_);
		define('DEFAULT_COMPATIBILITY_TO', _PS_VERSION_);
		define('DEFAULT_T_VER', '1.0');
		define('MAX_NAME_LENGTH', 128);
		define('MAX_EMAIL_LENGTH', 128);
		define('MAX_WEBSITE_LENGTH', 128);
		define('MAX_DESCRIPTION_LENGTH', 64);
		define('MAX_T_VER_LENGTH', 3);
		define('MAX_COMPATIBILITY_VER', 7);
		define('ARCHIVE_NAME', _IMPORT_FOLDER_.'uploaded.zip');
		define('XMLFILENAME', 'Config.xml');

		$this->_msg = '';
		$this->to_enable = false;
		$this->to_disable = false;
		$this->to_install = false;
		$this->errors = false;
		if ($this->page == 'exportPage' && Tools::isSubmit('exportTheme') && ($id_theme = Tools::getValue('mainTheme')))
		{
			$theme = new Theme($id_theme);
			if (!(is_dir(_PS_ALL_THEMES_DIR_.$theme->directory) && file_exists(_PS_ALL_THEMES_DIR_.$theme->directory.'/index.tpl')))
			{
				$this->page = 1;
				$this->_errors[] = Tools::displayErrors('%s is not a valid theme to export', $theme->name);
			}
		}
	}

	private function handleInformations()
	{
		if (Tools::isSubmit('submitImport1'))
		{
			if ($_FILES['themearchive']['error'] || !file_exists($_FILES['themearchive']['tmp_name']))
				$this->errors .= parent::displayError($this->l('An error has occurred during the file upload.'));
			else if (substr($_FILES['themearchive']['name'], -4) != '.zip')
				$this->errors .= parent::displayError($this->l('Only zip files are allowed'));
			else if (!rename($_FILES['themearchive']['tmp_name'], ARCHIVE_NAME))
				$this->errors .= parent::displayError($this->l('An error has occurred during the file copy.'));
			else if (Tools::ZipTest(ARCHIVE_NAME))
				$this->page = 2;
			else
				$this->errors .= parent::displayError($this->l('Zip file seems to be broken'));
		}
		else if (Tools::isSubmit('submitImport2'))
		{
			if (!Validate::isModuleUrl($url = Tools::getValue('linkurl'), $this->errors))
				$this->errors .= parent::displayError($this->l('Only zip files are allowed'));
			else if (!copy($url, ARCHIVE_NAME))
				$this->errors .= parent::displayError($this->l('Error during the file download'));
			else if (Tools::ZipTest(ARCHIVE_NAME))
				$this->errors .= parent::displayError($this->l('Zip file seems to be broken'));
			else
				$this->page = 2;
		}
		else if (Tools::isSubmit('submitImport3'))
		{
			$filename = _IMPORT_FOLDER_.Tools::getValue('ArchiveName');
			if (substr($filename, -4) != '.zip')
				$this->errors .= parent::displayError($this->l('Only zip files are allowed'));
			else if (!copy($filename, ARCHIVE_NAME))
				$this->errors .= parent::displayError($this->l('An error has occurred during the file copy.'));
			else if (Tools::ZipTest(ARCHIVE_NAME))
				$this->page = 2;
			else
				$this->errors .= parent::displayError($this->l('Zip file seems to be broken'));
		}
		else if (Tools::isSubmit('prevThemes'))
			$this->page = 2;
		else if (Tools::isSubmit('submitThemes'))
			$this->page = 3;
		else if (Tools::isSubmit('submitModules'))
			$this->page = 4;
		if ($this->page == 2 && file_exists(ARCHIVE_NAME))
		{
			if (!Tools::ZipExtract(ARCHIVE_NAME, _IMPORT_FOLDER_))
			{
				$this->errors .= parent::displayError($this->l('Error during zip extraction'));
				$this->page = 1;
			}
		}
		if (file_exists(ARCHIVE_NAME))
			unlink(ARCHIVE_NAME);
		if ($this->page != 1)
		{
			if (!self::checkXmlFields())
			{
				$this->errors .= parent::displayError($this->l('Bad configuration file'));
				$this->page = 1;
			}
			else
				return ;
		}
		self::deleteTmpFiles();
	}

	private function checkXmlFields()
	{
		if (!file_exists(_IMPORT_FOLDER_.XMLFILENAME) || !$xml = simplexml_load_file(_IMPORT_FOLDER_.XMLFILENAME))
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

	public function getContentExport()
	{
		$this->theme_list = Theme::getAvailable();
		$this->error = false;

		self::getModuleState();
		self::displayInformations();
		if (Tools::isSubmit('submitExport') && $this->error === false && $this->checkPostedDatas() == true)
		{
			self::getThemeVariations();
			self::getDocumentation();
			self::getHookState();
			self::getImageState();
			self::generateXML();
			self::generateArchive();
		}
		self::AuthorInformationForm();
		self::ModulesInformationForm();
		self::ThemeInformationForm();
		self::DocInformationForm();
		self::VariationInformationForm();
		return $this->_html;
	}

	/*
	** Main function
	*/
	public function getContent()
	{
		/* PrestaShop demo mode */
		if (_PS_MODE_DEMO_)
		{
			return '<div class="error">'.Tools::displayError('This functionality has been disabled.').'</div>';

		}
		self::init_defines();
		if (!Tools::isSubmit('cancelExport') && $this->page == 'exportPage')
			return self::getContentExport();
		self::handleInformations();
		switch ($this->page)
		{
			case 1:
				self::_displayForm1();
				break;
			case 2:
				self::_displayForm2();
				break;
			case 3:
				self::_displayForm3();
				break;
			case 4:
				self::_displayForm4();
				break;
		}
		return $this->errors.$this->_msg.$this->_html;
	}

	/*
	** Checker si le dossier doc existe : Si oui appeler la fonction !
	*/
	private function _loadDocForm()
	{
		$docname = array();
		$docpath = array();

		foreach ($this->xml->docs->doc as $row)
		{
			$docname[] = strval($row['name']);
			$docpath[] = strval($row['path']);
		}
		$doc = '
		<fieldset>
			<legend>'.$this->l('Documentation').'</legend>
			<label>'.$this->l('You may want to check the documentation').'</label>
			<div class="margin-form">
				<ul>';
		$i = 0;
		foreach ($docname as $row)
			$doc .= '<li><i><a target="_blank" href="'._MODULE_DIR_.$this->name.'/import/'.$docpath[$i++].'">'.$row.'</a></i>';
		$doc .= '
				</ul>
			<p class="clear">'.$this->l('Right click on the name and choose "save link as"').'
			</div>
		</fieldset>
		<p class="clear">&nbsp;</p>';
		$this->_html .= $doc;
	}

	private function getModules()
	{
		$this->native_modules = self::getTheNativeModules();
		foreach ($this->xml->modules->module as $row)
		{
			if (strval($row['action']) == 'install' && !in_array(strval($row['name']), $this->native_modules))
				$this->to_install[] = strval($row['name']);
			else if (strval($row['action']) == 'enable')
				$this->to_enable[] = strval($row['name']);
			else if (strval($row['action']) == 'disable')
				$this->to_disable[] = strval($row['name']);
		}
	}

	private function updateImages()
	{
		$return = array();
		foreach ($this->xml->images->image as $row)
		{
			if ($result = (bool)Db::getInstance()->executes(sprintf('SELECT * FROM `'._DB_PREFIX_.'image_type` WHERE `name` = \'%s\' ', pSQL($row['name']))))
			{
				$return['error'][] = array(
					'name' => $row['name'],
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
					'name' => $row['name'],
					'width' => (int)$row['width'],
					'height' => (int)$row['height']
				);
			}
		}
		return $return;
	}

	private function _displayForm4()
	{
		$xml = simplexml_load_file(_IMPORT_FOLDER_.XMLFILENAME);
		$this->xml = $xml;
		self::getModules();
		$hook = array();
		$hookedModule = array();
		$position = array();
		$msg = '';
		$shopID = $this->context->shop->id;

		foreach ($this->xml->modules->hooks->hook as $row)
		{
			$hookedModule[] = strval($row['module']);
			$hook[] = strval($row['hook']);
			$position[] = strval($row['position']);
			$exceptions[] = (isset($row['exceptions']) ? explode(',', strval($row['exceptions'])) : array());
		}

		if (file_exists(_IMPORT_FOLDER_.'doc') && count($xml->docs->doc) != 0)
			self::_loadDocForm();
		// install selected modules
		$flag = 0;
		if (isset($this->to_export) && $this->to_export)
			foreach ($this->to_export as $row)
			{
				if (in_array($row, $this->native_modules))
					continue;
				if ($flag++ == 0)
					$msg .= '<b>'.$this->l('The following modules have been installed').' :</b><br />';

				// We copy module only if it does not already exists
				if (!file_exists(_PS_ROOT_DIR_.'/modules/'.$row))
					self::recurseCopy(_IMPORT_FOLDER_.'modules/'.$row, _PS_ROOT_DIR_.'/modules/'.$row);

				$obj = Module::getInstanceByName($row);
				if (Validate::isLoadedObject($obj))
					Db::getInstance()->execute('
						UPDATE `'._DB_PREFIX_.'module`
						SET `active`= 1
						WHERE `name` = \''.pSQL($row).'\'');
				else if (!$obj || !$obj->install())
					continue;
				Db::getInstance()->execute('INSERT IGNORE INTO '._DB_PREFIX_.'module_shop (id_module, id_shop) VALUES('.(int)$obj->id.', '.$shopID.')');
				$msg .= '<i>- '.pSQL($row).'</i><br />';
				Db::getInstance()->execute('
					DELETE FROM `'._DB_PREFIX_.'hook_module`
					WHERE `id_module` = '.pSQL($obj->id).' AND id_shop = '.$shopID);
				$count = -1;
				while (isset($hookedModule[++$count]))
					if ($hookedModule[$count] == $row)
					{
						Db::getInstance()->execute('
							INSERT INTO `'._DB_PREFIX_.'hook_module` (`id_module`, `id_shop`, `id_hook`, `position`)
							VALUES ('.(int)$obj->id.', '.$shopID.', '.(int)Hook::getIdByName($hook[$count]).', '.(int)$position[$count].')');
						if ($exceptions[$count])
							foreach ($exceptions[$count] as $file_name)
								Db::getInstance()->execute('
									INSERT INTO `'._DB_PREFIX_.'hook_module_exceptions` (`id_module`, `id_hook`, `file_name`)
									VALUES ('.(int)$obj->id.', '.(int)Hook::getIdByName($hook[$count]).', "'.pSQL($file_name).'")');
			}
			}
		if (($val = (int)(Tools::getValue('nativeModules'))) != 1)
		{
			$flag = 0;
			// Disable native modules
			if ($val == 2 && $this->to_disable && count($this->to_disable))
				foreach ($this->to_disable as $row)
				{
					$obj = Module::getInstanceByName($row);
					if (Validate::isLoadedObject($obj))
					{
						if ($flag++ == 0)
							$msg .= '<b>'.$this->l('The following modules have been disabled').' :</b><br />';
						$msg .= '<i>- '.pSQL($row).'</i><br />';
				Db::getInstance()->execute('
					DELETE FROM `'._DB_PREFIX_.'module_shop`
					WHERE `id_module` = '.pSQL($obj->id).' AND id_shop = '.$shopID);
					}
				}
			$flag = 0;
			if ($this->to_enable && count($this->to_enable))
				foreach ($this->to_enable as $row)
				{
					$obj = Module::getInstanceByName($row);
					if (Validate::isLoadedObject($obj))
					{
						Db::getInstance()->execute('
							UPDATE `'._DB_PREFIX_.'module`
							SET `active`= 1
							WHERE `name` = \''.pSQL($row).'\'');
						Db::getInstance()->execute('INSERT IGNORE INTO '._DB_PREFIX_.'module_shop (id_module, id_shop) VALUES('.(int)$obj->id.', '.$shopID.')');
					}
					else if (!is_object($obj) || !$obj->install())
						continue ;
					if ($flag++ == 0)
						$msg .= '<b>'.$this->l('The following modules have been enabled').' :</b><br />';
					$msg .= '<i>- '.pSQL($row).'</i><br />';
					Db::getInstance()->execute('
						DELETE FROM `'._DB_PREFIX_.'hook_module`
						WHERE `id_module` = '.pSQL($obj->id).' AND id_shop = '.$shopID);
					$count = -1;
					while (isset($hookedModule[++$count]))
						if ($hookedModule[$count] == $row)
						{
							Db::getInstance()->execute('
								INSERT INTO `'._DB_PREFIX_.'hook_module` (`id_module`, `id_shop`, `id_hook`, `position`)
								VALUES ('.(int)$obj->id.', '.$shopID.', '.(int)Hook::getIdByName($hook[$count]).', '.(int)$position[$count].')');
							foreach ($exceptions[$count] as $filename)
								if (!empty($filename))
									Db::getInstance()->execute('
										INSERT INTO `'._DB_PREFIX_.'hook_module_exceptions` (`id_module`, id_shop, `id_hook`, `file_name`)
										VALUES ('.(int)$obj->id.', '.$shopID.', '.(int)Hook::getIdByName($hook[$count]).', "'.pSQL($filename).'")');
						}
				}
		}

		// note : theme was previously created at this point.
		// It is now created during displayForm3
		$result = $this->updateImages();
		if (!isset($result['error']))
			$msg .= $this->l('Images have been correctly updated in database');
		else
		{
			$errors = '<em><strong>'.$this->l('Warning: Copy/Paste your errors if you want to manually set the image type (in the tab Preferences > Images):').'</em></strong><br />';
			$errors .= $this->l('Some kind of image could not be added because they exists. Here\'s the list:');
			$errors .= '<ul>';
			foreach ($result['error'] as $error)
				$errors .= '<li style="color:#D8000C">'.$this->l('Name image type:').' <strong>'.$error['name'].'</strong> ('.$this->l('Width:').' '.$error['width'].'px, '.$this->l('Height:').' '.$error['height'].'px)</li>';
			$errors .= '</ul>';
		}

		if (isset($error))
			$this->_msg .= parent::displayError($errors);

		if (!empty($msg))
			$this->_msg .= parent::displayConfirmation($msg);

		$this->_html .= '
			<input type="submit" class="button" name="submitThemes" value="'.$this->l('Previous').'" />
			<input type="submit" class="button" name="Finish" value="'.$this->l('Finish').'" />
		</form>';

	}

	private function _displayForm3()
	{
		$res = true;
		$theme_directory = Tools::getValue('theme_directory');
		$xml = simplexml_load_file(_IMPORT_FOLDER_.XMLFILENAME);
		$this->xml = $xml;

		if ($this->selectedVariations && count($this->selectedVariations) > 0)
		{
			$ok = array();
			foreach ($this->selectedVariations as $variation)
			{
				if ($variation == 'prestashop')
					continue;

				if ($variation != $theme_directory)
					$theme_directory .= $variation;
				
				$target_dir = _PS_ALL_THEMES_DIR_.$theme_directory;

				$res &= self::recurseCopy(_IMPORT_FOLDER_.'themes/'.$variation, $target_dir);
				$new_theme = new Theme();
				$new_theme->name = (string)$this->xml['name'];
				$new_theme->directory = $theme_directory;
				$res &= $new_theme->add();

				if ($res)
					$ok[] = $variation;
			}

			if (count($ok) > 0)
			{
				$msg = $this->l('The following themes were successfully imported').':<ul><i>';
				foreach ($ok as $row)
					$msg .= '<li> '.$row;
				$msg .= '</i></ul>';
				$this->_msg = parent::displayConfirmation($msg);
			}
		}
		self::getModules();
		if (file_exists(_IMPORT_FOLDER_.'doc') && count($xml->docs->doc) != 0)
			self::_loadDocForm();
		$this->_html .= '<fieldset>';
		if ($this->to_install && count($this->to_install) > 0)
		{
			$var = '';
			foreach ($this->to_install as $row)
				if (file_exists(_IMPORT_FOLDER_.'modules/'.$row))
				{
					$module_already_exists = file_exists(_PS_MODULE_DIR_.$row);
					$var .= '<input type="checkbox" name="modulesToExport[]" id="'.$row.'" value="'.$row.'" checked="checked" /> <label style="display:bock;float:none" for="'.$row.'">'.$row.
						($module_already_exists ? ' <span style="font-size:0.8em">-> '.$this->l('Warning: a module with the same name already exists').'</span>' : '').'</label><br />';
				}

			if ($var != '')
				$this->_html .= '
					<fieldset>
						<legend>'.$this->l('Select the theme\'s modules you wish to install').'</legend>
						<p class="margin-form">'.$var.'</p>
					</fieldset>
					<p>&nbsp;</p>';
		}
		$this->_html .= '
			<fieldset>
				<legend>'.$this->l('Native modules configuration').'</legend>
				<ul class="margin-form" style="list-style:none">
					<li><input type="radio" name="nativeModules" value="1" id="nativemoduleconfig1"/> <label style="display:bock;float:none" for="nativemoduleconfig1">'.$this->l('Current configuration').'</label>
					<li><input type="radio" name="nativeModules" value="2" id="nativemoduleconfig2" checked="checked" /> <label style="display:bock;float:none" for="nativemoduleconfig2">'.$this->l('Theme\'s configuration').'</label>
					<li><input type="radio" name="nativeModules" value="3" id="nativemoduleconfig3" /> <label style="display:bock;float:none" for="nativemoduleconfig3">'.$this->l('Both').'</label>
				</ul>
			</fieldset>
			<p>&nbsp;</p>';
		$this->_html .= '
			<p class="clear">&nbsp;</p>
			<input type="submit" class="button" name="prevThemes" value="'.$this->l('Previous').'" />
			<input type="submit" class="button" name="submitModules" value="'.$this->l('Next').'" />
		</fieldset>
		</form>';
	}

	private function _displayForm2()
	{
		$iso = $this->context->language->iso_code;
		$xml = simplexml_load_file(_IMPORT_FOLDER_.XMLFILENAME);
		$this->xml = $xml;
		$res = $xml->xpath('/theme/descriptions/description[@iso="'.$iso.'"]');
		$description = (isset($res[0]) ? (string)$res[0] : '');

		$this->_msg = parent::displayConfirmation(
			$this->l('You are going to install the following theme').' :<br /> <b>'.
			$xml['name'].'</b> <i>v'.$xml['version'].'</i><br />'.
			(strlen($description) ? '<q>'.$description.'</q><br />' : '').
			$this->l('This theme is for Prestashop').' <i>v'.
			$xml->variations->variation[0]['from'].
			' -> v'.$xml->variations->variation[0]['to'].'<br />'.
			(file_exists(_PS_ALL_THEMES_DIR_.strval($xml->variations->variation[0]['directory'])) ? $this->l('Warning : You already have a theme with the same folder\'s name') : '').'
			</i>');
		if (file_exists(_IMPORT_FOLDER_.'doc') && count($xml->docs->doc) != 0)
			self::_loadDocForm();
		if (count($xml->variations->variation) > 1)
		{
			$count = 0;
			$var = '';
			while ($xml->variations->variation[++$count])
			{
				$foo = (file_exists(_PS_ALL_THEMES_DIR_.strval($xml->variations->variation[$count]['directory'])) ? 1 : 0);
				$var .= '<input type="checkbox" name="variation[]" id="'.strval($xml->variations->variation[$count]['directory']).'" value="'.
				strval($xml->variations->variation[$count]['directory']).'" '.
				($foo == 1 ? '' : 'checked').'/> <label style="display:bock;float:none" for="'.strval($xml->variations->variation[$count]['directory']).'">'.
				strval($xml->variations->variation[$count]['name']).' <span style="font-size:0.8em">'.
				$this->l('for Prestashop').' v'.
				strval($xml->variations->variation[$count]['from']).' -> v'.
				strval($xml->variations->variation[$count]['to']).'  '.
				($foo == 1 ? $this->l('(Warning: a folder with the same name already exists)') : '').'</span></label><br />';
			}
			$this->_html .= '
			<fieldset>
				<legend>'.$this->l('Choose the variations that you wish to import').'</legend>
				<label for="nomain">'.$this->l('Main theme').'</label>
				<div class="margin-form">
					<input type="checkbox" name="variation[]" id="nomain" value="'.$xml->variations->variation[0]['directory'].'" checked="checked" />
					<p class="clear">'.$this->l('Uncheck this field if you do not want to install the main theme.').'</p>
				</div>
				<label for="theme_name">'.$this->l('Theme name').'</label>
				<div class="margin-form">
					<input type="text" name="theme_name" id="theme_name" value="'.$xml->variations->variation[0]['directory'].'" />
				</div>
				<label for="theme_directory">'.$this->l('Theme directory').'</label>
				<div class="margin-form">
					<input type="text" name="theme_directory" id="theme_directory" value="'.$xml->variations->variation[0]['directory'].'" />
				</div>
				<h3>'.$this->l('Select the variations you wish to import').'</h3>'
				.'<div class="margin-form">'
				.'<p class="clear">'.$this->l('Note: The directory of the variation will be prefixed by the theme directory.').'</p>'
				.$var.'</div>
				<input type="submit" class="button" name="cancel" value="'.$this->l('Previous').'" />
				<input type="submit" class="button" name="submitThemes" value="'.$this->l('Next').'" />
			</fieldset>';
		}
		else
			$this->_html .= '
				<input type="hidden" name="variation[]" value="'.$xml->variations->variation[0]['directory'].'" />
				<input type="submit" class="button" name="cancel" value="'.$this->l('Previous').'" />
				<input type="submit" class="button" name="submitThemes" value="'.$this->l('Next').'" />
';
		$this->_html .= '</form>';
	}

	private function _displayForm1()
	{
		$tmp = scandir(_PS_ALL_THEMES_DIR_);
		$themeList = Theme::getThemes();

		$installed_themes = '<option value="" >'.$this->l('select a theme to export').'</option>';
		if (count($themeList) >  0)
		{
			$tmp = '';
			foreach ($themeList as $theme)
				$installed_themes .= '<option value="'.$theme->id.'" >'.$theme->name.'</option>';
			$this->_html .= '
				<fieldset>
					<legend>'.$this->l('Export a theme').'</legend>
					<label>'.$this->l('Select a theme').'</label>';
			$this->_html .= '<form action="'.AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::htmlentitiesUTF8(Tools::getValue('token')).'" method="POST" enctype=multipart/form-data>';
			$this->_html .= '<div class="margin-form">
						<select style="width:350px" name="id_theme">'.$installed_themes.'</select>
					</div>
					<input type="submit" class="button" name="exportTheme" value="'.$this->l('Export this theme').'" />';
			$this->_html .= '</form>';
			$this->_html .= '</fieldset>
				<div class="clear">&nbsp;</div>';
		}
		$this->_html .= '
			<fieldset>
				<legend>'.$this->l('Import from your computer').'</legend>
				<form action="'.AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::htmlentitiesUTF8(Tools::getValue('token')).'" method="POST" enctype=multipart/form-data>
				<input type="hidden" name="MAX_FILE_SIZE" value="100000000" />
				<label for="themearchive">'.$this->l('Archive File').'</label>
				<div class="margin-form">
					<input type="file"  id="themearchive" name="themearchive" />
					<p class="clear">'.$this->l('Where is your zip file?').'</p>
				</div>
				<input type="submit" class="button" name="submitImport1" value="'.$this->l('Next').'">
				</form>
			</fieldset>
			<div class="clear">&nbsp;</div>
		';
		$this->_html .= '
			<fieldset>
				<legend>'.$this->l('Import from the web').'</legend>
				<form action="'.AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::htmlentitiesUTF8(Tools::getValue('token')).'" method="POST" enctype=multipart/form-data>
				<label for="linkurl">'.$this->l('Archive URL').'</label>
				<div class="margin-form">
					<input type="text"  id="linkurl" name="linkurl" value="'.(Tools::getValue('linkurl') ? Tools::safeOutput(Tools::getValue('linkurl')) : 'http://').'"/>
				</div>
				<input type="submit" class="button" name="submitImport2" value="'.$this->l('Next').'">
				</form>
			</fieldset>
			<div class="clear">&nbsp;</div>';
		
		// Import folder is located in the module directory
		$import_dir = scandir(_IMPORT_FOLDER_);
		$list = array();
		foreach ($import_dir as $row)
			if (substr(_IMPORT_FOLDER_.$row, -4) == '.zip')
				$list[] = $row;
		$import_dir = '';
		foreach ($list as $row)
			$import_dir .= '<option value="'.$row.'">'.$row.'</option>';
		$this->_html .= '
			<fieldset>
				<legend>'.$this->l('Import from FTP').'</legend>
				<form action="'.AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::htmlentitiesUTF8(Tools::getValue('token')).'" method="POST" enctype=multipart/form-data>
				<label for="linkurl">'.$this->l('Select archive').'</label>
				<div class="margin-form">
					<select name="ArchiveName" style="width:350px">
						'.$import_dir.'
					</select>
					<p>'.sprintf($this->l('Select the ZIP file you want to use (previously uploaded in your %s directory)'), '<b>modules/themeinstallator/import/</b>').'</p>
				</div>
				<input type="submit" class="button" name="submitImport3" value="'.$this->l('Next').'">
				</form>
			</fieldset>
			<div class="clear">&nbsp;</div>';
	}
/*
** EXPORT FUNCTIONS ########################################
*/

	private function displayInformations()
	{
		$id_theme = (int)Tools::getValue('id_theme');
		$theme = new Theme($id_theme);
		if (!$theme->id)
			throw new PrestaShopException('Unable to load theme');
		
		$this->_html .=	'<input type="hidden" name="id_theme" value="'.$id_theme.'" />';
		if ($this->error === false && class_exists('ZipArchive', false) && ($zip = new ZipArchive()))
		{
			if (!($zip->open(_EXPORT_FOLDER_.'archive.zip', ZipArchive::OVERWRITE) === true) || !$zip->addEmptyDir('test') === true)
				$this->_html .= parent::displayError('Permission denied. Please set permisssion to 666 on this folder: '._EXPORT_FOLDER_);
			$zip->close();
			if ($this->error === false)
				$this->_html .= parent::displayConfirmation(
					sprintf($this->l('Fill this formular to export the theme %s in a ZIP file'), $theme->name)
				);
		}
	}

	private function archiveThisFile($obj, $file, $serverPath, $archivePath)
	{
		if (is_dir($serverPath.$file))
		{
			$dir = scandir($serverPath.$file);
			foreach ($dir as $row)
				if ($row != '.' && $row != '..')
					$this->archiveThisFile($obj, $row, $serverPath.$file.'/', $archivePath.$file.'/');
		}
		else if (!$obj->addFile($serverPath.$file, $archivePath.$file))
			$this->error = true;
	}

	/*
	** Generate Archive !
	*/
	private function generateArchive()
	{
		$count = 0;
		$zip = new ZipArchive();
		$zip_file_name = md5(time()).".zip";
		if ($zip->open(_EXPORT_FOLDER_.$zip_file_name, ZipArchive::OVERWRITE) === true)
		{
			if (!$zip->addFromString('Config.xml', $this->xmlFile))
				$this->error = true;
			while (isset($_FILES['mydoc_'.++$count]))
			{
				if (!$_FILES['mydoc_'.$count]['name'])
					continue;
				if (!$zip->addFile($_FILES['mydoc_'.$count]['tmp_name'], 'doc/'.$_FILES['mydoc_'.$count]['name']))
					$this->error = true;
			}
			foreach ($this->variations as $row)
			{
				// row = [name]¤[directory]¤[from]¤[to]
				// @todo : use array in post instead of hedgehog
				$array = explode('¤', $row);
				// archive this file using $row
				$this->archiveThisFile($zip, $array[1], _PS_ALL_THEMES_DIR_, 'themes/');
			}
			foreach ($this->to_export as $row)
				if (!in_array($row, $this->native_modules))
					$this->archiveThisFile($zip, $row, dirname(__FILE__).'/../../modules/', 'modules/');
			$zip->close();
			if ($this->error === false)
			{
				ob_end_clean();
				header('Content-Type: multipart/x-zip');
				header('Content-Disposition:attachment;filename="'.$zip_file_name.'"');
				readfile(_EXPORT_FOLDER_.$zip_file_name);
				unlink(_EXPORT_FOLDER_.$zip_file_name);
				die ;
			}
		}
		$this->_html .= parent::displayError($this->l('An error occurred during the archive generation'));
	}

	/*
	** XML Generation, all vars should be GOOD at this point
	*/
	private function generateXML()
	{
		$theme = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><!-- Copyright Prestashop --><theme></theme>');
		$theme->addAttribute('version', Tools::getValue('version'));
		$theme->addAttribute('name', Tools::htmlentitiesUTF8(Tools::getValue('theme_name')));
		$author = $theme->addChild('author');
		$author->addAttribute('name', Tools::htmlentitiesUTF8(Tools::getValue('author_name')));
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
		foreach ($this->variations as $row)
		{
			$array = explode('¤', $row);
			$variation = $variations->addChild('variation');
			$variation->addAttribute('name', Tools::htmlentitiesUTF8($array[0]));
			$variation->addAttribute('directory', $array[1]);
			$variation->addAttribute('from', $array[2]);
			$variation->addAttribute('to', $array[3]);
		}

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
		$this->xmlFile = $theme->asXML();
	}

	/*
	** Init modules and Hooks
	*/
	private function _initList()
	{
		$this->native_modules = self::getTheNativeModules();
		$this->module_list = Db::getInstance()->executeS('SELECT id_module, name, active FROM `'._DB_PREFIX_.'module`');
		$this->hook_list = Db::getInstance()->executeS('
			SELECT a.id_hook, a.name as name_hook, c.position, c.id_module, d.name as name_module, GROUP_CONCAT(hme.file_name, ",") as exceptions
			FROM `'._DB_PREFIX_.'hook` a
			LEFT JOIN `'._DB_PREFIX_.'hook_module` c ON c.id_hook = a.id_hook
			LEFT JOIN `'._DB_PREFIX_.'module` d ON c.id_module = d.id_module
			LEFT OUTER JOIN `'._DB_PREFIX_.'hook_module_exceptions` hme ON (hme.id_module = c.id_module AND hme.id_hook = a.id_hook)
			GROUP BY id_module, id_hook
			ORDER BY name_module');
		foreach ($this->hook_list as &$row)
			$row['exceptions'] = trim(preg_replace('/(,,+)/', ',', $row['exceptions']), ',');

	}

	/*
	** Fill module's vars
	*/
	private function getModuleState()
	{
		self::_initList();
		foreach ($this->module_list as $array)
		{
			if (!self::checkParentClass($array['name']))
				continue ;
			if (in_array($array['name'], $this->native_modules))
			{
				if ($array['active'] == 1)
					$this->to_enable[] = $array['name'];
				else
					$this->to_disable[] = $array['name'];
			}
			else if ($array['active'] == 1)
				$this->to_install[] = $array['name'];
		}
		foreach ($this->native_modules as $str)
		{
			$flag = 0;
			if (!self::checkParentClass($str))
				continue ;
			foreach ($this->module_list as $tmp)
				if (in_array($str, $tmp))
				{
					$flag = 1;
					break;
				}
			if ($flag == 0)
				$this->to_disable[] = $str;
		}
	}

	/*
	** Fill Hook Var
	*/
	private function getHookState()
	{
		foreach ($this->to_install as $string)
			foreach ($this->hook_list as $tmp)
				if ($tmp['name_module'] == $string)
					$this->to_hook[] = $string.';'.$tmp['name_hook'].';'.$tmp['position'].';'.$tmp['exceptions'];
		foreach ($this->to_enable as $string)
			foreach ($this->hook_list as $tmp)
				if ($tmp['name_module'] == $string)
					$this->to_hook[] = $string.';'.$tmp['name_hook'].';'.$tmp['position'].';'.$tmp['exceptions'];
	}

	/*
	** Fill Image var
	*/
	private function getImageState()
	{
		$table = Db::getInstance()->executeS('SELECT name, width, height, products, categories, manufacturers, suppliers, scenes FROM `'._DB_PREFIX_.'image_type`');
		foreach ($table as $row)
			$this->image_list[] = $row['name'].';'.$row['width'].';'.$row['height'].';'.
			($row['products'] == 1 ? 'true' : 'false').';'.
			($row['categories'] == 1 ? 'true' : 'false').';'.
			($row['manufacturers'] == 1 ? 'true' : 'false').';'.
			($row['suppliers'] == 1 ? 'true' : 'false').';'.
			($row['scenes'] == 1 ? 'true' : 'false');
	}

	/*
	** Takes current and submited theme's informations
	*/
	private function getThemeVariations()
	{
		// @todo check theme variation pertinence
		$from = Tools::getValue('compa_from');
		$to = Tools::getValue('compa_to');
		$this->variations[] = Tools::getValue('theme_name').'¤'.Tools::getValue('mainTheme').'¤'.$from.'¤'.$to;
		$count = 0;
		while (Tools::isSubmit('myvar_'.++$count))
		{
			if ((int)(Tools::getValue('myvar_'.$count)) == -1)
				continue ;
			$name = Tools::getValue('themevariationname_'.$count);
			$dir = Tools::getValue('myvar_'.$count);
			$from = Tools::getValue('compafrom_'.$count);
			$to = Tools::getValue('compato_'.$count);
			$this->variations[] = $name.'¤'.$dir.'¤'.$from.'¤'.$to;
		}
	}

	private function getDocumentation()
	{
		$count = 0;
		while (Tools::isSubmit('documentationName_'.++$count))
		{
			if (!($filename = Tools::htmlentitiesUTF8($_FILES['mydoc_'.$count]['name'])))
				continue ;
			$name = Tools::htmlentitiesUTF8(Tools::getValue('documentationName_'.$count));
			$this->user_doc[] = $name.'¤'.'doc/'.$filename;
		}
	}

	private function checkPostedDatas()
	{
		$mail = Tools::getValue('email');
		$website = Tools::getValue('website');

		if ($mail && !preg_match('#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#', $mail))
			$this->_html .= parent::displayError($this->l('There is an error in your e-mail syntax!'));
		else if ($website && (!Validate::isURL($website) || !Validate::isAbsoluteUrl($website)))
			$this->_html .= parent::displayError($this->l('There is an error in your URL syntax!'));
		else if (!$this->checkVersionsAndCompatibility() || !$this->checkNames() || !$this->checkDocumentation())
			return false;
		else
			return true;
		return false;
	}

	/*
	** Checks posted documentation
	*/
	private function checkDocumentation()
	{
		$count = 0;
		$extensions = array('.pdf', '.txt');
		while ($this->error == false && isset($_FILES['mydoc_'.++$count]))
		{
			if (!$_FILES['mydoc_'.$count]['name'])
				continue;
			$extension = strrchr($_FILES['mydoc_'.$count]['name'], '.');
			$name = Tools::getValue('documentationName_'.$count);

			if (!in_array($extension, $extensions))
				$this->_html .= parent::displayError($this->l('File extension must be .txt or .pdf'));
			else if ($_FILES['mydoc_'.$count]['error'] > 0 || $_FILES['mydoc_'.$count]['size'] > 1048576)
				$this->_html .= parent::displayError($this->l('An error occurred during documentation upload'));
			else if (!$name || !Validate::isGenericName($name) || strlen($name) > MAX_NAME_LENGTH)
				$this->_html .= parent::displayError($this->l('Please enter a valid documentation name'));
		}
		if ($this->error == true)
			return false;
		return true;
	}

	/*
	** Checks theme's and author's name syntax, existence and length
	*/
	private function checkNames()
	{
		$author = Tools::getValue('author_name');
		$name = Tools::getValue('theme_name');
		$count = 0;

		if (!$author || !Validate::isGenericName($author) || strlen($author) > MAX_NAME_LENGTH)
			$this->_html .= parent::displayError($this->l('Please enter a valid author name'));
		else if (!$name || !Validate::isGenericName($name) || strlen($name) > MAX_NAME_LENGTH)
			$this->_html .= parent::displayError($this->l('Please enter a valid theme name'));
		while ($this->error === false && Tools::isSubmit('myvar_'.++$count))
		{
			if ((int)(Tools::getValue('myvar_'.$count)) == -1)
				continue ;
			$name = Tools::getValue('themevariationname_'.$count);
			if (!$name || !Validate::isGenericName($name) || strlen($name) > MAX_NAME_LENGTH)
				$this->_html .= parent::displayError($this->l('Please enter a valid theme variation name'));
		}
		if ($this->error == true)
			return false;
		return true;
	}

	private function checkVersionsAndCompatibility()
	{
		$count = 0;
		$exp = "#^[0-9]+[.]+[0-9.]*[0-9]$#";

		if (!preg_match("#^[0-9][.][0-9]$#", Tools::getValue('version')) ||
			!preg_match($exp, Tools::getValue('compa_from')) || !preg_match($exp, Tools::getValue('compa_to')) ||
			version_compare(Tools::getValue('compa_from'), Tools::getValue('compa_to')) == 1)
			$this->_html .= parent::displayError($this->l('Syntax error on version field. Only digits and points are allowed and the compatibility should be increasing or equal.'));
		while ($this->error === false && Tools::isSubmit('myvar_'.++$count))
		{
			if ((int)(Tools::getValue('myvar_'.$count)) == -1)
				continue ;
			$from = Tools::getValue('compafrom_'.$count);
			$to = Tools::getValue('compato_'.$count);
			if (!preg_match($exp, $from) || !preg_match($exp, $to) || version_compare($from, $to) == 1)
				$this->_html .= parent::displayError($this->l('Syntax error on version. Only digits and points are allowed and compatibility should be increasing or equal.'));
		}
		if ($this->error == true)
			return false;
		return true;
	}

	private function ModulesInformationForm()
	{
		if ($this->to_install && count($this->to_install))
		{
			$tmp = '';
			foreach ($this->to_install as $key => $val)
				$tmp .= '<input type="checkbox" name="modulesToExport[]" value="'.$val.'" id="'.$val.'" '.(in_array($val, $this->to_export) ? 'checked="checked"' : "").'/> <label style="display:bock;float:none" for="'.$val.'">'.$val.'</label><br />';
			$this->_html .= '
				<fieldset>
					<legend>'.$this->l('Modules').'</legend>
					<label>'.$this->l('Select the modules that you wish to export').'</label>
					<div class="margin-form">'.$tmp.'</div>
				</fieldset>
				<p class="clear">&nbsp;</p>';
		}
	}

	private function AuthorInformationForm()
	{
		$employee = $this->context->employee;
		$mail = Tools::getValue('email') ? Tools::htmlentitiesUTF8(Tools::getValue('email')) : Tools::htmlentitiesUTF8($employee->email);
		$author = Tools::getValue('author_name') ? Tools::htmlentitiesUTF8(Tools::getValue('author_name')) : Tools::htmlentitiesUTF8(($employee->firstname).' '.$employee->lastname);
		$website = Tools::getValue('website') ? Tools::htmlentitiesUTF8(Tools::getValue('website')) : Tools::getHttpHost(true);

		$this->_html .= '
			<fieldset>
				<legend>'.$this->l('Author').'</legend>
					<label>'.$this->l('Name').'</label>
				<div class="margin-form">
					<input type="text" value="'.$author.'" name="author_name" maxlength="'.MAX_NAME_LENGTH.'" />
				</div>
					<label>'.$this->l('Email').'</label>
				<div class="margin-form">
					<input type="text" value="'.$mail.'" name="email" maxlength="'.MAX_EMAIL_LENGTH.'"/>
				</div>
					<label>'.$this->l('Website').'</label>
				<div class="margin-form">
					<input type="text" value="'.$website.'" name="website" maxlength="'.MAX_WEBSITE_LENGTH.'"/>
				</div>
			</fieldset>
			<div class="clear">&nbsp;</div>';
	}

	private function ThemeInformationForm()
	{
		$defaultLanguage = (int)$this->context->language->id;
		$languages = Language::getLanguages();
		$iso = $this->context->language->iso_code;
		$divLangName = 'title';
		$id_theme = (int)Tools::getValue('id_theme');
		$theme = new Theme($id_theme);

		$theme_name = Tools::getValue('theme_name') ? Tools::getValue('theme_name') : $theme->name;
		$theme_directory = $theme->directory;

		$this->_html .=	'
		<fieldset>
			<legend>'.$this->l('Theme').'</legend>
			<label>'.$this->l('Name').'</label>
			<div class="margin-form">
				<input type="text" value="'.$theme_name.'" name="theme_name" maxlength="'.MAX_NAME_LENGTH.'" />
				<p class="clear">'.$this->l('Your theme\'s name').'</p>
			</div>
			<label>'.$this->l('Theme directory').'</label>
			<div class="margin-form">
				<input type="text" value="'.$theme_directory.'" name="theme_directory" maxlength="'.MAX_NAME_LENGTH.'" />
			</div>
			<label>'.$this->l('Description').'</label>
			<div class="margin-form">';
		foreach ($languages as $language)
		{
			$val = Tools::htmlentitiesUTF8(Tools::getValue('body_title_'.$language['id_lang']));
			$this->_html .= '
				<div id="title_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">
					<input type="text" name="body_title_'.$language['id_lang'].'" id="body_title_'.$language['id_lang'].'" maxlength="'.MAX_DESCRIPTION_LENGTH.'" size="64" value="'.$val.'" />
				</div>';
		}
		$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'title', true);
		$this->_html .= '
				<p class="clear">'.$this->l('Enter a short description of your theme').'</p>
			</div>';
		$val = Tools::getValue('version') ? Tools::getValue('version') : DEFAULT_T_VER;
		$this->_html .= '
			<label>'.$this->l('Version').'</label>
			<div class="margin-form">
				<input type="text" value="'.$val.'" name="version" maxlength="'.MAX_T_VER_LENGTH.'" />
				<p class="clear">'.$this->l('Your theme\'s version').'</p>
			</div>';
		$val = Tools::getValue('compa_from') ? Tools::getValue('compa_from') : DEFAULT_COMPATIBILITY_FROM;
		$val2 = Tools::getValue('compa_to') ? Tools::getValue('compa_to') : DEFAULT_COMPATIBILITY_TO;
		$this->_html .= '
			<div style="float: left;">
				<label>'.$this->l('Compatible From').'</label>
				<div class="margin-form">
					<input type="text" value="'.$val.'" name="compa_from" maxlength="'.MAX_COMPATIBILITY_VER.'" />
				</div>
			</div>
			<div style="margin-left: 30px; float: left;">
				<label>'.$this->l('To').'</label>
				<div class="margin-form">
					<input type="text" value="'.$val2.'" name="compa_to" maxlength="'.MAX_COMPATIBILITY_VER.'"/>
				</div>
			</div>
			<p class="clear">&nbsp;</p>';
	}

	private function DocInformationForm()
	{
		$val = Tools::htmlentitiesUTF8(Tools::getValue('documentation'));
		$this->_html .= '
			<label>'.$this->l('Add documentation').'</label>
			<p class="margin-form">'.
				$this->l('Give the user some help. Add a field by clicking here').'
				<a href="javascript:addDocumentation(0);"><img alt="add" title="add" src="'._MODULE_DIR_.$this->name.'/add.png'.'" /></a>.<br />'.
				$this->l('File extension must be .txt or .pdf').'
			</p>
			<input type="hidden" name="MAX_FILE_SIZE" value="1000000">
			<table style="margin: 10px auto;" cellpadding="1" cellspacing="5" border="0" id="documentation_table"></table>
		';
		$this->_html .= '<p class="clear">&nbsp;</p>';
	}

	private function VariationInformationForm()
	{

		$this->_html .= '
			<label>'.$this->l('Add variation').'
				<a href="javascript:addVariation(-1);"><img alt="add" title="add" src="'._MODULE_DIR_.$this->name.'/add.png'.'" /></a>
			</label>
			<p class="margin-form">'.$this->l('Select theme to include and its compatibility.').'</p>
			<script type="text/javascript">
				var path = "'.$this->l('Path').'";
				var delete_img = "'._MODULE_DIR_.$this->name.'/delete.png";
				var writeName = "'.$this->l('Name').'";
				var compafrom = "'.$this->l('From').'";
				var compato = "'.$this->l('To').'";
				var name_length = "'.MAX_NAME_LENGTH.'";
				var doc_default_val = "'.$this->l('Documentation').'";
				var compatibility_length = "'.MAX_COMPATIBILITY_VER.'";
				var compatibility_from = "'.DEFAULT_COMPATIBILITY_FROM.'";
				var compatibility_to = "'.DEFAULT_COMPATIBILITY_TO.'";
				var select_default = "'.$this->l('Choose a theme').'";
				var themes = Array();
				var themes_id = Array();
		';
		$id = 0;
		foreach ($this->theme_list as $row)
		{
			if (!is_dir(_PS_ALL_THEMES_DIR_.$row) || !file_exists(_PS_ALL_THEMES_DIR_.$row.'/index.tpl') || $row == 'prestashop' || $row == Tools::getValue('mainTheme'))
				continue ;
			$this->_html .= 'themes['.$id.'] = "'.$row.'";';
			$this->_html .= 'themes_id['.$id.'] = '.$id.';';
			$id++;
		}
		$this->_html .= '
			</script>
			<script type="text/javascript" src="'._MODULE_DIR_.$this->name.'/themeinstallator.js"></script>
			<table style="margin: 10px auto;" cellpadding="1" cellspacing="5" border="0" id="variation_table"></table>
			<div class="clear">&nbsp;</div>';
		$this->_html .= '
		</fieldset>
		<div class="clear">&nbsp;</div>
			<input type="submit" class="button" name="cancelExport" value="'.$this->l('Cancel').'" />&nbsp;
			<input type="submit" class="button" name="submitExport" value="'.$this->l('Generate the archive now!').'" />
		</form>';
	}
}

