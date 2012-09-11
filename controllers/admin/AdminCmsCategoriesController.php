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
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminCmsCategoriesControllerCore extends AdminController
{
	/** @var object CMSCategory() instance for navigation*/
	protected $cms_category;

	protected $position_identifier = 'id_cms_category_to_move';

	public function __construct()
	{
		$this->table = 'cms_category';
		$this->className = 'CMSCategory';
		$this->lang = true;
		$this->addRowAction('view');
		$this->addRowAction('edit');
		$this->addRowAction('delete');
		$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

		$this->fields_list = array(
		'id_cms_category' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 30),
		'name' => array('title' => $this->l('Name'), 'width' => 'auto', 'callback' => 'hideCMSCategoryPosition', 'callback_object' => 'CMSCategory'),
		'description' => array('title' => $this->l('Description'), 'width' => 500, 'maxlength' => 90, 'orderby' => false),
		'position' => array('title' => $this->l('Position'), 'width' => 40,'filter_key' => 'position', 'align' => 'center', 'position' => 'position'),
		'active' => array(
			'title' => $this->l('Displayed'), 'width' => 25, 'active' => 'status',
			'align' => 'center','type' => 'bool', 'orderby' => false
		));

		// The controller can't be call directly
		// In this case, AdminCmsContentController::getCurrentCMSCategory() is null
		if (!AdminCmsContentController::getCurrentCMSCategory())
		{
			$this->redirect_after = '?controller=AdminCmsContent&token='.Tools::getAdminTokenLite('AdminCmsContent');
			$this->redirect();
		}

		$this->cms_category = AdminCmsContentController::getCurrentCMSCategory();
		$this->_filter = 'AND `id_parent` = '.(int)$this->cms_category->id;
		$this->_select = 'position ';

		parent::__construct();
	}

	public function renderList()
	{
		$this->initToolbar();
		return parent::renderList();
	}

	/**
	 * Modifying initial getList method to display position feature (drag and drop)
	 */
	public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
	{
		if ($order_by && $this->context->cookie->__get($this->table.'Orderby'))
			$order_by = $this->context->cookie->__get($this->table.'Orderby');
		else
			$order_by = 'position';

		parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);
	}

	public function postProcess()
	{
		$this->tabAccess = Profile::getProfileAccess($this->context->employee->id_profile, $this->id);
		if (Tools::isSubmit('submitAdd'.$this->table))
		{
			$this->action = 'save';
			if ($id_cms_category = (int)Tools::getValue('id_cms_category'))
			{
				$this->id_object = $id_cms_category;
				if (!CMSCategory::checkBeforeMove($id_cms_category, (int)Tools::getValue('id_parent')))
				{
					$this->errors[] = Tools::displayError('CMS Category cannot be moved here');
					return false;
				}
			}
		}
		/* Change object statuts (active, inactive) */
		elseif (Tools::isSubmit('statuscms_category') && Tools::getValue($this->identifier))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				if (Validate::isLoadedObject($object = $this->loadObject()))
				{
					if ($object->toggleStatus())
					{
						$identifier = ((int)$object->id_parent ? '&id_cms_category='.(int)$object->id_parent : '');
						Tools::redirectAdmin(self::$currentIndex.'&conf=5'.$identifier.'&token='.Tools::getValue('token'));
					}
					else
						$this->errors[] = Tools::displayError('An error occurred while updating status.');
				}
				else
					$this->errors[] = Tools::displayError('An error occurred while updating status for object.')
						.' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		/* Delete object */
		elseif (Tools::isSubmit('delete'.$this->table))
		{
			if ($this->tabAccess['delete'] === '1')
			{
				if (Validate::isLoadedObject($object = $this->loadObject()) && isset($this->fieldImageSettings))
				{
					// check if request at least one object with noZeroObject
					if (isset($object->noZeroObject) && count($taxes = call_user_func(array($this->className, $object->noZeroObject))) <= 1)
						$this->errors[] = Tools::displayError('You need at least one object.')
							.' <b>'.$this->table.'</b><br />'.Tools::displayError('You cannot delete all of the items.');
					else
					{
						if ($this->deleted)
						{
							$object->deleted = 1;
							if ($object->update())
								Tools::redirectAdmin(self::$currentIndex.'&conf=1&token='.Tools::getValue('token'));
						}
						elseif ($object->delete())
							Tools::redirectAdmin(self::$currentIndex.'&conf=1&token='.Tools::getValue('token'));
						$this->errors[] = Tools::displayError('An error occurred during deletion.');
					}
				}
				else
					$this->errors[] = Tools::displayError('An error occurred while deleting object.')
						.' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to delete here.');
		}
		elseif (Tools::isSubmit('position'))
		{
			$object = new CMSCategory((int)Tools::getValue($this->identifier, Tools::getValue('id_cms_category_to_move', 1)));
			if ($this->tabAccess['edit'] !== '1')
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
			elseif (!Validate::isLoadedObject($object))
				$this->errors[] = Tools::displayError('An error occurred while updating status for object.')
					.' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
			elseif (!$object->updatePosition((int)Tools::getValue('way'), (int)Tools::getValue('position')))
				$this->errors[] = Tools::displayError('Failed to update the position.');
			else
			{
				$identifier = '';
				if ($id_category = (int)Tools::getValue($this->identifier, Tools::getValue('id_cms_category_parent', 1)))
					$identifier = '&'.$this->identifier.'='.$id_category;
				$token = Tools::getAdminTokenLite('AdminCmsContent');

				Tools::redirectAdmin(
					self::$currentIndex.'&'.$this->table.'Orderby=position&'.$this->table.'Orderway=asc&conf=5'.$identifier.'&token='.$token
				);
			}
		}
		/* Delete multiple objects */
		elseif (Tools::getValue('submitDel'.$this->table) || Tools::getValue('submitBulkdelete'.$this->table))
		{
			if ($this->tabAccess['delete'] === '1')
			{
				if (Tools::isSubmit($this->table.'Box'))
				{
					$cms_category = new CMSCategory();
					$result = true;
					$result = $cms_category->deleteSelection(Tools::getValue($this->table.'Box'));
					if ($result)
					{
						$cms_category->cleanPositions((int)Tools::getValue('id_cms_category'));
						$token = Tools::getAdminTokenLite('AdminCmsContent');
						Tools::redirectAdmin(self::$currentIndex.'&conf=2&token='.$token.'&id_category='.(int)Tools::getValue('id_cms_category'));
					}
					$this->errors[] = Tools::displayError('An error occurred while deleting selection.');

				}
				else
					$this->errors[] = Tools::displayError('You must select at least one element to delete.');
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to delete here.');
		}
		parent::postProcess();
	}

	public function renderForm()
	{
		$this->display = 'edit';
		$this->initToolbar();
		if (!$this->loadObject(true))
			return;

		$categories = CMSCategory::getCategories($this->context->language->id, false);
		$html_categories = CMSCategory::recurseCMSCategory($categories, $categories[0][1], 1, $this->getFieldValue($this->object, 'id_parent'), 1);

		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('CMS Category'),
				'image' => '../img/admin/tab-categories.gif'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Name:'),
					'name' => 'name',
					'required' => true,
					'lang' => true,
					'class' => 'copy2friendlyUrl',
					'hint' => $this->l('Invalid characters:').' <>;=#{}'
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Displayed:'),
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
				),
				// custom template
				array(
					'type' => 'select_category',
					'label' => $this->l('Parent CMS Category:'),
					'name' => 'id_parent',
					'options' => array(
						'html' => $html_categories,
					),
				),
				array(
					'type' => 'textarea',
					'label' => $this->l('Description:'),
					'name' => 'description',
					'lang' => true,
					'rows' => 5,
					'cols' => 40,
					'hint' => $this->l('Invalid characters:').' <>;=#{}'
				),
				array(
					'type' => 'text',
					'label' => $this->l('Meta title:'),
					'name' => 'meta_title',
					'lang' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}'
				),
				array(
					'type' => 'text',
					'label' => $this->l('Meta description:'),
					'name' => 'meta_description',
					'lang' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}'
				),
				array(
					'type' => 'text',
					'label' => $this->l('Meta keywords:'),
					'name' => 'meta_keywords',
					'lang' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}'
				),
				array(
					'type' => 'text',
					'label' => $this->l('Friendly URL:'),
					'name' => 'link_rewrite',
					'required' => true,
					'lang' => true,
					'hint' => $this->l('Only letters and the minus (-) character are allowed')
				),
			),
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'button'
			)
		);

		return parent::renderForm();
	}
}
