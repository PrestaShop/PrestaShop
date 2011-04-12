<?php
/*
* 2007-2011 PrestaShop 
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_CAN_LOAD_FILES_'))
	exit;

class Editorial extends Module
{
	/** @var max image size */
	protected $maxImageSize = 307200;

	public function __construct()
	{
		$this->name = 'editorial';
		$this->tab = 'front_office_features';
		$this->version = '1.6';
		$this->author = 'PrestaShop';
		
		parent::__construct();

		$this->displayName = $this->l('Home text editor');
		$this->description = $this->l('A text editor module for your homepage.');
		$path = dirname(__FILE__);
		if (strpos(__FILE__, 'Module.php') !== false)
			$path .= '/../modules/'.$this->name;
		include_once($path.'/EditorialClass.php');
	}

	public function install()
	{
		if (!parent::install() OR !$this->registerHook('home') OR !$this->registerHook('header'))
			return false;
		
		if (!Db::getInstance()->Execute('
		CREATE TABLE `'._DB_PREFIX_.'editorial` (
		`id_editorial` int(10) unsigned NOT NULL auto_increment,
		`body_home_logo_link` varchar(255) NOT NULL,
		PRIMARY KEY (`id_editorial`))
		ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8'))
			return false;
		
		if (!Db::getInstance()->Execute('
		CREATE TABLE `'._DB_PREFIX_.'editorial_lang` (
		`id_editorial` int(10) unsigned NOT NULL,
		`id_lang` int(10) unsigned NOT NULL,
		`body_title` varchar(255) NOT NULL,
		`body_subheading` varchar(255) NOT NULL,
		`body_paragraph` text NOT NULL,
		`body_logo_subheading` varchar(255) NOT NULL,
		PRIMARY KEY (`id_editorial`, `id_lang`))
		ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8'))
			return false;
		
		if (!Db::getInstance()->Execute('
		INSERT INTO `'._DB_PREFIX_.'editorial`(`id_editorial`, `body_home_logo_link`) 
		VALUES(1, "http://www.prestashop.com")'))
			return false;
		
		if (!Db::getInstance()->Execute('
		INSERT INTO `'._DB_PREFIX_.'editorial_lang`(`id_editorial`, `id_lang`, `body_title`, `body_subheading`, `body_paragraph`, `body_logo_subheading`)
		VALUES(1, 1, "Lorem ipsum dolor sit amet", "Consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua", "&lt;p&gt;Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum&lt;/p&gt;", "Excepteur sint prestashop cupidatat non proident")'))
			return false;
		
		if (!Db::getInstance()->Execute('
		INSERT INTO `'._DB_PREFIX_.'editorial_lang`(`id_editorial`, `id_lang`, `body_title`, `body_subheading`, `body_paragraph`, `body_logo_subheading`)
		VALUES(1, 2, "Lorem ipsum dolor sit amet", "Excepteur sint occaecat cupidatat non proident", "&lt;p&gt;Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum&lt;/p&gt;", "Lorem ipsum presta shop amet")'))
			return false;
		return true;
	}
	
	public function uninstall()
	{
		if (!parent::uninstall())
			return false;
		return (Db::getInstance()->Execute('DROP TABLE `'._DB_PREFIX_.'editorial`') AND
				Db::getInstance()->Execute('DROP TABLE `'._DB_PREFIX_.'editorial_lang`'));
	}

	public function putContent($xml_data, $key, $field, $forbidden, $section)
	{
		foreach ($forbidden AS $line)
			if ($key == $line)
				return 0;
		if (!preg_match('/^'.$section.'_/i', $key))
			return 0;
		$key = preg_replace('/^'.$section.'_/i', '', $key);
		$field = htmlspecialchars($field);
		if (!$field)
			return 0;
		return ("\n".'		<'.$key.'>'.$field.'</'.$key.'>');
	}

	public function getContent()
	{
		global $cookie;
		
		/* display the module name */
		$this->_html = '<h2>'.$this->displayName.'</h2>';
		$errors = '';

		// Delete logo image
		if (Tools::isSubmit('deleteImage'))
		{
			if (!file_exists(dirname(__FILE__).'/homepage_logo.jpg'))
				$errors .= $this->displayError($this->l('This action cannot be taken.'));
			else
			{
				unlink(dirname(__FILE__).'/homepage_logo.jpg');
				Tools::redirectAdmin('index.php?tab=AdminModules&configure='.$this->name.'&token='.Tools::getAdminToken('AdminModules'.(int)(Tab::getIdFromClassName('AdminModules')).(int)($cookie->id_employee)));
			}
			$this->_html .= $errors;
		}

		/* update the editorial xml */
		if (Tools::isSubmit('submitUpdate'))
		{
			// Forbidden key
			$forbidden = array('submitUpdate');
			
			$editorial = new EditorialClass(1);
			$editorial->copyFromPost();
			$editorial->update();

			/* upload the image */
			if (isset($_FILES['body_homepage_logo']) AND isset($_FILES['body_homepage_logo']['tmp_name']) AND !empty($_FILES['body_homepage_logo']['tmp_name']))
			{
				Configuration::set('PS_IMAGE_GENERATION_METHOD', 1);
				if ($error = checkImage($_FILES['body_homepage_logo'], $this->maxImageSize))
					$errors .= $error;
				elseif (!$tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS') OR !move_uploaded_file($_FILES['body_homepage_logo']['tmp_name'], $tmpName))
					return false;
				elseif (!imageResize($tmpName, dirname(__FILE__).'/homepage_logo.jpg'))
					$errors .= $this->displayError($this->l('An error occurred during the image upload.'));
				if (isset($tmpName))
					unlink($tmpName);
			}
			$this->_html .= $errors == '' ? $this->displayConfirmation($this->l('Settings updated successfully')) : $errors;
			if (file_exists(dirname(__FILE__).'/homepage_logo.jpg'))
			{
				list($width, $height, $type, $attr) = getimagesize(dirname(__FILE__).'/homepage_logo.jpg');
				Configuration::updateValue('EDITORIAL_IMAGE_WIDTH', (int)round($width));
				Configuration::updateValue('EDITORIAL_IMAGE_HEIGHT', (int)round($height));
			}
		}

		/* display the editorial's form */
		$this->_displayForm();
	
		return $this->_html;
	}

	private function _displayForm()
	{
		global $cookie;
		/* Languages preliminaries */
		$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
		$languages = Language::getLanguages(false);
		$iso = Language::getIsoById((int)($cookie->id_lang));
		$divLangName = 'title¤subheading¤cpara¤logo_subheading';

		$editorial = new EditorialClass(1);
		// TinyMCE
		global $cookie;
		$iso = Language::getIsoById((int)($cookie->id_lang));
		$isoTinyMCE = (file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en');
		$ad = dirname($_SERVER["PHP_SELF"]);
		$this->_html .=  '
			<script type="text/javascript">	
			var iso = \''.$isoTinyMCE.'\' ;
			var pathCSS = \''._THEME_CSS_DIR_.'\' ;
			var ad = \''.$ad.'\' ;
			</script>
			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tiny_mce/tiny_mce.js"></script>
			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce.inc.js"></script>';
		$this->_html .= '
		<script type="text/javascript">id_language = Number('.$defaultLanguage.');</script>
		<form method="post" action="'.$_SERVER['REQUEST_URI'].'" enctype="multipart/form-data">
			<fieldset style="width: 905px;">
				<legend><img src="'.$this->_path.'logo.gif" alt="" title="" /> '.$this->displayName.'</legend>
				<label>'.$this->l('Main title').'</label>
				<div class="margin-form">';
				
				foreach ($languages as $language)
				{
					$this->_html .= '
					<div id="title_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">
						<input type="text" name="body_title_'.$language['id_lang'].'" id="body_title_'.$language['id_lang'].'" size="64" value="'.(isset($editorial->body_title[$language['id_lang']]) ? $editorial->body_title[$language['id_lang']] : '').'" />
					</div>';
				}
				$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'title', true);
				
				
		$this->_html .= '
					<p class="clear">'.$this->l('Appears along top of homepage').'</p>
				</div>
				<label>'.$this->l('Subheading').'</label>
				<div class="margin-form">';
				
				foreach ($languages as $language)
				{
					$this->_html .= '
					<div id="subheading_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">
						<input type="text" name="body_subheading_'.$language['id_lang'].'" id="body_subheading_'.$language['id_lang'].'" size="64" value="'.(isset($editorial->body_subheading[$language['id_lang']]) ? $editorial->body_subheading[$language['id_lang']] : '').'" />
					</div>';
				 }
				$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'subheading', true);
				
		$this->_html .= '
					<div class="clear"></div>
				</div>
				<label>'.$this->l('Introductory text').'</label>
				<div class="margin-form">';

				foreach ($languages as $language)
				{
					$this->_html .= '
					<div id="cpara_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">
						<textarea class="rte" cols="70" rows="30" id="body_paragraph_'.$language['id_lang'].'" name="body_paragraph_'.$language['id_lang'].'">'.(isset($editorial->body_paragraph[$language['id_lang']]) ? $editorial->body_paragraph[$language['id_lang']] : '').'</textarea>
					</div>';
				 }
				
				$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'cpara', true);
				
				$this->_html .= '
					<p class="clear">'.$this->l('Text of your choice; for example, explain your mission, highlight a new product, or describe a recent event.').'</p>
				</div>
				<label>'.$this->l('Homepage logo').' </label>
				<div class="margin-form">';
				if (file_exists(dirname(__FILE__).'/homepage_logo.jpg'))
						$this->_html .= '<div id="image" >
							<img src="'.$this->_path.'homepage_logo.jpg?t='.time().'" />
							<p align="center">'.$this->l('Filesize').' '.(filesize(dirname(__FILE__).'/homepage_logo.jpg') / 1000).'kb</p>
							<a href="'.$_SERVER['REQUEST_URI'].'&deleteImage" onclick="return confirm(\''.$this->l('Are you sure?', __CLASS__, true, false).'\');">
							<img src="../img/admin/delete.gif" alt="'.$this->l('Delete').'" /> '.$this->l('Delete').'</a>
						</div>';
						
				$this->_html .= '<input type="file" name="body_homepage_logo" />
					<p style="clear: both">'.$this->l('Will appear next to the Introductory Text above').'</p>
					
				</div>
				<label>'.$this->l('Homepage logo link').'</label>
				<div class="margin-form">
					<input type="text" name="body_home_logo_link" size="64" value="'.$editorial->body_home_logo_link.'" />
					<p style="clear: both">'.$this->l('Link used on the 2nd logo').'</p>
				</div>
				<label>'.$this->l('Homepage logo subheading').'</label>
				<div class="margin-form">';
				
				foreach ($languages as $language)
				{
					$this->_html .= '
					<div id="logo_subheading_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">
						<input type="text" name="body_logo_subheading_'.$language['id_lang'].'" id="logo_subheading_'.$language['id_lang'].'" size="64" value="'.(isset($editorial->body_logo_subheading[$language['id_lang']]) ? $editorial->body_logo_subheading[$language['id_lang']] : '').'" />
					</div>';
				 }
				
				$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'logo_subheading', true);
				
				$this->_html .= '
					<div class="clear"></div>
				</div>
				<div class="clear pspace"></div>
				<div class="margin-form clear"><input type="submit" name="submitUpdate" value="'.$this->l('Update the editor').'" class="button" /></div>
			</fieldset>
		</form>';
	}

	public function hookHome($params)
	{
		global $cookie, $smarty;
		
		$editorial = new EditorialClass(1, (int)$cookie->id_lang);
		$smarty->assign(array(
			'editorial' => $editorial,
			'default_lang' => (int)$cookie->id_lang,
			'image_width' => Configuration::get('EDITORIAL_IMAGE_WIDTH'),
			'image_height' => Configuration::get('EDITORIAL_IMAGE_HEIGHT'),
			'id_lang' => $cookie->id_lang,
			'homepage_logo' => file_exists('modules/editorial/homepage_logo.jpg'),
			'image_path' => $this->_path.'homepage_logo.jpg'
		));
		return $this->display(__FILE__, 'editorial.tpl');
	}
	
	public function hookHeader()
	{
		Tools::addCSS(($this->_path).'editorial.css', 'all');
	}
}
