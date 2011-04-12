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

class CategoryControllerCore extends FrontController
{
	protected $category;

	public function setMedia()
	{
		parent::setMedia();
		Tools::addCSS(array(
			_PS_CSS_DIR_.'jquery.cluetip.css' => 'all',
			_THEME_CSS_DIR_.'scenes.css' => 'all',
			_THEME_CSS_DIR_.'category.css' => 'all',
			_THEME_CSS_DIR_.'product_list.css' => 'all'));

		if (Configuration::get('PS_COMPARATOR_MAX_ITEM') > 0)
			Tools::addJS(_THEME_JS_DIR_.'products-comparison.js');
	}

	public function displayHeader()
	{
		parent::displayHeader();
		$this->productSort();
	}

	public function preProcess()
	{
		if ($id_category = (int)Tools::getValue('id_category'))
			$this->category = new Category($id_category, self::$cookie->id_lang);
		if (!Validate::isLoadedObject($this->category))
		{
			header('HTTP/1.1 404 Not Found');
			header('Status: 404 Not Found');
		}
		else
		{
			// Automatically redirect to the canonical URL if the current in is the right one
			// $_SERVER['HTTP_HOST'] must be replaced by the real canonical domain
			$currentURL = self::$link->getCategoryLink($this->category);
			$currentURL = preg_replace('/[?&].*$/', '', $currentURL);
			if (!preg_match('/^'.Tools::pRegexp($currentURL, '/').'([&?].*)?$/', Tools::getProtocol().$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']))
			{
				header('HTTP/1.0 301 Moved');
				if (defined('_PS_MODE_DEV_') AND _PS_MODE_DEV_ )
					die('[Debug] This page has moved<br />Please use the following URL instead: <a href="'.$currentURL.'">'.$currentURL.'</a>');
				Tools::redirectLink($currentURL);
			}
		}
	
		parent::preProcess();
		
		if((int)(Configuration::get('PS_REWRITING_SETTINGS')))
			if ($id_category = (int)Tools::getValue('id_category'))
			{
				$rewrite_infos = Category::getUrlRewriteInformations((int)$id_category);

				$default_rewrite = array();
				foreach ($rewrite_infos AS $infos)
					$default_rewrite[$infos['id_lang']] = self::$link->getCategoryLink((int)$id_category, $infos['link_rewrite'], $infos['id_lang']);

				self::$smarty->assign('lang_rewrite_urls', $default_rewrite);
			}
	}
	
	public function process()
	{
		parent::process();
		if (!($id_category = (int)Tools::getValue('id_category')) OR !Validate::isUnsignedId($id_category))
			$this->errors[] = Tools::displayError('Missing category ID');
		else
		{
			if (!Validate::isLoadedObject($this->category))
				$this->errors[] = Tools::displayError('Category does not exist');
			elseif (!$this->category->checkAccess((int)(self::$cookie->id_customer)))
				$this->errors[] = Tools::displayError('You do not have access to this category.');
			elseif (!$this->category->active)
				self::$smarty->assign('category', $this->category);
			else
			{
				$rewrited_url = self::$link->getCategoryLink((int)$this->category->id, $this->category->link_rewrite);

				/* Scenes  (could be externalised to another controler if you need them */
				self::$smarty->assign('scenes', Scene::getScenes((int)($this->category->id), (int)(self::$cookie->id_lang), true, false));
				
				/* Scenes images formats */
				if ($sceneImageTypes = ImageType::getImagesTypes('scenes'))
				{
					foreach ($sceneImageTypes AS $sceneImageType)
					{
						if ($sceneImageType['name'] == 'thumb_scene')
							$thumbSceneImageType = $sceneImageType;
						elseif ($sceneImageType['name'] == 'large_scene')
							$largeSceneImageType = $sceneImageType;
					}
					self::$smarty->assign('thumbSceneImageType', isset($thumbSceneImageType) ? $thumbSceneImageType : NULL);
					self::$smarty->assign('largeSceneImageType', isset($largeSceneImageType) ? $largeSceneImageType : NULL);
				}

				$this->category->description = nl2br2($this->category->description);
				$subCategories = $this->category->getSubCategories((int)(self::$cookie->id_lang));
				self::$smarty->assign('category', $this->category);
				if (Db::getInstance()->numRows())
				{
					self::$smarty->assign('subcategories', $subCategories);
					self::$smarty->assign(array(
						'subcategories_nb_total' => sizeof($subCategories),
						'subcategories_nb_half' => ceil(sizeof($subCategories) / 2)));
				}
				if ($this->category->id != 1)
				{
					$nbProducts = $this->category->getProducts(NULL, NULL, NULL, $this->orderBy, $this->orderWay, true);
					$this->pagination((int)$nbProducts);
					self::$smarty->assign('nb_products', (int)$nbProducts);
					$cat_products = $this->category->getProducts((int)(self::$cookie->id_lang), (int)($this->p), (int)($this->n), $this->orderBy, $this->orderWay);
				}
				self::$smarty->assign(array(
					'products' => (isset($cat_products) AND $cat_products) ? $cat_products : NULL,
					'id_category' => (int)($this->category->id),
					'id_category_parent' => (int)($this->category->id_parent),
					'return_category_name' => Tools::safeOutput($this->category->name),
					'path' => Tools::getPath((int)($this->category->id)),
					'add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
					'categorySize' => Image::getSize('category'),
					'mediumSize' => Image::getSize('medium'),
					'thumbSceneSize' => Image::getSize('thumb_scene'),
					'homeSize' => Image::getSize('home')
				));
			}
		}

		self::$smarty->assign(array(
			'allow_oosp' => (int)(Configuration::get('PS_ORDER_OUT_OF_STOCK')),
			'comparator_max_item' => (int)(Configuration::get('PS_COMPARATOR_MAX_ITEM')),
			'suppliers' => Supplier::getSuppliers()
		));
	}

	public function displayContent()
	{
		parent::displayContent();
		self::$smarty->display(_PS_THEME_DIR_.'category.tpl');
	}
}

