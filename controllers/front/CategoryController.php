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

class CategoryControllerCore extends FrontController
{
	public $php_self = 'category';
	protected $category;

	/**
	 * Set default medias for this controller
	 */
	public function setMedia()
	{
		parent::setMedia();

		if ($this->context->getMobileDevice() == false)
		{
			//TODO : check why cluetip css is include without js file
			$this->addCSS(array(
				_THEME_CSS_DIR_.'scenes.css' => 'all',
				_THEME_CSS_DIR_.'category.css' => 'all',
				_THEME_CSS_DIR_.'product_list.css' => 'all',
			));

			if (Configuration::get('PS_COMPARATOR_MAX_ITEM') > 0)
				$this->addJS(_THEME_JS_DIR_.'products-comparison.js');
		}
	}

	public function canonicalRedirection($canonicalURL = '')
	{
		if (!Validate::isLoadedObject($this->category) || !$this->category->inShop() || !$this->category->isAssociatedToShop())
		{
			$this->redirect_after = '404';
			$this->redirect();
		}
		if (!Tools::getValue('noredirect') && Validate::isLoadedObject($this->category))
			parent::canonicalRedirection($this->context->link->getCategoryLink($this->category));
	}

	/**
	 * Initialize category controller
	 * @see FrontController::init()
	 */
	public function init()
	{
		// Get category ID
		$id_category = (int)Tools::getValue('id_category');
		if (!$id_category || !Validate::isUnsignedId($id_category))
			$this->errors[] = Tools::displayError('Missing category ID');

		// Instantiate category
		$this->category = new Category($id_category, $this->context->language->id);

		parent::init();

		if (!$this->category->checkAccess($this->context->customer->id))
			$this->errors[] = Tools::displayError('You do not have access to this category.');
	}

	public function initContent()
	{
		parent::initContent();

		if (isset($this->context->cookie->id_compare))
			$this->context->smarty->assign('compareProducts', CompareProduct::getCompareProducts((int)$this->context->cookie->id_compare));

		$this->productSort(); // Product sort must be called before assignProductList()
		
		$this->assignScenes();
		$this->assignSubcategories();
		if ($this->category->id != 1)
			$this->assignProductList();

		$this->context->smarty->assign(array(
			'category' => $this->category,
			'products' => (isset($this->cat_products) && $this->cat_products) ? $this->cat_products : null,
			'id_category' => (int)$this->category->id,
			'id_category_parent' => (int)$this->category->id_parent,
			'return_category_name' => Tools::safeOutput($this->category->name),
			'path' => Tools::getPath($this->category->id),
			'add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
			'categorySize' => Image::getSize('category_default'),
			'mediumSize' => Image::getSize('medium_default'),
			'thumbSceneSize' => Image::getSize('m_scene_default'),
			'homeSize' => Image::getSize('home_default'),
			'allow_oosp' => (int)Configuration::get('PS_ORDER_OUT_OF_STOCK'),
			'comparator_max_item' => (int)Configuration::get('PS_COMPARATOR_MAX_ITEM'),
			'suppliers' => Supplier::getSuppliers()
		));


		$this->setTemplate(_PS_THEME_DIR_.'category.tpl');
	}

	/**
	 * Assign scenes template vars
	 */
	protected function assignScenes()
	{
		// Scenes (could be externalised to another controler if you need them)
		$scenes = Scene::getScenes($this->category->id, $this->context->language->id, true, false);
		$this->context->smarty->assign('scenes', $scenes);

		// Scenes images formats
		if ($scenes && ($sceneImageTypes = ImageType::getImagesTypes('scenes')))
		{
			foreach ($sceneImageTypes as $sceneImageType)
			{
				if ($sceneImageType['name'] == 'm_scene_default')
					$thumbSceneImageType = $sceneImageType;
				elseif ($sceneImageType['name'] == 'scene_default')
					$largeSceneImageType = $sceneImageType;
			}

			$this->context->smarty->assign(array(
				'thumbSceneImageType' => isset($thumbSceneImageType) ? $thumbSceneImageType : null,
				'largeSceneImageType' => isset($largeSceneImageType) ? $largeSceneImageType : null,
			));
		}
	}

	/**
	 * Assign sub categories templates vars
	 */
	protected function assignSubcategories()
	{
		if ($subCategories = $this->category->getSubCategories($this->context->language->id))
		{
			$this->context->smarty->assign(array(
				'subcategories' => $subCategories,
				'subcategories_nb_total' => count($subCategories),
				'subcategories_nb_half' => ceil(count($subCategories) / 2)
			));
		}
	}

	/**
	 * Assign list of products template vars
	 */
	public function assignProductList()
	{
		$hookExecuted = false;
		Hook::exec('actionProductListOverride', array(
			'nbProducts' => &$this->nbProducts,
			'catProducts' => &$this->cat_products,
			'hookExecuted' => &$hookExecuted,
		));

		// The hook was not executed, standard working
		if (!$hookExecuted)
		{
			$this->context->smarty->assign('categoryNameComplement', '');
			$this->nbProducts = $this->category->getProducts(null, null, null, $this->orderBy, $this->orderWay, true);
			$this->pagination((int)$this->nbProducts); // Pagination must be call after "getProducts"
			$this->cat_products = $this->category->getProducts($this->context->language->id, (int)$this->p, (int)$this->n, $this->orderBy, $this->orderWay);
		}
		// Hook executed, use the override
		else
			// Pagination must be call after "getProducts"
			$this->pagination($this->nbProducts);
		$this->context->smarty->assign('nb_products', $this->nbProducts);
	}
}

