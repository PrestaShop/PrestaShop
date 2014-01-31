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

class AdminNewsfeedContentControllerCore extends AdminController
{
	/** @var object adminNewsfeedCategories() instance */
	protected $admin_newsfeed_categories;

	/** @var object adminNewsfeed() instance */
	protected $admin_newsfeed;

	/** @var object Category() instance for navigation*/
	protected static $category = null;

	public function __construct()
	{
		$this->bootstrap = true;
		/* Get current category */
		$id_newsfeed_category = (int)Tools::getValue('id_newsfeed_category', Tools::getValue('id_newsfeed_category_parent', 1));
		self::$category = new NewsfeedCategory($id_newsfeed_category);
		if (!Validate::isLoadedObject(self::$category))
			die('Category cannot be loaded');

		$this->table = 'newsfeed';
		$this->className = 'Newsfeed';
		$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?'), 'icon' => 'icon-trash'));
		$this->admin_newsfeed_categories = new AdminNewsfeedCategoriesController();
		$this->admin_newsfeed_categories->init();
		$this->admin_newsfeed = new AdminNewsfeedController();
		$this->admin_newsfeed->init();

		parent::__construct();
	}

	/**
	 * Return current category
	 *
	 * @return object
	 */
	public static function getCurrentNewsfeedCategory()
	{
		return self::$category;
	}

	public function viewAccess($disable = false)
	{
		$result = parent::viewAccess($disable);
		$this->admin_newsfeed_categories->tabAccess = $this->tabAccess;
		$this->admin_newsfeed->tabAccess = $this->tabAccess;
		return $result;
	}

	public function initContent()
	{
		$this->initTabModuleList();
		$this->content .= $this->renderPageHeaderToolbar();

		$this->admin_newsfeed_categories->token = $this->token;
		$this->admin_newsfeed->token = $this->token;

		if ($this->display == 'edit_category')
			$this->content .= $this->admin_newsfeed_categories->renderForm();
		else if ($this->display == 'edit_page')
			$this->content .= $this->admin_newsfeed->renderForm();
		else if ($this->display == 'view_page')
			$fixme = 'fixme';// @FIXME
		else
		{
			$id_newsfeed_category = (int)Tools::getValue('id_newsfeed_category');
			if (!$id_newsfeed_category)
				$id_newsfeed_category = 1;

			// Newsfeed categories breadcrumb
			$newsfeed_tabs = array('newsfeed_category', 'newsfeed');
			// Cleaning links
			$cat_bar_index = self::$currentIndex;
			foreach ($newsfeed_tabs as $tab)
				if (Tools::getValue($tab.'Orderby') && Tools::getValue($tab.'Orderway'))
					$cat_bar_index = preg_replace('/&'.$tab.'Orderby=([a-z _]*)&'.$tab.'Orderway=([a-z]*)/i', '', self::$currentIndex);
			$this->context->smarty->assign(array(
				'newsfeed_breadcrumb' => getPath($cat_bar_index, $id_newsfeed_category, '', '', 'newsfeed'),
				'page_header_toolbar_btn' => $this->page_header_toolbar_btn,
				'page_header_toolbar_title' => $this->toolbar_title,
			));
			
			$this->content .= $this->admin_newsfeed_categories->renderList();
			$this->admin_newsfeed->id_newsfeed_category = $id_newsfeed_category;
			$this->content .= $this->admin_newsfeed->renderList();
			
		}

		$this->context->smarty->assign(array(
			'content' => $this->content
		));
	}

	public function renderPageHeaderToolbar()
	{
		$id_newsfeed_category = (int)Tools::getValue('id_newsfeed_category');
		$id_newsfeed_page = Tools::getValue('id_newsfeed');

		if (!$id_newsfeed_category)
			$id_newsfeed_category = 1;

		$newsfeed_category = new NewsfeedCategory($id_newsfeed_category);
		$this->toolbar_title[] = 'Newsfeed';

		if ($this->display == 'edit_category')
		{
			if (Tools::getValue('addnewsfeed_category') !== false)
				$this->toolbar_title[] =$this->l('Add new');
			else
				$this->toolbar_title[] = sprintf($this->l('Edit: %s'), $newsfeed_category->name[$this->context->employee->id_lang]);
		}
		elseif ($this->display == 'edit_page')
		{
			$this->toolbar_title[] = $newsfeed_category->name[$this->context->employee->id_lang];

			if (Tools::getValue('addnewsfeed') !== false)
				$this->toolbar_title[] = $this->l('Add new');
			elseif ($id_newsfeed_page)
			{
				$newsfeed_page = new Newsfeed($id_newsfeed_page);
				$this->toolbar_title[] = sprintf($this->l('Edit: %s'), $newsfeed_page->meta_title[$this->context->employee->id_lang]);
			}
		}
		else
			$this->toolbar_title[] = $newsfeed_category->name[$this->context->employee->id_lang];

		if ($this->display == 'list')
		{
			$this->page_header_toolbar_btn['new_newsfeed_category'] = array(
				'href' => self::$currentIndex.'&addnewsfeed_category&token='.$this->token,
				'desc' => $this->l('Add new Newsfeed category',null,null,false),
				'icon' => 'process-icon-new'
			);
			$this->page_header_toolbar_btn['new_newsfeed_page'] = array(
				'href' => self::$currentIndex.'&addnewsfeed&id_newsfeed_category='.$id_newsfeed_category.'token='.$this->token,
				'desc' => $this->l('Add new Newsfeed page',null,null,false),
				'icon' => 'process-icon-new'
			);
		}

		$this->page_header_toolbar_title = implode(' '.Configuration::get('PS_NAVIGATION_PIPE').' ', $this->toolbar_title);

		if (is_array($this->page_header_toolbar_btn)
			&& $this->page_header_toolbar_btn instanceof Traversable
			|| trim($this->page_header_toolbar_title) != '')
			$this->show_page_header_toolbar = true;

		$template = $this->context->smarty->createTemplate(
			$this->context->smarty->getTemplateDir(0).DIRECTORY_SEPARATOR
			.'page_header_toolbar.tpl', $this->context->smarty);

		$this->context->smarty->assign(array(
			'show_page_header_toolbar' => $this->show_page_header_toolbar,
			'title' => $this->page_header_toolbar_title,
			'toolbar_btn' => $this->page_header_toolbar_btn,
			'page_header_toolbar_btn' => $this->page_header_toolbar_btn,
			'page_header_toolbar_title' => $this->toolbar_title,
		));

		return $template->fetch();
	}

	public function postProcess()
	{
		$this->admin_newsfeed->postProcess();
		$this->admin_newsfeed_categories->postProcess();
		parent::postProcess();

		if (((Tools::isSubmit('submitAddnewsfeed_category') || Tools::isSubmit('submitAddnewsfeed_categoryAndStay')) && count($this->admin_newsfeed_categories->errors))
			|| Tools::isSubmit('updatenewsfeed_category')
			|| Tools::isSubmit('addnewsfeed_category'))
			$this->display = 'edit_category';
		else if (((Tools::isSubmit('submitAddnewsfeed') || Tools::isSubmit('submitAddnewsfeedAndStay')) && count($this->admin_newsfeed->errors))
			|| Tools::isSubmit('updatenewsfeed')
			|| Tools::isSubmit('addnewsfeed'))
			$this->display = 'edit_page';
		else
		{
			$this->display = 'list';
			$this->id_newsfeed_category = (int)Tools::getValue('id_newsfeed_category');
		}

		if (isset($this->admin_newsfeed->errors))
			$this->errors = array_merge($this->errors, $this->admin_newsfeed->errors);

		if (isset($this->admin_newsfeed_categories->errors))
			$this->errors = array_merge($this->errors, $this->admin_newsfeed_categories->errors);
	}

	public function setMedia()
	{
		parent::setMedia();
		$this->addJqueryUi('ui.widget');
		$this->addJqueryPlugin('tagify');
	}

	public function ajaxProcessUpdateNewsfeedPositions()
	{
		if ($this->tabAccess['edit'] === '1')
		{
			$id_newsfeed = (int)Tools::getValue('id_newsfeed');
			$id_category = (int)Tools::getValue('id_newsfeed_category');
			$way = (int)Tools::getValue('way');
			$positions = Tools::getValue('newsfeed');
			if (is_array($positions))
				foreach ($positions as $key => $value)
				{
					$pos = explode('_', $value);
					if ((isset($pos[1]) && isset($pos[2])) && ($pos[1] == $id_category && $pos[2] == $id_newsfeed))
					{
						$position = $key;
						break;
					}
				}
			$newsfeed = new Newsfeed($id_newsfeed);
			if (Validate::isLoadedObject($newsfeed))
			{
				if (isset($position) && $newsfeed->updatePosition($way, $position))
					die(true);
				else
					die('{"hasError" : true, "errors" : "Can not update newsfeed position"}');
			}
			else
				die('{"hasError" : true, "errors" : "This newsfeed can not be loaded"}');
		}
	}

	public function ajaxProcessUpdateNewsfeedCategoriesPositions()
	{
		if ($this->tabAccess['edit'] === '1')
		{
			$id_newsfeed_category_to_move = (int)Tools::getValue('id_newsfeed_category_to_move');
			$id_newsfeed_category_parent = (int)Tools::getValue('id_newsfeed_category_parent');
			$way = (int)Tools::getValue('way');
			$positions = Tools::getValue('newsfeed_category');
			if (is_array($positions))
				foreach ($positions as $key => $value)
				{
					$pos = explode('_', $value);
					if ((isset($pos[1]) && isset($pos[2])) && ($pos[1] == $id_newsfeed_category_parent && $pos[2] == $id_newsfeed_category_to_move))
					{
						$position = $key;
						break;
					}
				}
			$newsfeed_category = new NewsfeedCategory($id_newsfeed_category_to_move);
			if (Validate::isLoadedObject($newsfeed_category))
			{
				if (isset($position) && $newsfeed_category->updatePosition($way, $position))
					die(true);
				else
					die('{"hasError" : true, "errors" : "Can not update newsfeed categories position"}');
			}
			else
				die('{"hasError" : true, "errors" : "This newsfeed category can not be loaded"}');
		}
	}

	public function ajaxProcessPublishNewsfeed()
	{
		if ($this->tabAccess['edit'] === '1')
		{
			if ($id_newsfeed = (int)Tools::getValue('id_newsfeed'))
			{
				$bo_newsfeed_url = _PS_BASE_URL_.__PS_BASE_URI__.basename(_PS_ADMIN_DIR_).'/index.php?tab=AdminNewsfeedContent&id_newsfeed='.(int)$id_newsfeed.'&updatenewsfeed&token='.$this->token;

				if (Tools::getValue('redirect'))
					die($bo_newsfeed_url);

				$newsfeed = new Newsfeed((int)(Tools::getValue('id_newsfeed')));
				if (!Validate::isLoadedObject($newsfeed))
					die('error: invalid id');

				$newsfeed->active = 1;
				if ($newsfeed->save())
					die($bo_newsfeed_url);
				else
					die('error: saving');
			}
			else
				die ('error: parameters');
		}
	}

}
