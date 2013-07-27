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

class AdminCmsContentControllerCore extends AdminController
{
	/** @var object adminCMSCategories() instance */
	protected $admin_cms_categories;

	/** @var object adminCMS() instance */
	protected $admin_cms;

	/** @var object Category() instance for navigation*/
	protected static $category = null;

	public function __construct()
	{
		/* Get current category */
		$id_cms_category = (int)Tools::getValue('id_cms_category', Tools::getValue('id_cms_category_parent', 1));
		self::$category = new CMSCategory($id_cms_category);
		if (!Validate::isLoadedObject(self::$category))
			die('Category cannot be loaded');

		$this->table = 'cms';
		$this->className = 'CMS';
		$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));
		$this->admin_cms_categories = new AdminCmsCategoriesController();
		$this->admin_cms = new AdminCmsController();

		parent::__construct();
	}

	/**
	 * Return current category
	 *
	 * @return object
	 */
	public static function getCurrentCMSCategory()
	{
		return self::$category;
	}

	public function viewAccess($disable = false)
	{
		$result = parent::viewAccess($disable);
		$this->admin_cms_categories->tabAccess = $this->tabAccess;
		$this->admin_cms->tabAccess = $this->tabAccess;
		return $result;
	}

	public function initContent()
	{
		$this->admin_cms_categories->token = $this->token;
		$this->admin_cms->token = $this->token;

		if ($this->display == 'edit_category')
			$this->content .= $this->admin_cms_categories->renderForm();
		else if ($this->display == 'edit_page')
			$this->content .= $this->admin_cms->renderForm();
		else if ($this->display == 'view_page')
			$fixme = 'fixme';// @FIXME
		else
		{
			$id_cms_category = (int)Tools::getValue('id_cms_category');
			if (!$id_cms_category)
				$id_cms_category = 1;

			// CMS categories breadcrumb
			$cms_tabs = array('cms_category', 'cms');
			// Cleaning links
			$cat_bar_index = self::$currentIndex;
			foreach ($cms_tabs as $tab)
				if (Tools::getValue($tab.'Orderby') && Tools::getValue($tab.'Orderway'))
					$cat_bar_index = preg_replace('/&'.$tab.'Orderby=([a-z _]*)&'.$tab.'Orderway=([a-z]*)/i', '', self::$currentIndex);

			$this->content .= $this->admin_cms_categories->renderList();
			$this->admin_cms->id_cms_category = $id_cms_category;
			$this->content .= $this->admin_cms->renderList();
			$this->context->smarty->assign(array(
				'cms_breadcrumb' => getPath($cat_bar_index, $id_cms_category, '', '', 'cms'),
			));
		}

		$this->context->smarty->assign(array(
			'content' => $this->content
		));
	}

	public function postProcess()
	{
		if (Tools::isSubmit('submitDelcms')
			|| Tools::isSubmit('previewSubmitAddcmsAndPreview')
			|| Tools::isSubmit('submitAddcms')
            || Tools::isSubmit('submitBulkdeletecms')
			|| Tools::isSubmit('deletecms')
			|| Tools::isSubmit('viewcms')
			|| (Tools::isSubmit('statuscms') && Tools::isSubmit('id_cms'))
			|| (Tools::isSubmit('way') && Tools::isSubmit('id_cms')) && (Tools::isSubmit('position')))
			$this->admin_cms->postProcess();
		elseif (Tools::isSubmit('submitDelcms_category')
			|| Tools::isSubmit('submitAddcms_categoryAndBackToParent')
			|| Tools::isSubmit('submitBulkdeletecms_category')
			|| Tools::isSubmit('submitAddcms_category')
			|| Tools::isSubmit('deletecms_category')
			|| (Tools::isSubmit('statuscms_category') && Tools::isSubmit('id_cms_category'))
			|| (Tools::isSubmit('position') && Tools::isSubmit('id_cms_category_to_move')))
				$this->admin_cms_categories->postProcess();
        else
            parent::postProcess();

		if (((Tools::isSubmit('submitAddcms_category') || Tools::isSubmit('submitAddcms_categoryAndStay')) && count($this->admin_cms_categories->errors))
			|| Tools::isSubmit('updatecms_category')
			|| Tools::isSubmit('addcms_category'))
			$this->display = 'edit_category';
		else if (((Tools::isSubmit('submitAddcms') || Tools::isSubmit('submitAddcmsAndStay')) && count($this->admin_cms->errors))
			|| Tools::isSubmit('updatecms')
			|| Tools::isSubmit('addcms'))
			$this->display = 'edit_page';
		else
		{
			$this->display = 'list';
			$this->id_cms_category = (int)Tools::getValue('id_cms_category');
		}

		if (isset($this->admin_cms->errors))
			$this->errors = array_merge($this->errors, $this->admin_cms->errors);

		if (isset($this->admin_cms_categories->errors))
			$this->errors = array_merge($this->errors, $this->admin_cms_categories->errors);
	}

	public function setMedia()
	{
		parent::setMedia();
		$this->addJqueryUi('ui.widget');
		$this->addJqueryPlugin('tagify');
	}

	public function ajaxProcessUpdateCmsPositions()
	{
		if ($this->tabAccess['edit'] === '1')
		{
			$id_cms = (int)Tools::getValue('id_cms');
			$id_category = (int)Tools::getValue('id_cms_category');
			$way = (int)Tools::getValue('way');
			$positions = Tools::getValue('cms');
			if (is_array($positions))
				foreach ($positions as $key => $value)
				{
					$pos = explode('_', $value);
					if ((isset($pos[1]) && isset($pos[2])) && ($pos[1] == $id_category && $pos[2] == $id_cms))
					{
						$position = $key;
						break;
					}
				}
			$cms = new CMS($id_cms);
			if (Validate::isLoadedObject($cms))
			{
				if (isset($position) && $cms->updatePosition($way, $position))
					die(true);
				else
					die('{"hasError" : true, "errors" : "Can not update cms position"}');
			}
			else
				die('{"hasError" : true, "errors" : "This cms can not be loaded"}');
		}
	}

	public function ajaxProcessUpdateCmsCategoriesPositions()
	{
		if ($this->tabAccess['edit'] === '1')
		{
			$id_cms_category_to_move = (int)Tools::getValue('id_cms_category_to_move');
			$id_cms_category_parent = (int)Tools::getValue('id_cms_category_parent');
			$way = (int)Tools::getValue('way');
			$positions = Tools::getValue('cms_category');
			if (is_array($positions))
				foreach ($positions as $key => $value)
				{
					$pos = explode('_', $value);
					if ((isset($pos[1]) && isset($pos[2])) && ($pos[1] == $id_cms_category_parent && $pos[2] == $id_cms_category_to_move))
					{
						$position = $key;
						break;
					}
				}
			$cms_category = new CMSCategory($id_cms_category_to_move);
			if (Validate::isLoadedObject($cms_category))
			{
				if (isset($position) && $cms_category->updatePosition($way, $position))
					die(true);
				else
					die('{"hasError" : true, "errors" : "Can not update cms categories position"}');
			}
			else
				die('{"hasError" : true, "errors" : "This cms category can not be loaded"}');
		}
	}

	public function ajaxProcessPublishCMS()
	{
		if ($this->tabAccess['edit'] === '1')
		{
			if ($id_cms = (int)Tools::getValue('id_cms'))
			{
				$bo_cms_url = dirname($_SERVER['PHP_SELF']).'/index.php?tab=AdminCmsContent&id_cms='.(int)$id_cms.'&updatecms&token='.$this->token;

				if (Tools::getValue('redirect'))
					die($bo_cms_url);

				$cms = new CMS((int)(Tools::getValue('id_cms')));
				if (!Validate::isLoadedObject($cms))
					die('error: invalid id');

				$cms->active = 1;
				if ($cms->save())
					die($bo_cms_url);
				else
					die('error: saving');
			}
			else
				die ('error: parameters');
		}
	}

}
