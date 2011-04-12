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

class AdminLanguages extends AdminTab
{
	public function __construct()
	{
	 	$this->table = 'lang';
	 	$this->className = 'Language';
	 	$this->lang = false;
	 	$this->edit = true;
	 	$this->delete = true;
 		
 		$this->fieldImageSettings = array(array('name' => 'flag', 'dir' => 'l'), array('name' => 'no-picture', 'dir' => 'p'));
		
		$this->fieldsDisplay = array(
		'id_lang' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
		'flag' => array('title' => $this->l('Logo'), 'align' => 'center', 'image' => 'l', 'orderby' => false, 'search' => false),
		'name' => array('title' => $this->l('Name'), 'width' => 120),
		'iso_code' => array('title' => $this->l('ISO code'), 'width' => 70, 'align' => 'center'),
		'language_code' => array('title' => $this->l('Language code'), 'width' => 70, 'align' => 'center'),
		'active' => array('title' => $this->l('Enabled'), 'align' => 'center', 'active' => 'status', 'type' => 'bool'));
	
		$this->optionTitle = $this->l('Languages options');
		$this->_fieldsOptions = array(
		'PS_LANG_DEFAULT' => array('title' => $this->l('Default language:'), 'desc' => $this->l('The default language used in shop'), 'cast' => 'intval', 'type' => 'select', 'identifier' => 'id_lang', 'list' => Language::getlanguages(false)),
		);
		
		parent::__construct();
	}
	
	/**
	 * Copy a no-product image
	 *
	 * @param string $language Language iso_code for no-picture image filename
	 */
	public function copyNoPictureImage($language)
	{
		if (isset($_FILES['no-picture']) and $_FILES['no-picture']['error'] === 0)
			if ($error = checkImage($_FILES['no-picture'], $this->maxImageSize))
				$this->_errors[] = $error;
			else
			{
				if (!$tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS') OR !move_uploaded_file($_FILES['no-picture']['tmp_name'], $tmpName))
					return false;
				if (!imageResize($tmpName, _PS_IMG_DIR_.'p/'.$language.'.jpg'))
					$this->_errors[] = Tools::displayError('An error occurred while copying no-picture image to your product folder.');
				if (!imageResize($tmpName, _PS_IMG_DIR_.'c/'.$language.'.jpg'))
					$this->_errors[] = Tools::displayError('An error occurred while copying no-picture image to your category folder.');
				if (!imageResize($tmpName, _PS_IMG_DIR_.'m/'.$language.'.jpg'))
					$this->_errors[] = Tools::displayError('n error occurred while copying no-picture image to your manufacturer folder');
				else
				{	
					$imagesTypes = ImageType::getImagesTypes('products');
					foreach ($imagesTypes AS $k => $imageType)
					{
						if (!imageResize($tmpName, _PS_IMG_DIR_.'p/'.$language.'-default-'.stripslashes($imageType['name']).'.jpg', $imageType['width'], $imageType['height']))
							$this->_errors[] = Tools::displayError('An error occurred while resizing no-picture image to your product directory.');
						if (!imageResize($tmpName, _PS_IMG_DIR_.'c/'.$language.'-default-'.stripslashes($imageType['name']).'.jpg', $imageType['width'], $imageType['height']))
							$this->_errors[] = Tools::displayError('An error occurred while resizing no-picture image to your category directory.');
						if (!imageResize($tmpName, _PS_IMG_DIR_.'m/'.$language.'-default-'.stripslashes($imageType['name']).'.jpg', $imageType['width'], $imageType['height']))
							$this->_errors[] = Tools::displayError('An error occurred while resizing no-picture image to your manufacturer directory.');
					}
				}
				unlink($tmpName);
			}

	}
	
	/**
	 * deleteNoPictureImages will delete all default image created for the language id_language
	 * 
	 * @param string $id_language 
	 * @return boolean true if no error
	 */
	private function deleteNoPictureImages($id_language)
	{
	 	$language = Language::getIsoById($id_language);
		$imagesTypes = ImageType::getImagesTypes('products');
		$dirs = array(_PS_PROD_IMG_DIR_, _PS_CAT_IMG_DIR_, _PS_MANU_IMG_DIR_, _PS_SUPP_IMG_DIR_, _PS_MANU_IMG_DIR_);
		foreach ($dirs AS $dir)
		{
			foreach ($imagesTypes AS $k => $imageType)
				if (file_exists($dir.$language.'-default-'.stripslashes($imageType['name']).'.jpg'))
					if (!unlink($dir.$language.'-default-'.stripslashes($imageType['name']).'.jpg'))
						$this->_errors[] = Tools::displayError('An error occurred during image deletion.');

			if (file_exists($dir.$language.'.jpg'))
				if (!unlink($dir.$language.'.jpg'))
					$this->_errors[] = Tools::displayError('An error occurred during image deletion.');
		}

		return !sizeof($this->_errors) ? true : false;
	}


	public function postProcess()
	{
		global $currentIndex, $cookie;

		if (isset($_GET['delete'.$this->table]))
		{
			if ($this->tabAccess['delete'] === '1') 	
		 	{
				if (Validate::isLoadedObject($object = $this->loadObject()) AND isset($this->fieldImageSettings))
				{
					if ($object->id == Configuration::get('PS_LANG_DEFAULT'))
						$this->_errors[] = $this->l('you cannot delete the default language');
					elseif ($object->id == $cookie->id_lang)
						$this->_errors[] = $this->l('You cannot delete the language currently in use. Please change languages before deleting.');
					elseif ($this->deleteNoPictureImages((int)(Tools::getValue('id_lang'))) AND $object->delete())
						Tools::redirectAdmin($currentIndex.'&conf=1'.'&token='.$this->token);
				}
				else
					$this->_errors[] = Tools::displayError('An error occurred while deleting object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to delete here.');
		}
		elseif(Tools::getValue('submitDel'.$this->table) AND isset($_POST[$this->table.'Box']))
		{
		 	if ($this->tabAccess['delete'] === '1')
			{
				if (in_array(Configuration::get('PS_LANG_DEFAULT'), $_POST[$this->table.'Box']))
					$this->_errors[] = $this->l('you cannot delete the default language');
				elseif (in_array($cookie->id_lang, $_POST[$this->table.'Box']))
					$this->_errors[] = $this->l('you cannot delete the language currently in use, please change languages before deleting');
				else
				{
				 	foreach ($_POST[$this->table.'Box'] AS $language)
				 		$this->deleteNoPictureImages($language);
					parent::postProcess();
				}
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to delete here.');
		}
		elseif (Tools::isSubmit('submitAddlang'))
		{
			/* New language */
			if ((int)(Tools::getValue('id_'.$this->table)) == 0)
			{
				if ($this->tabAccess['add'] === '1')
				{
					if (isset($_POST['iso_code']) AND !empty($_POST['iso_code']) AND Validate::isLanguageIsoCode(Tools::getValue('iso_code')) AND Language::getIdByIso($_POST['iso_code']))
						$this->_errors[] = Tools::displayError('This ISO code is already linked to another language.');
					if ((!empty($_FILES['no-picture']['tmp_name']) OR !empty($_FILES['flag']['tmp_name'])) AND Validate::isLanguageIsoCode(Tools::getValue('iso_code')))
					{
						if ($_FILES['no-picture']['error'] == UPLOAD_ERR_OK)
							$this->copyNoPictureImage(strtolower(Tools::getValue('iso_code')));
						parent::postProcess();
					}
					else
					{
						$this->validateRules();
						$this->_errors[] = Tools::displayError('Flag and No-Picture image fields are required.');
					}
				}
				else
					$this->_errors[] = Tools::displayError('You do not have permission to add here.');
			}
			/* Language edition */
			else
			{
				if ($this->tabAccess['edit'] === '1')
				{
					if (( isset($_FILES['no-picture']) AND !$_FILES['no-picture']['error'] OR isset($_FILES['flag']) AND !$_FILES['flag']['error'])
						AND Validate::isLanguageIsoCode(Tools::getValue('iso_code')))
					{
						if ($_FILES['no-picture']['error'] == UPLOAD_ERR_OK)
							$this->copyNoPictureImage(strtolower(Tools::getValue('iso_code')));
						parent::postProcess();
					}

					if (!Validate::isLoadedObject($object = $this->loadObject()))
						die(Tools::displayError());
					if ((int)($object->id) == (int)(Configuration::get('PS_LANG_DEFAULT')) AND (int)($_POST['active']) != (int)($object->active))
						$this->_errors[] = Tools::displayError('You cannot change the status of the default language.');
					else
						parent::postProcess();

					$this->validateRules();
				}
				else
					$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
			}
		}
		elseif (isset($_GET['status']) AND isset($_GET['id_lang']))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				if (!Validate::isLoadedObject($object = $this->loadObject()))
					die(Tools::displayError());
				if ((int)($object->id) == (int)(Configuration::get('PS_LANG_DEFAULT')))
					$this->_errors[] = Tools::displayError('You cannot change the status of the default language.');
				else
					return parent::postProcess();
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		elseif (Tools::isSubmit('submitOptions'.$this->table))
		{
			$lang = new Language((int)Tools::getValue('PS_LANG_DEFAULT'));
			if (!$lang->active)
				$this->_errors[] = Tools::displayError('You cannot set this language as default language because it\'s disabled');
			else
				return parent::postProcess();
		}
		else
			return parent::postProcess();
	}
	
	public function displayList()
	{
		global $currentIndex;
		
		$this->displayWarning($this->l('When you delete a language, all related translations in the database will be deleted.'));
		parent::displayList();
		$languages = Language::getLanguages(false);
	}
	
	public function displayListContent($token=NULL)
	{
		global $currentIndex;

		$irow = 0;
		if ($this->_list)
			
			foreach ($this->_list AS $tr)
			{
				$id = $tr[$this->identifier];
				if ($tr['active'])
				{
					$active['title'] = "Enabled";
					$active['img'] = "enabled";
					if (!Language::checkFilesWithIsoCode($tr['iso_code']))
					{
						$active['title'] = "Warning, some translations files are missing for that iso-code";
						$active['img'] = "warning";
					}
				}
				else
				{
					$active['title'] = "Disabled";
					$active['img'] = "disabled";
				}
				echo '<tr'.($irow++ % 2 ? ' class="alt_row"' : '').' '.((isset($tr['color']) AND $this->colorOnBackground) ? 'style="background-color: '.$tr['color'].'"' : '').'>';
				echo '<td class="center"><input type="checkbox" name="'.$this->table.'Box[]" value="'.$id.'" class="noborder" /></td>';

				foreach ($this->fieldsDisplay AS $key => $params)
				{
					$tmp = explode('!', $key);
					$key = isset($tmp[1]) ? $tmp[1] : $tmp[0];
					echo '<td class="pointer '.(isset($params['align']) ? $params['align'] : '').'" onclick="document.location = \''.$currentIndex.'&'.$this->identifier.'='.$id.'&update'.$this->table.'&token='.($token!=NULL ? $token : $this->token).'\'">';
					if (isset($params['active']) AND isset($tr[$key]))
						echo '<a href="'.$currentIndex.'&'.$this->identifier.'='.$id.'&'.$params['active'].'&token='.($token != NULL ? $token : $this->token).'"><img src="../img/admin/'.$active['img'].'.gif" alt="active" title="'.$active['title'].'" /></a>';
					elseif (isset($params['image']))
						echo cacheImage(_PS_IMG_DIR_.$params['image'].'/'.$id.(isset($tr['id_image']) ? '-'.(int)($tr['id_image']) : '').'.'.$this->imageType, $this->table.'_mini_'.$id.'.'.$this->imageType, 45, $this->imageType);
					elseif (isset($tr[$key]))
						echo $tr[$key];
					else
						echo '--';
					'</td>';
				}
				if ($this->edit OR $this->delete OR ($this->view AND $this->view !== 'noActionColumn'))
				{
					echo '<td class="center">';
					if ($this->edit)
						echo '
						<a href="'.$currentIndex.'&'.$this->identifier.'='.$id.'&update'.$this->table.'&token='.($token != NULL ? $token : $this->token).'">
						<img src="../img/admin/edit.gif" border="0" alt="'.$this->l('Edit').'" title="'.$this->l('Edit').'" /></a>';
					if ($this->delete)
						echo '
						<a href="'.$currentIndex.'&'.$this->identifier.'='.$id.'&delete'.$this->table.'&token='.($token != NULL ? $token : $this->token).'" onclick="return confirm(\''.$this->l('When you delete a language, ALL RELATED TRANSLATIONS IN THE DATABASE WILL BE DELETED, are you sure you want to delete this language?', __CLASS__, true, false).'\');">
						<img src="../img/admin/delete.gif" border="0" alt="'.$this->l('Delete').'" title="'.$this->l('Delete').'" /></a>';
					echo '</td>';
				}
				echo '</tr>';
			}
	}
	
	public function displayForm($isMainTab = true)
	{
		global $currentIndex;
		parent::displayForm();
		
		if (!($obj = $this->loadObject(true)))
			return;

		echo '
		<script type="text/javascript">
		var langPackOk = "<img src=\"'._PS_IMG_.'admin/information.png\" alt=\"\" /> '.$this->l('A language pack is available for this ISO (name is').'";
		var langPackVersion = "'.$this->l('The compatible Prestashop version for this language and your system is: ').'";
		var langPackInfo = "'.$this->l('After creating the language, you can import the content of the language pack, which you can download above under "Tools - Translations"').'";
		var noLangPack = "<img src=\"'._PS_IMG_.'admin/information.png\" alt=\"\" /> '.$this->l('No language pack available on prestashop.com for this ISO code').'";
		var download = "'.$this->l('Download').'";
		</script>
		<script type="text/javascript" src="'._PS_JS_DIR_.'/checkLangPack.js"></script>
		<form action="'.$currentIndex.'&submitAdd'.$this->table.'=1&token='.$this->token.'" method="post" enctype="multipart/form-data">
		'.($obj->id ? '<input type="hidden" name="id_'.$this->table.'" value="'.$obj->id.'" />' : '').'
			<fieldset><legend><img src="../img/admin/world.gif" />'.$this->l('Languages').'</legend>
				<input type="hidden" value="'._PS_VERSION_.'" name="ps_version" id="ps_version" />
				<label>'.$this->l('Name:').' </label>
				<div class="margin-form">
					<input type="text" size="8" maxlength="32" name="name" value="'.htmlentities($this->getFieldValue($obj, 'name'), ENT_COMPAT, 'UTF-8').'" /> <sup>*</sup>
				</div>
				<label>'.$this->l('ISO code:').' </label>
				<div class="margin-form">
					<input type="text" size="4" maxlength="2" name="iso_code" id="iso_code" value="'.htmlentities($this->getFieldValue($obj, 'iso_code'), ENT_COMPAT, 'UTF-8').'" onKeyUp="checkLangPack();" /> <sup>*</sup>
					<p>'.$this->l('2-letter ISO code (e.g., fr, en, de)').'</p>
				</div>
				<label>'.$this->l('Language code:').' </label>
				<div class="margin-form">
					<input type="text" size="10" maxlength="5" name="language_code" id="language_code" value="'.htmlentities($this->getFieldValue($obj, 'language_code'), ENT_COMPAT, 'UTF-8').'"/> <sup>*</sup>
					<p>'.$this->l('Full language code (e.g., en-us, pt-br)').'</p>
				</div>
				<label>'.$this->l('Flag:').' </label>
				<div class="margin-form">
					<input type="file" name="flag" /> <sup>*</sup>
					<p>'.$this->l('Upload country flag from your computer').'</p>
				</div>
				<label>'.$this->l('"No-picture" image:').' </label>
				<div class="margin-form">
					<input type="file" name="no-picture" /> <sup>*</sup>
					<p>'.$this->l('Image displayed when "no picture found"').'</p>
				</div>
				<label>'.$this->l('Status:').' </label>
				<div class="margin-form">
					<input type="radio" name="active" id="active_on" value="1" '.((!$obj->id OR $this->getFieldValue($obj, 'active')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="active_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="active" id="active_off" value="0" '.((!$this->getFieldValue($obj, 'active') AND $obj->id) ? 'checked="checked" ' : '').'/>
					<label class="t" for="active_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
					<p>'.$this->l('Allow or disallow this language to be selected by the customer').'</p>
				</div>
				<p id="resultCheckLangPack"><img src="'._PS_IMG_.'admin/ajax-loader.gif" alt="" /> '.$this->l('Check if a language pack is available for this ISO code...').'</p>
				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitAdd'.$this->table.'" class="button" />
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
			</fieldset>
		</form>';
		
		if ($obj->id AND !$obj->checkFiles())
		{
			echo '
			<br /><br />
			<fieldset style="width:572px;"><legend><img src="../img/admin/warning.gif" />'.$this->l('Warning').'</legend>
					<p>'.$this->l('This language is NOT complete and cannot be used in the Front or Back Office because some files are missing.').'</p>
					<br />
					<label>'.$this->l('Translations files:').' </label>
					<div class="margin-form" style="margin-top:4px;">';
					$files = Language::getFilesList($obj->iso_code, _THEME_NAME_, false, false, 'tr', true);
					$this->displayFilesList($files);
			echo '
					</div><br style="clear:both;" />
					<label>'.$this->l('Theme files:').' </label>
					<div class="margin-form" style="margin-top:4px;">';
					$files = Language::getFilesList($obj->iso_code, _THEME_NAME_, false, false, 'theme', true);
					$this->displayFilesList($files);
			echo '
					</div><br style="clear:both;" />
					<label>'.$this->l('Mail files:').' </label>
					<div class="margin-form" style="margin-top:4px;">';
					$files = Language::getFilesList($obj->iso_code, _THEME_NAME_, false, false, 'mail', true);
					$this->displayFilesList($files);
			echo '
					</div>
					<br />
					<div class="small">'.$this->l('Missing files are marked in red').'</div>
			</fieldset>';
		}
	}
	
	public function displayFilesList($files)
	{
		foreach ($files as $key => $file)
		{
			if (!file_exists($key))
				echo '<font color="red">';
			echo $key;
			if (!file_exists($key))
				echo '</font>';
			echo '<br />';
		}
	}
}


