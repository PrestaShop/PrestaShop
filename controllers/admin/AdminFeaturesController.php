<?php
/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
class AdminFeaturesControllerCore extends AdminController
{
	protected $position_identifier = 'id_feature';

	public function __construct()
	{
	 	$this->table = 'feature';
		$this->className = 'Feature';
	 	$this->lang = true;

		$this->fields_list = array(
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
			'desc' => $this->l('Add new feature values')
		);

	 	$this->toolbar_btn['new'] = array(
			'href' => self::$currentIndex.'&amp;addfeature&amp;token='.$this->token,
			'desc' => $this->l('Add a new feature')
		);

		return parent::renderList();
	}

	/**
	 * Change object type to feature value (use when processing a feature value)
	 */
	protected function setTypeValue()
	{
		$this->table = 'feature_value';
		$this->className = 'FeatureValue';
		$this->identifier = 'id_feature_value';
	}

	/**
	 * Change object type to feature (use when processing a feature)
	 */
	protected function setTypeFeature()
	{
		$this->table = 'feature';
		$this->className = 'Feature';
		$this->identifier = 'id_feature';
	}

	/**
	 * method call when ajax request is made with the details row action
	 * @see AdminController::postProcess()
	 */
	public function ajaxProcessDetails()
	{
		if (($id = Tools::getValue('id')))
		{
			$this->setTypeValue();
			$this->lang = true;

			// override attributes
			$this->display = 'list';

			// Action for list
			$this->addRowAction('edit');
			$this->addRowAction('delete');

			if (!Validate::isLoadedObject($obj = new Feature((int)$id)))
				$this->errors[] = Tools::displayError('An error occurred while updating the status for an object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');

			$this->fields_list = array(
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
			$content = $helper->generateList($this->_list, $this->fields_list);

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
				'type' => 'shop',
				'label' => $this->l('Shop association:'),
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
						'desc' => $this->l('Save and add another value'),
						'force_desc' => true,
					);

				// Default cancel button - like old back link
				$back = Tools::safeOutput(Tools::getValue('back', ''));
				if (empty($back))
					$back = self::$currentIndex.'&token='.$this->token;

				$this->toolbar_btn['back'] = array(
					'href' => $back,
					'desc' => $this->l('Back to the list')
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
		$this->setTypeValue();

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
		$helper->table = $this->table;
		$helper->identifier = $this->identifier;
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
				// If a feature value was saved, we need to reset the values to display the list
				$this->setTypeFeature();
				$this->content .= $this->renderList();
			}
		}
		else
			$this->displayWarning($this->l('This feature has been disabled. You can activate it at:').'<a href="index.php?tab=AdminPerformance&token='.Tools::getAdminTokenLite('AdminPerformance').'#featuresDetachables">'.$this->l('Performance').'</a>');

		$this->context->smarty->assign(array(
			'content' => $this->content,
			'url_post' => self::$currentIndex.'&token='.$this->token,
		));
	}

	public function initProcess()
	{
		// Are we working on feature values?
		if (Tools::getValue('id_feature_value')
			|| Tools::isSubmit('deletefeature_value')
			|| Tools::isSubmit('submitAddfeature_value')
			|| Tools::isSubmit('addfeature_value')
			|| Tools::isSubmit('updatefeature_value')
			|| Tools::isSubmit('submitBulkdeletefeature_value'))
			$this->setTypeValue();

		parent::initProcess();

	}

	public function postProcess()
	{
		if (!Feature::isFeatureActive())
			return;

		if ($this->table == 'feature_value' && ($this->action == 'save' || $this->action == 'delete' || $this->action == 'bulkDelete'))
			Hook::exec('displayFeatureValuePostProcess',
				array('errors' => &$this->errors)); // send errors as reference to allow displayFeatureValuePostProcess to stop saving process
		else
			Hook::exec('displayFeaturePostProcess',
				array('errors' => &$this->errors)); // send errors as reference to allow displayFeaturePostProcess to stop saving process

		parent::postProcess();

		if ($this->table == 'feature_value' && ($this->display == 'edit' || $this->display == 'add'))
			$this->display = 'editFeatureValue';
	}

	/**
	 * Override processAdd to change SaveAndStay button action
	 * @see classes/AdminControllerCore::processAdd()
	 */
	public function processAdd()
	{
		$object = parent::processAdd();

		if (Tools::isSubmit('submitAdd'.$this->table.'AndStay') && !count($this->errors))
			$this->redirect_after = self::$currentIndex.'&'.$this->identifier.'=&conf=3&update'.$this->table.'&token='.$this->token;
		elseif (Tools::isSubmit('submitAdd'.$this->table.'AndStay') && count($this->errors))
			$this->display = 'editFeatureValue';

		return $object;
	}

	/**
	 * Override processUpdate to change SaveAndStay button action
	 * @see classes/AdminControllerCore::processUpdate()
	 */
	public function processUpdate()
	{
		$object = parent::processUpdate();

		if (Tools::isSubmit('submitAdd'.$this->table.'AndStay') && !count($this->errors))
			$this->redirect_after = self::$currentIndex.'&'.$this->identifier.'=&conf=3&update'.$this->table.'&token='.$this->token;
		
		return $object;
	}

	/**
	 * Call the right method for creating or updating object
	 *
	 * @return mixed
	 */
	public function processSave()
	{
		if ($this->table == 'feature')
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
		}
		return parent::processSave();
	}

	/**
	 * AdminController::getList() override
	 * @see AdminController::getList()
	 */
	public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
	{
		if ($this->table == 'feature_value')
			$this->_where .= ' AND a.custom = 0';
		
		parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);

		if ($this->table == 'feature')
		{
			$nb_items = count($this->_list);
			for ($i = 0; $i < $nb_items; ++$i)
			{
				$item = &$this->_list[$i];

				$query = new DbQuery();
				$query->select('COUNT(fv.id_feature_value) as count_values');
				$query->from('feature_value', 'fv');
				$query->where('fv.id_feature ='.(int)$item['id_feature']);
				$query->where('fv.custom =0');
				$res = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
				$item['value'] = (int)$res;
				unset($query);
			}
		}
	}

	public function ajaxProcessUpdatePositions()
	{
		if ($this->tabAccess['edit'] === '1')
		{
			$way = (int)Tools::getValue('way');
			$id_feature = (int)Tools::getValue('id');
			$positions = Tools::getValue('feature');

			$new_positions = array();
			foreach ($positions as $k => $v)
				if (!empty($v))
					$new_positions[] = $v;

			foreach ($new_positions as $position => $value)
			{
				$pos = explode('_', $value);

				if (isset($pos[2]) && (int)$pos[2] === $id_feature)
				{
					if ($feature = new Feature((int)$pos[2]))
						if (isset($position) && $feature->updatePosition($way, $position, $id_feature))
							echo 'ok position '.(int)$position.' for feature '.(int)$pos[1].'\r\n';
						else
							echo '{"hasError" : true, "errors" : "Can not update feature '.(int)$id_feature.' to position '.(int)$position.' "}';
					else
						echo '{"hasError" : true, "errors" : "This feature ('.(int)$id_feature.') can t be loaded"}';

					break;
				}
			}
		}
	}
}
