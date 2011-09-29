<?php
class AdminControllerCore extends Controller
{
	public $path;

	public static $currentIndex;
	public $content;
	public $warnings;

	public $content_only = false;
	public $layout = 'layout.tpl';

	public $meta_title = 'Administration panel';

	public $template = '';

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

	/** @var string Security token */
	public $token;

	protected $_object;

	/** @var string shop | group_shop */
	public $shopLinkType;

	/** @var string Default ORDER BY clause when $_orderBy is not defined */
	protected $_defaultOrderBy = false;

	/** @var array Errors displayed after post processing */
	public $_errors = array();

	protected $list_display;

	protected $shopLink;

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

	/** @var string Order way (ASC, DESC) determined by arrows in list header */
	protected $_orderWay;

	protected $bulk_action;

	protected $is_cms = false;

	protected $is_dnd_identifier = false;

	protected $identifiersDnd = array('id_product' => 'id_product', 'id_category' => 'id_category_to_move','id_cms_category' => 'id_cms_category_to_move', 'id_cms' => 'id_cms', 'id_attribute' => 'id_attribute');
	protected $view;
	protected $edit;
	protected $delete;
	protected $duplicate;
	protected $noLink;
	protected $specificConfirmDelete;
	protected $colorOnBackground;
	/** @string Action to perform : 'edit', 'view', 'add', ... */
	protected $action;
	protected $_includeContainer = true;

	public function __construct()
	{
	// retro-compatibility : className for admin without controller
	// This can be overriden in controllers (like for AdminCategories or AdminProducts
		$controller = get_class($this);
		// @todo : move this in class AdminCategoriesController and AdminProductsController
		if ($controller == 'AdminCategoriesController' && $controller == 'AdminProductsController')
			$controller = 'AdminCatalogController';

		// temporary fix for Token retrocompatibility
		// This has to be done when url is built instead of here)
		if(strpos($controller,'Controller'))
			$controller = substr($controller,0,-10);

		parent::__construct();

		// if this->template is empty,
		// generate the filename from the classname, without "Controller" suffix
		if (empty($this->template))
		{
			$tpl_name = substr(get_class($this), 0, -10).'.tpl';
			$tpl_name[0] = strtolower($tpl_name[0]);
			if (file_exists($this->context->smarty->template_dir.'/'.$tpl_name))
				$this->template = $tpl_name;
		}

		$this->id = Tab::getIdFromClassName($controller);
		$this->token = Tools::getAdminToken($controller.(int)$this->id.(int)$this->context->employee->id);

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
			23 => $this->l('Localization pack imported successfully'), 24 => $this->l('Refund Successful'),
			25 => $this->l('Images successfully moved'),
		);
		if (!$this->identifier) $this->identifier = 'id_'.$this->table;
		if (!$this->_defaultOrderBy) $this->_defaultOrderBy = $this->identifier;
		$this->tabAccess = Profile::getProfileAccess($this->context->employee->id_profile, $this->id);

		// Fix for AdminHome
		if ($controller == 'AdminHome')
			$_POST['token'] = $this->token;

		if (!Shop::isMultiShopActivated())
			$this->shopLinkType = '';
	}

	/**
	 * Check rights to view the current tab
	 *
	 * @return boolean
	 */
	public function viewAccess($disable = false)
	{
		if ($disable)
			return true;
		$this->tabAccess = Profile::getProfileAccess($this->context->employee->id_profile, $this->id);

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

	public function postProcess()
	{
		if ($this->ajax)
		{
			// from ajax-tab.php
			if (method_exists($this, 'ajaxPreprocess'))
				$this->ajaxPreProcess();

			$action = Tools::getValue('action');
			// no need to use displayConf() here
			if (!empty($action) && method_exists($this, 'ajaxProcess'.Tools::toCamelCase($action)))
				$this->{'ajaxProcess'.Tools::toCamelCase($action)}();
			else
				$this->ajaxProcess();

			// @TODO We should use a displayAjaxError
			/*$this->displayErrors();
			if (!empty($action) && method_exists($this, 'displayAjax'.Tools::toCamelCase($action)))
				$this->{'displayAjax'.$action}();
			else
				$this->displayAjax();	*/
		}
		else
		{
			if (!isset($this->table))
				return false;
			// set token
			$token = Tools::getValue('token') ? Tools::getValue('token') : $this->token;

			// Sub included tab postProcessing
			$this->includeSubTab('postProcess', array('status', 'submitAdd1', 'submitDel', 'delete', 'submitFilter', 'submitReset'));

			/* Delete object image */
			if (isset($_GET['deleteImage']))
			{
				if (Validate::isLoadedObject($object = $this->loadObject()))
					if (($object->deleteImage()))
						Tools::redirectAdmin(self::$currentIndex.'&add'.$this->table.'&'.$this->identifier.'='.Tools::getValue($this->identifier).'&conf=7&token='.$token);
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
							if ($this->deleted)
							{
								$object->deleteImage();
								$object->deleted = 1;
								if ($object->update())
									Tools::redirectAdmin(self::$currentIndex.'&conf=1&token='.$token);
							}
							elseif ($object->delete())
							{
								if(method_exists($object, 'cleanPositions'))
									$object->cleanPositions();
								Tools::redirectAdmin(self::$currentIndex.'&conf=1&token='.$token);
							}
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
							Tools::redirectAdmin(self::$currentIndex.'&conf=5'.((($id_category = (int)(Tools::getValue('id_category'))) AND Tools::getValue('id_product')) ? '&id_category='.$id_category : '').'&token='.$token);
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
					Tools::redirectAdmin(self::$currentIndex.'&'.$this->table.'Orderby=position&'.$this->table.'Orderway=asc&conf=5'.(($id_category = (int)(Tools::getValue($this->identifier))) ? ('&'.$this->identifier.'='.$id_category) : '').'&token='.$token);
					 Tools::redirectAdmin(self::$currentIndex.'&'.$this->table.'Orderby=position&'.$this->table.'Orderway=asc&conf=5'.((($id_category = (int)(Tools::getValue('id_category'))) AND Tools::getValue('id_product')) ? '&id_category='.$id_category : '').'&token='.$token);
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
								Tools::redirectAdmin(self::$currentIndex.'&conf=2&token='.$token);
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
						if ($this->tabAccess['edit'] === '1' OR ($this->table == 'employee' AND $this->context->employee->id == Tools::getValue('id_employee') AND Tools::isSubmit('updateemployee')))
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

								if ($object->id)
									$this->updateAssoShop($object->id);

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
										Tools::redirectAdmin(self::$currentIndex.'&'.$this->identifier.'='.$object->id.'&conf=4&updatescene&token='.$token);
									// Save and stay on same form
									if (Tools::isSubmit('submitAdd'.$this->table.'AndStay'))
										Tools::redirectAdmin(self::$currentIndex.'&'.$this->identifier.'='.$object->id.'&conf=4&update'.$this->table.'&token='.$token);
									// Save and back to parent
									if (Tools::isSubmit('submitAdd'.$this->table.'AndBackToParent'))
										Tools::redirectAdmin(self::$currentIndex.'&'.$this->identifier.'='.$parent_id.'&conf=4&token='.$token);
									// Default behavior (save and back)
									Tools::redirectAdmin(self::$currentIndex.($parent_id ? '&'.$this->identifier.'='.$object->id : '').'&conf=4&token='.$token);
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
								$this->_errors[] = Tools::displayError('An error occurred while creating object.').' <b>'.$this->table.' ('.Db::getInstance()->getMsgError().')</b>';
							elseif (($_POST[$this->identifier] = $object->id /* voluntary */) AND $this->postImage($object->id) AND !sizeof($this->_errors) AND $this->_redirect)
							{
								$parent_id = (int)(Tools::getValue('id_parent', 1));
								$this->afterAdd($object);
								$this->updateAssoShop($object->id);
								// Save and stay on same form
								if (Tools::isSubmit('submitAdd'.$this->table.'AndStay'))
									Tools::redirectAdmin(self::$currentIndex.'&'.$this->identifier.'='.$object->id.'&conf=3&update'.$this->table.'&token='.$token);
								// Save and back to parent
								if (Tools::isSubmit('submitAdd'.$this->table.'AndBackToParent'))
									Tools::redirectAdmin(self::$currentIndex.'&'.$this->identifier.'='.$parent_id.'&conf=3&token='.$token);
								// Default behavior (save and back)
								Tools::redirectAdmin(self::$currentIndex.($parent_id ? '&'.$this->identifier.'='.$object->id : '').'&conf=3&token='.$token);
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
				$filters = $this->context->cookie->getFamily($this->table.'Filter_');
				foreach ($filters AS $cookieKey => $filter)
					if (strncmp($cookieKey, $this->table.'Filter_', 7 + Tools::strlen($this->table)) == 0)
						{
							$key = substr($cookieKey, 7 + Tools::strlen($this->table));
							/* Table alias could be specified using a ! eg. alias!field */
							$tmpTab = explode('!', $key);
							$key = (count($tmpTab) > 1 ? $tmpTab[1] : $tmpTab[0]);
							if (array_key_exists($key, $this->fieldsDisplay))
								unset($this->context->cookie->$cookieKey);
						}
				if (isset($this->context->cookie->{'submitFilter'.$this->table}))
					unset($this->context->cookie->{'submitFilter'.$this->table});
				if (isset($this->context->cookie->{$this->table.'Orderby'}))
					unset($this->context->cookie->{$this->table.'Orderby'});
				if (isset($this->context->cookie->{$this->table.'Orderway'}))
					unset($this->context->cookie->{$this->table.'Orderway'});
				unset($_POST);
			}

			/* Submit options list */
			elseif (Tools::getValue('submitOptions'.$this->table))
			{
				$this->updateOptions($token);
			}

			/* Manage list filtering */
			elseif (Tools::isSubmit('submitFilter'.$this->table) OR $this->context->cookie->{'submitFilter'.$this->table} !== false)
			{
				$_POST = array_merge($this->context->cookie->getFamily($this->table.'Filter_'), (isset($_POST) ? $_POST : array()));
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
										$sqlFilter .= ' AND `'.bqSQL($key).'` >= \''.pSQL(Tools::dateFrom($value[0])).'\'';
								}

								if (isset($value[1]) AND !empty($value[1]))
								{
									if (!Validate::isDate($value[1]))
										$this->_errors[] = Tools::displayError('\'to:\' date format is invalid (YYYY-MM-DD)');
									else
										$sqlFilter .= ' AND `'.bqSQL($key).'` <= \''.pSQL(Tools::dateTo($value[1])).'\'';
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
					Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$token);
			}
		}
	}

	/**
	 * Display form
	 */
	public function displayForm($firstCall = true)
	{
		$content = '';
		$allowEmployeeFormLang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		if ($allowEmployeeFormLang && !$this->context->cookie->employee_form_lang)
			$this->context->cookie->employee_form_lang = (int)(Configuration::get('PS_LANG_DEFAULT'));
		$useLangFromCookie = false;
		$this->_languages = Language::getLanguages(false);
		if ($allowEmployeeFormLang)
			foreach ($this->_languages AS $lang)
				if ($this->context->cookie->employee_form_lang == $lang['id_lang'])
					$useLangFromCookie = true;
		if (!$useLangFromCookie)
			$this->_defaultFormLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
		else
			$this->_defaultFormLanguage = (int)($this->context->cookie->employee_form_lang);

		// Only if it is the first call to displayForm, otherwise it has already been defined
		if ($firstCall)
		{
			$content .='
			<script type="text/javascript">
				$(document).ready(function() {
					id_language = '.$this->_defaultFormLanguage.';
					languages = new Array();';
			foreach ($this->_languages AS $k => $language)
				$content .= '
					languages['.$k.'] = {
						id_lang: '.(int)$language['id_lang'].',
						iso_code: \''.$language['iso_code'].'\',
						name: \''.htmlentities($language['name'], ENT_COMPAT, 'UTF-8').'\'
					};';
			$content .= '
					displayFlags(languages, id_language, '.$allowEmployeeFormLang.');
				});
			</script>';
		}
		return $content;
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
	 * Check if the token is valid, else display a warning page
	 */
	public function checkAccess()
	{
		if (!$this->checkToken())
		{
			// If this is an XSS attempt, then we should only display a simple, secure page
			// ${1} in the replacement string of the regexp is required, because the token may begin with a number and mix up with it (e.g. $17)
			$url = preg_replace('/([&?]token=)[^&]*(&.*)?$/', '${1}'.$this->token.'$2', $_SERVER['REQUEST_URI']);
			if (false === strpos($url, '?token=') AND false === strpos($url, '&token='))
				$url .= '&token='.$this->token;
			if(strpos($url,'?') === false)
				$url = str_replace('&token', '?controller=AdminHome&token', $url);

			$this->context->smarty->assign('url', htmlentities($url));
			$this->context->smarty->display('invalid_token.tpl');
		}
	}

	/**
	 * @TODO
	 */
	public function includeSubTab($methodname, $actions = array())
	{
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

	public function displayNoSmarty()
	{
	}
	public function displayAjax()
	{
		echo $this->content;
	}
	public function display()
	{
		$this->context->smarty->assign('content', $this->content);

		$this->context->smarty->assign('meta_title',$this->meta_title);

		if (empty($this->template))
		{
			$class_name = get_class($this);
			$class_name = strtolower($class_name[0]).substr($class_name, 1);
			$default_tpl = substr($class_name,0,-10).'.tpl';
			if (file_exists($this->context->smarty->template_dir.'/'.$default_tpl))
			{
				$this->template = $default_tpladdress;
			}
			else
				$this->template = 'content.tpl';
		}
		else
			$page = $this->context->smarty->fetch($this->template);

		if ($this->content_only)
			echo $page;
		else
		{
			$this->context->smarty->assign('warnings',$this->warnings);
			$page = $this->context->smarty->fetch($this->template);
		}

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
	 * Assign smarty variables for the header
	 */
	public function initHeader()
	{
		// Shop context
		if (Shop::isMultiShopActivated())
		{
			if (Context::shop() == Shop::CONTEXT_ALL)
			{
				$shop_context = 'all';
				$shop_name = '';
			}
			elseif (Context::shop() == Shop::CONTEXT_GROUP)
			{
				$shop_context = 'group';
				$shop_name = $this->context->shop->getGroup()->name;
			}
			else
			{
				$shop_context = 'shop';
				$shop_name = $this->context->shop->name;
			}

			$this->context->smarty->assign(array(
				'shop_name' => $shop_name,
				'shop_context' => $shop_context,
			));
				$youEditFieldFor = sprintf($this->l('A modification of this field will be applied for the shop %s'), '<b>'.Context::getContext()->shop->name.'</b>');
		}

			// Multishop
		$is_multishop = Shop::isMultiShopActivated();// && Context::shop() != Shop::CONTEXT_ALL;
		/*if ($is_multishop)
		{
			if (Context::shop() == Shop::CONTEXT_GROUP)
			{
				$shop_context = 'group';
				$shop_name = $this->context->shop->getGroup()->name;
			}
			elseif (Context::shop() == Shop::CONTEXT_SHOP)
			{
				$shop_context = 'shop';
				$shop_name = $this->context->shop->name;
			}*/



		// Quick access
		$quick_access = QuickAccess::getQuickAccesses($this->context->language->id);
		foreach ($quick_access AS $index => $quick)
		{
			preg_match('/tab=(.+)(&.+)?$/', $quick['link'], $adminTab);
			if (isset($adminTab[1]))
			{
				if (strpos($adminTab[1], '&'))
					$adminTab[1] = substr($adminTab[1], 0, strpos($adminTab[1], '&'));
				$quick_access[$index]['link'] .= '&token='.Tools::getAdminToken($adminTab[1].(int)(Tab::getIdFromClassName($adminTab[1])).(int)($this->context->employee->id));
			}
		}

		// Tab list
		$tabs = Tab::getTabs($this->context->language->id, 0);
		foreach ($tabs AS $index => $tab)
		{
			if (Tab::checkTabRights($tab['id_tab']) === true)
			{
				$img_exists_cache = Tools::file_exists_cache(_PS_ADMIN_DIR_.'/themes/'.$this->context->employee->bo_theme.'/img/t/'.$tab['class_name'].'.gif');
				$img = ($img_exists_cache ? 'themes/'.Context::getContext()->employee->bo_theme.'/img/' : _PS_IMG_).'t/'.$tab['class_name'].'.gif';

				if (trim($tab['module']) != '')
					$img = _MODULE_DIR_.$tab['module'].'/'.$tab['class_name'].'.gif';

				// tab[class_name] does not contains the "Controller" suffix
				$tabs[$index]['current'] = ($tab['class_name'].'Controller' == get_class($this)) || (Tab::getCurrentParentId() == $tab['id_tab']);
				$tabs[$index]['img'] = $img;
				$tabs[$index]['href'] = $this->context->link->getAdminLink($tab['class_name']);

				$sub_tabs = Tab::getTabs($this->context->language->id, $tab['id_tab']);
				foreach ($sub_tabs AS $index2 => $sub_tab)
				{
					// class_name is the name of the class controller 
					if (Tab::checkTabRights($sub_tab) === true)
						$sub_tabs[$index2]['href'] = $this->context->link->getAdminLink($sub_tab['class_name']);
					else
						unset($sub_tabs[$index2]);
				}
				$tabs[$index]['sub_tabs'] = $sub_tabs;
			}
			else
				unset($tabs[$index]);
		}
		// Breadcrumbs
		$home_token = Tools::getAdminToken('AdminHome'.intval(Tab::getIdFromClassName('AdminHome')).(int)$this->context->employee->id);
		$tabs_breadcrumb = array();
		$tabs_breadcrumb = Tab::recursiveTab($this->id, $tabs_breadcrumb);
		$tabs_breadcrumb = array_reverse($tabs_breadcrumb);

		foreach ($tabs_breadcrumb AS $key => $item)
			for ($i = 0; $i < (count($tabs_breadcrumb) - 1); $i++)
				$tabs_breadcrumb[$key]['token'] = Tools::getAdminToken($item['class_name'].intval($item['id_tab']).(int)$this->context->employee->id);


		/* Hooks are volontary out the initialize array (need those variables already assigned) */
		$this->context->smarty->assign(array(
			'img_dir' => _PS_IMG_,
			'iso' => $this->context->language->iso_code,
			'class_name' => $this->className,
			'iso_user' => $this->context->language->id,
			'country_iso_code' => $this->context->country->iso_code,
			'version' => _PS_VERSION_,
			'help_box' => Configuration::get('PS_HELPBOX'),
			'round_mode' => Configuration::get('PS_PRICE_ROUND_MODE'),
			'brightness' => Tools::getBrightness(empty($this->context->employee->bo_color) ? '#FFFFFF' : $this->context->employee->bo_color) < 128 ? 'white' : '#383838',
			'edit_field' => isset($youEditFieldFor) ? $youEditFieldFor : '\'\'',
			'lang_iso' => $this->context->language->iso_code,
			'link' => $this->context->link,
			'bo_color' => isset($this->context->employee->bo_color) ? Tools::htmlentitiesUTF8($this->context->employee->bo_color) : null,
			'shop_name' => Configuration::get('PS_SHOP_NAME'),
			'show_new_orders' => Configuration::get('PS_SHOW_NEW_ORDERS'),
			'show_new_customers' => Configuration::get('PS_SHOW_NEW_CUSTOMERS'),
			'show_new_messages' => Configuration::get('PS_SHOW_NEW_MESSAGES'),
			'token_admin_orders' => Tools::getAdminTokenLite('AdminOrders'),
			'token_admin_customers' => Tools::getAdminTokenLite('AdminCustomers'),
			'token_admin_messages' => Tools::getAdminTokenLite('AdminMessages'),
			'token_admin_employees' => Tools::getAdminTokenLite('AdminEmployees'),
			'token_admin_search' => Tools::getAdminTokenLite('AdminSearch'),
			'first_name' => Tools::substr($this->context->employee->firstname, 0, 1),
			'last_name' => htmlentities($this->context->employee->lastname, ENT_COMPAT, 'UTF-8'),
			'base_url' => $this->context->shop->getBaseURL(),
			'employee' => $this->context->employee,
			'search_type' => Tools::getValue('bo_search_type'),
			'bo_query' => Tools::safeOutput(Tools::stripslashes(Tools::getValue('bo_query'))),
			'quick_access' => $quick_access,
			'multi_shop' => Shop::isMultiShopActivated(),
			'shop_list' => (Shop::isMultiShopActivated() ? generateShopList() : null), //@TODO refacto
			'tab' => $tab,
			'current_parent_id' => (int)Tab::getCurrentParentId(),
			'tabs' => $tabs,
			'install_dir_exists' => file_exists(_PS_ADMIN_DIR_.'/../install'),
			'home_token' => $home_token,
			'tabs_breadcrumb' => $tabs_breadcrumb,
			'is_multishop' => $is_multishop,

		));
		$this->context->smarty->assign(array(
			'HOOK_HEADER' => Module::hookExec('backOfficeHeader'),
			'HOOK_TOP' => Module::hookExec('backOfficeTop'),
		));

		$this->context->smarty->assign('css_files', $this->css_files);
		$this->context->smarty->assign('js_files', array_unique($this->js_files));
	}

	/**
	 * Assign smarty variables for the page main content
	 */
	public function initContent()
	{
		if ($this->_errors)
			$this->content = $this->displayErrors();
		else
		{
			if ($this->action == 'edit' && $this->id_entity)
			{
				$this->content .= $this->displayForm();
				if ($this->tabAccess['view']){
					if (Tools::getValue('back'))
						$this->context->smarty->assign('back', Tools::safeOutput(Tools::getValue('back')));
					else
						$this->context->smarty->assign('back', Tools::safeOutput(Tools::getValue(self::$currentIndex.'&token='.$this->token)));
				}
				// move to form.tpl
				$this->content .= '<br /><br /><a href="'.((Tools::getValue('back')) ? Tools::getValue('back') : self::$currentIndex.'&token='.$this->token).'"><img src="../img/admin/arrow2.gif" /> '.((Tools::getValue('back')) ? $this->l('Back') : $this->l('Back to list')).'</a><br />';
			}
			elseif ($this->action == 'list')
			{
				$this->getList($this->context->language->id);

				$helper = new HelperList();
				$helper->view = $this->view;
				$helper->edit = $this->edit;
				$helper->delete = $this->delete;
				$helper->duplicate = $this->duplicate;
				$helper::$currentIndex = self::$currentIndex;
				$helper->table = $this->table;
				$helper->shopLink = $this->shopLink;
				$helper->shopLinkType = $this->shopLinkType;
				$helper->identifier = $this->identifier;
				$helper->token = $this->token;
				$this->content .= $helper->generateList($this->_list, $this->fieldsDisplay);
			}
		}

	}

	/**
	 * Assign smarty variables for the footer
	 */
	public function initFooter()
	{
		$this->context->smarty->assign(array(
			'ps_version' => _PS_VERSION_,
			'end_time' => number_format(microtime(true) - $this->timerStart, 3, '.', ''),
			'iso_is_fr' => strtoupper($this->context->language->iso_code) == 'FR',
		));

		$this->context->smarty->assign(array(
			'HOOK_FOOTER' => Module::hookExec('backOfficeFooter'),
		));
	}

	public function setMedia()
	{
		$this->addCSS(_PS_JS_DIR_.'jquery/datepicker/datepicker.css', 'all');
		$this->addCSS(_PS_CSS_DIR_.'admin.css', 'all');
		$this->addCSS(_PS_CSS_DIR_.'jquery.cluetip.css', 'all');
		$this->addCSS(__PS_BASE_URI__.str_replace(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR,'', _PS_ADMIN_DIR_).'/themes/default/admin.css', 'all');
		if ($this->context->language->is_rtl)
			$this->addCSS(_THEME_CSS_DIR_.'rtl.css');

		$this->addJS(_PS_JS_DIR_.'jquery/jquery-1.4.4.min.js');
		$this->addJS(_PS_JS_DIR_.'jquery/jquery.hoverIntent.minified.js');
		$this->addJS(_PS_JS_DIR_.'jquery/jquery.cluetip.js');
		$this->addJS(_PS_JS_DIR_.'admin.js');
		$this->addJS(_PS_JS_DIR_.'toggle.js');
		$this->addJS(_PS_JS_DIR_.'tools.js');
		$this->addJS(_PS_JS_DIR_.'ajax.js');
		$this->addJS(_PS_JS_DIR_.'notifications.js');
	}

	public static function translate($string, $class, $addslashes = FALSE, $htmlentities = TRUE)
	{
		$class = strtolower($class);
		// if the class is extended by a module, use modules/[module_name]/xx.php lang file
		//$currentClass = get_class($this);
		if(false AND Module::getModuleNameFromClass($class))
		{
			$string = str_replace('\'', '\\\'', $string);
			return Module::findTranslation(Module::$classInModule[$class], $string, $class);
		}
		global $_LANGADM;
		if(is_array($_LANGADM))
			$_LANGADM = array_change_key_case($_LANGADM);
		else
			$_LANGADM = array();

        //if ($class == __CLASS__)
        //        $class = 'AdminTab';

		$key = md5(str_replace('\'', '\\\'', $string));

		$str = (key_exists($class.$key, $_LANGADM)) ? $_LANGADM[$class.$key] : ((key_exists($class.$key, $_LANGADM)) ? $_LANGADM[$class.$key] : $string);
		$str = $htmlentities ? htmlentities($str, ENT_QUOTES, 'utf-8') : $str;
		return str_replace('"', '&quot;', ($addslashes ? addslashes($str) : stripslashes($str)));
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
		$class = get_class($this);
		return self::translate($string, $class, $addslashes, $htmlentities);
	}

	public function init()
	{
		// ob_start();
		if (Tools::getValue('ajax'))
			$this->ajax = '1';
		$this->checkAccess();
		$this->timerStart = microtime(true);

		if (isset($_GET['logout']))
			$this->context->employee->logout();

		if (!isset($this->context->employee) || !$this->context->employee->isLoggedBack())
			Tools::redirectAdmin('login.php?redirect='.$_SERVER['REQUEST_URI']);

		// Set current index
		$currentIndex = $_SERVER['SCRIPT_NAME'].(($controller = Tools::getValue('controller')) ? '?controller='.$controller : '');

		if ($back = Tools::getValue('back'))
			$currentIndex .= '&back='.urlencode($back);
		self::$currentIndex = $currentIndex;
		$iso = $this->context->language->iso_code;
		include(_PS_TRANSLATIONS_DIR_.$iso.'/errors.php');
		include(_PS_TRANSLATIONS_DIR_.$iso.'/fields.php');
		include(_PS_TRANSLATIONS_DIR_.$iso.'/admin.php');

		/* Server Params */
		$protocol_link = (Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
		$protocol_content = (isset($useSSL) AND $useSSL AND Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
		$link = new Link($protocol_link, $protocol_content);
		$this->context->link = $link;
		//define('_PS_BASE_URL_', Tools::getShopDomain(true));
		//define('_PS_BASE_URL_SSL_', Tools::getShopDomainSsl(true));

		// Change shop context ?
		if (Shop::isMultiShopActivated() && Tools::getValue('setShopContext') !== false)
		{
			$this->context->cookie->shopContext = Tools::getValue('setShopContext');
			$url = parse_url($_SERVER['REQUEST_URI']);
			$query = (isset($url['query'])) ? $url['query'] : '';
			parse_str($query, $parseQuery);
			unset($parseQuery['setShopContext']);
			Tools::redirectAdmin($url['path'] . '?' . http_build_query($parseQuery));
		}

		$shopID = '';
		if ($this->context->cookie->shopContext)
		{
			$split = explode('-', $this->context->cookie->shopContext);
			if (count($split) == 2 && $split[0] == 's')
				$shopID = (int)$split[1];
		}
		$this->context->shop = new Shop($shopID);

		/* Filter memorization */
		if (isset($_POST) AND !empty($_POST) AND isset($this->table))
			foreach ($_POST AS $key => $value)
				if (is_array($this->table))
				{
					foreach ($this->table AS $table)
						if (strncmp($key, $table.'Filter_', 7) === 0 OR strncmp($key, 'submitFilter', 12) === 0)
							$this->context->cookie->$key = !is_array($value) ? $value : serialize($value);
				}
				elseif (strncmp($key, $this->table.'Filter_', 7) === 0 OR strncmp($key, 'submitFilter', 12) === 0)
					$this->context->cookie->$key = !is_array($value) ? $value : serialize($value);

		if (isset($_GET) AND !empty($_GET) AND isset($this->table))
			foreach ($_GET AS $key => $value)
				if (is_array($this->table))
				{
					foreach ($this->table AS $table)
						if (strncmp($key, $table.'OrderBy', 7) === 0 OR strncmp($key, $table.'Orderway', 8) === 0)
							$this->context->cookie->$key = $value;
				}
				elseif (strncmp($key, $this->table.'OrderBy', 7) === 0 OR strncmp($key, $this->table.'Orderway', 12) === 0)
					$this->context->cookie->$key = $value;

		// Code from postProcess
		if (isset($_GET['update'.$this->table]) && isset($_GET['id_'.$this->table]))
		{
			if ($this->tabAccess['edit'] === '1' OR ($this->table == 'employee' AND $this->context->employee->id == Tools::getValue('id_employee')))
			{
				$this->action = 'edit';
				$this->id_entity = (int)$_GET['id_'.$this->table];
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}
	}

	/**
	 * Display errors
	 */
	public function displayErrors()
	{
		if ($nbErrors = count($this->_errors) AND $this->_includeContainer)
		{
			$content = '<script type="text/javascript">
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
				$content .= $this->_errors[0];
			else
			{
				$content .= $nbErrors.' '.$this->l('errors').'<br /><ol>';
				foreach ($this->_errors AS $error)
					$content .= '<li>'.$error.'</li>';
				$content .= '</ol>';
			}
			$content .= '</div>';
		}
		// @TODO includesubtab
		$this->includeSubTab('displayErrors');
		return $content;
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
	public function getList($id_lang, $orderBy = NULL, $orderWay = NULL, $start = 0, $limit = NULL, $id_lang_shop = false)
	{
		/* Manage default params values */
		if (empty($limit))
			$limit = ((!isset($this->context->cookie->{$this->table.'_pagination'})) ? $this->_pagination[1] : $limit = $this->context->cookie->{$this->table.'_pagination'});

		if (!Validate::isTableOrIdentifier($this->table))
			die (Tools::displayError('Table name is invalid:').' "'.$this->table.'"');

		if (empty($orderBy))
			$orderBy = $this->context->cookie->__get($this->table.'Orderby') ? $this->context->cookie->__get($this->table.'Orderby') : $this->_defaultOrderBy;
		if (empty($orderWay))
			$orderWay = $this->context->cookie->__get($this->table.'Orderway') ? $this->context->cookie->__get($this->table.'Orderway') : 'ASC';

		$limit = (int)(Tools::getValue('pagination', $limit));
		$this->context->cookie->{$this->table.'_pagination'} = $limit;


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

		// Add SQL shop restriction
		$selectShop = $joinShop = $whereShop = '';
		if ($this->shopLinkType)
		{
			$selectShop = ', shop.name as shop_name ';
			$joinShop = ' LEFT JOIN '._DB_PREFIX_.$this->shopLinkType.' shop
							ON a.id_'.$this->shopLinkType.' = shop.id_'.$this->shopLinkType;
			$whereShop = $this->context->shop->sqlRestriction($this->shopShareDatas, 'a', $this->shopLinkType);
		}
		$assos = Shop::getAssoTables();
		if (isset($assos[$this->table]) && $assos[$this->table]['type'] == 'shop')
		{
			$filterKey = $assos[$this->table]['type'];
			$idenfierShop = $this->context->shop->getListOfID();
		}
		else if (Context::shop() == Shop::CONTEXT_GROUP)
		{
			$assos = GroupShop::getAssoTables();
			if (isset($assos[$this->table]) AND $assos[$this->table]['type'] == 'group_shop')
			{
				$filterKey = $assos[$this->table]['type'];
				$idenfierShop = array($this->context->shop->getGroupID());
			}
		}

		$filterShop = '';
		if (isset($filterKey))
		{
			if (!$this->_group)
				$this->_group = 'GROUP BY a.'.pSQL($this->identifier);
			else if (!preg_match('#(\s|,)\s*a\.`?'.pSQL($this->identifier).'`?(\s|,|$)#', $this->_group))
				$this->_group .= ', a.'.pSQL($this->identifier);

			if (Shop::isMultiShopActivated() && Context::shop() != Shop::CONTEXT_ALL && !preg_match('#`?'.preg_quote(_DB_PREFIX_.$this->table.'_'.$filterKey).'`? *sa#', $this->_join))
				$filterShop = 'JOIN `'._DB_PREFIX_.$this->table.'_'.$filterKey.'` sa ON (sa.'.$this->identifier.' = a.'.$this->identifier.' AND sa.id_'.$filterKey.' IN ('.implode(', ', $idenfierShop).'))';
		}

		/* Query in order to get results with all fields */
		$sql = 'SELECT SQL_CALC_FOUND_ROWS
			'.($this->_tmpTableFilter ? ' * FROM (SELECT ' : '').'
			'.($this->lang ? 'b.*, ' : '').'a.*'.(isset($this->_select) ? ', '.$this->_select.' ' : '').$selectShop.'
			FROM `'._DB_PREFIX_.$sqlTable.'` a
			'.$filterShop.'
			'.($this->lang ? 'LEFT JOIN `'._DB_PREFIX_.$this->table.'_lang` b ON (b.`'.$this->identifier.'` = a.`'.$this->identifier.'` AND b.`id_lang` = '.(int)$id_lang.($id_lang_shop ? ' AND b.`id_shop`='.(int)$id_lang_shop : '').')' : '').'
			'.(isset($this->_join) ? $this->_join.' ' : '').'
			'.$joinShop.'
			WHERE 1 '.(isset($this->_where) ? $this->_where.' ' : '').($this->deleted ? 'AND a.`deleted` = 0 ' : '').(isset($this->_filter) ? $this->_filter : '').$whereShop.'
			'.(isset($this->_group) ? $this->_group.' ' : '').'
			'.((isset($this->_filterHaving) || isset($this->_having)) ? 'HAVING ' : '').(isset($this->_filterHaving) ? ltrim($this->_filterHaving, ' AND ') : '').(isset($this->_having) ? $this->_having.' ' : '').'
			ORDER BY '.(($orderBy == $this->identifier) ? 'a.' : '').'`'.pSQL($orderBy).'` '.pSQL($orderWay).
			($this->_tmpTableFilter ? ') tmpTable WHERE 1'.$this->_tmpTableFilter : '').'
			LIMIT '.(int)$start.','.(int)$limit;
		$this->_list = Db::getInstance()->ExecuteS($sql);
		$this->_listTotal = Db::getInstance()->getValue('SELECT FOUND_ROWS() AS `'._DB_PREFIX_.$this->table.'`');
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
			if (($empty = Tools::getValue($fieldLang.'_'.$defaultLanguage->id)) === false OR $empty !== '0' AND empty($empty))
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
	 * Display object details
	 */
	public function viewDetails() {}

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
				foreach (array_keys($rules['validateLang']) AS $field)
					if (isset($_POST[$field.'_'.(int)($language['id_lang'])]))
						$object->{$field}[(int)($language['id_lang'])] = $_POST[$field.'_'.(int)($language['id_lang'])];
		}
	}

	protected function updateAssoShop($id_object = false)
	{
		if (!Shop::isMultiShopActivated())
			return ;

		$shopAsso = Shop::getAssoTables();
		$groupShopAsso = GroupShop::getAssoTables();
		if (isset($shopAsso[$this->table]) && $shopAsso[$this->table]['type'] == 'shop')
			$type = 'shop';
		else if (isset($groupShopAsso[$this->table]) && $groupShopAsso[$this->table]['type'] == 'group_shop')
			$type = 'group_shop';
		else
			return ;

		$assos = array();
		foreach ($_POST AS $k => $row)
		{
			if (!preg_match('/^checkBox'.Tools::toCamelCase($type, true).'Asso_'.$this->table.'_([0-9]+)?_([0-9]+)$/Ui', $k, $res))
				continue;
			$id_asso_object = (!empty($res[1]) ? $res[1] : $id_object);
			$assos[] = array('id_object' => (int)$id_asso_object, 'id_'.$type => (int)$res[2]);
		}

		Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.$this->table.'_'.$type.($id_object ? ' WHERE `'.$this->identifier.'`='.(int)$id_object : ''));
		foreach ($assos AS $asso)
			Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.$this->table.'_'.$type.' (`'.pSQL($this->identifier).'`, id_'.$type.')
											VALUES('.(int)$asso['id_object'].', '.(int)$asso['id_'.$type].')');
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
}
