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
*  @version  Release: $Revision: 7331 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
class AdminFeaturesControllerCore extends AdminController
{
	public function __construct()
	{
	 	$this->table = 'feature';
		$this->className = 'Feature';
	 	$this->lang = true;

		$this->fieldsDisplay = array(
			'id_feature' => array(
				'title' => $this->l('ID'),
				'width' => 25
			),
			'name' => array(
				'title' => $this->l('Name'),
				'width' => 'auto',
				'filter_key' => 'b!name'
			),
			'value' => array(
				'title' => $this->l('Values'),
				'width' => 255,
				'orderby' => false,
				'search' => false
			),
			'position' => array(
				'title' => $this->l('Position'),
				'width' => 40,
				'filter_key' => 'a!position',
				'align' => 'center',
				'position' => 'position'
			)
		);

	 	$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

		parent::__construct();
	}

	/**
	 * AdminController::renderList() override
	 * @see AdminController::renderList()
	 */
	public function renderList()
	{
		$this->addRowAction('edit');
		$this->addRowAction('delete');
		$this->addRowAction('details');
	 	$this->_defaultOrderBy = 'position';

	 	// Added specific button in toolbar
	 	$this->toolbar_btn['newAttributes'] = array(
			'href' => self::$currentIndex.'&amp;addfeature_value&amp;token='.$this->token,
			'desc' => $this->l('Add new feature value')
		);

	 	$this->toolbar_btn['new'] = array(
			'href' => self::$currentIndex.'&amp;addfeature&amp;token='.$this->token,
			'desc' => $this->l('Add new feature')
		);

		return parent::renderList();
	}

	/**
	 * method call when ajax request is made with the details row action
	 * @see AdminController::postProcess()
	 */
	public function ajaxProcessDetails()
	{
		if (($id = Tools::getValue('id')))
		{
			$this->table = 'feature_value';
			$this->className = 'FeatureValue';
			$this->identifier = 'id_feature_value';
			$this->lang = true;

			// override attributes
			$this->display = 'list';

			// Action for list
			$this->addRowAction('edit');
			$this->addRowAction('delete');

			if (!Validate::isLoadedObject($obj = new Feature((int)$id)))
				$this->errors[] = Tools::displayError('An error occurred while updating status for object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');

			$this->fieldsDisplay = array(
				'id_feature_value' => array(
					'title' => $this->l('ID'),
					'width' => 25
				),
				'value' => array(
					'title' => $this->l('Value')
				)
			);

			$this->_where = sprintf('AND `id_feature` = %d', (int)$id);

			// get list and force no limit clause in the request
			$this->getList($this->context->language->id);

			// Render list
			$helper = new HelperList();
			$helper->actions = $this->actions;
			$helper->no_link = true;
			$helper->shopLinkType = '';
			$helper->identifier = $this->identifier;
			$helper->toolbar_scroll = false;
			$helper->orderBy = 'position';
			$helper->orderWay = 'ASC';
			$helper->currentIndex = self::$currentIndex;
			$helper->token = $this->token;
			$helper->table = $this->table;
			$helper->simple_header = true;
			$helper->show_toolbar = false;
			$helper->bulk_actions = $this->bulk_actions;
			$content = $helper->generateList($this->_list, $this->fieldsDisplay);

			echo Tools::jsonEncode(array('use_parent_structure' => false, 'data' => $content));
			exit;
		}
	}

	/**
	 * AdminController::renderForm() override
	 * @see AdminController::renderForm()
	 */
	public function renderForm()
	{
		$this->toolbar_title = $this->l('Add a new feature');
		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Feature'),
				'image' => '../img/t/AdminFeatures.gif'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Name:'),
					'name' => 'name',
					'lang' => true,
					'size' => 33,
					'hint' => $this->l('Invalid characters:').' <>;=#{}',
					'required' => true
				)
			)
		);

		if (Shop::isFeatureActive())
		{
			$this->fields_form['input'][] = array(
				'type' => 'group_shop',
				'label' => $this->l('GroupShop association:'),
				'name' => 'checkBoxShopAsso',
			);
		}

		$this->fields_form['submit'] = array(
			'title' => $this->l('   Save   '),
			'class' => 'button'
		);

		return parent::renderForm();
	}

	/**
	 * AdminController::initToolbar() override
	 * @see AdminController::initToolbar()
	 */
	public function initToolbar()
	{
		switch ($this->display)
		{
			case 'editFeatureValue':
			case 'add':
			case 'edit':
				$this->toolbar_btn['save'] = array(
					'href' => '#',
					'desc' => $this->l('Save')
				);

				if ($this->display == 'editFeatureValue')
					$this->toolbar_btn['save-and-stay'] = array(
						'short' => 'SaveAndStay',
						'href' => '#',
						'desc' => $this->l('Save and add'),
						'force_desc' => true,
					);

				// Default cancel button - like old back link
				$back = Tools::safeOutput(Tools::getValue('back', ''));
				if (empty($back))
					$back = self::$currentIndex.'&token='.$this->token;

				$this->toolbar_btn['back'] = array(
					'href' => $back,
					'desc' => $this->l('Back to list')
				);
			break;

			default:
				parent::initToolbar();
		}
	}

	/**
	 * AdminController::renderForm() override
	 * @see AdminController::renderForm()
	 */
	public function initFormFeatureValue()
	{
		$this->table = 'feature_value';
		$this->className = 'FeatureValue';
		$this->identifier = 'id_feature_value';

		$this->fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('Feature value'),
				'image' => '../img/t/AdminFeatures.gif'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Value:'),
					'name' => 'value',
					'lang' => true,
					'size' => 33,
					'hint' => $this->l('Invalid characters:').' <>;=#{}',
					'required' => true
				),
				array(
					'type' => 'select',
					'label' => $this->l('Feature:'),
					'name' => 'id_feature',
					'options' => array(
						'query' => Feature::getFeatures($this->context->language->id),
						'id' => 'id_feature',
						'name' => 'name'
					),
					'required' => true
				)
			),
			'submit' => array(
				'title' => $this->l('   Save   '),
				'class' => 'button'
			)
		);

		// Create Object FeatureValue
		$feature_value = new FeatureValue(Tools::getValue('id_feature_value'));

		$this->tpl_vars = array(
			'feature_value' => $feature_value,
		);

		$this->getlanguages();
		$helper = new HelperForm();
		$helper->currentIndex = self::$currentIndex;
		$helper->token = $this->token;
		$helper->table = 'feature_value';
		$helper->identifier = 'id_feature_value';
		$helper->override_folder = 'feature_value/';
		$helper->id = $feature_value->id;
		$helper->toolbar_scroll = false;
		$helper->tpl_vars = $this->tpl_vars;
		$helper->languages = $this->_languages;
		$helper->default_form_language = $this->default_form_language;
		$helper->allow_employee_form_lang = $this->allow_employee_form_lang;
		$helper->fields_value = $this->getFieldsValue($feature_value);
		$helper->toolbar_btn = $this->toolbar_btn;
		$helper->title = $this->l('Add a new feature value');
		$this->content .= $helper->generateForm($this->fields_form);
	}

	/**
	 * AdminController::init() override
	 * @see AdminController::init()
	 */
	public function init()
	{
		if (isset($_POST['submitAddfeature_value']) || isset($_GET['updatefeature_value']) || isset($_GET['addfeature_value']))
			$this->display = 'editFeatureValue';

		parent::init();
	}

	/**
	 * AdminController::initContent() override
	 * @see AdminController::initContent()
	 */
	public function initContent()
	{
		if (Feature::isFeatureActive())
		{
			// toolbar (save, cancel, new, ..)
			$this->initToolbar();
			if ($this->display == 'edit' || $this->display == 'add')
			{
				if (!$this->loadObject(true))
					return;
				$this->content .= $this->renderForm();
			}
			else if ($this->display == 'view')
			{
				// Some controllers use the view action without an object
				if ($this->className)
					$this->loadObject(true);
				$this->content .= $this->renderView();
			}
			else if ($this->display == 'editFeatureValue')
			{
				if (!$this->object = new FeatureValue((int)Tools::getValue('id_feature_value')))
					return;

				$this->content .= $this->initFormFeatureValue();
			}
			else if (!$this->ajax)
			{
				$this->content .= $this->renderList();
				$this->content .= $this->renderOptions();
			}
		}
		else
			$this->displayWarning($this->l('This feature has been disabled, you can active this feature at this page:').'<a href="index.php?tab=AdminPerformance&token='.Tools::getAdminTokenLite('AdminPerformance').'#featuresDetachables">'.$this->l('Performances').'</a>');

		$this->context->smarty->assign(array(
			'content' => $this->content,
			'url_post' => self::$currentIndex.'&token='.$this->token,
		));
	}

	public function postProcess()
	{
		if (!Feature::isFeatureActive())
			return;

		if (Tools::isSubmit('deletefeature_value') || Tools::isSubmit('submitAddfeature_value'))
		{
			Hook::exec('displayFeatureValuePostProcess',
				array('errors' => &$this->errors)); // send errors as reference to allow displayFeatureValuePostProcess to stop saving process

			if (Tools::isSubmit('deletefeature_value'))
			{
				if ($this->tabAccess['delete'] === '1')
				{
					if (Tools::getValue('id_feature_value'))
					{
						$object = new FeatureValue((int)Tools::getValue('id_feature_value'));
						if ($object->delete())
							Tools::redirectAdmin(self::$currentIndex.'&conf=2'.'&token='.$this->token);
						else
							$this->errors[] = Tools::displayError('An error occurred during deletion.');
					}
				}
				else
					$this->errors[] = Tools::displayError('You do not have permission to delete here.');
			}
			else if (Tools::isSubmit('submitAddfeature_value') || Tools::isSubmit('submitAddfeature_valueAndStay'))
			{
				$this->table = 'feature_value';
				$this->className = 'FeatureValue';
				$this->identifier = 'id_feature_value';

				$id = (int)Tools::getValue('id_feature_value');
				$feature_value = new FeatureValue($id);
				$feature_value->value = array();

				if (!Tools::getValue('value_'.$this->context->language->id))
					$this->errors[] = Tools::displayError('The value is required for the default language.');

				$languages = Language::getLanguages(false);
					foreach ($languages as $language)
						$feature_value->value[$language['id_lang']] = Tools::getValue('value_'.$language['id_lang']);
				$feature_value->id_feature = Tools::getValue('id_feature');

				if (count($this->errors) > 0)
				{
					$this->display = 'editFeatureValue';
					parent::postProcess();
				}
				else if (isset($id) && !empty($id))
				{
					// Update
					if (!$feature_value->update())
						$this->errors[] = Tools::displayError('An error has occured: Can\'t save the current feature value');
					else if (Tools::isSubmit('submitAdd'.$this->table.'AndStay') && !count($this->errors))
						Tools::redirectAdmin(self::$currentIndex.'&'.$this->identifier.'=&id_feature='.(int)Tools::getValue('id_feature').'&conf=3&update'.$this->table.'&token='.$this->token);
					else
						Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
				}
				else
				{
					// Create
					if (!$feature_value->add())
						$this->errors[] = Tools::displayError('An error has occured: Can\'t save the current feature value');
					else if (Tools::isSubmit('submitAdd'.$this->table.'AndStay') && !count($this->errors))
						Tools::redirectAdmin(self::$currentIndex.'&'.$this->identifier.'=&id_feature='.(int)Tools::getValue('id_feature').'&conf=3&update'.$this->table.'&token='.$this->token);
					else
						Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
				}
			}
			else
				parent::postProcess();
		}
		else
		{
			Hook::exec('displayFeaturePostProcess',
				array('errors' => &$this->errors)); // send errors as reference to allow displayFeaturePostProcess to stop saving process

			if (Tools::getValue('submitDel'.$this->table))
			{
				if ($this->tabAccess['delete'] === '1')
				{
					if (isset($_POST[$this->table.'Box']))
					{
						$object = new $this->className();
						if ($object->deleteSelection($_POST[$this->table.'Box']))
							Tools::redirectAdmin(self::$currentIndex.'&conf=2'.'&token='.$this->token);
						$this->errors[] = Tools::displayError('An error occurred while deleting selection.');
					}
					else
						$this->errors[] = Tools::displayError('You must select at least one element to delete.');
				}
				else
					$this->errors[] = Tools::displayError('You do not have permission to delete here.');
			}
			else if (Tools::isSubmit('submitAdd'.$this->table))
			{
				if ($this->tabAccess['add'] === '1')
				{
					$id_feature = (int)Tools::getValue('id_feature');
					// Adding last position to the feature if not exist
					if ($id_feature <= 0)
					{
						$sql = 'SELECT `position`+1
								FROM `'._DB_PREFIX_.'feature`
								ORDER BY position DESC';
					// set the position of the new feature in $_POST for postProcess() method
						$_POST['position'] = DB::getInstance()->getValue($sql);
					}
					// clean \n\r characters
					foreach ($_POST as $key => $value)
						if (preg_match('/^name_/Ui', $key))
							$_POST[$key] = str_replace ('\n', '', str_replace('\r', '', $value));
					parent::postProcess();
				}
			}
			else
				parent::postProcess();
		}
	}

	/**
	 * Override processAdd to change SaveAndStay button action
	 * @see classes/AdminControllerCore::processAdd()
	 */
	public function processAdd($token)
	{
		parent::processAdd($token);

		if (Tools::isSubmit('submitAdd'.$this->table.'AndStay') && !count($this->errors))
			$this->redirect_after = self::$currentIndex.'&'.$this->identifier.'=&conf=3&update'.$this->table.'&token='.$token;
	}

	/**
	 * Override processUpdate to change SaveAndStay button action
	 * @see classes/AdminControllerCore::processUpdate()
	 */
	public function processUpdate($token)
	{
		parent::processUpdate($token);

		if (Tools::isSubmit('submitAdd'.$this->table.'AndStay') && !count($this->errors))
			$this->redirect_after = self::$currentIndex.'&'.$this->identifier.'=&conf=3&update'.$this->table.'&token='.$token;
	}

	/**
	 * Call the right method for creating or updating object
	 *
	 * @param $token
	 * @return mixed
	 */
	public function processSave($token)
	{
		if ((int)Tools::getValue('id_feature_value') <= 0 && $this->display == 'add'
			|| (int)Tools::getValue('id_feature') <= 0 && $this->display != 'add')
			return $this->processAdd($token);
		else
			return $this->processUpdate($token);
	}

	/**
	 * AdminController::getList() override
	 * @see AdminController::getList()
	 */
	public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
	{
		parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);

		if ($this->table == 'feature')
		{
			$nb_items = count($this->_list);
			for ($i = 0; $i < $nb_items; ++$i)
			{
				$item = &$this->_list[$i];

				$query = new DbQuery();
				$query->select('COUNT(id_feature_value) as count_values');
				$query->from('feature_value', 'fv');
				$query->where('fv.id_feature ='.(int)$item['id_feature']);
				$res = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
				$item['value'] = (int)$res;
				unset($query);
			}
		}
	}

}
