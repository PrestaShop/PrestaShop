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

class NewsfeedControllerCore extends FrontController
{
	public $php_self = 'newsfeed';
	public $assignCase;
	public $newsfeed;
	public $newsfeed_category;
	public $ssl = false;

	public function canonicalRedirection($canonicalURL = '')
	{
		if (Tools::getValue('live_edit'))
			return ;
		if (Validate::isLoadedObject($this->newsfeed) && ($canonicalURL = $this->context->link->getNewsfeedLink($this->newsfeed, $this->newsfeed->link_rewrite, $this->ssl)))
			parent::canonicalRedirection($canonicalURL);
		else if (Validate::isLoadedObject($this->newsfeed_category) && ($canonicalURL = $this->context->link->getNewsfeedCategoryLink($this->newsfeed_category)))
			parent::canonicalRedirection($canonicalURL);
	}

	/**
	 * Initialize newsfeed controller
	 * @see FrontController::init()
	 */
	public function init()
	{
		if ($id_newsfeed = (int)Tools::getValue('id_newsfeed'))
			$this->newsfeed = new Newsfeed($id_newsfeed, $this->context->language->id);
		else if ($id_newsfeed_category = (int)Tools::getValue('id_newsfeed_category'))
			$this->newsfeed_category = new NewsfeedCategory($id_newsfeed_category, $this->context->language->id);

		if (Configuration::get('PS_SSL_ENABLED') && Tools::getValue('content_only') && Tools::getValue('id_newsfeed') == (int)Configuration::get('PS_CONDITIONS_Newsfeed_ID') && Validate::isLoadedObject($this->newsfeed))
			$this->ssl = true;
		
		parent::init();

		$this->canonicalRedirection();

		// assignCase (1 = Newsfeed page, 2 = Newsfeed category)
		if (Validate::isLoadedObject($this->newsfeed))
		{
			$adtoken = Tools::getAdminToken('AdminNewsfeedContent'.(int)Tab::getIdFromClassName('AdminNewsfeedContent').(int)Tools::getValue('id_employee'));
			if (!$this->newsfeed->isAssociatedToShop() || !$this->newsfeed->active && Tools::getValue('adtoken') != $adtoken)
			{
				header('HTTP/1.1 404 Not Found');
				header('Status: 404 Not Found');
			}
			else
				$this->assignCase = 1;
		}
		else if (Validate::isLoadedObject($this->newsfeed_category))
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
			$this->addJS(_THEME_JS_DIR_.'newsfeed.js');

		$this->addCSS(_THEME_CSS_DIR_.'newsfeed.css');
	}

	/**
	 * Assign template vars related to page content
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();

		$parent_cat = new NewsfeedCategory(1, $this->context->language->id);
		$this->context->smarty->assign('id_current_lang', $this->context->language->id);
		$this->context->smarty->assign('home_title', $parent_cat->name);
		if (isset($this->newsfeed->id_newsfeed_category) && $this->newsfeed->id_newsfeed_category)
			$path = Tools::getFullPath($this->newsfeed->id_newsfeed_category, $this->newsfeed->meta_title, 'Newsfeed');
		else if (isset($this->newsfeed_category->meta_title))
			$path = Tools::getFullPath(1, $this->newsfeed_category->meta_title, 'Newsfeed');
		if ($this->assignCase == 1)
		{
			$this->context->smarty->assign(array(
				'newsfeed' => $this->newsfeed,
				'content_only' => (int)(Tools::getValue('content_only')),
				'path' => $path,
				'body_classes' => array($this->php_self.'-'.$this->newsfeed->id, $this->php_self.'-'.$this->newsfeed->link_rewrite)
			));

			if ($this->newsfeed->indexation == 0)
				$this->context->smarty->assign('nobots', true);
		}
		else if ($this->assignCase == 2)
		{
			$this->context->smarty->assign(array(
				'category' => $this->newsfeed_category, //for backward compatibility
				'newsfeed_category' => $this->newsfeed_category,
				'sub_category' => $this->newsfeed_category->getSubCategories($this->context->language->id),
				'newsfeed_pages' => Newsfeed::getNewsfeedPages($this->context->language->id, (int)($this->newsfeed_category->id), true, (int)$this->context->shop->id),
				'path' => ($this->newsfeed_category->id !== 1) ? Tools::getPath($this->newsfeed_category->id, $this->newsfeed_category->name, false, 'Newsfeed') : '',
				'body_classes' => array($this->php_self.'-'.$this->newsfeed_category->id, $this->php_self.'-'.$this->newsfeed_category->link_rewrite)
			));
		}

		$this->setTemplate(_PS_THEME_DIR_.'newsfeed.tpl');
	}
}
