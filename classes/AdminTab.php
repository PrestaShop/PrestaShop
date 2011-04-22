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

abstract class AdminTabCore
{
	/** @var integer Tab id */
	public $id = -1;

	/** @var string Associated table name */
	public $table;

	/** @var string Object identifier inside the associated table */
	protected $identifier = false;

	/** @var string Tab name */
	public $name;

	/** @var string Security token */
	public $token;

	/** @var boolean Automatically join language table if true */
	public $lang = false;

	/** @var boolean Tab Automatically displays edit/delete icons if true */
	public $edit = false;

	/** @var boolean Tab Automatically displays view icon if true */
	public $view = false;

	/** @var boolean Tab Automatically displays delete icon if true */
	public $delete = false;

	/** @var boolean Table records are not deleted but marked as deleted */
	public $deleted = false;

	/** @var boolean Tab Automatically displays duplicate icon if true */
	public $duplicate = false;

	/** @var boolean Content line is clickable if true */
	public $noLink = false;

	/** @var boolean select other required fields */
	public $requiredDatabase = false;

	/** @var boolean Tab Automatically displays '$color' as background color on listing if true */
	public $colorOnBackground = false;

	/** @var string Add fields into data query to display list */
	protected $_select;

	/** @var string Join tables into data query to display list */
	protected $_join;

	/** @var string Add conditions into data query to display list */
	protected $_where;

	/** @var string Group rows into data query to display list */
	protected $_group;

	/** @var string Having rows into data query to display list */
	protected $_having;

	/** @var array Name and directory where class image are located */
	public $fieldImageSettings = array();

	/** @var string Image type */
	public $imageType = 'jpg';

	/** @var array Fields to display in list */
	public $fieldsDisplay = array();

	/** @var array Cache for query results */
	protected $_list = array();

	/** @var integer Number of results in list */
	protected $_listTotal = 0;

	/** @var array WHERE clause determined by filter fields */
	protected $_filter;

	/** @var array Temporary SQL table WHERE clause determinated by filter fields */
	protected $_tmpTableFilter = '';

	/** @var array Number of results in list per page (used in select field) */
	protected $_pagination = array(20, 50, 100, 300);

	/** @var string ORDER BY clause determined by field/arrows in list header */
	protected $_orderBy;

	/** @var string Default ORDER BY clause when $_orderBy is not defined */
	protected $_defaultOrderBy = false;

	/** @var string Order way (ASC, DESC) determined by arrows in list header */
	protected $_orderWay;

	/** @var integer Max image size for upload */
	protected $maxImageSize = 2000000;

	/** @var array Errors displayed after post processing */
	public $_errors = array();

	/** @var array Confirmations displayed after post processing */
	protected $_conf;

	/** @var object Object corresponding to the tab */
	protected $_object = false;

	/** @var array tabAccess */
	public $tabAccess;

	/** @var string specificConfirmDelete */
	public $specificConfirmDelete = NULL;

	protected $identifiersDnd = array('id_product' => 'id_product', 'id_category' => 'id_category_to_move','id_cms_category' => 'id_cms_category_to_move', 'id_cms' => 'id_cms');

	/** @var bool Redirect or not ater a creation */
	protected $_redirect = true;

	protected	$_languages = NULL;
	protected	$_defaultFormLanguage = NULL;

	protected $_includeObj = array();
	protected $_includeVars = false;
	protected $_includeContainer = true;

	public static $tabParenting = array(
		'AdminProducts' => 'AdminCatalog',
		'AdminCategories' => 'AdminCatalog',
		'AdminImageResize' => 'AdminImages',
		'AdminCMS' => 'AdminCMSContent',
		'AdminCMSCategories' => 'AdminCMSContent',
		'AdminOrdersStates' => 'AdminStatuses',
		'AdminAttributeGenerator' => 'AdminProducts',
		'AdminAttributes' => 'AdminAttributesGroups',
		'AdminFeaturesValues' => 'AdminFeatures',
		'AdminReturnStates' => 'AdminStatuses',
		'AdminStatsTab' => 'AdminStats'
	);

	public function __construct()
	{
		global $cookie;

		$this->id = Tab::getCurrentTabId();
		$this->_conf = array(
		1 => $this->l('Deletion successful'), 2 => $this->l('Selection successfully deleted'),
		3 => $this->l('Creation successful'), 4 => $this->l('Update successful'),
		5 => $this->l('Status update successful'), 6 => $this->l('Settings update successful'),
		7 => $this->l('Image successfully deleted'), 8 => $this->l('Module downloaded successfully'),
		9 => $this->l('Thumbnails successfully regenerated'), 10 => $this->l('Message sent to the customer'),
		11 => $this->l('Comment added'), 12 => $this->l('Module installed successfully'),
		13 => $this->l('Module uninstalled successfully'), 14 => $this->l('Language successfully copied'),
		15 => $this->l('Translations successfully added'), 16 => $this->l('Module transplanted successfully to hook'),
		17 => $this->l('Module removed successfully from hook'), 18 => $this->l('Upload successful'),
		19 => $this->l('Duplication completed successfully'), 20 => $this->l('Translation added successfully but the language has not been created'),
		21 => $this->l('Module reset successfully'), 22 => $this->l('Module deleted successfully'),
		23 => $this->l('Localization pack imported successfully'), 24 => $this->l('Refund Successful'));
		if (!$this->identifier) $this->identifier = 'id_'.$this->table;
		if (!$this->_defaultOrderBy) $this->_defaultOrderBy = $this->identifier;
		$className = get_class($this);
		if ($className == 'AdminCategories' OR $className == 'AdminProducts')
			$className = 'AdminCatalog';
		$this->token = Tools::getAdminToken($className.(int)($this->id).(int)($cookie->id_employee));

	}


	/**
	 * use translations files to replace english expression.
	 *
	 * @param mixed $string term or expression in english
	 * @param string $class
	 * @param boolan $addslashes if set to true, the return value will pass through addslashes(). Otherwise, stripslashes().
	 * @param boolean $htmlentities if set to true(default), the return value will pass through htmlentities($string, ENT_QUOTES, 'utf-8')
	 * @return string the translation if available, or the english default text.
	 */
	protected function l($string, $class = 'AdminTab', $addslashes = FALSE, $htmlentities = TRUE)
	{
		// if the class is extended by a module, use modules/[module_name]/xx.php lang file
		$currentClass = get_class($this);
		if(Module::getModuleNameFromClass($currentClass))
		{
			$string = str_replace('\'', '\\\'', $string);
			return Module::findTranslation(Module::$classInModule[$currentClass], $string, $currentClass);
		}
		global $_LANGADM;

        if ($class == __CLASS__)
                $class = 'AdminTab';

		$key = md5(str_replace('\'', '\\\'', $string));
		$str = (key_exists(get_class($this).$key, $_LANGADM)) ? $_LANGADM[get_class($this).$key] : ((key_exists($class.$key, $_LANGADM)) ? $_LANGADM[$class.$key] : $string);
		$str = $htmlentities ? htmlentities($str, ENT_QUOTES, 'utf-8') : $str;
		return str_replace('"', '&quot;', ($addslashes ? addslashes($str) : stripslashes($str)));
	}

	/**
	 * Manage page display (form, list...)
	 *
	 * @global string $currentIndex Current URL in order to keep current Tab
	 */
	public function display()
	{
		global $currentIndex, $cookie;

		// Include other tab in current tab
		if ($this->includeSubTab('display', array('submitAdd2', 'add', 'update', 'view')));

		// Include current tab
		elseif ((Tools::getValue('submitAdd'.$this->table) AND sizeof($this->_errors)) OR isset($_GET['add'.$this->table]))
		{
			if ($this->tabAccess['add'] === '1')
			{
				$this->displayForm();
				if ($this->tabAccess['view'])
					echo '<br /><br /><a href="'.((Tools::getValue('back')) ? Tools::getValue('back') : $currentIndex.'&token='.$this->token).'"><img src="../img/admin/arrow2.gif" /> '.((Tools::getValue('back')) ? $this->l('Back') : $this->l('Back to list')).'</a><br />';
			}
			else
				echo $this->l('You do not have permission to add here');
		}
		elseif (isset($_GET['update'.$this->table]))
		{
			if ($this->tabAccess['edit'] === '1' OR ($this->table == 'employee' AND $cookie->id_employee == Tools::getValue('id_employee')))
			{
				$this->displayForm();
				if ($this->tabAccess['view'])
					echo '<br /><br /><a href="'.((Tools::getValue('back')) ? Tools::getValue('back') : $currentIndex.'&token='.$this->token).'"><img src="../img/admin/arrow2.gif" /> '.((Tools::getValue('back')) ? $this->l('Back') : $this->l('Back to list')).'</a><br />';
			}
			else
				echo $this->l('You do not have permission to edit here');
		}
		elseif (isset($_GET['view'.$this->table]))
			$this->{'view'.$this->table}();

		else
		{
			$this->getList((int)($cookie->id_lang));
			$this->displayList();
			$this->displayOptionsList();
			$this->displayRequiredFields();
			$this->includeSubTab('display');
		}
	}

	public function displayRequiredFields()
	{
		global $currentIndex;
		if (!$this->tabAccess['add'] OR !$this->tabAccess['delete'] === '1' OR !$this->requiredDatabase)
			return;
		$rules = call_user_func_array(array($this->className, 'getValidationRules'), array($this->className));
		$required_class_fields = array($this->identifier);
		foreach ($rules['required'] AS $required)
			$required_class_fields[] = $required;


		echo '<br />
		<a href="#" onclick="if ($(\'.requiredFieldsParameters:visible\').length == 0) $(\'.requiredFieldsParameters\').slideDown(\'slow\'); else $(\'.requiredFieldsParameters\').slideUp(\'slow\'); return false;"><img src="../img/admin/duplicate.gif" alt="" /> '.$this->l('Advanced parameters...').'</a>
		<fieldset style="display:none" class="width1 requiredFieldsParameters">
		<legend>'.$this->l('Required Fields').'</legend>
		<form name="updateFields" action="'.$currentIndex.'&submitFields'.$this->table.'=1&token='.$this->token.'" method="post">
		<p><b>'.$this->l('Select the fields you require.').'<br />
		'.$this->l('Be careful in using this feature, a misuse can alter your store.').'</b></p>
		<table cellspacing="0" cellpadding="0" class="table width1 clear">
		<tr>
			<th><input type="checkbox" onclick="checkDelBoxes(this.form, \'tablesBox[]\', this.checked)" class="noborder" name="checkme"></th>
			<th>'.$this->l('Field Name').'</th>
		</tr>';

		$object = new $this->className();
		$res = $object->getFieldsRequiredDatabase();

		$required_fields = array();
		if ($res)
			foreach ($res AS $row)
				$required_fields[(int)$row['id_required_field']] = $row['field_name'];


		$table_fields = Db::getInstance()->ExecuteS('SHOW COLUMNS FROM '.pSQL(_DB_PREFIX_.$this->table));
		$irow = 0;
		foreach ($table_fields AS $field)
		{
			if (in_array($field['Field'], $required_class_fields))
				continue;
			echo '<tr class="'.($irow++ % 2 ? 'alt_row' : '').'">
						<td class="noborder"><input type="checkbox" name="fieldsBox[]" value="'.$field['Field'].'" '.(in_array($field['Field'], $required_fields) ? 'checked="checked"' : '').' /></td>
						<td>'.$field['Field'].'</td>
					</tr>';
		}
		echo '</table><br />
				<input style="margin-left:15px;" class="button" type="submit" value="'.$this->l('Submit').'" name="submitFields" />
				<br />
		</fieldset>';
	}

	public function includeSubTab($methodname, $actions = array())
	{
		if (!isset($this->_includeTab) OR !is_array($this->_includeTab))
			return ;
		$key = 0;
		foreach ($this->_includeTab as $subtab => $extraVars)
		{
			/* New tab loading */
			$classname = 'Admin'.$subtab;
			if ($module = Db::getInstance()->getValue('SELECT `module` FROM `'._DB_PREFIX_.'tab` WHERE `class_name` = \''.pSQL($classname).'\'') AND file_exists(_PS_MODULE_DIR_.'/'.$module.'/'.$classname.'.php'))
				include_once(_PS_MODULE_DIR_.'/'.$module.'/'.$classname.'.php');
			elseif (file_exists(PS_ADMIN_DIR.'/tabs/'.$classname.'.php'))
				include_once('tabs/'.$classname.'.php');
			if (!isset($this->_includeObj[$key]))
				$this->_includeObj[$key] = new $classname;
			$adminTab = $this->_includeObj[$key];
			$adminTab->token = $this->token;

			/* Extra variables addition */
			if (!empty($extraVars) AND is_array($extraVars))
				foreach ($extraVars AS $varKey => $varValue)
					$adminTab->$varKey = $varValue;

			/* Actions management */
			foreach ($actions as $action)
			{
				switch ($action)
				{

					case 'submitAdd1':
						if (Tools::getValue('submitAdd'.$adminTab->table))
							$ok_inc = true;
						break;
					case 'submitAdd2':
						if (Tools::getValue('submitAdd'.$adminTab->table) AND sizeof($adminTab->_errors))
							$ok_inc = true;
						break;
					case 'submitDel':
						if (Tools::getValue('submitDel'.$adminTab->table))
							$ok_inc = true;
						break;
					case 'submitFilter':
						if (Tools::isSubmit('submitFilter'.$adminTab->table))
							$ok_inc = true;
					case 'submitReset':
						if (Tools::isSubmit('submitReset'.$adminTab->table))
							$ok_inc = true;
					default:
						if (isset($_GET[$action.$adminTab->table]))
							$ok_inc = true;
				}
			}
			$inc = false;
			if ((isset($ok_inc) AND $ok_inc) OR !sizeof($actions))
			{
				if (!$adminTab->viewAccess())
				{
					echo Tools::displayError('Access denied');
					return false;
				}
				if (!sizeof($actions))
					if (($methodname == 'displayErrors' AND sizeof($adminTab->_errors)) OR $methodname != 'displayErrors')
						echo (isset($this->_includeTabTitle[$key]) ? '<h2>'.$this->_includeTabTitle[$key].'</h2>' : '');
				if ($adminTab->_includeVars)
					foreach ($adminTab->_includeVars AS $var => $value)
						$adminTab->$var = $this->$value;
				$adminTab->$methodname();
				$inc = true;
			}
			$key++;
		}
		return $inc;
	}

	/**
	 * Manage page display (form, list...)
	 *
	 * @param string $className Allow to validate a different class than the current one
	 */
	public function validateRules($className = false)
	{
		if (!$className)
			$className = $this->className;

		/* Class specific validation rules */
		$rules = call_user_func(array($className, 'getValidationRules'), $className);

		if ((sizeof($rules['requiredLang']) OR sizeof($rules['sizeLang']) OR sizeof($rules['validateLang'])))
		{
			/* Language() instance determined by default language */
			$defaultLanguage = new Language((int)(Configuration::get('PS_LANG_DEFAULT')));

			/* All availables languages */
			$languages = Language::getLanguages(false);
		}

		/* Checking for required fields */
		foreach ($rules['required'] AS $field)
			if (($value = Tools::getValue($field)) == false AND (string)$value != '0')
				if (!Tools::getValue($this->identifier) OR ($field != 'passwd' AND $field != 'no-picture'))
					$this->_errors[] = $this->l('the field').' <b>'.call_user_func(array($className, 'displayFieldName'), $field, $className).'</b> '.$this->l('is required');

		/* Checking for multilingual required fields */
		foreach ($rules['requiredLang'] AS $fieldLang)
			if (($empty = Tools::getValue($fieldLang.'_'.$defaultLanguage->id)) === false OR empty($empty))
				$this->_errors[] = $this->l('the field').' <b>'.call_user_func(array($className, 'displayFieldName'), $fieldLang, $className).'</b> '.$this->l('is required at least in').' '.$defaultLanguage->name;

		/* Checking for maximum fields sizes */
		foreach ($rules['size'] AS $field => $maxLength)
			if (Tools::getValue($field) !== false AND Tools::strlen(Tools::getValue($field)) > $maxLength)
				$this->_errors[] = $this->l('the field').' <b>'.call_user_func(array($className, 'displayFieldName'), $field, $className).'</b> '.$this->l('is too long').' ('.$maxLength.' '.$this->l('chars max').')';

		/* Checking for maximum multilingual fields size */
		foreach ($rules['sizeLang'] AS $fieldLang => $maxLength)
			foreach ($languages AS $language)
				if (Tools::getValue($fieldLang.'_'.$language['id_lang']) !== false AND Tools::strlen(Tools::getValue($fieldLang.'_'.$language['id_lang'])) > $maxLength)
					$this->_errors[] = $this->l('the field').' <b>'.call_user_func(array($className, 'displayFieldName'), $fieldLang, $className).' ('.$language['name'].')</b> '.$this->l('is too long').' ('.$maxLength.' '.$this->l('chars max, html chars including').')';

		/* Overload this method for custom checking */
		$this->_childValidation();

		/* Checking for fields validity */
		foreach ($rules['validate'] AS $field => $function)
			if (($value = Tools::getValue($field)) !== false AND ($field != 'passwd'))
				if (!Validate::$function($value))
					$this->_errors[] = $this->l('the field').' <b>'.call_user_func(array($className, 'displayFieldName'), $field, $className).'</b> '.$this->l('is invalid');

		/* Checking for passwd_old validity */
		if (($value = Tools::getValue('passwd')) != false)
		{
			if ($className == 'Employee' AND !Validate::isPasswdAdmin($value))
				$this->_errors[] = $this->l('the field').' <b>'.call_user_func(array($className, 'displayFieldName'), 'passwd', $className).'</b> '.$this->l('is invalid');
			elseif ($className == 'Customer' AND !Validate::isPasswd($value))
				$this->_errors[] = $this->l('the field').' <b>'.call_user_func(array($className, 'displayFieldName'), 'passwd', $className).'</b> '.$this->l('is invalid');
		}

		/* Checking for multilingual fields validity */
		foreach ($rules['validateLang'] AS $fieldLang => $function)
			foreach ($languages AS $language)
				if (($value = Tools::getValue($fieldLang.'_'.$language['id_lang'])) !== false AND !empty($value))
					if (!Validate::$function($value))
						$this->_errors[] = $this->l('the field').' <b>'.call_user_func(array($className, 'displayFieldName'), $fieldLang, $className).' ('.$language['name'].')</b> '.$this->l('is invalid');
	}

	/**
	 * Overload this method for custom checking
	 */
	protected function _childValidation() { }

	/**
	 * Overload this method for custom checking
	 *
	 * @param integer $id Object id used for deleting images
	 */
	public function deleteImage($id)
	{
		$dir = null;
		/* Deleting object images and thumbnails (cache) */
		if (key_exists('dir', $this->fieldImageSettings))
		{
			$dir = $this->fieldImageSettings['dir'].'/';
			if (file_exists(_PS_IMG_DIR_.$dir.$id.'.'.$this->imageType) AND !unlink(_PS_IMG_DIR_.$dir.$id.'.'.$this->imageType))
				return false;
		}
		if (file_exists(_PS_TMP_IMG_DIR_.$this->table.'_'.$id.'.'.$this->imageType) AND !unlink(_PS_TMP_IMG_DIR_.$this->table.'_'.$id.'.'.$this->imageType))
			return false;
		if (file_exists(_PS_TMP_IMG_DIR_.$this->table.'_mini_'.$id.'.'.$this->imageType) AND !unlink(_PS_TMP_IMG_DIR_.$this->table.'_mini_'.$id.'.'.$this->imageType))
			return false;
		$types = ImageType::getImagesTypes();
		foreach ($types AS $imageType)
			if (file_exists(_PS_IMG_DIR_.$dir.$id.'-'.stripslashes($imageType['name']).'.'.$this->imageType) AND !unlink(_PS_IMG_DIR_.$dir.$id.'-'.stripslashes($imageType['name']).'.'.$this->imageType))
				return false;
		return true;
	}

	/**
	 * Manage page processing
	 *
	 * @global string $currentIndex Current URL in order to keep current Tab
	 */
	public function postProcess()
	{
		global $currentIndex, $cookie;
		if (!isset($this->table))
			return false;

		// set token
		$token = Tools::getValue('token') ? Tools::getValue('token') : $this->token;

		// Sub included tab postProcessing
		$this->includeSubTab('postProcess', array('status', 'submitAdd1', 'submitDel', 'delete', 'submitFilter', 'submitReset'));

		/* Delete object image */
		if (isset($_GET['deleteImage']))
		{
			if (Validate::isLoadedObject($object = $this->loadObject()) AND isset($this->fieldImageSettings))
				if ($this->deleteImage($object->id))
					Tools::redirectAdmin($currentIndex.'&add'.$this->table.'&'.$this->identifier.'='.Tools::getValue($this->identifier).'&conf=7&token='.$token);
			$this->_errors[] = Tools::displayError('An error occurred during image deletion (cannot load object).');
		}

		/* Delete object */
		elseif (isset($_GET['delete'.$this->table]))
		{
			if ($this->tabAccess['delete'] === '1')
			{
				if (Validate::isLoadedObject($object = $this->loadObject()) AND isset($this->fieldImageSettings))
				{
					// check if request at least one object with noZeroObject
					if (isset($object->noZeroObject) AND sizeof(call_user_func(array($this->className, $object->noZeroObject))) <= 1)
						$this->_errors[] = Tools::displayError('You need at least one object.').' <b>'.$this->table.'</b><br />'.Tools::displayError('You cannot delete all of the items.');
					else
					{
						$this->deleteImage($object->id);
						if ($this->deleted)
						{
							$object->deleted = 1;
							if ($object->update())
								Tools::redirectAdmin($currentIndex.'&conf=1&token='.$token);
						}
						elseif ($object->delete())
							Tools::redirectAdmin($currentIndex.'&conf=1&token='.$token);
						$this->_errors[] = Tools::displayError('An error occurred during deletion.');
					}
				}
				else
					$this->_errors[] = Tools::displayError('An error occurred while deleting object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to delete here.');
		}

		/* Change object statuts (active, inactive) */
		elseif ((isset($_GET['status'.$this->table]) OR isset($_GET['status'])) AND Tools::getValue($this->identifier))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				if (Validate::isLoadedObject($object = $this->loadObject()))
				{
					if ($object->toggleStatus())
						Tools::redirectAdmin($currentIndex.'&conf=5'.((($id_category = (int)(Tools::getValue('id_category'))) AND Tools::getValue('id_product')) ? '&id_category='.$id_category : '').'&token='.$token);
					else
						$this->_errors[] = Tools::displayError('An error occurred while updating status.');
				}
				else
					$this->_errors[] = Tools::displayError('An error occurred while updating status for object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		/* Move an object */
		elseif (isset($_GET['position']))
		{
			if ($this->tabAccess['edit'] !== '1')
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
			elseif (!Validate::isLoadedObject($object = $this->loadObject()))
				$this->_errors[] = Tools::displayError('An error occurred while updating status for object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
			elseif (!$object->updatePosition((int)(Tools::getValue('way')), (int)(Tools::getValue('position'))))
				$this->_errors[] = Tools::displayError('Failed to update the position.');
			else
				Tools::redirectAdmin($currentIndex.'&'.$this->table.'Orderby=position&'.$this->table.'Orderway=asc&conf=5'.(($id_category = (int)(Tools::getValue($this->identifier))) ? ('&'.$this->identifier.'='.$id_category) : '').'&token='.$token);
				 Tools::redirectAdmin($currentIndex.'&'.$this->table.'Orderby=position&'.$this->table.'Orderway=asc&conf=5'.((($id_category = (int)(Tools::getValue('id_category'))) AND Tools::getValue('id_product')) ? '&id_category='.$id_category : '').'&token='.$token);
		}
		/* Delete multiple objects */
		elseif (Tools::getValue('submitDel'.$this->table))
		{
			if ($this->tabAccess['delete'] === '1')
			{
				if (isset($_POST[$this->table.'Box']))
				{
					$object = new $this->className();
					if (isset($object->noZeroObject) AND
						// Check if all object will be deleted
						(sizeof(call_user_func(array($this->className, $object->noZeroObject))) <= 1 OR sizeof($_POST[$this->table.'Box']) == sizeof(call_user_func(array($this->className, $object->noZeroObject)))))
						$this->_errors[] = Tools::displayError('You need at least one object.').' <b>'.$this->table.'</b><br />'.Tools::displayError('You cannot delete all of the items.');
					else
					{
						$result = true;
						if ($this->deleted)
						{
							foreach(Tools::getValue($this->table.'Box') as $id)
							{
								$toDelete = new $this->className($id);
								$toDelete->deleted = 1;
								$result = $result AND $toDelete->update();
							}
						}
						else
							$result = $object->deleteSelection(Tools::getValue($this->table.'Box'));

						if ($result)
							Tools::redirectAdmin($currentIndex.'&conf=2&token='.$token);
						$this->_errors[] = Tools::displayError('An error occurred while deleting selection.');
					}
				}
				else
					$this->_errors[] = Tools::displayError('You must select at least one element to delete.');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to delete here.');
		}

		/* Create or update an object */
		elseif (Tools::getValue('submitAdd'.$this->table))
		{
			/* Checking fields validity */
			$this->validateRules();
			if (!sizeof($this->_errors))
			{
				$id = (int)(Tools::getValue($this->identifier));

				/* Object update */
				if (isset($id) AND !empty($id))
				{
					if ($this->tabAccess['edit'] === '1' OR ($this->table == 'employee' AND $cookie->id_employee == Tools::getValue('id_employee') AND Tools::isSubmit('updateemployee')))
					{
						$object = new $this->className($id);
						if (Validate::isLoadedObject($object))
						{
							/* Specific to objects which must not be deleted */
							if ($this->deleted AND $this->beforeDelete($object))
							{
								// Create new one with old objet values
								$objectNew = new $this->className($object->id);
								$objectNew->id = NULL;
								$objectNew->date_add = '';
								$objectNew->date_upd = '';

								// Update old object to deleted
								$object->deleted = 1;
								$object->update();

								// Update new object with post values
								$this->copyFromPost($objectNew, $this->table);
								$result = $objectNew->add();
								if (Validate::isLoadedObject($objectNew))
									$this->afterDelete($objectNew, $object->id);
							}
							else
							{
								$this->copyFromPost($object, $this->table);
								$result = $object->update();
								$this->afterUpdate($object);
							}
							if (!$result)
								$this->_errors[] = Tools::displayError('An error occurred while updating object.').' <b>'.$this->table.'</b> ('.Db::getInstance()->getMsgError().')';
							elseif ($this->postImage($object->id) AND !sizeof($this->_errors))
							{
								$parent_id = (int)(Tools::getValue('id_parent', 1));
								// Specific back redirect
								if ($back = Tools::getValue('back'))
									Tools::redirectAdmin(urldecode($back).'&conf=4');
								// Specific scene feature
								if (Tools::getValue('stay_here') == 'on' || Tools::getValue('stay_here') == 'true' || Tools::getValue('stay_here') == '1')
									Tools::redirectAdmin($currentIndex.'&'.$this->identifier.'='.$object->id.'&conf=4&updatescene&token='.$token);
								// Save and stay on same form
								if (Tools::isSubmit('submitAdd'.$this->table.'AndStay'))
									Tools::redirectAdmin($currentIndex.'&'.$this->identifier.'='.$object->id.'&conf=4&update'.$this->table.'&token='.$token);
								// Save and back to parent
								if (Tools::isSubmit('submitAdd'.$this->table.'AndBackToParent'))
									Tools::redirectAdmin($currentIndex.'&'.$this->identifier.'='.$parent_id.'&conf=4&token='.$token);
								// Default behavior (save and back)
								Tools::redirectAdmin($currentIndex.($parent_id ? '&'.$this->identifier.'='.$object->id : '').'&conf=4&token='.$token);
							}
						}
						else
							$this->_errors[] = Tools::displayError('An error occurred while updating object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
					}
					else
						$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
				}

				/* Object creation */
				else
				{
					if ($this->tabAccess['add'] === '1')
					{
						$object = new $this->className();
						$this->copyFromPost($object, $this->table);
						if (!$object->add())
							$this->_errors[] = Tools::displayError('An error occurred while creating object.').' <b>'.$this->table.' ('.mysql_error().')</b>';
						elseif (($_POST[$this->identifier] = $object->id /* voluntary */) AND $this->postImage($object->id) AND !sizeof($this->_errors) AND $this->_redirect)
						{
							$parent_id = (int)(Tools::getValue('id_parent', 1));
							$this->afterAdd($object);
							// Save and stay on same form
							if (Tools::isSubmit('submitAdd'.$this->table.'AndStay'))
								Tools::redirectAdmin($currentIndex.'&'.$this->identifier.'='.$object->id.'&conf=3&update'.$this->table.'&token='.$token);
							// Save and back to parent
							if (Tools::isSubmit('submitAdd'.$this->table.'AndBackToParent'))
								Tools::redirectAdmin($currentIndex.'&'.$this->identifier.'='.$parent_id.'&conf=3&token='.$token);
							// Default behavior (save and back)
							Tools::redirectAdmin($currentIndex.($parent_id ? '&'.$this->identifier.'='.$object->id : '').'&conf=3&token='.$token);
						}
					}
					else
						$this->_errors[] = Tools::displayError('You do not have permission to add here.');
				}
			}
			$this->_errors = array_unique($this->_errors);
		}

		/* Cancel all filters for this tab */
		elseif (isset($_POST['submitReset'.$this->table]))
		{
			$filters = $cookie->getFamily($this->table.'Filter_');
			foreach ($filters AS $cookieKey => $filter)
				if (strncmp($cookieKey, $this->table.'Filter_', 7 + Tools::strlen($this->table)) == 0)
					{
						$key = substr($cookieKey, 7 + Tools::strlen($this->table));
						/* Table alias could be specified using a ! eg. alias!field */
						$tmpTab = explode('!', $key);
						$key = (count($tmpTab) > 1 ? $tmpTab[1] : $tmpTab[0]);
						if (array_key_exists($key, $this->fieldsDisplay))
							unset($cookie->$cookieKey);
					}
			if (isset($cookie->{'submitFilter'.$this->table}))
				unset($cookie->{'submitFilter'.$this->table});
			if (isset($cookie->{$this->table.'Orderby'}))
				unset($cookie->{$this->table.'Orderby'});
			if (isset($cookie->{$this->table.'Orderway'}))
				unset($cookie->{$this->table.'Orderway'});
			unset($_POST);
		}

		/* Submit options list */
		elseif (Tools::getValue('submitOptions'.$this->table))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				foreach ($this->_fieldsOptions as $key => $field)
				{
					if ($field['type'] == 'textLang' OR $field['type'] == 'textareaLang')
					{
						$languages = Language::getLanguages(false);
						$list = array();
						foreach ($languages as $language)
							$list[$language['id_lang']] = (isset($field['cast']) ? $field['cast'](Tools::getValue($key.'_'.$language['id_lang'])) : Tools::getValue($key.'_'.$language['id_lang']));
						Configuration::updateValue($key, $list);
					} else
						Configuration::updateValue($key, (isset($field['cast']) ? $field['cast'](Tools::getValue($key)) : Tools::getValue($key)));
				}
				Tools::redirectAdmin($currentIndex.'&conf=6&token='.$token);
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}

		/* Manage list filtering */
		elseif (Tools::isSubmit('submitFilter'.$this->table) OR $cookie->{'submitFilter'.$this->table} !== false)
		{
			$_POST = array_merge($cookie->getFamily($this->table.'Filter_'), (isset($_POST) ? $_POST : array()));
			foreach ($_POST AS $key => $value)
			{
				/* Extracting filters from $_POST on key filter_ */
				if ($value != NULL AND !strncmp($key, $this->table.'Filter_', 7 + Tools::strlen($this->table)))
				{
					$key = Tools::substr($key, 7 + Tools::strlen($this->table));
					/* Table alias could be specified using a ! eg. alias!field */
					$tmpTab = explode('!', $key);
					$filter = count($tmpTab) > 1 ? $tmpTab[1] : $tmpTab[0];
					if ($field = $this->filterToField($key, $filter))
					{
						$type = (array_key_exists('filter_type', $field) ? $field['filter_type'] : (array_key_exists('type', $field) ? $field['type'] : false));
						if (($type == 'date' OR $type == 'datetime') AND is_string($value))
							$value = unserialize($value);
						$key = isset($tmpTab[1]) ? $tmpTab[0].'.`'.$tmpTab[1].'`' : '`'.$tmpTab[0].'`';
						if (array_key_exists('tmpTableFilter', $field))
							$sqlFilter = & $this->_tmpTableFilter;
						elseif (array_key_exists('havingFilter', $field))
							$sqlFilter = & $this->_filterHaving;
						else
							$sqlFilter = & $this->_filter;

						/* Only for date filtering (from, to) */
						if (is_array($value))
						{
							if (isset($value[0]) AND !empty($value[0]))
							{
								if (!Validate::isDate($value[0]))
									$this->_errors[] = Tools::displayError('\'from:\' date format is invalid (YYYY-MM-DD)');
								else
									$sqlFilter .= ' AND '.pSQL($key).' >= \''.pSQL(Tools::dateFrom($value[0])).'\'';
							}

							if (isset($value[1]) AND !empty($value[1]))
							{
								if (!Validate::isDate($value[1]))
									$this->_errors[] = Tools::displayError('\'to:\' date format is invalid (YYYY-MM-DD)');
								else
									$sqlFilter .= ' AND '.pSQL($key).' <= \''.pSQL(Tools::dateTo($value[1])).'\'';
							}
						}
						else
						{
							$sqlFilter .= ' AND ';
							if ($type == 'int' OR $type == 'bool')
								$sqlFilter .= (($key == $this->identifier OR $key == '`'.$this->identifier.'`' OR $key == '`active`') ? 'a.' : '').pSQL($key).' = '.(int)($value).' ';
							elseif ($type == 'decimal')
								$sqlFilter .= (($key == $this->identifier OR $key == '`'.$this->identifier.'`') ? 'a.' : '').pSQL($key).' = '.(float)($value).' ';
							elseif ($type == 'select')
								$sqlFilter .= (($key == $this->identifier OR $key == '`'.$this->identifier.'`') ? 'a.' : '').pSQL($key).' = \''.pSQL($value).'\' ';
							else
								$sqlFilter .= (($key == $this->identifier OR $key == '`'.$this->identifier.'`') ? 'a.' : '').pSQL($key).' LIKE \'%'.pSQL($value).'%\' ';
						}
					}
				}
			}
		}
		elseif(Tools::isSubmit('submitFields') AND $this->requiredDatabase AND $this->tabAccess['add'] === '1' AND $this->tabAccess['delete'] === '1')
		{
			if (!is_array($fields = Tools::getValue('fieldsBox')))
				$fields = array();

			$object = new $this->className();
			if (!$object->addFieldsRequiredDatabase($fields))
				$this->_errors[] = Tools::displayError('Error in updating required fields');
			else
				Tools::redirectAdmin($currentIndex.'&conf=4&token='.$token);
		}
	}

	protected function uploadImage($id, $name, $dir, $ext = false)
	{
		if (isset($_FILES[$name]['tmp_name']) AND !empty($_FILES[$name]['tmp_name']))
		{
			// Delete old image
			$this->deleteImage($id);

			// Check image validity
			if ($error = checkImage($_FILES[$name], $this->maxImageSize))
				$this->_errors[] = $error;
			elseif (!$tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS') OR !move_uploaded_file($_FILES[$name]['tmp_name'], $tmpName))
				return false;
			else
			{
				$_FILES[$name]['tmp_name'] = $tmpName;
				// Copy new image
				if (!imageResize($tmpName, _PS_IMG_DIR_.$dir.$id.'.'.$this->imageType, NULL, NULL, ($ext ? $ext : $this->imageType)))
					$this->_errors[] = Tools::displayError('An error occurred while uploading image.');
				if (sizeof($this->_errors))
					return false;
				if ($this->afterImageUpload())
				{
					unlink($tmpName);
					return true;
				}
				return false;
			}
		}
		return true;
	}



	protected function uploadIco($name, $dest)
	{

		if (isset($_FILES[$name]['tmp_name']) AND !empty($_FILES[$name]['tmp_name']))
		{
			/* Check ico validity */
			if ($error = checkIco($_FILES[$name], $this->maxImageSize))
				$this->_errors[] = $error;

			/* Copy new ico */
			elseif(!copy($_FILES[$name]['tmp_name'], $dest))
				$this->_errors[] = Tools::displayError('an error occurred while uploading favicon: '.$_FILES[$name]['tmp_name'].' to '.$dest);
		}
		return !sizeof($this->_errors) ? true : false;
	}

	/**
	 * Overload this method for custom checking
	 *
	 * @param integer $id Object id used for deleting images
	 * @return boolean
	 */
	protected function postImage($id)
	{
		if (isset($this->fieldImageSettings['name']) AND isset($this->fieldImageSettings['dir']))
			return $this->uploadImage($id, $this->fieldImageSettings['name'], $this->fieldImageSettings['dir'].'/');
		elseif (!empty($this->fieldImageSettings))
			foreach ($this->fieldImageSettings AS $image)
				if (isset($image['name']) AND isset($image['dir']))
					$this->uploadImage($id, $image['name'], $image['dir'].'/');
		return !sizeof($this->_errors) ? true : false;
	}

	/**
	 * Copy datas from $_POST to object
	 *
	 * @param object &$object Object
	 * @param string $table Object table
	 */
	protected function copyFromPost(&$object, $table)
	{
		/* Classical fields */
		foreach ($_POST AS $key => $value)
			if (key_exists($key, $object) AND $key != 'id_'.$table)
			{
				/* Do not take care of password field if empty */
				if ($key == 'passwd' AND Tools::getValue('id_'.$table) AND empty($value))
					continue;
				/* Automatically encrypt password in MD5 */
				if ($key == 'passwd' AND !empty($value))
					$value = Tools::encrypt($value);
				$object->{$key} = $value;
			}

		/* Multilingual fields */
		$rules = call_user_func(array(get_class($object), 'getValidationRules'), get_class($object));
		if (sizeof($rules['validateLang']))
		{
			$languages = Language::getLanguages(false);
			foreach ($languages AS $language)
				foreach ($rules['validateLang'] AS $field => $validation)
					if (isset($_POST[$field.'_'.(int)($language['id_lang'])]))
						$object->{$field}[(int)($language['id_lang'])] = $_POST[$field.'_'.(int)($language['id_lang'])];
		}
	}

	/**
	 * Display errors
	 */
	public function displayErrors()
	{
		if ($nbErrors = count($this->_errors) AND $this->_includeContainer)
		{
			echo '<script type="text/javascript">
				$(document).ready(function() {
					$(\'#hideError\').unbind(\'click\').click(function(){
						$(\'.error\').hide(\'slow\', function (){
							$(\'.error\').remove();
						});
						return false;
					});
				});
			  </script>
			<div class="error"><span style="float:right"><a id="hideError" href=""><img alt="X" src="../img/admin/close.png" /></a></span><img src="../img/admin/error2.png" />';
			if (count($this->_errors) == 1)
				echo $this->_errors[0];
			else
			{
				echo $nbErrors.' '.$this->l('errors').'<br /><ol>';
				foreach ($this->_errors AS $error)
					echo '<li>'.$error.'</li>';
				echo '</ol>';
			}
			echo '</div>';
		}
		$this->includeSubTab('displayErrors');
	}

	/**
	 * Display a warning message
	 *
	 * @param string $warn Warning message to display
	 */
	public function	displayWarning($warn)
	{
		$str_output = '';
		if (!empty($warn))
		{
			$str_output .= '<script type="text/javascript">
					$(document).ready(function() {
						$(\'#linkSeeMore\').unbind(\'click\').click(function(){
							$(\'#seeMore\').show(\'slow\');
							$(this).hide();
							$(\'#linkHide\').show();
							return false;
						});
						$(\'#linkHide\').unbind(\'click\').click(function(){
							$(\'#seeMore\').hide(\'slow\');
							$(this).hide();
							$(\'#linkSeeMore\').show();
							return false;
						});
						$(\'#hideWarn\').unbind(\'click\').click(function(){
							$(\'.warn\').hide(\'slow\', function (){
								$(\'.warn\').remove();
							});
							return false;
						});
					});
				  </script>
			<div class="warn">';
			if (!is_array($warn))
				$str_output .= '<img src="../img/admin/warn2.png" />'.$warn;
			else
			{	$str_output .= '<span style="float:right"><a id="hideWarn" href=""><img alt="X" src="../img/admin/close.png" /></a></span><img src="../img/admin/warn2.png" />'.
				(count($warn) > 1 ? $this->l('There are') : $this->l('There is')).' '.count($warn).' '.(count($warn) > 1 ? $this->l('warnings') : $this->l('warning'))
				.'<span style="margin-left:20px;" id="labelSeeMore">
				<a id="linkSeeMore" href="#" style="text-decoration:underline">'.$this->l('Click here to see more').'</a>
				<a id="linkHide" href="#" style="text-decoration:underline;display:none">'.$this->l('Hide warning').'</a></span><ul style="display:none;" id="seeMore">';
				foreach($warn as $val)
					$str_output .= '<li>'.$val.'</li>';
				$str_output .= '</ul>';
			}
			$str_output .= '</div>';
		}
		echo $str_output;
	}

	/**
	 * Display confirmations
	 */
	public function displayConf()
	{
		if ($conf = Tools::getValue('conf'))
			echo '<div class="conf">
				<img src="../img/admin/ok2.png" />
				'.$this->_conf[(int)($conf)].'
			</div>';
	}

	/**
	 * Get the current objects' list form the database
	 *
	 * @param integer $id_lang Language used for display
	 * @param string $orderBy ORDER BY clause
	 * @param string $_orderWay Order way (ASC, DESC)
	 * @param integer $start Offset in LIMIT clause
	 * @param integer $limit Row count in LIMIT clause
	 */
	public function getList($id_lang, $orderBy = NULL, $orderWay = NULL, $start = 0, $limit = NULL)
	{
		global $cookie;

		/* Manage default params values */
		if (empty($limit))
			$limit = ((!isset($cookie->{$this->table.'_pagination'})) ? $this->_pagination[1] : $limit = $cookie->{$this->table.'_pagination'});

		if (!Validate::isTableOrIdentifier($this->table))
			die (Tools::displayError('Table name is invalid:').' "'.$this->table.'"');

		if (empty($orderBy))
			$orderBy = $cookie->__get($this->table.'Orderby') ? $cookie->__get($this->table.'Orderby') : $this->_defaultOrderBy;
		if (empty($orderWay))
			$orderWay = $cookie->__get($this->table.'Orderway') ? $cookie->__get($this->table.'Orderway') : 'ASC';

		$limit = (int)(Tools::getValue('pagination', $limit));
		$cookie->{$this->table.'_pagination'} = $limit;

		/* Check params validity */
		if (!Validate::isOrderBy($orderBy) OR !Validate::isOrderWay($orderWay)
			OR !is_numeric($start) OR !is_numeric($limit)
			OR !Validate::isUnsignedId($id_lang))
			die(Tools::displayError('get list params is not valid'));

		/* Determine offset from current page */
		if ((isset($_POST['submitFilter'.$this->table]) OR
		isset($_POST['submitFilter'.$this->table.'_x']) OR
		isset($_POST['submitFilter'.$this->table.'_y'])) AND
		!empty($_POST['submitFilter'.$this->table]) AND
		is_numeric($_POST['submitFilter'.$this->table]))
			$start = (int)($_POST['submitFilter'.$this->table] - 1) * $limit;

		/* Cache */
		$this->_lang = (int)($id_lang);
		$this->_orderBy = $orderBy;
		$this->_orderWay = Tools::strtoupper($orderWay);

		/* SQL table : orders, but class name is Order */
		$sqlTable = $this->table == 'order' ? 'orders' : $this->table;

		/* Query in order to get results with all fields */
		$sql = 'SELECT SQL_CALC_FOUND_ROWS
			'.($this->_tmpTableFilter ? ' * FROM (SELECT ' : '').'
			'.($this->lang ? 'b.*, ' : '').'a.*'.(isset($this->_select) ? ', '.$this->_select.' ' : '').'
			FROM `'._DB_PREFIX_.$sqlTable.'` a
			'.($this->lang ? 'LEFT JOIN `'._DB_PREFIX_.$this->table.'_lang` b ON (b.`'.$this->identifier.'` = a.`'.$this->identifier.'` AND b.`id_lang` = '.(int)($id_lang).')' : '').'
			'.(isset($this->_join) ? $this->_join.' ' : '').'
			WHERE 1 '.(isset($this->_where) ? $this->_where.' ' : '').($this->deleted ? 'AND a.`deleted` = 0 ' : '').(isset($this->_filter) ? $this->_filter : '').'
			'.(isset($this->_group) ? $this->_group.' ' : '').'
			'.((isset($this->_filterHaving) || isset($this->_having)) ? 'HAVING ' : '').(isset($this->_filterHaving) ? ltrim($this->_filterHaving, ' AND ') : '').(isset($this->_having) ? $this->_having.' ' : '').'
			ORDER BY '.(($orderBy == $this->identifier) ? 'a.' : '').'`'.pSQL($orderBy).'` '.pSQL($orderWay).
			($this->_tmpTableFilter ? ') tmpTable WHERE 1'.$this->_tmpTableFilter : '').'
			LIMIT '.(int)($start).','.(int)($limit);

		$this->_list = Db::getInstance()->ExecuteS($sql);
		$this->_listTotal = count(Db::getInstance()->ExecuteS('SELECT
			'.($this->_tmpTableFilter ? ' * FROM (SELECT ' : '').'
			'.($this->lang ? 'b.*, ' : '').'a.*'.(isset($this->_select) ? ', '.$this->_select.' ' : '').'
			FROM `'._DB_PREFIX_.$sqlTable.'` a
			'.($this->lang ? 'LEFT JOIN `'._DB_PREFIX_.$this->table.'_lang` b ON (b.`'.$this->identifier.'` = a.`'.$this->identifier.'` AND b.`id_lang` = '.(int)($id_lang).')' : '').'
			'.(isset($this->_join) ? $this->_join.' ' : '').'
			WHERE 1 '.(isset($this->_where) ? $this->_where.' ' : '').($this->deleted ? 'AND a.`deleted` = 0 ' : '').(isset($this->_filter) ? $this->_filter : '').'
			'.(isset($this->_group) ? $this->_group.' ' : '').'
			'.((isset($this->_filterHaving) || isset($this->_having)) ? 'HAVING ' : '').(isset($this->_filterHaving) ? ltrim($this->_filterHaving, ' AND ') : '').(isset($this->_having) ? $this->_having.' ' : '').'
			ORDER BY '.(($orderBy == $this->identifier) ? 'a.' : '').'`'.pSQL($orderBy).'` '.pSQL($orderWay).
			($this->_tmpTableFilter ? ') tmpTable WHERE 1'.$this->_tmpTableFilter : '')));


	}

	/**
	 * Display image aside object form
	 *
	 * @param integer $id Object id
	 * @param string $image Local image filepath
	 * @param integer $size Image width
	 * @param integer $id_image Image id (for products with several images)
	 *
	 * @global string $currentIndex Current URL in order to keep current Tab
	 */
	public function displayImage($id, $image, $size, $id_image = NULL, $token = NULL)
	{
		global $currentIndex;

		if (!isset($token) OR empty($token))
			$token = $this->token;
		if ($id AND file_exists($image))
			echo '
			<div id="image" >
				'.cacheImage($image, $this->table.'_'.(int)($id).'.'.$this->imageType, $size, $this->imageType).'
				<p align="center">'.$this->l('File size').' '.(filesize($image) / 1000).'kb</p>
				<a href="'.$currentIndex.'&'.$this->identifier.'='.(int)($id).'&token='.$token.($id_image ? '&id_image='.(int)($id_image) : '').'&deleteImage=1">
				<img src="../img/admin/delete.gif" alt="'.$this->l('Delete').'" /> '.$this->l('Delete').'</a>
			</div>';
	}

	/**
	 * Display list header (filtering, pagination and column names)
	 *
	 * @global string $currentIndex Current URL in order to keep current Tab
	 */
	public function displayListHeader($token = NULL)
	{
		global $currentIndex, $cookie;
		$isCms = false;
		if (preg_match('/cms/Ui', $this->identifier))
			$isCms = true;
		$id_cat = Tools::getValue('id_'.($isCms ? 'cms_' : '').'category');

		if (!isset($token) OR empty($token))
			$token = $this->token;

		/* Determine total page number */
		$totalPages = ceil($this->_listTotal / Tools::getValue('pagination', (isset($cookie->{$this->table.'_pagination'}) ? $cookie->{$this->table.'_pagination'} : $this->_pagination[0])));
		if (!$totalPages) $totalPages = 1;

		echo '<a name="'.$this->table.'">&nbsp;</a>';
		echo '<form method="post" action="'.$currentIndex;
		if(Tools::getIsset($this->identifier))
			echo '&'.$this->identifier.'='.(int)(Tools::getValue($this->identifier));
		if (Tools::getIsset($this->table.'Orderby'))
			echo '&'.$this->table.'Orderby='.urlencode($this->_orderBy).'&'.$this->table.'Orderway='.urlencode(strtolower($this->_orderWay));
		echo '#'.$this->table.'" class="form">
		<input type="hidden" id="submitFilter'.$this->table.'" name="submitFilter'.$this->table.'" value="0">
		<table>
			<tr>
				<td style="vertical-align: bottom;">
					<span style="float: left;">';

		/* Determine current page number */
		$page = (int)(Tools::getValue('submitFilter'.$this->table));
		if (!$page) $page = 1;
		if ($page > 1)
			echo '
						<input type="image" src="../img/admin/list-prev2.gif" onclick="getE(\'submitFilter'.$this->table.'\').value=1"/>
						&nbsp; <input type="image" src="../img/admin/list-prev.gif" onclick="getE(\'submitFilter'.$this->table.'\').value='.($page - 1).'"/> ';
		echo $this->l('Page').' <b>'.$page.'</b> / '.$totalPages;
		if ($page < $totalPages)
			echo '
						<input type="image" src="../img/admin/list-next.gif" onclick="getE(\'submitFilter'.$this->table.'\').value='.($page + 1).'"/>
						 &nbsp;<input type="image" src="../img/admin/list-next2.gif" onclick="getE(\'submitFilter'.$this->table.'\').value='.$totalPages.'"/>';
		echo '			| '.$this->l('Display').'
						<select name="pagination">';
		/* Choose number of results per page */
		$selectedPagination = Tools::getValue('pagination', (isset($cookie->{$this->table.'_pagination'}) ? $cookie->{$this->table.'_pagination'} : NULL));
		foreach ($this->_pagination AS $value)
			echo '<option value="'.(int)($value).'"'.($selectedPagination == $value ? ' selected="selected"' : (($selectedPagination == NULL && $value == $this->_pagination[1]) ? ' selected="selected2"' : '')).'>'.(int)($value).'</option>';
		echo '
						</select>
						/ '.(int)($this->_listTotal).' '.$this->l('result(s)').'
					</span>
					<span style="float: right;">
						<input type="submit" name="submitReset'.$this->table.'" value="'.$this->l('Reset').'" class="button" />
						<input type="submit" id="submitFilterButton_'.$this->table.'" name="submitFilter" value="'.$this->l('Filter').'" class="button" />
					</span>
					<span class="clear"></span>
				</td>
			</tr>
			<tr>
				<td>';

		/* Display column names and arrows for ordering (ASC, DESC) */
		if (array_key_exists($this->identifier,$this->identifiersDnd) AND $this->_orderBy == 'position')
		{
			echo '
			<script type="text/javascript" src="../js/jquery/jquery.tablednd_0_5.js"></script>
			<script type="text/javascript">
				var token = \''.($token!=NULL ? $token : $this->token).'\';
				var come_from = \''.$this->table.'\';
				var alternate = \''.($this->_orderWay == 'DESC' ? '1' : '0' ).'\';
			</script>
			<script type="text/javascript" src="../js/admin-dnd.js"></script>
			';
		}
		echo '<table'.(array_key_exists($this->identifier,$this->identifiersDnd) ? ' id="'.(($id_category = (int)(Tools::getValue($this->identifiersDnd[$this->identifier], 1))) ? substr($this->identifier,3,strlen($this->identifier)) : '').'"' : '' ).' class="table'.((array_key_exists($this->identifier,$this->identifiersDnd) AND ($this->_orderBy != 'position 'AND $this->_orderWay != 'DESC')) ? ' tableDnD'  : '' ).'" cellpadding="0" cellspacing="0">
			<thead>
				<tr class="nodrag nodrop">
					<th>';
		if ($this->delete)
			echo '		<input type="checkbox" name="checkme" class="noborder" onclick="checkDelBoxes(this.form, \''.$this->table.'Box[]\', this.checked)" />';
		echo '		</th>';
		foreach ($this->fieldsDisplay AS $key => $params)
		{
			echo '	<th '.(isset($params['widthColumn']) ? 'style="width: '.$params['widthColumn'].'px"' : '').'>'.$params['title'];
			if (!isset($params['orderby']) OR $params['orderby'])
			{
				// Cleaning links
				if (Tools::getValue($this->table.'Orderby') && Tools::getValue($this->table.'Orderway'))
					$currentIndex = preg_replace('/&'.$this->table.'Orderby=([a-z _]*)&'.$this->table.'Orderway=([a-z]*)/i', '', $currentIndex);
				echo '	<br />
						<a href="'.$currentIndex.'&'.$this->identifier.'='.$id_cat.'&'.$this->table.'Orderby='.urlencode($key).'&'.$this->table.'Orderway=desc&token='.$token.'"><img border="0" src="../img/admin/down'.((isset($this->_orderBy) AND ($key == $this->_orderBy) AND ($this->_orderWay == 'DESC')) ? '_d' : '').'.gif" /></a>
						<a href="'.$currentIndex.'&'.$this->identifier.'='.$id_cat.'&'.$this->table.'Orderby='.urlencode($key).'&'.$this->table.'Orderway=asc&token='.$token.'"><img border="0" src="../img/admin/up'.((isset($this->_orderBy) AND ($key == $this->_orderBy) AND ($this->_orderWay == 'ASC')) ? '_d' : '').'.gif" /></a>';
			}
			echo '	</th>';
		}

		/* Check if object can be modified, deleted or detailed */
		if ($this->edit OR $this->delete OR ($this->view AND $this->view !== 'noActionColumn'))
			echo '	<th style="width: 52px">'.$this->l('Actions').'</th>';
		echo '	</tr>
				<tr class="nodrag nodrop" style="height: 35px;">
					<td class="center">';
		if ($this->delete)
			echo '		--';
		echo '		</td>';

		/* Javascript hack in order to catch ENTER keypress event */
		$keyPress = 'onkeypress="formSubmit(event, \'submitFilterButton_'.$this->table.'\');"';

		/* Filters (input, select, date or bool) */
		foreach ($this->fieldsDisplay AS $key => $params)
		{
			$width = (isset($params['width']) ? ' style="width: '.(int)($params['width']).'px;"' : '');
			echo '<td'.(isset($params['align']) ? ' class="'.$params['align'].'"' : '').'>';
			if (!isset($params['type']))
				$params['type'] = 'text';

			$value = Tools::getValue($this->table.'Filter_'.(array_key_exists('filter_key', $params) ? $params['filter_key'] : $key));
			if (isset($params['search']) AND !$params['search'])
			{
				echo '--</td>';
				continue;
			}
			switch ($params['type'])
			{
				case 'bool':
					echo '
					<select name="'.$this->table.'Filter_'.$key.'">
						<option value="">--</option>
						<option value="1"'.($value == 1 ? ' selected="selected"' : '').'>'.$this->l('Yes').'</option>
						<option value="0"'.(($value == 0 AND $value != '') ? ' selected="selected"' : '').'>'.$this->l('No').'</option>
					</select>';
					break;

				case 'date':
				case 'datetime':
					if (is_string($value))
						$value = unserialize($value);
					$name = $this->table.'Filter_'.(isset($params['filter_key']) ? $params['filter_key'] : $key);
					$nameId = str_replace('!', '__', $name);
					includeDatepicker(array($nameId.'_0', $nameId.'_1'));
					echo $this->l('From').' <input type="text" id="'.$nameId.'_0" name="'.$name.'[0]" value="'.(isset($value[0]) ? $value[0] : '').'"'.$width.' '.$keyPress.' /><br />
					'.$this->l('To').' <input type="text" id="'.$nameId.'_1" name="'.$name.'[1]" value="'.(isset($value[1]) ? $value[1] : '').'"'.$width.' '.$keyPress.' />';
					break;

				case 'select':

					if (isset($params['filter_key']))
					{
						echo '<select onchange="$(\'#submitFilter'.$this->table.'\').focus();$(\'#submitFilter'.$this->table.'\').click();" name="'.$this->table.'Filter_'.$params['filter_key'].'" '.(isset($params['width']) ? 'style="width: '.$params['width'].'px"' : '').'>
								<option value=""'.(($value == 0 AND $value != '') ? ' selected="selected"' : '').'>--</option>';
						if (isset($params['select']) AND is_array($params['select']))
							foreach ($params['select'] AS $optionValue => $optionDisplay)
							{
								echo '<option value="'.$optionValue.'"'.((isset($_POST[$this->table.'Filter_'.$params['filter_key']]) AND Tools::getValue($this->table.'Filter_'.$params['filter_key']) == $optionValue AND Tools::getValue($this->table.'Filter_'.$params['filter_key']) != '') ? ' selected="selected"' : '').'>'.$optionDisplay.'</option>';
								}
						echo '</select>';
						break;
					}

				case 'text':
				default:
					echo '<input type="text" name="'.$this->table.'Filter_'.(isset($params['filter_key']) ? $params['filter_key'] : $key).'" value="'.htmlentities($value, ENT_COMPAT, 'UTF-8').'"'.$width.' '.$keyPress.' />';
			}
			echo '</td>';
		}

		if ($this->edit OR $this->delete OR ($this->view AND $this->view !== 'noActionColumn'))
			echo '<td class="center">--</td>';

		echo '</tr>
			</thead>';
	}

	public function displayTop()
	{
	}

	/**
	 * Display list
	 *
	 * @global string $currentIndex Current URL in order to keep current Tab
	 */
	public function displayList()
	{
		global $currentIndex;

		$this->displayTop();

		if ($this->edit AND (!isset($this->noAdd) OR !$this->noAdd))
			echo '<br /><a href="'.$currentIndex.'&add'.$this->table.'&token='.$this->token.'"><img src="../img/admin/add.gif" border="0" /> '.$this->l('Add new').'</a><br /><br />';
		/* Append when we get a syntax error in SQL query */
		if ($this->_list === false)
		{
			$this->displayWarning($this->l('Bad SQL query'));
			return false;
		}

		/* Display list header (filtering, pagination and column names) */
		$this->displayListHeader();
		if (!sizeof($this->_list))
			echo '<tr><td class="center" colspan="'.(sizeof($this->fieldsDisplay) + 2).'">'.$this->l('No items found').'</td></tr>';

		/* Show the content of the table */
		$this->displayListContent();

		/* Close list table and submit button */
		$this->displayListFooter();
	}

	public function displayListContent($token = NULL)
	{
		/* Display results in a table
		 *
		 * align  : determine value alignment
		 * prefix : displayed before value
		 * suffix : displayed after value
		 * image  : object image
		 * icon   : icon determined by values
		 * active : allow to toggle status
		 */

		global $currentIndex, $cookie;
		$currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));

		$id_category = 1; // default categ

		$irow = 0;
		if ($this->_list AND isset($this->fieldsDisplay['position']))
		{
			$positions = array_map(create_function('$elem', 'return (int)($elem[\'position\']);'), $this->_list);
			sort($positions);
		}
		if ($this->_list)
		{
			$isCms = false;
			if (preg_match('/cms/Ui', $this->identifier))
				$isCms = true;
			$keyToGet = 'id_'.($isCms ? 'cms_' : '').'category'.(in_array($this->identifier, array('id_category', 'id_cms_category')) ? '_parent' : '');
			foreach ($this->_list AS $i => $tr)
			{
				$id = $tr[$this->identifier];
				echo '<tr'.(array_key_exists($this->identifier,$this->identifiersDnd) ? ' id="tr_'.(($id_category = (int)(Tools::getValue('id_'.($isCms ? 'cms_' : '').'category', '1'))) ? $id_category : '').'_'.$id.'_'.$tr['position'].'"' : '').($irow++ % 2 ? ' class="alt_row"' : '').' '.((isset($tr['color']) AND $this->colorOnBackground) ? 'style="background-color: '.$tr['color'].'"' : '').'>
							<td class="center">';
				if ($this->delete AND (!isset($this->_listSkipDelete) OR !in_array($id, $this->_listSkipDelete)))
					echo '<input type="checkbox" name="'.$this->table.'Box[]" value="'.$id.'" class="noborder" />';
				echo '</td>';
				foreach ($this->fieldsDisplay AS $key => $params)
				{
					$tmp = explode('!', $key);
					$key = isset($tmp[1]) ? $tmp[1] : $tmp[0];
					echo '
					<td '.(isset($params['position']) ? ' id="td_'.(isset($id_category) AND $id_category ? $id_category : 0).'_'.$id.'"' : '').' class="'.((!isset($this->noLink) OR !$this->noLink) ? 'pointer' : '').((isset($params['position']) AND $this->_orderBy == 'position')? ' dragHandle' : ''). (isset($params['align']) ? ' '.$params['align'] : '').'" ';
					if (!isset($params['position']) AND (!isset($this->noLink) OR !$this->noLink))
						echo ' onclick="document.location = \''.$currentIndex.'&'.$this->identifier.'='.$id.($this->view? '&view' : '&update').$this->table.'&token='.($token!=NULL ? $token : $this->token).'\'">'.(isset($params['prefix']) ? $params['prefix'] : '');
					else
						echo '>';
					if (isset($params['active']) AND isset($tr[$key]))
					    $this->_displayEnableLink($token, $id, $tr[$key], $params['active'], Tools::getValue('id_category'), Tools::getValue('id_product'));
					elseif (isset($params['activeVisu']) AND isset($tr[$key]))
						echo '<img src="../img/admin/'.($tr[$key] ? 'enabled.gif' : 'disabled.gif').'"
						alt="'.($tr[$key] ? $this->l('Enabled') : $this->l('Disabled')).'" title="'.($tr[$key] ? $this->l('Enabled') : $this->l('Disabled')).'" />';
					elseif (isset($params['position']))
					{
						if ($this->_orderBy == 'position' AND $this->_orderWay != 'DESC')
						{
							echo '<a'.(!($tr[$key] != $positions[sizeof($positions) - 1]) ? ' style="display: none;"' : '').' href="'.$currentIndex.
									'&'.$keyToGet.'='.(int)($id_category).'&'.$this->identifiersDnd[$this->identifier].'='.$id.'
									&way=1&position='.(int)($tr['position'] + 1).'&token='.($token!=NULL ? $token : $this->token).'">
									<img src="../img/admin/'.($this->_orderWay == 'ASC' ? 'down' : 'up').'.gif"
									alt="'.$this->l('Down').'" title="'.$this->l('Down').'" /></a>';

							echo '<a'.(!($tr[$key] != $positions[0]) ? ' style="display: none;"' : '').' href="'.$currentIndex.
									'&'.$keyToGet.'='.(int)($id_category).'&'.$this->identifiersDnd[$this->identifier].'='.$id.'
									&way=0&position='.(int)($tr['position'] - 1).'&token='.($token!=NULL ? $token : $this->token).'">
									<img src="../img/admin/'.($this->_orderWay == 'ASC' ? 'up' : 'down').'.gif"
									alt="'.$this->l('Up').'" title="'.$this->l('Up').'" /></a>';						}
						else
							echo (int)($tr[$key] + 1);
					}
					elseif (isset($params['image']))
					{
						$image_id = isset($params['image_id']) ? $tr[$params['image_id']] : $id;
						echo cacheImage(_PS_IMG_DIR_.$params['image'].'/'.$image_id.(isset($tr['id_image']) ? '-'.(int)($tr['id_image']) : '').'.'.$this->imageType, $this->table.'_mini_'.$image_id.'.'.$this->imageType, 45, $this->imageType);
					}
					elseif (isset($params['icon']) AND (isset($params['icon'][$tr[$key]]) OR isset($params['icon']['default'])))
						echo '<img src="../img/admin/'.(isset($params['icon'][$tr[$key]]) ? $params['icon'][$tr[$key]] : $params['icon']['default'].'" alt="'.$tr[$key]).'" title="'.$tr[$key].'" />';
                    elseif (isset($params['price']))
						echo Tools::displayPrice($tr[$key], (isset($params['currency']) ? Currency::getCurrencyInstance((int)($tr['id_currency'])) : $currency), false, false);
					elseif (isset($params['float']))
						echo rtrim(rtrim($tr[$key], '0'), '.');
					elseif (isset($params['type']) AND $params['type'] == 'date')
						echo Tools::displayDate($tr[$key], $cookie->id_lang);
					elseif (isset($params['type']) AND $params['type'] == 'datetime')
						echo Tools::displayDate($tr[$key], $cookie->id_lang, true);
					elseif (isset($tr[$key]))
					{
						$echo = ($key == 'price' ? round($tr[$key], 2) : isset($params['maxlength']) ? Tools::substr($tr[$key], 0, $params['maxlength']).'...' : $tr[$key]);
						echo isset($params['callback']) ? call_user_func_array(array($this->className, $params['callback']), array($echo, $tr)) : $echo;
					}
					else
						echo '--';

					echo (isset($params['suffix']) ? $params['suffix'] : '').
					'</td>';
				}

				if ($this->edit OR $this->delete OR ($this->view AND $this->view !== 'noActionColumn'))
				{
					echo '<td class="center" style="white-space: nowrap;">';
					if ($this->view)
                        $this->_displayViewLink($token, $id);
					if ($this->edit)
					    $this->_displayEditLink($token, $id);
					if ($this->delete AND (!isset($this->_listSkipDelete) OR !in_array($id, $this->_listSkipDelete)))
					    $this->_displayDeleteLink($token, $id);
					if ($this->duplicate)
                        $this->_displayDuplicate($token, $id);
					echo '</td>';
				}
				echo '</tr>';
			}
		}
	}


	protected function _displayEnableLink($token, $id, $value, $active,  $id_category = NULL, $id_product = NULL)
	{
	    global $currentIndex;

	    echo '<a href="'.$currentIndex.'&'.$this->identifier.'='.$id.'&'.$active.$this->table.
	        ((int)$id_category AND (int)$id_product ? '&id_category='.$id_category : '').'&token='.($token!=NULL ? $token : $this->token).'">
	        <img src="../img/admin/'.($value ? 'enabled.gif' : 'disabled.gif').'"
	        alt="'.($value ? $this->l('Enabled') : $this->l('Disabled')).'" title="'.($value ? $this->l('Enabled') : $this->l('Disabled')).'" /></a>';
	}

    protected function 	_displayDuplicate($token = NULL, $id)
    {
	    global $currentIndex;

        $_cacheLang['Duplicate'] = $this->l('Duplicate');
		$_cacheLang['Copy images too?'] = $this->l('Copy images too?', __CLASS__, TRUE, FALSE);

    	$duplicate = $currentIndex.'&'.$this->identifier.'='.$id.'&duplicate'.$this->table;

		echo '
			<a class="pointer" onclick="if (confirm(\''.$_cacheLang['Copy images too?'].'\')) document.location = \''.$duplicate.'&token='.($token!=NULL ? $token : $this->token).'\'; else document.location = \''.$duplicate.'&noimage=1&token='.($token ? $token : $this->token).'\';">
    		<img src="../img/admin/duplicate.png" alt="'.$_cacheLang['Duplicate'].'" title="'.$_cacheLang['Duplicate'].'" /></a>';
    }

	protected function _displayViewLink($token = NULL, $id)
	{
	    global $currentIndex;

		$_cacheLang['View'] = $this->l('View');

    	echo '
			<a href="'.$currentIndex.'&'.$this->identifier.'='.$id.'&view'.$this->table.'&token='.($token!=NULL ? $token : $this->token).'">
			<img src="../img/admin/details.gif" alt="'.$_cacheLang['View'].'" title="'.$_cacheLang['View'].'" /></a>';
	}

	protected function _displayEditLink($token = NULL, $id)
	{
	    global $currentIndex;

		$_cacheLang['Edit'] = $this->l('Edit');

		echo '
    		<a href="'.$currentIndex.'&'.$this->identifier.'='.$id.'&update'.$this->table.'&token='.($token!=NULL ? $token : $this->token).'">
    		<img src="../img/admin/edit.gif" alt="" title="'.$_cacheLang['Edit'].'" /></a>';
	}

	protected function _displayDeleteLink($token = NULL, $id)
	{
	    global $currentIndex;

		$_cacheLang['Delete'] = $this->l('Delete');
		$_cacheLang['DeleteItem'] = $this->l('Delete item #', __CLASS__, TRUE, FALSE);

		echo '
			<a href="'.$currentIndex.'&'.$this->identifier.'='.$id.'&delete'.$this->table.'&token='.($token!=NULL ? $token : $this->token).'" onclick="return confirm(\''.$_cacheLang['DeleteItem'].$id.' ?'.
    				(!is_null($this->specificConfirmDelete) ? '\r'.$this->specificConfirmDelete : '').'\');">
			<img src="../img/admin/delete.gif" alt="'.$_cacheLang['Delete'].'" title="'.$_cacheLang['Delete'].'" /></a>';
	}

	/**
	 * Close list table and submit button
	 */
	public function displayListFooter($token = NULL)
	{
		echo '</table>';
		if ($this->delete)
			echo '<p><input type="submit" class="button" name="submitDel'.$this->table.'" value="'.$this->l('Delete selection').'" onclick="return confirm(\''.$this->l('Delete selected items?', __CLASS__, TRUE, FALSE).'\');" /></p>';
		echo '
				</td>
			</tr>
		</table>
		<input type="hidden" name="token" value="'.($token ? $token : $this->token).'" />
		</form>';
		if (isset($this->_includeTab) AND sizeof($this->_includeTab))
			echo '<br /><br />';
	}

	/**
	 * Options lists
	 */
	public function displayOptionsList()
	{
		global $currentIndex, $cookie, $tab;

		if (!isset($this->_fieldsOptions) OR !sizeof($this->_fieldsOptions))
			return ;

		$defaultLanguage = (int)Configuration::get('PS_LANG_DEFAULT');
		$this->_languages = Language::getLanguages(false);
		$tab = Tab::getTab((int)$cookie->id_lang, Tab::getIdFromClassName($tab));
		echo '<br /><br />';
		echo (isset($this->optionTitle) ? '<h2>'.$this->optionTitle.'</h2>' : '');
		echo '
		<script type="text/javascript">
			id_language = Number('.$defaultLanguage.');
		</script>
		<form action="'.$currentIndex.'" id="'.$tab['name'].'" name="'.$tab['name'].'" method="post">
			<fieldset>';
				echo (isset($this->optionTitle) ? '<legend>
					<img src="'.(!empty($tab['module']) && file_exists($_SERVER['DOCUMENT_ROOT']._MODULE_DIR_.$tab['module'].'/'.$tab['class_name'].'.gif') ? _MODULE_DIR_.$tab['module'].'/' : '../img/t/').$tab['class_name'].'.gif" />'
					.$this->optionTitle.'</legend>' : '');
		foreach ($this->_fieldsOptions AS $key => $field)
		{
			$val = Tools::getValue($key, Configuration::get($key));
			echo '<label>'.$field['title'].' </label>
			<div class="margin-form">';
			switch ($field['type'])
			{
				case 'select':
					echo '<select name="'.$key.'">';
					foreach ($field['list'] AS $value)
						echo '<option
							value="'.(isset($field['cast']) ? $field['cast']($value[$field['identifier']]) : $value[$field['identifier']]).'"'.($val == $value[$field['identifier']] ? ' selected="selected"' : '').'>'.$value['name'].'</option>';
					echo '</select>';
					break;
				case 'bool':
					echo '<label class="t" for="'.$key.'_on"><img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'" /></label>
					<input type="radio" name="'.$key.'" id="'.$key.'_on" value="1"'.($val ? ' checked="checked"' : '').' />
					<label class="t" for="'.$key.'_on"> '.$this->l('Yes').'</label>
					<label class="t" for="'.$key.'_off"><img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'" style="margin-left: 10px;" /></label>
					<input type="radio" name="'.$key.'" id="'.$key.'_off" value="0" '.(!$val ? 'checked="checked"' : '').'/>
					<label class="t" for="'.$key.'_off"> '.$this->l('No').'</label>';
					break;
				case 'textLang':
					foreach ($this->_languages as $language)
					{
						$val = Tools::getValue($key.'_'.$language['id_lang'], Configuration::get($key, $language['id_lang']));
						echo '
						<div id="'.$key.'_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">
							<input size="'.$field['size'].'" type="text" name="'.$key.'_'.$language['id_lang'].'" value="'.$val.'" />
						</div>';
					}
					$this->displayFlags($this->_languages, $defaultLanguage, $key, $key);
					echo '<br style="clear:both">';
					break;
				case 'textareaLang':
					foreach ($this->_languages as $language)
					{
						$val = Configuration::get($key, $language['id_lang']);
						echo '
						<div id="'.$key.'_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">
							<textarea rows="'.(int)($field['rows']).'" cols="'.(int)($field['cols']).'"  name="'.$key.'_'.$language['id_lang'].'">'.str_replace('\r\n', "\n", $val).'</textarea>
						</div>';
					}
					$this->displayFlags($this->_languages, $defaultLanguage, $key, $key);
					echo '<br style="clear:both">';
					break;
				case 'text':
				default:
					echo '<input type="text" name="'.$key.'" value="'.$val.'" size="'.$field['size'].'" />'.(isset($field['suffix']) ? $field['suffix'] : '');
			}

			if (isset($field['required']) AND $field['required'])
				echo ' <sup>*</sup>';

			echo (isset($field['desc']) ? '<p>'.$field['desc'].'</p>' : '');
			echo '</div>';
		}
			echo '<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitOptions'.$this->table.'" class="button" />
				</div>
			</fieldset>
			<input type="hidden" name="token" value="'.$this->token.'" />
		</form>';
	}

	/**
	 * Load class object using identifier in $_GET (if possible)
	 * otherwise return an empty object, or die
	 *
	 * @param boolean $opt Return an empty object if load fail
	 * @return object
	 */
	protected function loadObject($opt = false)
	{
		if ($id = (int)(Tools::getValue($this->identifier)) AND Validate::isUnsignedId($id))
		{
			if (!$this->_object)
				$this->_object = new $this->className($id);
			if (Validate::isLoadedObject($this->_object))
				return $this->_object;
			$this->_errors[] = Tools::displayError('Object cannot be loaded (not found)');
		}
		elseif ($opt)
		{
			$this->_object = new $this->className();
			return $this->_object;
		}
		else
			$this->_errors[] = Tools::displayError('Object cannot be loaded (identifier missing or invalid)');

		$this->displayErrors();
	}

	/**
	 * Return field value if possible (both classical and multilingual fields)
	 *
	 * Case 1 : Return value if present in $_POST / $_GET
	 * Case 2 : Return object value
	 *
	 * @param object $obj Object
	 * @param string $key Field name
	 * @param integer $id_lang Language id (optional)
	 * @return string
	 */
	protected function getFieldValue($obj, $key, $id_lang = NULL)
	{
		if ($id_lang)
			$defaultValue = ($obj->id AND isset($obj->{$key}[$id_lang])) ? $obj->{$key}[$id_lang] : '';
		else
			$defaultValue = isset($obj->{$key}) ? $obj->{$key} : '';

		return Tools::getValue($key.($id_lang ? '_'.$id_lang : ''), $defaultValue);
	}

	/**
	 * Display form
	 *
	 * @global string $currentIndex Current URL in order to keep current Tab
	 */
	public function displayForm($firstCall = true)
	{
		global $cookie;

		$allowEmployeeFormLang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		if ($allowEmployeeFormLang && !$cookie->employee_form_lang)
			$cookie->employee_form_lang = (int)(Configuration::get('PS_LANG_DEFAULT'));
		$useLangFromCookie = false;
		$this->_languages = Language::getLanguages(false);
		if ($allowEmployeeFormLang)
			foreach ($this->_languages AS $lang)
				if ($cookie->employee_form_lang == $lang['id_lang'])
					$useLangFromCookie = true;
		if (!$useLangFromCookie)
			$this->_defaultFormLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
		else
			$this->_defaultFormLanguage = (int)($cookie->employee_form_lang);

		// Only if it is the first call to displayForm, otherwise it has already been defined
		if ($firstCall)
		{
			echo '
			<script type="text/javascript">
				$(document).ready(function() {
					id_language = '.$this->_defaultFormLanguage.';
					languages = new Array();';
			foreach ($this->_languages AS $k => $language)
				echo '
					languages['.$k.'] = {
						id_lang: '.(int)$language['id_lang'].',
						iso_code: \''.$language['iso_code'].'\',
						name: \''.htmlentities($language['name'], ENT_COMPAT, 'UTF-8').'\'
					};';
			echo '
					displayFlags(languages, id_language, '.$allowEmployeeFormLang.');
				});
			</script>';
		}
	}

	/**
	 * Display object details
	 *
	 * @global string $currentIndex Current URL in order to keep current Tab
	 */
	public function viewDetails() {	global $currentIndex; }

	/**
	 * Called before deletion
	 *
	 * @param object $object Object
	 * @return boolean
	 */
	protected function beforeDelete($object) { return true; }

	/**
	 * Called before deletion
	 *
	 * @param object $object Object
	 * @return boolean
	 */
	protected function afterDelete($object, $oldId) { return true; }

	protected function afterAdd($object) { return true; }

	protected function afterUpdate($object) { return true; }

	/**
	 * Check rights to view the current tab
	 *
	 * @return boolean
	 */

	protected function afterImageUpload() {
		return true;
	}

	/**
	 * Check rights to view the current tab
	 *
	 * @return boolean
	 */

	public function viewAccess($disable = false)
	{
		global $cookie;

		if ($disable)
			return true;

		$this->tabAccess = Profile::getProfileAccess($cookie->profile, $this->id);

		if ($this->tabAccess['view'] === '1')
			return true;
		return false;
	}

	/**
	 * Check for security token
	 */
	public function checkToken()
	{
		$token = Tools::getValue('token');
		return (!empty($token) AND $token === $this->token);
	}

	/**
	  * Display flags in forms for translations
	  *
	  * @param array $languages All languages available
	  * @param integer $defaultLanguage Default language id
	  * @param string $ids Multilingual div ids in form
	  * @param string $id Current div id]
	  * #param boolean $return define the return way : false for a display, true for a return
	  */
	public function displayFlags($languages, $defaultLanguage, $ids, $id, $return = false)
	{
		if (sizeof($languages) == 1)
			return false;
		$defaultIso = Language::getIsoById($defaultLanguage);
		$output = '
		<div class="displayed_flag">
			<img src="../img/l/'.$defaultLanguage.'.jpg" class="pointer" id="language_current_'.$id.'" onclick="toggleLanguageFlags(this);" alt="" />
		</div>
		<div id="languages_'.$id.'" class="language_flags">
			'.$this->l('Choose language:').'<br /><br />';
		foreach ($languages as $language)
			$output .= '<img src="../img/l/'.(int)($language['id_lang']).'.jpg" class="pointer" alt="'.$language['name'].'" title="'.$language['name'].'" onclick="changeLanguage(\''.$id.'\', \''.$ids.'\', '.$language['id_lang'].', \''.$language['iso_code'].'\');" /> ';
		$output .= '</div>';

		if ($return)
			return $output;
		echo $output;
	}

	protected function filterToField($key, $filter)
	{
		foreach ($this->fieldsDisplay AS $field)
			if (array_key_exists('filter_key', $field) AND $field['filter_key'] == $key)
				return $field;
		if (array_key_exists($filter, $this->fieldsDisplay))
			return $this->fieldsDisplay[$filter];
		return false;
	}
}

