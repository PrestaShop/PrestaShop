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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminControllerCore extends Controller
{
	public $path;
	public static $currentIndex;
	public $content;
	public $warnings = array();
	public $informations = array();
	public $confirmations = array();
	public $shopShareDatas = false;

	public $_languages = array();
	public $default_form_language;
	public $allow_employee_form_lang;

	public $layout = 'layout.tpl';

	public $meta_title = 'Administration panel';

	public $template = 'content.tpl';

	/** @var string Associated table name */
	public $table;

	/** @var string Object identifier inside the associated table */
	protected $identifier = false;

	/** @var string Tab name */
	public $className;

	/** @var array tabAccess */
	public $tabAccess;

	/** @var integer Tab id */
	public $id = -1;

	public $required_database = false;

	/** @var string Security token */
	public $token;

	/** @var string shop | group_shop */
	public $shopLinkType;

	/** @var string Default ORDER BY clause when $_orderBy is not defined */
	protected $_defaultOrderBy = false;
	protected $_defaultOrderWay = 'ASC';

	public $tpl_form_vars = array();
	public $tpl_list_vars = array();
	public $tpl_delete_link_vars = array();
	public $tpl_option_vars = array();
	public $tpl_view_vars = array();
	public $tpl_required_fields_vars = array();

	public $base_tpl_view = null;
	public $base_tpl_form = null;

	/** @var bool if you want more fieldsets in the form */
	public $multiple_fieldsets = false;

	public $fields_value = false;

	/** @var array Errors displayed after post processing */
	public $errors = array();

	/** @var define if the header of the list contains filter and sorting links or not */
	protected $list_simple_header;

	/** @var array list to be generated */
	protected $fields_list;

	/** @var array edit form to be generated */
	protected $fields_form;

	/** @var override of $fields_form */
	protected $fields_form_override;

	/** @var array list of option forms to be generated */
	protected $fields_options;

	protected $shopLink;

	/** @var array Cache for query results */
	protected $_list = array();

	/** @var define if the header of the list contains filter and sorting links or not */
	protected $toolbar_title;

	/** @var array list of toolbar buttons */
	protected $toolbar_btn = null;

	/** @var boolean scrolling toolbar */
	protected $toolbar_scroll = true;

	/** @var boolean set to false to hide toolbar and page title */
	protected $show_toolbar = true;

	/** @var boolean set to true to show toolbar and page title for options */
	protected $show_toolbar_options = false;

	/** @var integer Number of results in list */
	protected $_listTotal = 0;

	/** @var boolean Automatically join language table if true */
	public $lang = false;

	/** @var array WHERE clause determined by filter fields */
	protected $_filter;

	/** @var array Temporary SQL table WHERE clause determinated by filter fields */
	protected $_tmpTableFilter = '';

	/** @var array Number of results in list per page (used in select field) */
	protected $_pagination = array(20, 50, 100, 300);

	/** @var string ORDER BY clause determined by field/arrows in list header */
	protected $_orderBy;

	/** @var string Order way (ASC, DESC) determined by arrows in list header */
	protected $_orderWay;

	/** @var array list of available actions for each list row - default actions are view, edit, delete, duplicate */
	protected $actions_available = array('view', 'edit', 'delete', 'duplicate');

	/** @var array list of required actions for each list row */
	protected $actions = array();

	/** @var array list of row ids associated with a given action for witch this action have to not be available */
	protected $list_skip_actions = array();

	/* @var boolean don't show header & footer */
	protected $lite_display = false;
	/** @var bool boolean List content lines are clickable if true */
	protected $list_no_link = false;

	/** @var array $cache_lang cache for traduction */
	public static $cache_lang = array();

	/** @var array required_fields to display in the Required Fields form */
	public $required_fields = array();

	/**
	 * @var array actions to execute on multiple selections
	 * Usage:
	 * array(
	 * 		'actionName' => array(
	 * 			'text' => $this->l('Message displayed on the submit button (mandatory)'),
	 * 			'confirm' => $this->l('If set, this confirmation message will pop-up (optional)')),
	 * 		'anotherAction' => array(...)
	 * );
	 *
	 * If your action is named 'actionName', you need to have a method named bulkactionName() that will be executed when the button is clicked.
	 */
	protected $bulk_actions;

	/**
	 * @var array ids of the rows selected
	 */
	protected $boxes;

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

	protected $is_cms = false;

	/** @var string	identifier to use for changing positions in lists (can be omitted if positions cannot be changed) */
	protected $position_identifier;

	/** @var boolean Table records are not deleted but marked as deleted if set to true */
	protected $deleted = false;
	/**
	 * @var bool is a list filter set
	 */
	protected $filter;
	protected $noLink;
	protected $specificConfirmDelete = null;
	protected $colorOnBackground;
	/** @var bool If true, activates color on hover */
	protected $row_hover = true;
	/** @string Action to perform : 'edit', 'view', 'add', ... */
	protected $action;
	protected $display;
	protected $_includeContainer = true;

	public $tpl_folder;

	protected $bo_theme;

	/** @var bool Redirect or not ater a creation */
	protected $_redirect = true;

	/** @var array Name and directory where class image are located */
	public $fieldImageSettings = array();

	/** @var string Image type */
	public $imageType = 'jpg';

	/** @var instanciation of the class associated with the AdminController */
	protected $object;

	/** @var current object ID */
	protected $id_object;

	/**
	 * @var current controller name without suffix
	 */
	public $controller_name;

	public $multishop_context = -1;

	public function __construct()
	{
		// Has to be remove for the next Prestashop version
		global $token;

		$this->controller_type = 'admin';
		$this->controller_name = get_class($this);
		if (strpos($this->controller_name, 'Controller'))
			$this->controller_name = substr($this->controller_name, 0, -10);

		parent::__construct();

		if ($this->multishop_context == -1)
			$this->multishop_context = Shop::CONTEXT_ALL | Shop::CONTEXT_GROUP | Shop::CONTEXT_SHOP;

		$this->bo_theme = ((Validate::isLoadedObject($this->context->employee) && $this->context->employee->bo_theme) ? $this->context->employee->bo_theme : 'default');
		$this->context->smarty->setTemplateDir(_PS_BO_ALL_THEMES_DIR_.$this->bo_theme.'/template');

		$this->id = Tab::getIdFromClassName($this->controller_name);
		$this->token = Tools::getAdminToken($this->controller_name.(int)$this->id.(int)$this->context->employee->id);

		$token = $this->token;

		$this->_conf = array(
			1 => $this->l('Deletion successful'), 2 => $this->l('Selection successfully deleted'),
			3 => $this->l('Creation successful'), 4 => $this->l('Update successful'),
			5 => $this->l('Status update successful'), 6 => $this->l('Settings update successful'),
			7 => $this->l('Image successfully deleted'), 8 => $this->l('Module downloaded successfully'),
			9 => $this->l('Thumbnails successfully regenerated'), 10 => $this->l('Message sent to the customer'),
			11 => $this->l('Comment added'), 12 => $this->l('Module(s) installed successfully'),
			13 => $this->l('Module(s) uninstalled successfully'), 14 => $this->l('Language successfully copied'),
			15 => $this->l('Translations successfully added'), 16 => $this->l('Module transplanted successfully to hook'),
			17 => $this->l('Module removed successfully from hook'), 18 => $this->l('Upload successful'),
			19 => $this->l('Duplication completed successfully'), 20 => $this->l('Translation added successfully but the language has not been created'),
			21 => $this->l('Module reset successfully'), 22 => $this->l('Module deleted successfully'),
			23 => $this->l('Localization pack imported successfully'), 24 => $this->l('Refund Successful'),
			25 => $this->l('Images successfully moved'),
			26 => $this->l('Cover selection saved'),
			27 => $this->l('Image shop association modified'),
			28 => $this->l('Zone assigned to the selection successfully'),
			29 => $this->l('Upgrade successful')
		);
		if (!$this->identifier) $this->identifier = 'id_'.$this->table;
		if (!$this->_defaultOrderBy) $this->_defaultOrderBy = $this->identifier;
		$this->tabAccess = Profile::getProfileAccess($this->context->employee->id_profile, $this->id);

		// Fix for AdminHome
		if ($this->controller_name == 'AdminHome')
			$_POST['token'] = $this->token;

		if (!Shop::isFeatureActive())
			$this->shopLinkType = '';

		//$this->base_template_folder = _PS_BO_ALL_THEMES_DIR_.$this->bo_theme.'/template';
		$this->override_folder = Tools::toUnderscoreCase(substr($this->controller_name, 5)).'/';
		// Get the name of the folder containing the custom tpl files
		$this->tpl_folder = Tools::toUnderscoreCase(substr($this->controller_name, 5)).'/';

		$this->context->currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));

		$this->initShopContext();
	}

	/**
	 * set default toolbar_title to admin breadcrumb
	 *
	 * @return void
	 */
	public function initToolbarTitle()
	{
		// Breadcrumbs
		$tabs = array();
		$tabs = Tab::recursiveTab($this->id, $tabs);
		$tabs = array_reverse($tabs);

		$bread = '';
		switch ($this->display)
		{
			case 'edit':
				$current_tab = array_pop($tabs);
				$tabs[] = array('name' => sprintf($this->l('Edit %s'), $current_tab['name']));
				break;

			case 'add':
				$current_tab = array_pop($tabs);
				$tabs[] = array('name' => sprintf($this->l('Add %s'), $current_tab['name']));
				break;

			case 'view':
				$current_tab = array_pop($tabs);
				$tabs[] = array('name' => sprintf($this->l('View %s'), $current_tab['name']));
				break;
		}
		// note : this should use a tpl file
		foreach ($tabs as $key => $item)
			$bread .= '<span class="breadcrumb item-'.$key.' ">'.Tools::safeOutput($item['name']).'</span> : ';

		$bread = rtrim($bread, ': ');

		$this->toolbar_title = $bread;
	}

	/**
	 * Check rights to view the current tab
	 *
	 * @param bool $disable
	 * @return boolean
	 */
	public function viewAccess($disable = false)
	{
		if ($disable)
			return true;

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
		return (!empty($token) && $token === $this->token);
	}

	public function ajaxProcessHelpAccess()
	{
		$this->json = true;
		$item = Tools::getValue('item');
		$iso_user = Tools::getValue('isoUser');
		$country = Tools::getValue('country');
		$version = Tools::getValue('version');

		if (isset($item) && isset($iso_user) && isset($country))
        {
            $helper = new HelperHelpAccess($item, $iso_user, $country, $version);
            $this->content = $helper->generate();
        }
		else
			$this->content = 'none';
		$this->display = 'content';
	}

	/**
	 * Set the filters used for the list display
	 */
	public function processFilter()
	{
		$filters = $this->context->cookie->getFamily($this->table.'Filter_');

		foreach ($filters as $key => $value)
		{
			/* Extracting filters from $_POST on key filter_ */
			if ($value != null && !strncmp($key, $this->table.'Filter_', 7 + Tools::strlen($this->table)))
			{
				$key = Tools::substr($key, 7 + Tools::strlen($this->table));
				/* Table alias could be specified using a ! eg. alias!field */
				$tmp_tab = explode('!', $key);
				$filter = count($tmp_tab) > 1 ? $tmp_tab[1] : $tmp_tab[0];

				if ($field = $this->filterToField($key, $filter))
				{
					$type = (array_key_exists('filter_type', $field) ? $field['filter_type'] : (array_key_exists('type', $field) ? $field['type'] : false));
					if (($type == 'date' || $type == 'datetime') && is_string($value))
						$value = unserialize($value);
					$key = isset($tmp_tab[1]) ? $tmp_tab[0].'.`'.$tmp_tab[1].'`' : '`'.$tmp_tab[0].'`';

					// Assignement by reference
					if (array_key_exists('tmpTableFilter', $field))
						$sql_filter = & $this->_tmpTableFilter;
					elseif (array_key_exists('havingFilter', $field))
						$sql_filter = & $this->_filterHaving;
					else
						$sql_filter = & $this->_filter;

					/* Only for date filtering (from, to) */
					if (is_array($value))
					{
						if (isset($value[0]) && !empty($value[0]))
						{
							if (!Validate::isDate($value[0]))
								$this->errors[] = Tools::displayError('\'From:\' date format is invalid (YYYY-MM-DD)');
							else
								$sql_filter .= ' AND '.pSQL($key).' >= \''.pSQL(Tools::dateFrom($value[0])).'\'';
						}

						if (isset($value[1]) && !empty($value[1]))
						{
							if (!Validate::isDate($value[1]))
								$this->errors[] = Tools::displayError('\'To:\' date format is invalid (YYYY-MM-DD)');
							else
								$sql_filter .= ' AND '.pSQL($key).' <= \''.pSQL(Tools::dateTo($value[1])).'\'';
						}
					}
					else
					{
						$sql_filter .= ' AND ';
						$check_key = ($key == $this->identifier || $key == '`'.$this->identifier.'`');

						if ($type == 'int' || $type == 'bool')
							$sql_filter .= (($check_key || $key == '`active`') ? 'a.' : '').pSQL($key).' = '.(int)$value.' ';
						elseif ($type == 'decimal')
							$sql_filter .= ($check_key ? 'a.' : '').pSQL($key).' = '.(float)$value.' ';
						elseif ($type == 'select')
							$sql_filter .= ($check_key ? 'a.' : '').pSQL($key).' = \''.pSQL($value).'\' ';
						else
							$sql_filter .= ($check_key ? 'a.' : '').pSQL($key).' LIKE \'%'.pSQL($value).'%\' ';
					}
				}
			}
		}
	}

	/**
	 * @todo uses redirectAdmin only if !$this->ajax
	 */
	public function postProcess()
	{
		if ($this->ajax)
		{
			// from ajax-tab.php
			$action = Tools::getValue('action');
			// no need to use displayConf() here
			if (!empty($action) && method_exists($this, 'ajaxProcess'.Tools::toCamelCase($action)))
				return $this->{'ajaxProcess'.Tools::toCamelCase($action)}();
			elseif (method_exists($this, 'ajaxProcess'))
				return $this->ajaxProcess();
		}
		else
		{
			// Process list filtering
			if ($this->filter)
				$this->processFilter();

			// If the method named after the action exists, call "before" hooks, then call action method, then call "after" hooks
			if (!empty($this->action) && method_exists($this, 'process'.ucfirst(Tools::toCamelCase($this->action))))
			{
				// Hook before action
				Hook::exec('actionAdmin'.ucfirst($this->action).'Before', array('controller' => $this));
				Hook::exec('action'.get_class($this).ucfirst($this->action).'Before', array('controller' => $this));
				// Call process
				$return = $this->{'process'.Tools::toCamelCase($this->action)}();
				// Hook After Action
				Hook::exec('actionAdmin'.ucfirst($this->action).'After', array('controller' => $this, 'return' => $return));
				Hook::exec('action'.get_class($this).ucfirst($this->action).'After', array('controller' => $this, 'return' => $return));

				return $return;
			}
		}
	}

	/**
	 * Object Delete images
	 */
	public function processDeleteImage()
	{
		if (Validate::isLoadedObject($object = $this->loadObject()))
		{
			if (($object->deleteImage()))
			{
				$redirect = self::$currentIndex.'&add'.$this->table.'&'.$this->identifier.'='.Tools::getValue($this->identifier).'&conf=7&token='.$this->token;
				if (!$this->ajax)
					$this->redirect_after = $redirect;
				else
					$this->content = 'ok';
			}
		}
		$this->errors[] = Tools::displayError('An error occurred during image deletion (cannot load object).');
		return $object;
	}

	/**
	 * Object Delete
	 */
	public function processDelete()
	{
		if (Validate::isLoadedObject($object = $this->loadObject()))
		{
			$res = true;
			// check if request at least one object with noZeroObject
			if (isset($object->noZeroObject) && count(call_user_func(array($this->className, $object->noZeroObject))) <= 1)
			{
				$this->errors[] = Tools::displayError('You need at least one object.').
					' <b>'.$this->table.'</b><br />'.
					Tools::displayError('You cannot delete all of the items.');
			}
			elseif (array_key_exists('delete', $this->list_skip_actions) && in_array($object->id, $this->list_skip_actions['delete'])) //check if some ids are in list_skip_actions and forbid deletion
					$this->errors[] = Tools::displayError('You cannot delete this items.');
			else
			{
				if ($this->deleted)
				{
					if (!empty($this->fieldImageSettings))
						$res = $object->deleteImage();

					if (!$res)
						$this->errors[] = Tools::displayError('Unable to delete associated images');

					$object->deleted = 1;
					if ($object->update())
						$this->redirect_after = self::$currentIndex.'&conf=1&token='.$this->token;
				}
				elseif ($object->delete())
				{
					if (method_exists($object, 'cleanPositions'))
						$object->cleanPositions();
					$this->redirect_after = self::$currentIndex.'&conf=1&token='.$this->token;
				}
				$this->errors[] = Tools::displayError('An error occurred during deletion.');
			}
		}
		else
		{
			$this->errors[] = Tools::displayError('An error occurred while deleting object.').
				' <b>'.$this->table.'</b> '.
				Tools::displayError('(cannot load object)');
		}
		return $object;
	}

	/**
	 * Call the right method for creating or updating object
	 *
	 * @return mixed
	 */
	public function processSave()
	{
		if ($this->id_object)
		{
			$this->object = $this->loadObject();
			return $this->processUpdate();
		}
		else
			return $this->processAdd();
	}

	/**
	 * Object creation
	 */
	public function processAdd()
	{
		/* Checking fields validity */
		$this->validateRules();

		if (count($this->errors) <= 0)
		{
			$object = new $this->className();

			$this->copyFromPost($object, $this->table);
			$this->beforeAdd($object);
			if (method_exists($object, 'add') && !$object->add())
			{
				$this->errors[] = Tools::displayError('An error occurred while creating object.').
					' <b>'.$this->table.' ('.Db::getInstance()->getMsgError().')</b>';
			}
			/* voluntary do affectation here */
			elseif (($_POST[$this->identifier] = $object->id) && $this->postImage($object->id) && !count($this->errors) && $this->_redirect)
			{
				$parent_id = (int)Tools::getValue('id_parent', 1);
				$this->afterAdd($object);
				$this->updateAssoShop($object->id);
				// Save and stay on same form
				if (empty($this->redirect_after) && $this->redirect_after !== false && Tools::isSubmit('submitAdd'.$this->table.'AndStay'))
					$this->redirect_after = self::$currentIndex.'&'.$this->identifier.'='.$object->id.'&conf=3&update'.$this->table.'&token='.$this->token;
				// Save and back to parent
				if (empty($this->redirect_after) && $this->redirect_after !== false && Tools::isSubmit('submitAdd'.$this->table.'AndBackToParent'))
					$this->redirect_after = self::$currentIndex.'&'.$this->identifier.'='.$parent_id.'&conf=3&token='.$this->token;
				// Default behavior (save and back)
				if (empty($this->redirect_after) && $this->redirect_after !== false)
					$this->redirect_after = self::$currentIndex.($parent_id ? '&'.$this->identifier.'='.$object->id : '').'&conf=3&token='.$this->token;
			}
		}

		$this->errors = array_unique($this->errors);
		if (!empty($this->errors))
		{
			// if we have errors, we stay on the form instead of going back to the list
			$this->display = 'edit';
			return false;
		}

		return $object;
	}


	/**
	 * Object update
	 */
	public function processUpdate()
	{
		/* Checking fields validity */
		$this->validateRules();

		if (empty($this->errors))
		{
			$id = (int)Tools::getValue($this->identifier);

			/* Object update */
			if (isset($id) && !empty($id))
			{
				$object = new $this->className($id);
				if (Validate::isLoadedObject($object))
				{
					/* Specific to objects which must not be deleted */
					if ($this->deleted && $this->beforeDelete($object))
					{
						// Create new one with old objet values
						$object_new = new $this->className($object->id);
						$object_new->id = null;
						$object_new->date_add = '';
						$object_new->date_upd = '';

						// Update old object to deleted
						$object->deleted = 1;
						$object->update();

						// Update new object with post values
						$this->copyFromPost($object_new, $this->table);
						$result = $object_new->add();
						if (Validate::isLoadedObject($object_new))
							$this->afterDelete($object_new, $object->id);
					}
					else
					{
						$this->copyFromPost($object, $this->table);
						$result = $object->update();
						$this->afterUpdate($object);
					}

					if ($object->id)
						$this->updateAssoShop($object->id);

					if (!$result)
					{
						$this->errors[] = Tools::displayError('An error occurred while updating object.').
							' <b>'.$this->table.'</b> ('.Db::getInstance()->getMsgError().')';
					}
					elseif ($this->postImage($object->id) && !count($this->errors) && $this->_redirect)
					{
						$parent_id = (int)Tools::getValue('id_parent', 1);
						// Specific back redirect
						if ($back = Tools::getValue('back'))
							$this->redirect_after = urldecode($back).'&conf=4';
						// Specific scene feature
						// @todo change stay_here submit name (not clear for redirect to scene ... )
						if (Tools::getValue('stay_here') == 'on' || Tools::getValue('stay_here') == 'true' || Tools::getValue('stay_here') == '1')
							$this->redirect_after = self::$currentIndex.'&'.$this->identifier.'='.$object->id.'&conf=4&updatescene&token='.$this->token;
						// Save and stay on same form
						// @todo on the to following if, we may prefer to avoid override redirect_after previous value
						if (Tools::isSubmit('submitAdd'.$this->table.'AndStay'))
							$this->redirect_after = self::$currentIndex.'&'.$this->identifier.'='.$object->id.'&conf=4&update'.$this->table.'&token='.$this->token;
						// Save and back to parent
						if (Tools::isSubmit('submitAdd'.$this->table.'AndBackToParent'))
							$this->redirect_after = self::$currentIndex.'&'.$this->identifier.'='.$parent_id.'&conf=4&token='.$this->token;

						// Default behavior (save and back)
						if (empty($this->redirect_after))
							$this->redirect_after = self::$currentIndex.($parent_id ? '&'.$this->identifier.'='.$object->id : '').'&conf=4&token='.$this->token;
					}
				}
				else
					$this->errors[] = Tools::displayError('An error occurred while updating object.').
						' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
			}
		}
		$this->errors = array_unique($this->errors);
		if (!empty($this->errors))
		{
			// if we have errors, we stay on the form instead of going back to the list
			$this->display = 'edit';
			return false;
		}

		if (isset($object))
			return $object;
		return;
	}

	/**
	 * Change object required fields
	 */
	public function processUpdateFields()
	{
		if (!is_array($fields = Tools::getValue('fieldsBox')))
			$fields = array();

		$object = new $this->className();
		if (!$object->addFieldsRequiredDatabase($fields))
			$this->errors[] = Tools::displayError('Error in updating required fields');
		else
			$this->redirect_after = self::$currentIndex.'&conf=4&token='.$this->token;

		return $object;
	}

	/**
	 * Change object status (active, inactive)
	 */
	public function processStatus()
	{
		if (Validate::isLoadedObject($object = $this->loadObject()))
		{
			if ($object->toggleStatus())
			{
				$id_category = (($id_category = (int)Tools::getValue('id_category')) && Tools::getValue('id_product')) ? '&id_category='.$id_category : '';
				$this->redirect_after = self::$currentIndex.'&conf=5'.$id_category.'&token='.$this->token;
			}
			else
				$this->errors[] = Tools::displayError('An error occurred while updating status.');
		}
		else
			$this->errors[] = Tools::displayError('An error occurred while updating status for object.').
				' <b>'.$this->table.'</b> '.
				Tools::displayError('(cannot load object)');

		return $object;
	}

	/**
	 * Change object position
	 */
	public function processPosition()
	{
		if (!Validate::isLoadedObject($object = $this->loadObject()))
		{
			$this->errors[] = Tools::displayError('An error occurred while updating status for object.').
				' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
		}
		elseif (!$object->updatePosition((int)Tools::getValue('way'), (int)Tools::getValue('position')))
			$this->errors[] = Tools::displayError('Failed to update the position.');
		else
		{
			$id_identifier_str = ($id_identifier = (int)Tools::getValue($this->identifier)) ? '&'.$this->identifier.'='.$id_identifier : '';
			$redirect = self::$currentIndex.'&'.$this->table.'Orderby=position&'.$this->table.'Orderway=asc&conf=5'.$id_identifier_str.'&token='.$this->token;
			$this->redirect_after = $redirect;
		}
		return $object;
	}

	/**
	 * Cancel all filters for this tab
	 */
	public function processResetFilters()
	{
		$filters = $this->context->cookie->getFamily($this->table.'Filter_');

		foreach ($filters as $cookie_key => $filter)
			if (strncmp($cookie_key, $this->table.'Filter_', 7 + Tools::strlen($this->table)) == 0)
			{
				$key = substr($cookie_key, 7 + Tools::strlen($this->table));
				/* Table alias could be specified using a ! eg. alias!field */
				$tmp_tab = explode('!', $key);
				$key = (count($tmp_tab) > 1 ? $tmp_tab[1] : $tmp_tab[0]);

				if (array_key_exists($key, $this->fields_list))
					unset($this->context->cookie->$cookie_key);
			}

		if (isset($this->context->cookie->{'submitFilter'.$this->table}))
			unset($this->context->cookie->{'submitFilter'.$this->table});

		if (isset($this->context->cookie->{$this->table.'Orderby'}))
			unset($this->context->cookie->{$this->table.'Orderby'});

		if (isset($this->context->cookie->{$this->table.'Orderway'}))
			unset($this->context->cookie->{$this->table.'Orderway'});

		unset($_POST);
		$this->_filter = false;
		unset($this->_filterHaving);
		unset($this->_having);
	}

	/**
	 * Update options and preferences
	 */
	protected function processUpdateOptions()
	{
		$this->beforeUpdateOptions();

		$languages = Language::getLanguages(false);

		foreach ($this->fields_options as $category_data)
		{
			if (!isset($category_data['fields']))
				continue;

			$fields = $category_data['fields'];

			foreach ($fields as $field => $values)
			{
				if (isset($values['type']) && $values['type'] == 'selectLang')
				{
					foreach ($languages as $lang)
						if (Tools::getValue($field.'_'.strtoupper($lang['iso_code'])))
							$fields[$field.'_'.strtoupper($lang['iso_code'])] = array(
								'type' => 'select',
								'cast' => 'strval',
								'identifier' => 'mode',
								'list' => $values['list']
							);
				}
			}

			// Validate fields
			foreach ($fields as $field => $values)
			{
				// We don't validate fields with no visibility
				if (Shop::isFeatureActive() && isset($values['visibility']) && ($values['visibility'] > Shop::getContext()))
					continue;

				// Check if field is required
				if (isset($values['required']) && $values['required'] && !isset($_POST['configUseDefault'][$field]))
					if (isset($values['type']) && $values['type'] == 'textLang')
					{
						foreach ($languages as $language)
							if (($value = Tools::getValue($field.'_'.$language['id_lang'])) == false && (string)$value != '0')
								$this->errors[] = Tools::displayError('field').' <b>'.$values['title'].'</b> '.Tools::displayError('is required.');
					}
					elseif (($value = Tools::getValue($field)) == false && (string)$value != '0')
						$this->errors[] = Tools::displayError('field').' <b>'.$values['title'].'</b> '.Tools::displayError('is required.');

				// Check field validator
				if (isset($values['type']) && $values['type'] == 'textLang')
				{
					foreach ($languages as $language)
						if (Tools::getValue($field.'_'.$language['id_lang']) && isset($values['validation']))
							if (!Validate::$values['validation'](Tools::getValue($field.'_'.$language['id_lang'])))
								$this->errors[] = Tools::displayError('field').' <b>'.$values['title'].'</b> '.Tools::displayError('is invalid.');
				}
				elseif (Tools::getValue($field) && isset($values['validation']))
					if (!Validate::$values['validation'](Tools::getValue($field)))
						$this->errors[] = Tools::displayError('field').' <b>'.$values['title'].'</b> '.Tools::displayError('is invalid.');

				// Set default value
				if (!Tools::getValue($field) && isset($values['default']))
					$_POST[$field] = $values['default'];
			}

			if (!count($this->errors))
			{
				foreach ($fields as $key => $options)
				{
					if (Shop::isFeatureActive() && isset($options['visibility']) && ($options['visibility'] > Shop::getContext()))
						continue;

					if (Shop::isFeatureActive() && isset($_POST['configUseDefault'][$key]))
					{
						Configuration::deleteFromContext($key);
						continue;
					}

					// check if a method updateOptionFieldName is available
					$method_name = 'updateOption'.Tools::toCamelCase($key, true);
					if (method_exists($this, $method_name))
						$this->$method_name(Tools::getValue($key));
					elseif (isset($options['type']) && in_array($options['type'], array('textLang', 'textareaLang')))
					{
						$list = array();
						foreach ($languages as $language)
						{
							$key_lang = Tools::getValue($key.'_'.$language['id_lang']);
							$val = (isset($options['cast']) ? $options['cast']($key_lang) : $key_lang);
							if ($this->validateField($val, $options))
							{
								if (Validate::isCleanHtml($val))
									$list[$language['id_lang']] = $val;
								else
									$this->errors[] = Tools::displayError('Can not add configuration '.$key.' for lang '.Language::getIsoById((int)$language['id_lang']));
							}
						}
						Configuration::updateValue($key, $list);
					}
					else
					{
						$val = (isset($options['cast']) ? $options['cast'](Tools::getValue($key)) : Tools::getValue($key));
						if ($this->validateField($val, $options))
						{
							if (Validate::isCleanHtml($val))
								Configuration::updateValue($key, $val);
							else
								$this->errors[] = Tools::displayError('Can not add configuration '.$key);
						}
					}
				}
			}
		}
		if (empty($this->errors))
			$this->confirmations[] = $this->_conf[6];
	}


	/**
	 * assign default action in toolbar_btn smarty var, if they are not set.
	 * uses override to specifically add, modify or remove items
	 *
	 */
	public function initToolbar()
	{
		switch ($this->display)
		{
			case 'add':
			case 'edit':
				// Default save button - action dynamically handled in javascript
				$this->toolbar_btn['save'] = array(
					'href' => '#',
					'desc' => $this->l('Save')
				);
				//no break
			case 'view':
				// Default cancel button - like old back link
				$back = Tools::safeOutput(Tools::getValue('back', ''));
				if (empty($back))
					$back = self::$currentIndex.'&token='.$this->token;
				if (!$this->lite_display)
					$this->toolbar_btn['back'] = array(
						'href' => $back,
						'desc' => $this->l('Back to list')
					);
				break;
			case 'options':
				$this->toolbar_btn['save'] = array(
					'href' => '#',
					'desc' => $this->l('Save')
				);
				break;
			case 'view':
				break;
			default: // list
				$this->toolbar_btn['new'] = array(
					'href' => self::$currentIndex.'&amp;add'.$this->table.'&amp;token='.$this->token,
					'desc' => $this->l('Add new')
				);
		}

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
		$id = (int)Tools::getValue($this->identifier);
		if ($id && Validate::isUnsignedId($id))
		{
			if (!$this->object)
				$this->object = new $this->className($id);
			if (Validate::isLoadedObject($this->object))
				return $this->object;
			// throw exception
			$this->errors[] = Tools::displayError('Object cannot be loaded (not found)');
			return false;
		}
		elseif ($opt)
		{
			if (!$this->object)
				$this->object = new $this->className();
			return $this->object;
		}
		else
		{
			$this->errors[] = Tools::displayError('Object cannot be loaded (identifier missing or invalid)');
			return false;
		}

		return $this->object;
	}

	/**
	 * Check if the token is valid, else display a warning page
	 */
	public function checkAccess()
	{
		if (!$this->checkToken())
		{
			// If this is an XSS attempt, then we should only display a simple, secure page
			// ${1} in the replacement string of the regexp is required,
			// because the token may begin with a number and mix up with it (e.g. $17)
			$url = preg_replace('/([&?]token=)[^&]*(&.*)?$/', '${1}'.$this->token.'$2', $_SERVER['REQUEST_URI']);
			if (false === strpos($url, '?token=') && false === strpos($url, '&token='))
				$url .= '&token='.$this->token;
			if (strpos($url, '?') === false)
				$url = str_replace('&token', '?controller=AdminHome&token', $url);

			$this->context->smarty->assign('url', htmlentities($url));
			return false;
		}
		return true;
	}

	protected function filterToField($key, $filter)
	{
		foreach ($this->fields_list as $field)
			if (array_key_exists('filter_key', $field) && $field['filter_key'] == $key)
				return $field;
		if (array_key_exists($filter, $this->fields_list))
			return $this->fields_list[$filter];
		return false;
	}

	public function displayNoSmarty()
	{
	}

	public function displayAjax()
	{
		if ($this->json)
		{
			$this->context->smarty->assign(array(
				'json' => true,
				'status' => $this->status,
			));
		}
		$this->layout = 'layout-ajax.tpl';
		return $this->display();
	}

	protected function redirect()
	{
		header('Location: '.$this->redirect_after);
		exit;
	}
	public function display()
	{
		$this->context->smarty->assign('display_header', $this->display_header);
		$this->context->smarty->assign('display_footer', $this->display_footer);
		$this->context->smarty->assign('meta_title', $this->meta_title);

		$tpl_action = $this->tpl_folder.$this->display.'.tpl';

		// Check if action template has been override

		// new smarty : template_dir is an array.
		// @todo : add override path to the smarty config, and checking all array item
		if (file_exists($this->context->smarty->getTemplateDir(0).'/'.$tpl_action) && $this->display != 'view' && $this->display != 'options')
		{
			if (method_exists($this, $this->display.Tools::toCamelCase($this->className)))
				$this->{$this->display.Tools::toCamelCase($this->className)}();
			$this->context->smarty->assign('content', $this->context->smarty->fetch($tpl_action));
		}

		if (!$this->ajax)
		{
			$template = $this->createTemplate($this->template);
			$page = $template->fetch();
		}
		else
			$page = $this->content;

		if ($conf = Tools::getValue('conf'))
			if ($this->json)
				$this->context->smarty->assign('conf', Tools::jsonEncode($this->_conf[(int)$conf]));
			else
				$this->context->smarty->assign('conf', $this->_conf[(int)$conf]);


		if ($this->json)
			$this->context->smarty->assign('errors', Tools::jsonEncode($this->errors));
		else
			$this->context->smarty->assign('errors', $this->errors);

		if ($this->json)
			$this->context->smarty->assign('warnings', Tools::jsonEncode($this->warnings));
		else
			$this->context->smarty->assign('warnings', $this->warnings);


		if ($this->json)
			$this->context->smarty->assign('informations', Tools::jsonEncode($this->informations));
		else
			$this->context->smarty->assign('informations', $this->informations);

		if ($this->json)
			$this->context->smarty->assign('confirmations', Tools::jsonEncode($this->confirmations));
		else
			$this->context->smarty->assign('confirmations', $this->confirmations);

		if ($this->json)
			$this->context->smarty->assign('page', Tools::jsonEncode($page));
		else
			$this->context->smarty->assign('page', $page);

		$this->context->smarty->display($this->layout);
	}

	/**
	 * add a warning message to display at the top of the page
	 *
	 * @param string $msg
	 */
	protected function displayWarning($msg)
	{
		$this->warnings[] = $msg;
	}

	/**
	 * add a info message to display at the top of the page
	 *
	 * @param string $msg
	 */
	protected function displayInformation($msg)
	{
		$this->informations[] = $msg;
	}

	/**
	 * Assign smarty variables for the header
	 */
	public function initHeader()
	{
		// Multishop
		$is_multishop = Shop::isFeatureActive();

		// Quick access
		$quick_access = QuickAccess::getQuickAccesses($this->context->language->id);
		foreach ($quick_access as $index => $quick)
		{
			if ($quick['link'] == '../' && Shop::getContext() == Shop::CONTEXT_SHOP)
				$quick_access[$index]['link'] = $this->context->shop->getBaseURL();
			else
			{
				preg_match('/controller=(.+)(&.+)?$/', $quick['link'], $admin_tab);
				if (isset($admin_tab[1]))
				{
					if (strpos($admin_tab[1], '&'))
						$admin_tab[1] = substr($admin_tab[1], 0, strpos($admin_tab[1], '&'));

					$token = Tools::getAdminToken($admin_tab[1].(int)Tab::getIdFromClassName($admin_tab[1]).(int)$this->context->employee->id);
					$quick_access[$index]['link'] .= '&token='.$token;
				}
			}
		}

		// Tab list
		$tabs = Tab::getTabs($this->context->language->id, 0);
		$current_id = Tab::getCurrentParentId();
		foreach ($tabs as $index => $tab)
		{
			if ($tab['name'] == 'Stock' && Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') == 0)
			{
				unset($tabs[$index]);
				continue;
			}

			$img_cache_url = 'themes/'.$this->context->employee->bo_theme.'/img/t/'.$tab['class_name'].'.png';
			$img_exists_cache = Tools::file_exists_cache(_PS_ADMIN_DIR_.$img_cache_url);
			// retrocompatibility : change png to gif if icon not exists
			if (!$img_exists_cache)
				$img_exists_cache = Tools::file_exists_cache(_PS_ADMIN_DIR_.str_replace('.png', '.gif', $img_cache_url));
			$img = $img_exists_cache ? $img_cache_url : _PS_IMG_.'t/'.$tab['class_name'].'.png';

			if (trim($tab['module']) != '')
				$img = _MODULE_DIR_.$tab['module'].'/'.$tab['class_name'].'.png';

			// retrocompatibility
			if (!file_exists(dirname(_PS_ROOT_DIR_).$img))
				$img = str_replace('png', 'gif', $img);
			// tab[class_name] does not contains the "Controller" suffix
			$tabs[$index]['current'] = ($tab['class_name'].'Controller' == get_class($this)) || ($current_id == $tab['id_tab']);
			$tabs[$index]['img'] = $img;
			$tabs[$index]['href'] = $this->context->link->getAdminLink($tab['class_name']);

			$sub_tabs = Tab::getTabs($this->context->language->id, $tab['id_tab']);
			foreach ($sub_tabs as $index2 => $sub_tab)
			{
				// class_name is the name of the class controller
				if (Tab::checkTabRights($sub_tab['id_tab']) === true
					&& (bool)$sub_tab['active'])
					$sub_tabs[$index2]['href'] = $this->context->link->getAdminLink($sub_tab['class_name']);
				else
					unset($sub_tabs[$index2]);
			}
			$tabs[$index]['sub_tabs'] = $sub_tabs;

			// If there are no subtabs, we don't want to display the parent tab in menu
			if (empty($sub_tabs))
				unset($tabs[$index]);
		}

		/* Hooks are volontary out the initialize array (need those variables already assigned) */
		$bo_color = empty($this->context->employee->bo_color) ? '#FFFFFF' : $this->context->employee->bo_color;
		$this->context->smarty->assign(array(
			'img_dir' => _PS_IMG_,
			'iso' => $this->context->language->iso_code,
			'class_name' => $this->className,
			'iso_user' => $this->context->language->iso_code,
			'country_iso_code' => $this->context->country->iso_code,
			'version' => _PS_VERSION_,
			'autorefresh_notifications' => Configuration::get('PS_ADMIN_REFRESH_NOTIFICATION'),
			'help_box' => Configuration::get('PS_HELPBOX'),
			'round_mode' => Configuration::get('PS_PRICE_ROUND_MODE'),
			'brightness' => Tools::getBrightness($bo_color) < 128 ? 'white' : '#383838',
			'lang_iso' => $this->context->language->iso_code,
			'link' => $this->context->link,
			'bo_width' => (int)$this->context->employee->bo_width,
			'bo_color' => isset($this->context->employee->bo_color) ? Tools::htmlentitiesUTF8($this->context->employee->bo_color) : null,
			'shop_name' => Configuration::get('PS_SHOP_NAME'),
			'show_new_orders' => Configuration::get('PS_SHOW_NEW_ORDERS'),
			'show_new_customers' => Configuration::get('PS_SHOW_NEW_CUSTOMERS'),
			'show_new_messages' => Configuration::get('PS_SHOW_NEW_MESSAGES'),
			'first_name' => Tools::substr($this->context->employee->firstname, 0, 1),
			'last_name' => htmlentities($this->context->employee->lastname, ENT_COMPAT, 'UTF-8'),
			'base_url' => $this->context->shop->getBaseURL(),
			'employee' => $this->context->employee,
			'search_type' => Tools::getValue('bo_search_type'),
			'bo_query' => Tools::safeOutput(Tools::stripslashes(Tools::getValue('bo_query'))),
			'quick_access' => $quick_access,
			'multi_shop' => Shop::isFeatureActive(),
			'shop_list' => Helper::renderShopList(),
			'shop' => $this->context->shop,
			'group_shop' => $this->context->shop->getGroup(),
			'tab' => $tab,
			'current_parent_id' => (int)Tab::getCurrentParentId(),
			'tabs' => $tabs,
			'install_dir_exists' => file_exists(_PS_ADMIN_DIR_.'/../install'),
			'is_multishop' => $is_multishop,
			'multishop_context' => $this->multishop_context,
			'pic_dir' => _THEME_PROD_PIC_DIR_,
			'controller_name' => htmlentities(Tools::getValue('controller')),
		));

		// Shop context
		if ($is_multishop)
		{
			if (Shop::getContext() == Shop::CONTEXT_SHOP)
				$shop_name = $this->context->shop->name;
			else
				$shop_name = 'PrestaShop';

			$this->context->smarty->assign(array(
				'shop_name' => $shop_name,
			));
		}
	}

	/**
	 * Declare an action to use for each row in the list
	 */
	public function addRowAction($action)
	{
		$action = strtolower($action);
		$this->actions[] = $action;
	}

	/**
	 * Add  an action to use for each row in the list
	 */
	public function addRowActionSkipList($action, $list)
	{
		$action = strtolower($action);
		$list = (array)$list;

		if (array_key_exists($action, $this->list_skip_actions))
			$this->list_skip_actions[$action] = array_merge($this->list_skip_actions[$action], $list);
		else
			$this->list_skip_actions[$action] = $list;
	}

	/**
	 * Assign smarty variables for all default views, list and form, then call other init functions
	 */
	public function initContent()
	{
		// toolbar (save, cancel, new, ..)
		$this->initToolbar();
		if ($this->display == 'edit' || $this->display == 'add')
		{
			if (!$this->loadObject(true))
				return;

			$this->content .= $this->renderForm();
		}
		elseif ($this->display == 'view')
		{
			// Some controllers use the view action without an object
			if ($this->className)
				$this->loadObject(true);
			$this->content .= $this->renderView();
		}
		elseif (!$this->ajax)
		{
			$this->content .= $this->renderList();
			$this->content .= $this->renderOptions();

			// if we have to display the required fields form
			if ($this->required_database)
				$this->content .= $this->displayRequiredFields();
		}

		$this->context->smarty->assign(array(
			'content' => $this->content,
			'url_post' => self::$currentIndex.'&token='.$this->token,
		));
	}

	/**
	 * initialize the invalid doom page of death
	 *
	 * @return void
	 */
	public function initCursedPage()
	{
		$this->layout = 'invalid_token.tpl';
	}

	/**
	 * Assign smarty variables for the footer
	 */
	public function initFooter()
	{
		// We assign js and css files on the last step before display template, because controller can add many js and css files
		$this->context->smarty->assign('css_files', $this->css_files);
		$this->context->smarty->assign('js_files', array_unique($this->js_files));

		$this->context->smarty->assign(array(
			'ps_version' => _PS_VERSION_,
			'end_time' => number_format(microtime(true) - $this->timerStart, 3, '.', ''),
			'iso_is_fr' => strtoupper($this->context->language->iso_code) == 'FR',
		));
	}

	/**
	 * Function used to render the list to display for this controller
	 */
	public function renderList()
	{
		if (!($this->fields_list && is_array($this->fields_list)))
			return false;
		$this->getList($this->context->language->id);

		// Empty list is ok
		if (!is_array($this->_list))
			return false;

		$helper = new HelperList();

		$this->setHelperDisplay($helper);
		$helper->tpl_vars = $this->tpl_list_vars;
		$helper->tpl_delete_link_vars = $this->tpl_delete_link_vars;

		// For compatibility reasons, we have to check standard actions in class attributes
		foreach ($this->actions_available as $action)
		{
			if (!in_array($action, $this->actions) && isset($this->$action) && $this->$action)
				$this->actions[] = $action;
		}

		$list = $helper->generateList($this->_list, $this->fields_list);

		return $list;
	}

	/**
	 * Override to render the view page
	 */
	public function renderView()
	{
		$helper = new HelperView($this);
		$this->setHelperDisplay($helper);
		$helper->tpl_vars = $this->tpl_view_vars;
		!is_null($this->base_tpl_view) ? $helper->base_tpl = $this->base_tpl_view : '';
		$view = $helper->generateView();

		return $view;
	}

	/**
	 * Function used to render the form for this controller
	 */
	public function renderForm()
	{
		if (Tools::getValue('submitFormAjax'))
			$this->content .= $this->context->smarty->fetch('form_submit_ajax.tpl');
		if ($this->fields_form && is_array($this->fields_form))
		{
			if (!$this->multiple_fieldsets)
				$this->fields_form = array(array('form' => $this->fields_form));

			// For add a fields via an override of $fields_form, use $fields_form_override
			if (is_array($this->fields_form_override) && !empty($this->fields_form_override))
				$this->fields_form[0]['form']['input'][] = $this->fields_form_override;

			$this->getlanguages();
			$helper = new HelperForm($this);
			$this->setHelperDisplay($helper);
			$helper->fields_value = $this->getFieldsValue($this->object);
			$helper->tpl_vars = $this->tpl_form_vars;
			!is_null($this->base_tpl_form) ? $helper->base_tpl = $this->base_tpl_form : '';
			if ($this->tabAccess['view'])
			{
				if (Tools::getValue('back'))
					$helper->tpl_vars['back'] = Tools::safeOutput(Tools::getValue('back'));
				else
					$helper->tpl_vars['back'] = Tools::safeOutput(Tools::getValue(self::$currentIndex.'&token='.$this->token));
			}
			$form = $helper->generateForm($this->fields_form);

			return $form;
		}
	}

	/**
	 * Function used to render the options for this controller
	 */
	public function renderOptions()
	{
		if ($this->fields_options && is_array($this->fields_options))
		{
			if ($this->display != 'options')
				$this->show_toolbar = false;

			$helper = new HelperOptions($this);
			$this->setHelperDisplay($helper);
			$helper->id = $this->id;
			$helper->tpl_vars = $this->tpl_option_vars;
			$options = $helper->generateOptions($this->fields_options);

			return $options;
		}
	}

	/**
	 * this function set various display option for helper list
	 *
	 * @param Helper $helper
	 * @return void
	 */
	public function setHelperDisplay(Helper $helper)
	{
		if (empty($this->toolbar_title))
			$this->initToolbarTitle();
		// tocheck
		if ($this->object && $this->object->id)
			$helper->id = $this->object->id;

		// @todo : move that in Helper
		$helper->title = $this->toolbar_title;
		$helper->toolbar_btn = $this->toolbar_btn;
		$helper->show_toolbar = $this->show_toolbar;
		$helper->toolbar_scroll = $this->toolbar_scroll;
		$helper->override_folder = $this->tpl_folder;
		$helper->actions = $this->actions;
		$helper->simple_header = $this->list_simple_header;
		$helper->bulk_actions = $this->bulk_actions;
		$helper->currentIndex = self::$currentIndex;
		$helper->className = $this->className;
		$helper->table = $this->table;
		$helper->name_controller = Tools::getValue('controller');
		$helper->orderBy = $this->_orderBy;
		$helper->orderWay = $this->_orderWay;
		$helper->listTotal = $this->_listTotal;
		$helper->shopLink = $this->shopLink;
		$helper->shopLinkType = $this->shopLinkType;
		$helper->identifier = $this->identifier;
		$helper->token = $this->token;
		$helper->languages = $this->_languages;
		$helper->specificConfirmDelete = $this->specificConfirmDelete;
		$helper->imageType = $this->imageType;
		$helper->no_link = $this->list_no_link;
		$helper->colorOnBackground = $this->colorOnBackground;
		$helper->ajax_params = (isset($this->ajax_params) ? $this->ajax_params : null);
		$helper->default_form_language = $this->default_form_language;
		$helper->allow_employee_form_lang = $this->allow_employee_form_lang;
		$helper->multiple_fieldsets = $this->multiple_fieldsets;
		$helper->row_hover = $this->row_hover;
		$helper->position_identifier = $this->position_identifier;

		// For each action, try to add the corresponding skip elements list
		$helper->list_skip_actions = $this->list_skip_actions;
	}

	public function setMedia()
	{
		$this->addCSS(_PS_CSS_DIR_.'admin.css', 'all');
		$this->addCSS(__PS_BASE_URI__.str_replace(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR, '', _PS_ADMIN_DIR_).'/themes/'.$this->bo_theme.'/css/admin.css', 'all');
		if ($this->context->language->is_rtl)
			$this->addCSS(_THEME_CSS_DIR_.'rtl.css');

		$this->addJquery();
		$this->addjQueryPlugin(array('cluetip', 'hoverIntent', 'scrollTo', 'alerts', 'chosen'));

		$this->addJS(array(
			_PS_JS_DIR_.'admin.js',
			_PS_JS_DIR_.'toggle.js',
			_PS_JS_DIR_.'tools.js',
			_PS_JS_DIR_.'ajax.js',
			_PS_JS_DIR_.'toolbar.js'
		));

		if (!Tools::getValue('submitFormAjax'))
		{
			$this->addJs(_PS_JS_DIR_.'notifications.js');
			if (Configuration::get('PS_HELPBOX'))
				$this->addJS(_PS_JS_DIR_.'helpAccess.js');
		}
	}

	/**
	 * non-static method which uses AdminController::translate()
	 *
	 * @param mixed $string term or expression in english
	 * @param string $class name of the class, without "Controller" suffix
	 * @param boolan $addslashes if set to true, the return value will pass through addslashes(). Otherwise, stripslashes().
	 * @param boolean $htmlentities if set to true(default), the return value will pass through htmlentities($string, ENT_QUOTES, 'utf-8')
	 * @return string the translation if available, or the english default text.
	 */
	protected function l($string, $class = 'AdminTab', $addslashes = false, $htmlentities = true)
	{
		// classname has changed, from AdminXXX to AdminXXXController
		// So we remove 10 characters and we keep same keys
		if (strtolower(substr($class, -10)) == 'controller')
			$class = substr($class, 0, -10);
		elseif ($class == 'AdminTab')
			$class = substr(get_class($this), 0, -10);
		return Translate::getAdminTranslation($string, $class, $addslashes, $htmlentities);
	}

	/**
	 * Init context and dependencies, handles POST and GET
	 */
	public function init()
	{
		// Has to be removed for the next Prestashop version
		global $currentIndex;

		parent::init();

		if (Tools::getValue('ajax'))
			$this->ajax = '1';

		/* Server Params */
		$protocol_link = (Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
		$protocol_content = (isset($useSSL) && $useSSL && Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
		$this->context->link = new Link($protocol_link, $protocol_content);

		$this->timerStart = microtime(true);

		if (isset($_GET['logout']))
			$this->context->employee->logout();

		if ($this->controller_name != 'AdminLogin' && (!isset($this->context->employee) || !$this->context->employee->isLoggedBack()))
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminLogin').(!isset($_GET['logout']) ? '&redirect='.$this->controller_name : ''));

		// Set current index
		$current_index = __PS_BASE_URI__.basename(_PS_ADMIN_DIR_).'/index.php'.(($controller = Tools::getValue('controller')) ? '?controller='.$controller : '');
		if ($back = Tools::getValue('back'))
			$current_index .= '&back='.urlencode($back);
		self::$currentIndex = $current_index;
		$currentIndex = $current_index;

		if ((int)Tools::getValue('liteDisplaying'))
		{
			$this->display_header = false;
			$this->display_footer = false;
			$this->content_only = false;
			$this->lite_display = true;
		}

		if ($this->ajax && method_exists($this, 'ajaxPreprocess'))
			$this->ajaxPreProcess();

		$this->context->smarty->assign(array(
			'table' => $this->table,
			'current' => self::$currentIndex,
			'token' => $this->token,
		));

		$this->context->smarty->assign('submit_form_ajax', (int)Tools::getValue('submitFormAjax'));

		$this->initProcess();
	}

	public function initShopContext()
	{
		// Change shop context ?
		if (Shop::isFeatureActive() && Tools::getValue('setShopContext') !== false)
		{
			$this->context->cookie->shopContext = Tools::getValue('setShopContext');
			$url = parse_url($_SERVER['REQUEST_URI']);
			$query = (isset($url['query'])) ? $url['query'] : '';
			parse_str($query, $parse_query);
			unset($parse_query['setShopContext'], $parse_query['conf']);
			$this->redirect_after = $url['path'].'?'.http_build_query($parse_query);
		}
		elseif (!Shop::isFeatureActive())
			$this->context->cookie->shopContext = 's-1';

		$shop_id = '';
		Shop::setContext(Shop::CONTEXT_ALL);
		if ($this->context->cookie->shopContext)
		{
			$split = explode('-', $this->context->cookie->shopContext);
			if (count($split) == 2)
			{
				if ($split[0] == 'g')
					Shop::setContext(Shop::CONTEXT_GROUP, $split[1]);
				else
				{
					Shop::setContext(Shop::CONTEXT_SHOP, $split[1]);
					$shop_id = $split[1];
				}
			}
		}
		elseif ($this->context->employee->id_profile == _PS_ADMIN_PROFILE_)
			$shop_id = '';
		elseif ($this->context->shop->getTotalShopsWhoExists() != Employee::getTotalEmployeeShopById((int)$this->context->employee->id))
		{
			$shops = Employee::getEmployeeShopById((int)$this->context->employee->id);
			if (count($shops))
				$shop_id = (int)$shops[0];
		}
		else
			Employee::getEmployeeShopAccess((int)$this->context->employee->id);

		// Check multishop context and set right context if need
		if (!($this->multishop_context & Shop::getContext()))
		{
			if (Shop::getContext() == Shop::CONTEXT_SHOP && !($this->multishop_context & Shop::CONTEXT_SHOP))
				Shop::setContext(Shop::CONTEXT_GROUP, Shop::getContextGroupShopID());
			if (Shop::getContext() == Shop::CONTEXT_GROUP && !($this->multishop_context & Shop::CONTEXT_GROUP))
				Shop::setContext(Shop::CONTEXT_ALL);
		}

		// Replace existing shop if necessary
		if (!$shop_id)
			$this->context->shop = new Shop(Configuration::get('PS_SHOP_DEFAULT'));
		elseif ($this->context->shop->id != $shop_id)
			$this->context->shop = new Shop($shop_id);
	}

	/**
	 * Retrieve GET and POST value and translate them to actions
	 */
	public function initProcess()
	{
		// Filter memorization
		if (isset($_POST) && !empty($_POST) && isset($this->table))
			foreach ($_POST as $key => $value)
				if (is_array($this->table))
				{
					foreach ($this->table as $table)
						if (strncmp($key, $table.'Filter_', 7) === 0 || strncmp($key, 'submitFilter', 12) === 0)
							$this->context->cookie->$key = !is_array($value) ? $value : serialize($value);
				}
				elseif (strncmp($key, $this->table.'Filter_', 7) === 0 || strncmp($key, 'submitFilter', 12) === 0)
					$this->context->cookie->$key = !is_array($value) ? $value : serialize($value);
		if (isset($_GET) && !empty($_GET) && isset($this->table))
			foreach ($_GET as $key => $value)
				if (is_array($this->table))
				{
					foreach ($this->table as $table)
						if (strncmp($key, $table.'OrderBy', 7) === 0 || strncmp($key, $table.'Orderway', 8) === 0)
							$this->context->cookie->$key = $value;
				}
				elseif (strncmp($key, $this->table.'OrderBy', 7) === 0 || strncmp($key, $this->table.'Orderway', 12) === 0)
					$this->context->cookie->$key = $value;

		// Manage list filtering
		if (Tools::isSubmit('submitFilter'.$this->table) || $this->context->cookie->{'submitFilter'.$this->table} !== false)
			$this->filter = true;

		$this->id_object = (int)Tools::getValue('id_'.$this->table);

		/* Delete object image */
		if (isset($_GET['deleteImage']))
		{
			if ($this->tabAccess['delete'] === '1')
				$this->action = 'delete_image';
			else
				$this->errors[] = Tools::displayError('You do not have permission to delete here.');
		}
		/* Delete object */
		elseif (isset($_GET['delete'.$this->table]))
		{
			if ($this->tabAccess['delete'] === '1')
				$this->action = 'delete';
			else
				$this->errors[] = Tools::displayError('You do not have permission to delete here.');
		}
		/* Change object statuts (active, inactive) */
		elseif ((isset($_GET['status'.$this->table]) || isset($_GET['status'])) && Tools::getValue($this->identifier))
		{
			if ($this->tabAccess['edit'] === '1')
				$this->action = 'status';
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		/* Move an object */
		elseif (isset($_GET['position']))
		{
			if ($this->tabAccess['edit'] == '1')
				$this->action = 'position';
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		elseif ($submitted_action = Tools::getValue('submitAction'.$this->table))
				$this->action = $submitted_action;
		elseif (Tools::getValue('submitAdd'.$this->table)
				 || Tools::getValue('submitAdd'.$this->table.'AndStay')
				 || Tools::getValue('submitAdd'.$this->table.'AndPreview'))
		{
			// case 1: updating existing entry
			if ($this->id_object)
			{
				if ($this->tabAccess['edit'] === '1')
				{
					$this->action = 'save';
					if (Tools::getValue('submitAdd'.$this->table.'AndStay'))
						$this->display = 'edit';
					else
						$this->display = 'list';
				}
				else
					$this->errors[] = Tools::displayError('You do not have permission to edit here.');
			}
			// case 2: creating new entry
			else
			{
				if ($this->tabAccess['add'] === '1')
				{
					$this->action = 'save';
					if (Tools::getValue('submitAdd'.$this->table.'AndStay'))
						$this->display = 'edit';
					else
						$this->display = 'list';
				}
				else
					$this->errors[] = Tools::displayError('You do not have permission to add here.');
			}
		}
		elseif (isset($_GET['add'.$this->table]))
		{
			if ($this->tabAccess['add'] === '1')
			{
				$this->action = 'new';
				$this->display = 'add';
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to add here.');
		}
		elseif (isset($_GET['update'.$this->table]) && isset($_GET['id_'.$this->table]))
		{
			$this->display = 'edit';
			if ($this->tabAccess['edit'] !== '1')
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		elseif (isset($_GET['view'.$this->table]))
		{
			if ($this->tabAccess['view'] === '1')
			{
				$this->display = 'view';
				$this->action = 'view';
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to view here.');
		}
		/* Cancel all filters for this tab */
		elseif (isset($_POST['submitReset'.$this->table]))
			$this->action = 'reset_filters';
		/* Submit options list */
		elseif (Tools::getValue('submitOptions'.$this->table) || Tools::getValue('submitOptions'))
		{
			$this->display = 'options';
			if ($this->tabAccess['edit'] === '1')
				$this->action = 'update_options';
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		elseif (Tools::isSubmit('submitFields') && $this->required_database && $this->tabAccess['add'] === '1' && $this->tabAccess['delete'] === '1')
			$this->action = 'update_fields';
		elseif (is_array($this->bulk_actions))
			foreach ($this->bulk_actions as $bulk_action => $params)
			{
				if (Tools::isSubmit('submitBulk'.$bulk_action.$this->table) || Tools::isSubmit('submitBulk'.$bulk_action))
				{
					if ($this->tabAccess['edit'] === '1')
					{
						$this->action = 'bulk'.$bulk_action;
						$this->boxes = Tools::getValue($this->table.'Box');
					}
					else
						$this->errors[] = Tools::displayError('You do not have permission to edit here.');
					break;
				}
				elseif (Tools::isSubmit('submitBulk'))
				{
					if ($this->tabAccess['edit'] === '1')
					{
						$this->action = 'bulk'.Tools::getValue('select_submitBulk');
						$this->boxes = Tools::getValue($this->table.'Box');
					}
					else
						$this->errors[] = Tools::displayError('You do not have permission to edit here.');
					break;
				}
			}
		elseif (!empty($this->fields_options) && empty($this->fields_list))
			$this->display = 'options';
	}

	/**
	 * Get the current objects' list form the database
	 *
	 * @param integer $id_lang Language used for display
	 * @param string $order_by ORDER BY clause
	 * @param string $_orderWay Order way (ASC, DESC)
	 * @param integer $start Offset in LIMIT clause
	 * @param integer $limit Row count in LIMIT clause
	 */
	public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
	{
		/* Manage default params values */
		$use_limit = true;
		if ($limit === false)
			$use_limit = false;
		elseif (empty($limit))
		{
			if (isset($this->context->cookie->{$this->table.'_pagination'}) && $this->context->cookie->{$this->table.'_pagination'})
				$limit = $this->context->cookie->{$this->table.'_pagination'};
			else
				$limit = $this->_pagination[1];
		}

		if (!Validate::isTableOrIdentifier($this->table))
			throw new PrestaShopException(sprintf('Table name %s is invalid:', $this->table));

		if (empty($order_by))
		{
			if ($this->context->cookie->{$this->table.'Orderby'})
				$order_by = $this->context->cookie->{$this->table.'Orderby'};
			elseif ($this->_orderBy)
				$order_by = $this->_orderBy;
			else
				$order_by = $this->_defaultOrderBy;
		}

		if (empty($order_way))
		{
			if ($this->context->cookie->{$this->table.'Orderway'})
				$order_way = $this->context->cookie->{$this->table.'Orderway'};
			elseif ($this->_orderWay)
				$order_way = $this->_orderWay;
			else
				$order_way = $this->_defaultOrderWay;
		}

		$limit = (int)Tools::getValue('pagination', $limit);
		$this->context->cookie->{$this->table.'_pagination'} = $limit;

		/* Check params validity */
		if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)
			|| !is_numeric($start) || !is_numeric($limit)
			|| !Validate::isUnsignedId($id_lang))
			throw new PrestaShopException('get list params is not valid');

		/* Determine offset from current page */
		if ((isset($_POST['submitFilter'.$this->table]) ||
		isset($_POST['submitFilter'.$this->table.'_x']) ||
		isset($_POST['submitFilter'.$this->table.'_y'])) &&
		!empty($_POST['submitFilter'.$this->table]) &&
		is_numeric($_POST['submitFilter'.$this->table]))
			$start = ((int)$_POST['submitFilter'.$this->table] - 1) * $limit;

		/* Cache */
		$this->_lang = (int)$id_lang;
		$this->_orderBy = (strpos($order_by, '.') !== false) ? substr($order_by, strpos($order_by, '.') + 1) : $order_by;
		$this->_orderWay = Tools::strtoupper($order_way);

		/* SQL table : orders, but class name is Order */
		$sql_table = $this->table == 'order' ? 'orders' : $this->table;

		// Add SQL shop restriction
		$select_shop = $join_shop = $where_shop = '';
		if ($this->shopLinkType)
		{
			$select_shop = ', shop.name as shop_name ';
			$join_shop = ' LEFT JOIN '._DB_PREFIX_.$this->shopLinkType.' shop
							ON a.id_'.$this->shopLinkType.' = shop.id_'.$this->shopLinkType;
			$where_shop = Shop::addSqlRestriction($this->shopShareDatas, 'a', $this->shopLinkType);
		}

		if ($this->multishop_context)
		{
			$assos = Shop::getAssoTables();
			$assos_group = GroupShop::getAssoTables();
			if (isset($assos[$this->table]) && $assos[$this->table]['type'] == 'shop')
			{
				$filter_key = $assos[$this->table]['type'];
				$idenfier_shop = Shop::getContextListShopID();
			}
			elseif (isset($assos_group[$this->table]) && $assos_group[$this->table]['type'] == 'group_shop')
			{
				$filter_key = $assos_group[$this->table]['type'];
				$idenfier_shop = array(Shop::getContextGroupShopID());
			}
		}

		$filter_shop = '';
		if (isset($filter_key))
		{
			if (!$this->_group)
				$this->_group = ' GROUP BY a.'.pSQL($this->identifier);
			elseif (!preg_match('#(\s|,)\s*a\.`?'.pSQL($this->identifier).'`?(\s|,|$)#', $this->_group))
				$this->_group .= ', a.'.pSQL($this->identifier);

			$test_join = !preg_match('#`?'.preg_quote(_DB_PREFIX_.$this->table.'_'.$filter_key).'`? *sa#', $this->_join);
			if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_ALL && $test_join)
			{
				$filter_shop = ' JOIN `'._DB_PREFIX_.$this->table.'_'.$filter_key.'` sa ';
				$filter_shop .= 'ON (sa.'.$this->identifier.' = a.'.$this->identifier.' AND sa.id_'.$filter_key.' IN ('.implode(', ', $idenfier_shop).'))';
			}
		}

		/* Query in order to get results with all fields */
		$lang_join = '';
		if ($this->lang)
		{
			$lang_join = 'LEFT JOIN `'._DB_PREFIX_.$this->table.'_lang` b ON (b.`'.$this->identifier.'` = a.`'.$this->identifier.'`';
			$lang_join .= ' AND b.`id_lang` = '.(int)$id_lang;
			if ($id_lang_shop)
			 	 $lang_join .= ' AND b.`id_shop`='.(int)$id_lang_shop;
			$lang_join .= ')';
		}

		$having_clause = '';
		if (isset($this->_filterHaving) || isset($this->_having))
		{
			 $having_clause = ' HAVING ';
			 if (isset($this->_filterHaving))
			 	$having_clause .= ltrim($this->_filterHaving, ' AND ');
			 if (isset($this->_having))
			 	$having_clause .= $this->_having.' ';
		}

		if (strpos($order_by, '.') > 0)
		{
			$order_by = explode('.', $order_by);
			$order_by = pSQL($order_by[0]).'.`'.pSQL($order_by[1]).'`';
		}

		$sql = 'SELECT SQL_CALC_FOUND_ROWS
			'.($this->_tmpTableFilter ? ' * FROM (SELECT ' : '').'
			'.($this->lang ? 'b.*, ' : '').'a.*'.(isset($this->_select) ? ', '.$this->_select.' ' : '').$select_shop.'
			FROM `'._DB_PREFIX_.$sql_table.'` a
			'.$filter_shop.'
			'.$lang_join.'
			'.(isset($this->_join) ? $this->_join.' ' : '').'
			'.$join_shop.'
			WHERE 1 '.(isset($this->_where) ? $this->_where.' ' : '').($this->deleted ? 'AND a.`deleted` = 0 ' : '').
			(isset($this->_filter) ? $this->_filter : '').$where_shop.'
			'.(isset($this->_group) ? $this->_group.' ' : '').'
			'.$having_clause.'
			ORDER BY '.(($order_by == $this->identifier) ? 'a.' : '').pSQL($order_by).' '.pSQL($order_way).
			($this->_tmpTableFilter ? ') tmpTable WHERE 1'.$this->_tmpTableFilter : '').
			(($use_limit === true) ? ' LIMIT '.(int)$start.','.(int)$limit : '');

		$this->_list = Db::getInstance()->executeS($sql);
		$this->_listTotal = Db::getInstance()->getValue('SELECT FOUND_ROWS() AS `'._DB_PREFIX_.$this->table.'`');
	}

	public function getLanguages()
	{
		$cookie = $this->context->cookie;
		$this->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		if ($this->allow_employee_form_lang && !$cookie->employee_form_lang)
			$cookie->employee_form_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		$use_lang_from_cookie = false;
		$this->_languages = Language::getLanguages(false);
		if ($this->allow_employee_form_lang)
			foreach ($this->_languages as $lang)
				if ($cookie->employee_form_lang == $lang['id_lang'])
					$use_lang_from_cookie = true;
		if (!$use_lang_from_cookie)
			$this->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
		else
			$this->default_form_language = (int)$cookie->employee_form_lang;

		foreach ($this->_languages as $k => $language)
			$this->_languages[$k]['is_default'] = (int)($language['id_lang'] == $this->default_form_language);

		return $this->_languages;
	}


	/**
	 * Return the list of fields value
	 *
	 * @param object $obj Object
	 * @return array
	 */
	public function getFieldsValue($obj)
	{
		foreach ($this->fields_form as $fieldset)
			if (isset($fieldset['form']['input']))
				foreach ($fieldset['form']['input'] as $input)
					if (!isset($this->fields_value[$input['name']]))
						if (isset($input['type']) && ($input['type'] == 'group_shop' || $input['type'] == 'shop'))
						{
							if ($obj->id)
							{
								if ($input['type'] == 'group_shop')
									$result = GroupShop::getGroupShopById((int)$obj->id, $this->identifier, $this->table);
								else
									$result = Shop::getShopById((int)$obj->id, $this->identifier, $this->table);

								foreach ($result as $row)
									$this->fields_value['shop'][$row['id_'.$input['type']]][] = $row[$this->identifier];
							}
						}
						elseif (isset($input['lang']) && $input['lang'])
							foreach ($this->_languages as $language)
							{
								$fieldValue = $this->getFieldValue($obj, $input['name'], $language['id_lang']);
								if (empty($fieldValue))
								{
									if (isset($input['default_value']) && is_array($input['default_value']) && isset($input['default_value'][$language['id_lang']]))
										$fieldValue = $input['default_value'][$language['id_lang']];
									elseif (isset($input['default_value']))
										$fieldValue = $input['default_value'];
								}
								$this->fields_value[$input['name']][$language['id_lang']] = $fieldValue;
							}
						else
						{
							$fieldValue = $this->getFieldValue($obj, $input['name']);
							if (empty($fieldValue) && isset($input['default_value']))
								$fieldValue = $input['default_value'];
							$this->fields_value[$input['name']] = $fieldValue;
						}

		return $this->fields_value;
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
	public function getFieldValue($obj, $key, $id_lang = null)
	{
		if ($id_lang)
			$default_value = ($obj->id && isset($obj->{$key}[$id_lang])) ? $obj->{$key}[$id_lang] : '';
		else
			$default_value = isset($obj->{$key}) ? $obj->{$key} : '';

		return Tools::getValue($key.($id_lang ? '_'.$id_lang : ''), $default_value);
	}

	/**
	 * Manage page display (form, list...)
	 *
	 * @param string $className Allow to validate a different class than the current one
	 */
	public function validateRules($class_name = false)
	{
		if (!$class_name)
			$class_name = $this->className;

		/* Class specific validation rules */
		$rules = call_user_func(array($class_name, 'getValidationRules'), $class_name);

		if ((count($rules['requiredLang']) || count($rules['sizeLang']) || count($rules['validateLang'])))
		{
			/* Language() instance determined by default language */
			$default_language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));

			/* All availables languages */
			$languages = Language::getLanguages(false);
		}

		/* Checking for required fields */
		foreach ($rules['required'] as $field)
			if (($value = Tools::getValue($field)) == false && (string)$value != '0')
				if (!Tools::getValue($this->identifier) || ($field != 'passwd' && $field != 'no-picture'))
					$this->errors[] = $this->l('the field').
						' <b>'.call_user_func(array($class_name, 'displayFieldName'), $field, $class_name).'</b> '.
						$this->l('is required');

		/* Checking for multilingual required fields */
		foreach ($rules['requiredLang'] as $field_lang)
			if (($empty = Tools::getValue($field_lang.'_'.$default_language->id)) === false || $empty !== '0' && empty($empty))
				$this->errors[] = $this->l('the field').
					' <b>'.call_user_func(array($class_name, 'displayFieldName'), $field_lang, $class_name).'</b> '.
					$this->l('is required at least in').' '.$default_language->name;

		/* Checking for maximum fields sizes */
		foreach ($rules['size'] as $field => $max_length)
			if (Tools::getValue($field) !== false && Tools::strlen(Tools::getValue($field)) > $max_length)
				$this->errors[] = $this->l('the field').
					' <b>'.call_user_func(array($class_name, 'displayFieldName'), $field, $class_name).'</b> '.
					$this->l('is too long').' ('.$max_length.' '.$this->l('chars max').')';

		/* Checking for maximum multilingual fields size */
		foreach ($rules['sizeLang'] as $field_lang => $max_length)
			foreach ($languages as $language)
			{
				$field_lang = Tools::getValue($field_lang.'_'.$language['id_lang']);
				if ($field_lang !== false && Tools::strlen($field_lang) > $max_length)
					$this->errors[] = $this->l('the field').
						' <b>'.call_user_func(array($class_name, 'displayFieldName'), $field_lang, $class_name).' ('.$language['name'].')</b> '.
						$this->l('is too long').' ('.$max_length.' '.$this->l('chars max, html chars including').')';
			}
		/* Overload this method for custom checking */
		$this->_childValidation();

		/* Checking for fields validity */
		foreach ($rules['validate'] as $field => $function)
			if (($value = Tools::getValue($field)) !== false && ($field != 'passwd'))
				if (!Validate::$function($value) && !empty($value))
					$this->errors[] = $this->l('the field').
						' <b>'.call_user_func(array($class_name, 'displayFieldName'), $field, $class_name).'</b> '.
						$this->l('is invalid');

		/* Checking for passwd_old validity */
		if (($value = Tools::getValue('passwd')) != false)
		{
			if ($class_name == 'Employee' && !Validate::isPasswdAdmin($value))
				$this->errors[] = $this->l('the field').
					' <b>'.call_user_func(array($class_name, 'displayFieldName'), 'passwd', $class_name).'</b> '.
					$this->l('is invalid');
			elseif ($class_name == 'Customer' && !Validate::isPasswd($value))
				$this->errors[] = $this->l('the field').
					' <b>'.call_user_func(array($class_name, 'displayFieldName'), 'passwd', $class_name).
					'</b> '.$this->l('is invalid');
		}

		/* Checking for multilingual fields validity */
		foreach ($rules['validateLang'] as $field_lang => $function)
			foreach ($languages as $language)
				if (($value = Tools::getValue($field_lang.'_'.$language['id_lang'])) !== false && !empty($value))
					if (!Validate::$function($value))
						$this->errors[] = $this->l('the field').
							' <b>'.call_user_func(array($class_name, 'displayFieldName'), $field_lang, $class_name).' ('.$language['name'].')</b> '.
							$this->l('is invalid');
	}

	/**
	 * Overload this method for custom checking
	 */
	protected function _childValidation()
	{
	}

	/**
	 * Display object details
	 */
	public function viewDetails()
	{
	}

	/**
	 * Called before deletion
	 *
	 * @param object $object Object
	 * @return boolean
	 */
	protected function beforeDelete($object)
	{
		return false;
	}

	/**
	 * Called before deletion
	 *
	 * @param object $object Object
	 * @return boolean
	 */
	protected function afterDelete($object, $oldId)
	{
		return true;
	}

	protected function afterAdd($object)
	{
		return true;
	}

	protected function afterUpdate($object)
	{
		return true;
	}

	/**
	 * Check rights to view the current tab
	 *
	 * @return boolean
	 */

	protected function afterImageUpload()
	{
		return true;
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
		foreach ($_POST as $key => $value)
			if (key_exists($key, $object) && $key != 'id_'.$table)
			{
				/* Do not take care of password field if empty */
				if ($key == 'passwd' && Tools::getValue('id_'.$table) && empty($value))
					continue;
				/* Automatically encrypt password in MD5 */
				if ($key == 'passwd' && !empty($value))
					$value = Tools::encrypt($value);
				$object->{$key} = $value;
			}

		/* Multilingual fields */
		$rules = call_user_func(array(get_class($object), 'getValidationRules'), get_class($object));
		if (count($rules['validateLang']))
		{
			$languages = Language::getLanguages(false);
			foreach ($languages as $language)
				foreach (array_keys($rules['validateLang']) as $field)
					if (isset($_POST[$field.'_'.(int)$language['id_lang']]))
						$object->{$field}[(int)$language['id_lang']] = $_POST[$field.'_'.(int)$language['id_lang']];
		}
	}

	/**
	 * Returns an array with selected shops and type (group or boutique shop)
	 *
	 * @param string $table
	 * @param int $id_object
	 */
	protected function getAssoShop($table, $id_object = false)
	{
		$shop_asso = Shop::getAssoTables();
		$group_shop_asso = GroupShop::getAssoTables();
		if (isset($shop_asso[$table]) && $shop_asso[$table]['type'] == 'shop')
			$type = 'shop';
		elseif (isset($group_shop_asso[$table]) && $group_shop_asso[$table]['type'] == 'group_shop')
			$type = 'group_shop';
		else
			return;

		$assos = array();
		if (Tools::isSubmit('checkBox'.Tools::toCamelCase($type, true).'Asso_'.$table))
		{
			$check_box = Tools::getValue('checkBox'.Tools::toCamelCase($type, true).'Asso_'.$table);
			foreach ($check_box as $id_asso_object => $row)
			{
				if ($id_object)
					$id_asso_object = $id_object;
				foreach ($row as $id_shop => $value)
					$assos[] = array('id_object' => (int)$id_asso_object, 'id_'.$type => (int)$id_shop);
			}
		}
		return array($assos, $type);
	}

	/**
	 * Update the associations of shops
	 *
	 * @param int $id_object
	 * @param int $new_id_object
	 */
	protected function updateAssoShop($id_object = false, $new_id_object = false)
	{
		if (!Shop::isFeatureActive())
			return;

		$assos_data = $this->getAssoShop($this->table, $id_object);
		$assos = $assos_data[0];
		$type = $assos_data[1];

		if (!$type)
			return;

		Db::getInstance()->execute('
			DELETE FROM '._DB_PREFIX_.$this->table.'_'.$type.($id_object ? '
			WHERE `'.$this->identifier.'`='.(int)$id_object : ''));

		foreach ($assos as $asso)
			Db::getInstance()->execute('
				INSERT INTO '._DB_PREFIX_.$this->table.'_'.$type.' (`'.pSQL($this->identifier).'`, id_'.$type.')
				VALUES('.($new_id_object ? $new_id_object : (int)$asso['id_object']).', '.(int)$asso['id_'.$type].')');
	}

	protected function validateField($value, $field)
	{
		if (isset($field['validation']))
		{
			$valid_method_exists = method_exists('Validate', $field['validation']);
			if ((!isset($field['empty']) || !$field['empty'] || (isset($field['empty']) && $field['empty'] && $value)) && $valid_method_exists)
			{
				if (!Validate::$field['validation']($value))
				{
					$this->errors[] = Tools::displayError($field['title'].' : Incorrect value');
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Can be overriden
	 */
	public function beforeUpdateOptions()
	{
	}

	/**
	 * Overload this method for custom checking
	 *
	 * @param integer $id Object id used for deleting images
	 * @return boolean
	 */
	protected function postImage($id)
	{
		if (isset($this->fieldImageSettings['name']) && isset($this->fieldImageSettings['dir']))
			return $this->uploadImage($id, $this->fieldImageSettings['name'], $this->fieldImageSettings['dir'].'/');
		elseif (!empty($this->fieldImageSettings))
			foreach ($this->fieldImageSettings as $image)
				if (isset($image['name']) && isset($image['dir']))
					$this->uploadImage($id, $image['name'], $image['dir'].'/');
		return !count($this->errors) ? true : false;
	}

	protected function uploadImage($id, $name, $dir, $ext = false, $width = null, $height = null)
	{
		if (isset($_FILES[$name]['tmp_name']) && !empty($_FILES[$name]['tmp_name']))
		{
			// Delete old image
			if (Validate::isLoadedObject($object = $this->loadObject()))
				$object->deleteImage();
			else
				return false;

			// Check image validity
			$max_size = isset($this->max_image_size) ? $this->max_image_size : 0;
			if ($error = ImageManager::validateUpload($_FILES[$name], Tools::getMaxUploadSize($max_size)))
				$this->errors[] = $error;

			$tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
			if (!$tmp_name)
				return false;

			if (!move_uploaded_file($_FILES[$name]['tmp_name'], $tmp_name))
				return false;

			// Copy new image
			if (!ImageManager::resize($tmp_name, _PS_IMG_DIR_.$dir.$id.'.'.$this->imageType, (int)$width, (int)$height, ($ext ? $ext : $this->imageType)))
				$this->errors[] = Tools::displayError('An error occurred while uploading image.');
			if (count($this->errors))
				return false;
			if ($this->afterImageUpload())
			{
				unlink($tmp_name);
				return true;
			}
			return false;
		}
		return true;
	}

	/**
	 * Delete multiple items
	 *
	 * @return boolean true if succcess
	 */
	protected function processBulkDelete()
	{
		if (is_array($this->boxes) && !empty($this->boxes))
		{
			$object = new $this->className();

			if (isset($object->noZeroObject))
			{
				$objects_count = count(call_user_func(array($this->className, $object->noZeroObject)));

				// Check if all object will be deleted
				if ($objects_count <= 1 || count($this->boxes) == $objects_count)
					$this->errors[] = Tools::displayError('You need at least one object.').
						' <b>'.$this->table.'</b><br />'.
						Tools::displayError('You cannot delete all of the items.');
			}
			else
			{
				$result = true;
				if ($this->deleted)
				{
					foreach ($this->boxes as $id)
					{
						$to_delete = new $this->className($id);
						$to_delete->deleted = 1;
						$result = $result && $to_delete->update();
					}
				}
				else
					$result = $object->deleteSelection(Tools::getValue($this->table.'Box'));

				if ($result)
					$this->redirect_after = self::$currentIndex.'&conf=2&token='.$this->token;
				$this->errors[] = Tools::displayError('An error occurred while deleting selection.');
			}
		}
		else
			$this->errors[] = Tools::displayError('You must select at least one element to delete.');

		if (isset($result))
			return $result;
		else
			return false;
	}

	protected function processBulkAffectZone()
	{
		if (is_array($this->boxes) && !empty($this->boxes))
		{
			$object = new $this->className();
			$result = $object->affectZoneToSelection(Tools::getValue($this->table.'Box'), Tools::getValue('zone_to_affect'));

			if ($result)
				$this->redirect_after = self::$currentIndex.'&conf=28&token='.$this->token;
			$this->errors[] = Tools::displayError('An error occurred while affecting a zone to the selection.');
		}
		else
			$this->errors[] = Tools::displayError('You must select at least one element to affect a new zone.');

		return $result;
	}

	/**
	  * @TODO delete method after AdminProducts cleanup
	  * Display flags in forms for translations
	  *
	  * @param array $languages All languages available
	  * @param integer $default_language Default language id
	  * @param string $ids Multilingual div ids in form
	  * @param string $id Current div id]
	  * @param boolean $use_vars_instead_of_ids use an js vars instead of ids seperate by "¤"
	  *
		* @param return define the return way : false for a display, true for a return
		*
		*	@return string
	  */
	public function getTranslationsFlags($languages, $default_language, $ids, $id, $return = false, $use_vars_instead_of_ids = false)
	{
		if (count($languages) == 1)
			return false;
		$output = '
		<div class="displayed_flag">
			<img src="../img/l/'.$default_language.'.jpg" class="pointer" id="language_current_'.$id.'" onclick="toggleLanguageFlags(this);" alt="" />
		</div>
		<div id="languages_'.$id.'" class="language_flags">
			'.$this->l('Choose language:').'<br /><br />';
		foreach ($languages as $language)
			if ($use_vars_instead_of_ids)
				$output .= '<img src="../img/l/'.(int)$language['id_lang'].'.jpg" class="pointer" alt="'.$language['name'].'" title="'.$language['name'].'"
								onclick="changeLanguage(\''.$id.'\', '.$ids.', '.$language['id_lang'].', \''.$language['iso_code'].'\');" /> ';
			else
				$output .= '<img src="../img/l/'.(int)$language['id_lang'].'.jpg" class="pointer" alt="'.$language['name'].'" title="'.$language['name'].'"
								onclick="changeLanguage(\''.$id.'\', \''.$ids.'\', '.$language['id_lang'].', \''.$language['iso_code'].'\');" /> ';
		$output .= '</div>';

		if ($return)
			return $output;

		return $output;
	}

	/**
	 * Called before Add
	 *
	 * @param object $object Object
	 * @return boolean
	 */
	protected function beforeAdd($object)
	{
		return true;
	}

	/**
	 * prepare the view to display the required fields form
	 */
	public function displayRequiredFields()
	{
		if (!$this->tabAccess['add'] || !$this->tabAccess['delete'] === '1' || !$this->required_database)
			return;

		$helper = new Helper();
		$helper->currentIndex = self::$currentIndex;
		$helper->token = $this->token;
		return $helper->renderRequiredFields($this->className, $this->identifier, $this->required_fields);
	}

	/**
	 * Create a template from the override file, else from the base file.
	 *
	 * @param string $tpl_name filename
	 * @return Template
	 */
	public function createTemplate($tpl_name)
	{
		// Use override tpl if it exists
		// If view access is denied, we want to use the default template that will be used to display an error
		if ($this->viewAccess()
			&& $this->override_folder
			&& file_exists($this->context->smarty->getTemplateDir(0).'controllers/'.$this->override_folder.$tpl_name))
			return $this->context->smarty->createTemplate('controllers/'.$this->override_folder.$tpl_name, $this->context->smarty);

		return $this->context->smarty->createTemplate($this->context->smarty->getTemplateDir(0).$tpl_name, $this->context->smarty);
	}
}

