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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

include_once(PS_ADMIN_DIR.'/../classes/AdminTab.php');
include(PS_ADMIN_DIR.'/tabs/AdminCategories.php');
include(PS_ADMIN_DIR.'/tabs/AdminProducts.php');

class AdminCatalog extends AdminTab
{
	/** @var object AdminCategories() instance */
	private $adminCategories;

	/** @var object AdminProducts() instance */
	private $adminProducts;

	/** @var object AttributeGenerator() instance */
	private $attributeGenerator;

	/** @var object imageResize() instance */
	private $imageResize;

	/** @var object Category() instance for navigation*/
	private static $_category = NULL;

	public function __construct()
	{
		/* Get current category */
		$id_category = abs((int)(Tools::getValue('id_category')));
		if (!$id_category) $id_category = 1;
		self::$_category = new Category($id_category);
		if (!Validate::isLoadedObject(self::$_category))
			die('Category cannot be loaded');

		$this->table = array('category', 'product');
		$this->adminCategories = new AdminCategories();
		$this->adminProducts = new AdminProducts();

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
		if (!Tools::getValue('id_product'))
			$this->adminCategories->postProcess();
		elseif (isset($_GET['attributegenerator']))
		{
			if (!isset($this->attributeGenerator))
			{
				include_once(PS_ADMIN_DIR.'/tabs/AdminAttributeGenerator.php');
				$this->attributeGenerator = new AdminAttributeGenerator();
			}
			$this->attributeGenerator->postProcess();
		}
		elseif (isset($_GET['imageresize']))
		{
			if (!isset($this->imageResize))
			{
				include_once(PS_ADMIN_DIR.'/tabs/AdminImageResize.php');
				$this->imageResize = new AdminImageResize();
			}
			$this->imageResize->postProcess();
		}
		$this->adminProducts->postProcess($this->token);
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

	public function display()
	{
		global $currentIndex;

		if (((Tools::isSubmit('submitAddcategory') OR Tools::isSubmit('submitAddcategoryAndStay')) AND sizeof($this->adminCategories->_errors)) OR isset($_GET['updatecategory']) OR isset($_GET['addcategory']))
		{
			$this->adminCategories->displayForm($this->token);
			echo '<br /><br /><a href="'.$currentIndex.'&token='.$this->token.'"><img src="../img/admin/arrow2.gif" /> '.$this->l('Back to list').'</a><br />';
		}
		elseif (((Tools::isSubmit('submitAddproduct') OR Tools::isSubmit('submitAddproductAndPreview') OR Tools::isSubmit('submitAddproductAndStay') OR Tools::isSubmit('submitSpecificPricePriorities') OR Tools::isSubmit('submitPriceAddition') OR Tools::isSubmit('submitPricesModification')) AND sizeof($this->adminProducts->_errors)) OR Tools::isSubmit('updateproduct') OR Tools::isSubmit('addproduct'))
		{
			$this->adminProducts->displayForm($this->token);
			if (Tools::getValue('id_category') > 1)
				echo '<br /><br /><a href="index.php?tab='.Tools::getValue('tab').'&token='.$this->token.'"><img src="../img/admin/arrow2.gif" /> '.$this->l('Back to home').'</a><br />';
			else
				echo '<br /><br /><a href="index.php?tab='.Tools::getValue('tab').'&token='.$this->token.'"><img src="../img/admin/arrow2.gif" /> '.$this->l('Back to catalog').'</a><br />';
		}
		elseif (isset($_GET['attributegenerator']))
		{
			if (!isset($this->attributeGenerator))
			{
				include_once(PS_ADMIN_DIR.'/tabs/AdminAttributeGenerator.php');
				$this->attributeGenerator = new AdminAttributeGenerator();
			}
			$this->attributeGenerator->displayForm();
		}
		elseif (isset($_GET['imageresize']))
		{
			if (!isset($this->imageResize))
			{
				include_once(PS_ADMIN_DIR.'/tabs/AdminImageResize.php');
				$this->imageResize = new AdminImageResize();
			}
			$this->imageResize->displayForm();
		}
		elseif (!isset($_GET['editImage']))
		{
			$id_category = (int)(Tools::getValue('id_category'));
			if (!$id_category)
				$id_category = 1;
			$catalog_tabs = array('category', 'product');
			// Cleaning links
			$catBarIndex = $currentIndex;
			foreach ($catalog_tabs AS $tab)
				if (Tools::getValue($tab.'Orderby') && Tools::getValue($tab.'Orderway')) 
					$catBarIndex = preg_replace('/&'.$tab.'Orderby=([a-z _]*)&'.$tab.'Orderway=([a-z]*)/i', '', $currentIndex);
			
			echo '<div class="cat_bar"><span style="color: #3C8534;">'.$this->l('Current category').' :</span>&nbsp;&nbsp;&nbsp;'.getPath($catBarIndex, $id_category).'</div>';
			echo '<h2>'.$this->l('Categories').'</h2>';
			$this->adminCategories->display($this->token);
			echo '<div style="margin:10px">&nbsp;</div>';
			echo '<h2>'.$this->l('Products in this category').'</h2>';
			$this->adminProducts->display($this->token);
		}
	}
}


