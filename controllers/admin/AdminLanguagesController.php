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
*  @version  Release: $Revision: 8971 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminLanguagesControllerCore extends AdminController
{
	public function __construct()
	{
	 	$this->table = 'lang';
		$this->className = 'Language';
	 	$this->lang = false;
		$this->deleted = false;
		$this->multishop_context = Shop::CONTEXT_ALL;

		$this->context = Context::getContext();

 		$this->fieldImageSettings = array(
 			array(
 				'name' => 'flag',
 				'dir' => 'l'
 			),
 			array(
 				'name' => 'no-picture',
 				'dir' => 'p'
 			)
 		);

		$this->fields_list = array(
			'id_lang' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'width' => 25
			),
			'flag' => array(
				'title' => $this->l('Logo'),
				'align' => 'center',
				'image' => 'l',
				'orderby' => false,
				'search' => false
			),
			'name' => array(
				'title' => $this->l('Name'),
				'width' => 120
			),
			'iso_code' => array(
				'title' => $this->l('ISO code'),
				'width' => 70,
				'align' => 'center'
			),
			'language_code' => array(
				'title' => $this->l('Language code'),
				'width' => 70,
				'align' => 'center'
			),
			'date_format_lite' => array(
				'title' => $this->l('Date format')
			),
			'date_format_full' => array(
				'title' => $this->l('Date format (full)')
			),
			'active' => array(
				'title' => $this->l('Enabled'),
				'align' => 'center',
				'active' => 'status',
				'type' => 'bool'
			)
		);

		$this->bulk_actions = array(
			'delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')),
			'enableSelection' => array('text' => $this->l('Enable selection')),
			'disableSelection' => array('text' => $this->l('Disable selection'))
			);
		$this->specificConfirmDelete = $this->l('When you delete a language, all related translations in the database will be deleted. Are you sure you want to delete this language?');

		parent::__construct();
	}

	public function renderList()
	{
		$this->addRowAction('edit');
		$this->addRowAction('delete');

		$this->displayWarning($this->l('When you delete a language, all related translations in the database will be deleted.'));
		$this->displayInformation($this->l('Your .htaccess file must be writable.'));
		return parent::renderList();
	}

	public function renderForm()
	{
		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Languages'),
				'image' => '../img/admin/world.gif'
			),
			'input' => array(
				array(
					'type' => 'hidden',
					'name' => 'ps_version'
				),
				array(
					'type' => 'text',
					'label' => $this->l('Name:'),
					'name' => 'name',
					'size' => 32,
					'maxlength' => 32,
					'required' => true
				),
				array(
					'type' => 'text',
					'label' => $this->l('ISO code:'),
					'name' => 'iso_code',
					'required' => true,
					'size' => 2,
					'maxlength' => 2,
					'desc' => $this->l('2-letter ISO code (e.g. fr, en, de)')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Language code:'),
					'name' => 'language_code',
					'required' => true,
					'size' => 2,
					'maxlength' => 5,
					'desc' => $this->l('Full language code (e.g. en-us, pt-br)')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Date format:'),
					'name' => 'date_format_lite',
					'required' => true,
					'size' => 15,
					'desc' => $this->l('Short date format (e.g. Y-m-d, d/m/Y)')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Date format (full):'),
					'name' => 'date_format_full',
					'required' => true,
					'size' => 25,
					'desc' => $this->l('Full date format (e.g., Y-m-d H:i:s, d/m/Y H:i)')
				),
				array(
					'type' => 'file',
					'label' => $this->l('Flag:'),
					'name' => 'flag',
					'required' => true,
					'desc' => $this->l('Upload country flag from your computer')
				),
				array(
					'type' => 'file',
					'label' => $this->l('"No-picture" image:'),
					'name' => 'no-picture',
					'desc' => $this->l('Image displayed when "no picture found"')
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Is RTL language:'),
					'name' => 'is_rtl',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'is_rtl_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'active_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					),
					'desc' => $this->l('Enable if this language is read from right to left').' '.
							$this->l('(Experimental: your theme must be compliant with RTL languages)')
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Status:'),
					'name' => 'active',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
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
					'desc' => $this->l('Allow or disallow this language to be selected by the customer')
				),
				array(
					'type' => 'special',
					'name' => 'resultCheckLangPack',
					'text' => $this->l('Check if a language pack is available for this ISO code...'),
					'img' => 'ajax-loader.gif'
				)
			)
		);

		if (Shop::isFeatureActive())
		{
			$this->fields_form['input'][] = array(
				'type' => 'shop',
				'label' => $this->l('Shop association:'),
				'name' => 'checkBoxShopAsso',
			);
		}

		$this->fields_form['submit'] = array(
			'title' => $this->l('   Save   '),
			'class' => 'button'
		);

		if (!($obj = $this->loadObject(true)))
			return;

		if ($obj->id && !$obj->checkFiles())
		{
			$this->fields_form['new'] = array(
				'legend' => array(
					'title' => $this->l('Warning'),
					'image' => '../img/admin/warning.gif'
				),
				'list_files' => array(
					array(
						'label' => $this->l('Translation files:'),
						'files' => Language::getFilesList($obj->iso_code, _THEME_NAME_, false, false, 'tr', true)
					),
					array(
						'label' => $this->l('Theme files:'),
						'files' => Language::getFilesList($obj->iso_code, _THEME_NAME_, false, false, 'theme', true)
					),
					array(
						'label' => $this->l('Mail files:'),
						'files' => Language::getFilesList($obj->iso_code, _THEME_NAME_, false, false, 'mail', true)
					)
				)
			);
		}

		$this->fields_value = array(
			'ps_version' => _PS_VERSION_
		);

		$this->addJS(_PS_JS_DIR_.'checkLangPack.js');

		return parent::renderForm();
	}

	public function postProcess()
	{
		if (isset($_GET['delete'.$this->table]))
		{
			if ($this->tabAccess['delete'] === '1')
		 	{
				if (Validate::isLoadedObject($object = $this->loadObject()) && isset($this->fieldImageSettings))
				{
					// English is needed by the system (ex. translations)
					if ($object->id == Language::getIdByIso('en'))
						$this->errors[] = $this->l('You cannot delete the English language because it is a system requirement, you can only deactivate it.');
					if ($object->id == Configuration::get('PS_LANG_DEFAULT'))
						$this->errors[] = $this->l('You cannot delete the default language');
					else if ($object->id == $this->context->language->id)
						$this->errors[] = $this->l('You cannot delete the language currently in use. Please change languages before deleting.');
					else if ($this->deleteNoPictureImages((int)Tools::getValue('id_lang')) && $object->delete())
					{
						$this->checkEmployeeIdLang($object->id);
						Tools::redirectAdmin(self::$currentIndex.'&conf=1'.'&token='.$this->token);
					}
						
				}
				else
					$this->errors[] = Tools::displayError('An error occurred while deleting object.').' <b>'.$this->table.'</b> '.
						Tools::displayError('(cannot load object)');
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to delete here.');
		}
		else if (Tools::getValue('submitDel'.$this->table) && isset($_POST[$this->table.'Box']))
		{
		 	if ($this->tabAccess['delete'] === '1')
			{
				if (in_array(Configuration::get('PS_LANG_DEFAULT'), $_POST[$this->table.'Box']))
					$this->errors[] = $this->l('You cannot delete the default language');
				else if (in_array($this->context->language->id, $_POST[$this->table.'Box']))
					$this->errors[] = $this->l('you cannot delete the language currently in use, please change languages before deleting');
				else
				{
				 	foreach ($_POST[$this->table.'Box'] as $language)
				 		$this->deleteNoPictureImages($language);
				 	if (Validate::isLoadedObject($object = $this->loadObject()))
				 		$this->checkEmployeeIdLang($object->id);
					parent::postProcess();
				}
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to delete here.');
		}
		else if (Tools::isSubmit('submitAddlang'))
		{
			/* New language */
			if ((int)Tools::getValue('id_'.$this->table) == 0)
			{
				if ($this->tabAccess['add'] === '1')
				{
					if (isset($_POST['iso_code']) && !empty($_POST['iso_code']) && Validate::isLanguageIsoCode(Tools::getValue('iso_code')) && Language::getIdByIso($_POST['iso_code']))
						$this->errors[] = Tools::displayError('This ISO code is already linked to another language.');
					if ((!empty($_FILES['no-picture']['tmp_name']) || !empty($_FILES['flag']['tmp_name'])) && Validate::isLanguageIsoCode(Tools::getValue('iso_code')))
					{
						if ($_FILES['no-picture']['error'] == UPLOAD_ERR_OK)
							$this->copyNoPictureImage(strtolower(Tools::getValue('iso_code')));
						// class AdminTab deal with every $_FILES content, don't do that for no-picture
						unset($_FILES['no-picture']);
						parent::postProcess();
					}
					else
					{
						$this->validateRules();
						$this->errors[] = Tools::displayError('Flag and "No picture" image fields are required.');
					}
				}
				else
					$this->errors[] = Tools::displayError('You do not have permission to add here.');
			}
			/* Language edition */
			else
			{
				if ($this->tabAccess['edit'] === '1')
				{
					if (( isset($_FILES['no-picture']) && !$_FILES['no-picture']['error'] || isset($_FILES['flag']) && !$_FILES['flag']['error'])
						&& Validate::isLanguageIsoCode(Tools::getValue('iso_code')))
					{
						if ($_FILES['no-picture']['error'] == UPLOAD_ERR_OK)
							$this->copyNoPictureImage(strtolower(Tools::getValue('iso_code')));
						// class AdminTab deal with every $_FILES content, don't do that for no-picture
						unset($_FILES['no-picture']);
						parent::postProcess();
					}

					if (!Validate::isLoadedObject($object = $this->loadObject()))
						$this->errors[] = Tools::displayError('An error occurred while updating status for object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
					if ((int)$object->id == (int)Configuration::get('PS_LANG_DEFAULT') && (int)$_POST['active'] != (int)$object->active)
						$this->errors[] = Tools::displayError('You cannot change the status of the default language.');
					else
						parent::postProcess();

					$this->validateRules();
				}
				else
					$this->errors[] = Tools::displayError('You do not have permission to edit here.');
			}
		}
		else if (isset($_GET['status'.$this->table]) && isset($_GET['id_lang']))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				if (!Validate::isLoadedObject($object = $this->loadObject()))
					$this->errors[] = Tools::displayError('An error occurred while updating status for object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
				if ((int)$object->id == (int)Configuration::get('PS_LANG_DEFAULT'))
					$this->errors[] = Tools::displayError('You cannot change the status of the default language.');
				else
				{
					if (Validate::isLoadedObject($object))
						$this->checkEmployeeIdLang($object->id);
					return parent::postProcess();
				}
					
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		else
			return parent::postProcess();
	}

	/**
	 * Copy a no-product image
	 *
	 * @param string $language Language iso_code for no-picture image filename
	 */
	public function copyNoPictureImage($language)
	{
		if (isset($_FILES['no-picture']) && $_FILES['no-picture']['error'] === 0)
			if ($error = ImageManager::validateUpload($_FILES['no-picture'], Tools::getMaxUploadSize()))
				$this->errors[] = $error;
			else
			{
				if (!($tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS')) || !move_uploaded_file($_FILES['no-picture']['tmp_name'], $tmp_name))
					return false;
				if (!ImageManager::resize($tmp_name, _PS_IMG_DIR_.'p/'.$language.'.jpg'))
					$this->errors[] = Tools::displayError('An error occurred while copying no-picture image to your product folder.');
				if (!ImageManager::resize($tmp_name, _PS_IMG_DIR_.'c/'.$language.'.jpg'))
					$this->errors[] = Tools::displayError('An error occurred while copying "No picture" image to your category folder.');
				if (!ImageManager::resize($tmp_name, _PS_IMG_DIR_.'m/'.$language.'.jpg'))
					$this->errors[] = Tools::displayError('An error occurred while copying "No picture" image to your manufacturer folder');
				else
				{
					$images_types = ImageType::getImagesTypes('products');
					foreach ($images_types as $k => $image_type)
					{
						if (!ImageManager::resize($tmp_name, _PS_IMG_DIR_.'p/'.$language.'-default-'.stripslashes($image_type['name']).'.jpg', $image_type['width'], $image_type['height']))
							$this->errors[] = Tools::displayError('An error occurred while resizing "No picture" image to your product directory.');
						if (!ImageManager::resize($tmp_name, _PS_IMG_DIR_.'c/'.$language.'-default-'.stripslashes($image_type['name']).'.jpg', $image_type['width'], $image_type['height']))
							$this->errors[] = Tools::displayError('An error occurred while resizing "No picture" image to your category directory.');
						if (!ImageManager::resize($tmp_name, _PS_IMG_DIR_.'m/'.$language.'-default-'.stripslashes($image_type['name']).'.jpg', $image_type['width'], $image_type['height']))
							$this->errors[] = Tools::displayError('An error occurred while resizing "No picture" image to your manufacturer directory.');
					}
				}
				unlink($tmp_name);
			}
	}

	/**
	 * deleteNoPictureImages will delete all default image created for the language id_language
	 *
	 * @param string $id_language
	 * @return boolean true if no error
	 */
	protected function deleteNoPictureImages($id_language)
	{
	 	$language = Language::getIsoById($id_language);
		$images_types = ImageType::getImagesTypes('products');
		$dirs = array(_PS_PROD_IMG_DIR_, _PS_CAT_IMG_DIR_, _PS_MANU_IMG_DIR_, _PS_SUPP_IMG_DIR_, _PS_MANU_IMG_DIR_);
		foreach ($dirs as $dir)
		{
			foreach ($images_types as $k => $image_type)
				if (file_exists($dir.$language.'-default-'.stripslashes($image_type['name']).'.jpg'))
					if (!unlink($dir.$language.'-default-'.stripslashes($image_type['name']).'.jpg'))
						$this->errors[] = Tools::displayError('An error occurred during image deletion.');

			if (file_exists($dir.$language.'.jpg'))
				if (!unlink($dir.$language.'.jpg'))
					$this->errors[] = Tools::displayError('An error occurred during image deletion.');
		}

		return !count($this->errors) ? true : false;
	}

	protected function copyFromPost(&$object, $table)
	{
		if ($object->id && ($object->iso_code != $_POST['iso_code']))
			if (Validate::isLanguageIsoCode($_POST['iso_code']))
				$object->moveToIso($_POST['iso_code']);
		parent::copyFromPost($object, $table);
	}

	public function ajaxProcessCheckLangPack()
	{
		$this->json = true;
		if (empty($_GET['iso_lang']))
		{
			$this->status = 'error';
			$this->errors[] = '[TECHNICAL ERROR] iso_lang not set or empty';
		}
		if (empty($_GET['ps_version']))
		{
			$this->status = 'error';
			$this->errors[] = '[TECHNICAL ERROR] ps_version not set or empty';
		}
		if (@fsockopen('www.prestashop.com', 80))
		{
			// Get all iso code available
			$lang_packs = Tools::file_get_contents('http://www.prestashop.com/download/lang_packs/get_language_pack.php?version='.(string)$_GET['ps_version'].'&iso_lang='.(string)$_GET['iso_lang']);
			
			if ($lang_packs !== '' && Tools::jsonDecode($lang_packs) !== null)
			{
				$this->status = 'ok';
				$this->content = $lang_packs;
			}
			else
			{
				$this->status = 'error';
				$this->errors[] = $this->l('wrong ISO code or language pack unavailable');
			}
		}
		else
		{
			$this->status = 'error';
			$this->errors[] = '[TECHNICAL ERROR] Server unreachable';
		}
	}
	
	protected function checkEmployeeIdLang($current_id_lang)
	{
		//update employee lang if current id lang is disabled
		Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'employee` set `id_lang`='.(int)Configuration::get('PS_LANG_DEFAULT').' WHERE `id_lang`='.(int)$current_id_lang);
	}
}


