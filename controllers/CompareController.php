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

class CompareControllerCore extends FrontController
{
	public function setMedia()
	{
		parent::setMedia();
		Tools::addCSS(_THEME_CSS_DIR_.'/comparator.css');
	}

	public function process()
	{
		parent::process();

		$hasProduct = false;
		$product_list = Tools::getValue('compare_product_list');
		$postProducts = isset($product_list) ? rtrim($product_list,'|') : '';

		if (!Configuration::get('PS_COMPARATOR_MAX_ITEM'))
				return Tools::redirect('404.php');

		if ($postProducts)
		{
			$ids = array_unique(explode('|', $postProducts));

			if (sizeof($ids) > 0)
			{
				if (sizeof($ids) > Configuration::get('PS_COMPARATOR_MAX_ITEM'))
					$ids = array_slice($ids, 0,  Configuration::get('PS_COMPARATOR_MAX_ITEM'));

				$listProducts = array();
				$listFeatures = array();

				foreach ($ids AS $id)
				{
					$curProduct = new Product((int)($id), true, (int)(self::$cookie->id_lang));
					if (!Validate::isLoadedObject($curProduct))
						continue;

					foreach ($curProduct->getFrontFeatures(self::$cookie->id_lang) AS $feature)
						$listFeatures[$curProduct->id][$feature['id_feature']] = $feature['value'];

					$cover = Product::getCover((int)$id);

					$curProduct->id_image = Tools::htmlentitiesUTF8(Product::defineProductImage(array('id_image' => $cover['id_image'], 'id_product' => $id), self::$cookie->id_lang));
					$listProducts[] = $curProduct;
				}

				if (sizeof($listProducts) > 0)
				{
					$width = 80 / sizeof($listProducts);

					$hasProduct = true;
					$ordered_features = Feature::getFeaturesForComparison($ids, self::$cookie->id_lang);
					self::$smarty->assign(array(
						'ordered_features' => $ordered_features,
						'product_features' => $listFeatures,
						'products' => $listProducts,
						'link' => new Link(),
						'width' => $width,
						'homeSize' => Image::getSize('home')
					));
					self::$smarty->assign('HOOK_EXTRA_PRODUCT_COMPARISON', Module::hookExec('extraProductComparison', array('list_ids_product' => $ids)));
				}
			}
		}
		self::$smarty->assign('hasProduct', $hasProduct);
	}

	public function displayContent()
	{
		parent::displayContent();
		self::$smarty->display(_PS_THEME_DIR_.'products-comparison.tpl');
	}
}

