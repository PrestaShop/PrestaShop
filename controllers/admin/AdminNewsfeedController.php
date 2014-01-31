<?php
/*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminNewsfeedControllerCore extends AdminController
{
	protected $category;

	public $id_newsfeed_category;

	protected $position_identifier = 'id_newsfeed';

	public function __construct()
	{
		$this->bootstrap = true;
		$this->table = 'newsfeed';
		$this->list_id = 'newsfeed';
		$this->className = 'Newsfeed';
		$this->lang = true;
		$this->addRowAction('edit');
		$this->addRowAction('delete');
		$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?'), 'icon' => 'icon-trash'));
		$this->fields_list = array(
			'id_newsfeed' => array('title' => $this->l('ID'), 'align' => 'center', 'class' => 'fixed-width-xs'),
			'link_rewrite' => array('title' => $this->l('URL')),
			'meta_title' => array('title' => $this->l('Title'), 'filter_key' => 'b!meta_title'),
			'position' => array('title' => $this->l('Position'),'filter_key' => 'position', 'align' => 'center', 'class' => 'fixed-width-sm', 'position' => 'position'),
			'active' => array('title' => $this->l('Displayed'), 'align' => 'center', 'active' => 'status', 'class' => 'fixed-width-sm', 'type' => 'bool', 'orderby' => false)
		);

		// The controller can't be call directly
		// In this case, AdminNewsfeedContentController::getCurrentNewsfeedCategory() is null
		if (!AdminNewsfeedContentController::getCurrentNewsfeedCategory())
		{
			$this->redirect_after = '?controller=AdminNewsfeedContent&token='.Tools::getAdminTokenLite('AdminNewsfeedContent');
			$this->redirect();
		}

		$this->_category = AdminNewsfeedContentController::getCurrentNewsfeedCategory();
		$this->tpl_list_vars['icon'] = 'icon-folder-close';
		$this->tpl_list_vars['title'] = sprintf($this->l('Pages in category "%s"'),
			$this->_category->name[Context::getContext()->employee->id_lang]);
		$this->_join = '
		LEFT JOIN `'._DB_PREFIX_.'newsfeed_category` c ON (c.`id_newsfeed_category` = a.`id_newsfeed_category`)';
		$this->_select = 'a.position ';
		$this->_where = ' AND c.id_newsfeed_category = '.(int)$this->_category->id;

		parent::__construct();
	}

	public function initPageHeaderToolbar()
	{
		$this->page_header_toolbar_btn['save-and-preview'] = array(
			'href' => '#',
			'desc' => $this->l('Save and preview', null, null, false)
		);
		$this->page_header_toolbar_btn['save-and-stay'] = array(
			'short' => $this->l('SaveAndStay', null, null, false),
			'href' => '#',
			'desc' => $this->l('Save and stay', null, null, false),
		);
		
		return parent::initPageHeaderToolbar();
	}

	public function renderForm()
	{
		if (!$this->loadObject(true))
			return;

		if (Validate::isLoadedObject($this->object))
			$this->display = 'edit';
		else
			$this->display = 'add';

		$this->initToolbar();
		$this->initPageHeaderToolbar();

		$categories = NewsfeedCategory::getCategories($this->context->language->id, false);
		$html_categories = NewsfeedCategory::recurseNewsfeedCategory($categories, $categories[0][1], 1, $this->getFieldValue($this->object, 'id_newsfeed_category'), 1);

		$this->fields_form = array(
			'tinymce' => true,
			'legend' => array(
				'title' => $this->l('Newsfeed Page'),
				'icon' => 'icon-folder-close'
			),
			'input' => array(
				// custom template
				array(
					'type' => 'select_category',
					'label' => $this->l('Newsfeed Category'),
					'name' => 'id_newsfeed_category',
					'options' => array(
						'html' => $html_categories,
					),
				),
				array(
					'type' => 'text',
					'label' => $this->l('Meta title'),
					'name' => 'meta_title',
					'id' => 'name', // for copyMeta2friendlyURL compatibility
					'lang' => true,
					'required' => true,
					'class' => 'copyMeta2friendlyURL',
					'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
				),
				array(
					'type' => 'text',
					'label' => $this->l('Meta description'),
					'name' => 'meta_description',
					'lang' => true,
					'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
				),
				array(
					'type' => 'tags',
					'label' => $this->l('Meta keywords'),
					'name' => 'meta_keywords',
					'lang' => true,
					'hint' => array(
						$this->l('To add "tags" click in the field, write something, and then press "Enter."'),
						$this->l('Invalid characters:').' &lt;&gt;;=#{}'
					)
				),
				array(
					'type' => 'text',
					'label' => $this->l('Friendly URL'),
					'name' => 'link_rewrite',
					'required' => true,
					'lang' => true,
					'hint' => $this->l('Only letters and the minus (-) character are allowed')
				),
				array(
					'type' => 'textarea',
					'label' => $this->l('Short content description'),
					'name' => 'short_content',
					'lang' => true,
					'rows' => 3,
					'cols' => 40,
					'hint' => $this->l('Invalid characters:').' <>;=#{}'
				),
				array(
					'type' => 'textarea',
					'label' => $this->l('Page content'),
					'name' => 'content',
					'autoload_rte' => true,
					'lang' => true,
					'rows' => 5,
					'cols' => 40,
					'hint' => $this->l('Invalid characters:').' <>;=#{}'
				),
				array(
					'type' => 'switch',
					'label' => $this->l('Indexation by search engines'),
					'name' => 'indexation',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'indexation_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'indexation_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					),
				),
				array(
					'type' => 'switch',
					'label' => $this->l('Displayed'),
					'name' => 'active',
					'required' => false,
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
			),
			'submit' => array(
				'title' => $this->l('Save'),
			),
			'buttons' => array(
				'save_and_preview' => array(
				'name' => 'viewnewsfeed',
				'type' => 'submit',
				'title' => $this->l('Save and preview'),
				'class' => 'btn btn-default pull-right',
				'icon' => 'process-icon-preview'
			)
		);

		if (Shop::isFeatureActive())
		{
			$this->fields_form['input'][] = array(
				'type' => 'shop',
				'label' => $this->l('Shop association'),
				'name' => 'checkBoxShopAsso',
			);
		}

		$this->tpl_form_vars = array(
			'active' => $this->object->active,
			'PS_ALLOW_ACCENTED_CHARS_URL', (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL')
		);
		return parent::renderForm();
	}

	public function renderList()
	{
		//self::$currentIndex = self::$currentIndex.'&newsfeed';
		$this->toolbar_title = $this->l('Pages in this category');
		$this->toolbar_btn['new'] = array(
			'href' => self::$currentIndex.'&amp;add'.$this->table.'&amp;id_newsfeed_category='.(int)$this->id_newsfeed_category.'&amp;token='.$this->token,
			'desc' => $this->l('Add new')
		);

		return parent::renderList();
	}

	public function displayList($token = null)
	{
		/* Display list header (filtering, pagination and column names) */
		$this->displayListHeader($token);
		if (!count($this->_list))
			echo '<tr><td class="center" colspan="'.(count($this->fields_list) + 2).'">'.$this->l('No items found').'</td></tr>';

		/* Show the content of the table */
		$this->displayListContent($token);

		/* Close list table and submit button */
		$this->displayListFooter($token);
	}
	
	public function postProcess()
	{	
		if (Tools::isSubmit('viewnewsfeed') && ($id_newsfeed = (int)Tools::getValue('id_newsfeed')))
		{
			parent::postProcess();
			if (($newsfeed= new Newsfeed($id_newsfeed, $this->context->language->id)) && Validate::isLoadedObject($newsfeed))
			{
				$this->redirect_after = $this->getPreviewUrl($newsfeed);
				Tools::redirectAdmin($this->redirect_after);
			}
		}
		elseif (Tools::isSubmit('deletenewsfeed'))
		{
			if (Tools::getValue('id_newsfeed') == Configuration::get('PS_CONDITIONS_Newsfeed_ID'))
			{
				Configuration::updateValue('PS_CONDITIONS', 0);
				Configuration::updateValue('PS_CONDITIONS_Newsfeed_ID', 0);
			}
			$newsfeed = new Newsfeed((int)Tools::getValue('id_newsfeed'));
			$newsfeed->cleanPositions($newsfeed->id_newsfeed_category);
			if (!$newsfeed->delete())
				$this->errors[] = Tools::displayError('An error occurred while deleting the object.')
					.' <b>'.$this->table.' ('.Db::getInstance()->getMsgError().')</b>';
			else
				Tools::redirectAdmin(self::$currentIndex.'&id_newsfeed_category='.$newsfeed->id_newsfeed_category.'&conf=1&token='.Tools::getAdminTokenLite('AdminNewsfeedContent'));
		}/* Delete multiple objects */
		elseif (Tools::getValue('submitDel'.$this->table))
		{
			if ($this->tabAccess['delete'] === '1')
			{
				if (Tools::isSubmit($this->table.'Box'))
				{
					$newsfeed = new Newsfeed();
					$result = true;
					$result = $newsfeed->deleteSelection(Tools::getValue($this->table.'Box'));
					if ($result)
					{
						$newsfeed->cleanPositions((int)Tools::getValue('id_newsfeed_category'));
						$token = Tools::getAdminTokenLite('AdminNewsfeedContent');
						Tools::redirectAdmin(self::$currentIndex.'&conf=2&token='.$token.'&id_newsfeed_category='.(int)Tools::getValue('id_newsfeed_category'));
					}
					$this->errors[] = Tools::displayError('An error occurred while deleting this selection.');

				}
				else
					$this->errors[] = Tools::displayError('You must select at least one element to delete.');
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to delete this.');
		}
		elseif (Tools::isSubmit('submitAddnewsfeed') || Tools::isSubmit('submitAddnewsfeedAndPreview'))
		{
			parent::validateRules();
			if (count($this->errors))
                return false;
            if (!$id_newsfeed = (int)Tools::getValue('id_newsfeed'))
            {
                $newsfeed = new Newsfeed();
                $this->copyFromPost($newsfeed, 'newsfeed');
                if (!$newsfeed->add())
                    $this->errors[] = Tools::displayError('An error occurred while creating an object.')
                        .' <b>'.$this->table.' ('.Db::getInstance()->getMsgError().')</b>';
                else
                    $this->updateAssoShop($newsfeed->id);
            }
            else
            {
                $newsfeed = new Newsfeed($id_newsfeed);
                $this->copyFromPost($newsfeed, 'newsfeed');
                if (!$newsfeed->update())
                    $this->errors[] = Tools::displayError('An error occurred while updating an object.')
                        .' <b>'.$this->table.' ('.Db::getInstance()->getMsgError().')</b>';
                else
                    $this->updateAssoShop($newsfeed->id);
            }
            if (Tools::isSubmit('submitAddnewsfeedAndPreview'))
					$this->redirect_after = $this->previewUrl($newsfeed);
            elseif (Tools::isSubmit('submitAdd'.$this->table.'AndStay'))
                Tools::redirectAdmin(self::$currentIndex.'&'.$this->identifier.'='.$newsfeed->id.'&conf=4&update'.$this->table.'&token='.Tools::getAdminTokenLite('AdminNewsfeedContent'));
            else
                Tools::redirectAdmin(self::$currentIndex.'&id_newsfeed_category='.$newsfeed->id_newsfeed_category.'&conf=4&token='.Tools::getAdminTokenLite('AdminNewsfeedContent'));
		}
		elseif (Tools::isSubmit('way') && Tools::isSubmit('id_newsfeed') && (Tools::isSubmit('position')))
		{
			if ($this->tabAccess['edit'] !== '1')
				$this->errors[] = Tools::displayError('You do not have permission to edit this.');
			elseif (!Validate::isLoadedObject($object = $this->loadObject()))
				$this->errors[] = Tools::displayError('An error occurred while updating the status for an object.')
					.' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
			elseif (!$object->updatePosition((int)Tools::getValue('way'), (int)Tools::getValue('position')))
				$this->errors[] = Tools::displayError('Failed to update the position.');
			else
				Tools::redirectAdmin(self::$currentIndex.'&'.$this->table.'Orderby=position&'.$this->table.'Orderway=asc&conf=4&id_newsfeed_category='.(int)$object->id_newsfeed_category.'&token='.Tools::getAdminTokenLite('AdminNewsfeedContent'));
		}
		/* Change object statuts (active, inactive) */
		elseif (Tools::isSubmit('statusnewsfeed') && Tools::isSubmit($this->identifier))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				if (Validate::isLoadedObject($object = $this->loadObject()))
				{
					if ($object->toggleStatus())
						Tools::redirectAdmin(self::$currentIndex.'&conf=5&id_newsfeed_category='.(int)$object->id_newsfeed_category.'&token='.Tools::getValue('token'));
					else
						$this->errors[] = Tools::displayError('An error occurred while updating the status.');
				}
				else
					$this->errors[] = Tools::displayError('An error occurred while updating the status for an object.')
						.' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit this.');
		}
        /* Delete multiple Newsfeed content */
		elseif (Tools::isSubmit('submitBulkdeletenewsfeed'))
		{
			if ($this->tabAccess['delete'] === '1')
			{
                $this->action = 'bulkdelete';
                $this->boxes = Tools::getValue($this->table.'Box');
                if (is_array($this->boxes) && array_key_exists(0, $this->boxes))
                {
                    $firstNewsfeed = new Newsfeed((int)$this->boxes[0]);
                    $id_newsfeed_category = (int)$firstNewsfeed->id_newsfeed_category;
                    if (!$res = parent::postProcess(true))
                        return $res;
                    Tools::redirectAdmin(self::$currentIndex.'&conf=2&token='.Tools::getAdminTokenLite('AdminNewsfeedContent').'&id_newsfeed_category='.$id_newsfeed_category);
                }
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to delete this.');
		}
		else
			parent::postProcess(true);
	}

	public function getPreviewUrl(Newsfeed $newsfeed)
	{			
		$preview_url = $this->context->link->getNewsfeedLink($newsfeed, null, null, $this->context->language->id);
		if (!$newsfeed->active)
		{
			$params = http_build_query(array(
				'adtoken' => Tools::getAdminTokenLite('AdminNewsfeedContent'),
				'ad' => basename(_PS_ADMIN_DIR_),
				'id_employee' => (int)$this->context->employee->id
				)
			);
			$preview_url .= (strpos($preview_url, '?') === false ? '?' : '&').$params;
		}

		return $preview_url;
	}
}


