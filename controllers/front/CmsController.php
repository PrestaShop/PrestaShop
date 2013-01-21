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

class CmsControllerCore extends FrontController
{
	public $php_self = 'cms';
	public $assignCase;
	public $cms;
	public $cms_category;

	public function canonicalRedirection($canonicalURL = '')
	{
		if (Validate::isLoadedObject($this->cms) && ($canonicalURL = $this->context->link->getCMSLink($this->cms)))
			parent::canonicalRedirection($canonicalURL);
		else if (Validate::isLoadedObject($this->cms_category) && ($canonicalURL = $this->context->link->getCMSCategoryLink($this->cms_category)))
			parent::canonicalRedirection($canonicalURL);
	}

	/**
	 * Initialize cms controller
	 * @see FrontController::init()
	 */
	public function init()
	{
		parent::init();

		if ($id_cms = (int)Tools::getValue('id_cms'))
			$this->cms = new CMS($id_cms, $this->context->language->id);
		else if ($id_cms_category = (int)Tools::getValue('id_cms_category'))
			$this->cms_category = new CMSCategory($id_cms_category, $this->context->language->id);

		$this->canonicalRedirection();

		// assignCase (1 = CMS page, 2 = CMS category)
		if (Validate::isLoadedObject($this->cms))
		{
			$adtoken = Tools::getAdminToken('AdminCmsContent'.(int)Tab::getIdFromClassName('AdminCmsContent').(int)Tools::getValue('id_employee'));
			if (!$this->cms->isAssociatedToShop() || !$this->cms->active && Tools::getValue('adtoken') != $adtoken)
			{
				header('HTTP/1.1 404 Not Found');
				header('Status: 404 Not Found');
			}
			else
				$this->assignCase = 1;
		}
		else if (Validate::isLoadedObject($this->cms_category))
			$this->assignCase = 2;
		else
		{
			header('HTTP/1.1 404 Not Found');
			header('Status: 404 Not Found');
		}
	}

	public function setMedia()
	{
		parent::setMedia();

		if ($this->assignCase == 1)
			$this->addJS(_THEME_JS_DIR_.'cms.js');

		$this->addCSS(_THEME_CSS_DIR_.'cms.css');
	}

	/**
	 * Assign template vars related to page content
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();

		$parent_cat = new CMSCategory(1, $this->context->language->id);
		$this->context->smarty->assign('id_current_lang', $this->context->language->id);
		$this->context->smarty->assign('home_title', $parent_cat->name);
		$this->context->smarty->assign('cgv_id', Configuration::get('PS_CONDITIONS_CMS_ID'));
		if (isset($this->cms->id_cms_category) && $this->cms->id_cms_category)
			$path = Tools::getFullPath($this->cms->id_cms_category, $this->cms->meta_title, 'CMS');
		else if (isset($this->cms_category->meta_title))
			$path = Tools::getFullPath(1, $this->cms_category->meta_title, 'CMS');
		if ($this->assignCase == 1)
		{
			$this->context->smarty->assign(array(
				'cms' => $this->cms,
				'content_only' => (int)(Tools::getValue('content_only')),
				'path' => $path
			));
		}
		else if ($this->assignCase == 2)
		{
			$this->context->smarty->assign(array(
				'category' => $this->cms_category, //for backward compatibility
				'cms_category' => $this->cms_category,
				'sub_category' => $this->cms_category->getSubCategories($this->context->language->id),
				'cms_pages' => CMS::getCMSPages($this->context->language->id, (int)($this->cms_category->id) ),
				'path' => ($this->cms_category->id !== 1) ? Tools::getPath($this->cms_category->id, $this->cms_category->name, false, 'CMS') : '',
			));
		}

		$this->setTemplate(_PS_THEME_DIR_.'cms.tpl');
	}
}
