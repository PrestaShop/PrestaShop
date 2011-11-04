<?php
/*
* 2007-2011 PrestaShop 
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 7499 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/


class AdminCatalogController extends AdminController
{
	/** @var object AdminCategories() instance */
	//private $adminCategories;

	/** @var object AdminProducts() instance */
	// private $adminProducts;

	/** @var object AttributeGenerator() instance */
	private $attributeGenerator;

	/** @var object imageResize() instance */
	private $imageResize;

	/** @var object Category() instance for navigation*/
	private static $_category = NULL;

	public function __construct()
	{
		/* Get current category */
		$id_category = abs(Tools::getValue('id_category'));
		$shop = Context::getContext()->shop;
		if (!$id_category)
			$id_category = $shop->getCategory();
		else if ($id_category != $shop->getCategory())
		{
			// Check if current category is "inside" shop default category
			$sql = 'SELECT nleft, nright FROM '._DB_PREFIX_.'category
					WHERE id_category = '.$shop->getCategory();
			if ($interval = Category::getInterval($shop->getCategory()))
			{
				$sql = 'SELECT id_category FROM '._DB_PREFIX_.'category
						WHERE id_category = '.(int)$id_category.'
							AND nleft >= '.$interval['nleft'].'
							AND nright <= '.$interval['nright'];
				if (!Db::getInstance()->getValue($sql))
					$id_category = $shop->getCategory();
			}
		}
		self::$_category = new Category($id_category);
		if (!Validate::isLoadedObject(self::$_category))
			throw new PrestashopException('Category cannot be loaded');

		$this->table = array('category', 'product');
		// $this->adminCategories = new AdminCategories();
		// $this->adminProducts = new AdminProducts();

		parent::__construct();
	}

	/**
	 * Return current category
	 *
	 * @return object
	 */
	public static function getCurrentCategory()
	{
		return self::$_category;
	}

	public function viewAccess($disable = false)
	{
		$result = parent::viewAccess($disable);
		$this->adminCategories->tabAccess = $this->tabAccess;
		$this->adminProducts->tabAccess = $this->tabAccess;
		return $result;
	}

	public function postProcess()
	{
		if (isset($_GET['attributegenerator']))
		{
			if (!isset($this->attributeGenerator))
			{
				include_once(_PS_ADMIN_DIR_.'/tabs/AdminAttributeGenerator.php');
				$this->attributeGenerator = new AdminAttributeGenerator();
			}
			$this->attributeGenerator->postProcess();
		}
	 //	$this->adminProducts->postProcess($this->token);
	}
	public function ajaxProcess()
	{
		if (Tools::getValue('addImage') !== false)
			$this->adminProducts->ajaxProcess();
		if (Tools::getValue('updateProductImageShopAsso'))
			$this->adminProducts->ajaxProcess();
		if (Tools::getValue('deleteImage'))
			$this->adminProducts->ajaxProcess();
			
	}

	public function displayErrors()
	{
		parent::displayErrors();
		$this->adminProducts->displayErrors();
		$this->adminCategories->displayErrors();
		if (Validate::isLoadedObject($this->attributeGenerator))
			$this->attributeGenerator->displayErrors();
		if (Validate::isLoadedObject($this->imageResize))
			$this->imageResize->displayErrors();
	}

	public function initContent()
	{
		// @todo : this has to be dealt in AdminAttributeGenerator !!
		if (isset($_GET['attributegenerator']))
		{
			if (!isset($this->attributeGenerator))
			{
				include_once(_PS_ADMIN_DIR_.'/tabs/AdminAttributeGenerator.php');
				$this->attributeGenerator = new AdminAttributeGenerator();
			}
			$this->attributeGenerator->displayForm();
		}
		elseif (!isset($_GET['editImage']))
		{
			$home = false;
			$id_category = (int)(Tools::getValue('id_category'));
			if (!$id_category)
			{
				$home = true;
				$id_category = Context::getContext()->shop->getCategory();
			}
			$catalog_tabs = array('category', 'product');
			// Cleaning links
			$catBarIndex = self::$currentIndex;
			foreach ($catalog_tabs AS $tab)
				if (Tools::getValue($tab.'Orderby') && Tools::getValue($tab.'Orderway')) 
					$catBarIndex = preg_replace('/&'.$tab.'Orderby=([a-z _]*)&'.$tab.'Orderway=([a-z]*)/i', '', self::$currentIndex);
					
			$this->context->smarty->assign('cat_bar',getPath($catBarIndex, $id_category, '', '', 'catalog', $home));
		}
		$this->content = ''; 
		parent::initContent();
	}
}


