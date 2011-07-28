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
*  @version  Release: $Revision: 7507 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class CompareControllerCore extends FrontController
{
	public $php_self = 'products-comparison.php';
	
	public function setMedia()
	{
		parent::setMedia();
		$this->addCSS(_THEME_CSS_DIR_.'/comparator.css');
		
		if (Configuration::get('PS_COMPARATOR_MAX_ITEM') > 0)
			Tools::addJS(_THEME_JS_DIR_.'products-comparison.js');
	}

	public function preProcess()
	{
		parent::preProcess();
		
		//Add or remove product with Ajax
		if (Tools::getValue('ajax') AND Tools::getValue('id_product') AND Tools::getValue('action'))
		{				
			if (Tools::getValue('action') == 'add')
			{
				if (isset(self::$cookie->id_customer))
				{
					if(CompareProduct::getCustomerNumberProducts(self::$cookie->id_customer) < Configuration::get('PS_COMPARATOR_MAX_ITEM'))
						CompareProduct::addCustomerCompareProduct((int)self::$cookie->id_customer, (int)Tools::getValue('id_product'));
					else
						die('0');
				}					
				else
				{
					if ((isset(self::$cookie->id_guest) AND CompareProduct::getGuestNumberProducts(self::$cookie->id_guest) < Configuration::get('PS_COMPARATOR_MAX_ITEM')))
						CompareProduct::addGuestCompareProduct((int)self::$cookie->id_guest, (int)Tools::getValue('id_product'));
					else
						die('0');
				}
			}
			elseif (Tools::getValue('action') == 'remove')
			{
				if (isset(self::$cookie->id_customer))
					CompareProduct::removeCustomerCompareProduct((int)self::$cookie->id_customer, (int)Tools::getValue('id_product'));			
				elseif (isset(self::$cookie->id_guest))
					CompareProduct::removeGuestCompareProduct((int)self::$cookie->id_guest, (int)Tools::getValue('id_product'));
				else
					die('0');
			}
			else
				die('0');
			
			die('1');
		}	
	}

	public function process()
	{
		parent::process();

		//Clean compare product table
		CompareProduct::cleanCompareProducts('week');
		
		$hasProduct = false;

		if (!Configuration::get('PS_COMPARATOR_MAX_ITEM'))
				return Tools::redirect('index.php?controller=404');

		if ($product_list = Tools::getValue('compare_product_list') AND $postProducts = (isset($product_list) ? rtrim($product_list,'|') : ''))
			$ids = array_unique(explode('|', $postProducts));
		elseif (isset(self::$cookie->id_customer))
			$ids = CompareProduct::getCustomerCompareProducts(self::$cookie->id_customer);
		elseif(isset(self::$cookie->id_guest))
			$ids = CompareProduct::getGuestCompareProducts(self::$cookie->id_guest);
		else
			$ids = null;

		if ($ids)
		{
			if (sizeof($ids) > 0)
			{
				if (sizeof($ids) > Configuration::get('PS_COMPARATOR_MAX_ITEM'))
					$ids = array_slice($ids, 0,  Configuration::get('PS_COMPARATOR_MAX_ITEM'));

				$listProducts = array();
				$listFeatures = array();

				foreach ($ids AS $k => &$id)
				{
					$curProduct = new Product((int)$id, true, $this->context->language->id);
					if (!$curProduct->active OR !$curProduct->isAssociatedToShop())
					{
						unset($ids[$k]);
						continue;
					}

					if (!$curProduct->active OR !$curProduct->isAssociatedToShop())
					{
						unset($ids[$k]);
						continue;
					}

					if (!Validate::isLoadedObject($curProduct))
						continue;

					if (!$curProduct->active)
					{
						unset($ids[$k]);
						continue;
					}

					foreach ($curProduct->getFrontFeatures($this->context->language->id) AS $feature)
						$listFeatures[$curProduct->id][$feature['id_feature']] = $feature['value'];

					$cover = Product::getCover((int)$id);

					$curProduct->id_image = Tools::htmlentitiesUTF8(Product::defineProductImage(array('id_image' => $cover['id_image'], 'id_product' => $id), $this->context->language->id));
					$curProduct->allow_oosp = Product::isAvailableWhenOutOfStock($curProduct->out_of_stock);
					$listProducts[] = $curProduct;
				}

				if (sizeof($listProducts) > 0)
				{
					$width = 80 / sizeof($listProducts);

					$hasProduct = true;
					$ordered_features = Feature::getFeaturesForComparison($ids, $this->context->language->id);
					$this->context->smarty->assign(array(
						'ordered_features' => $ordered_features,
						'product_features' => $listFeatures,
						'products' => $listProducts,
						'width' => $width,
						'homeSize' => Image::getSize('home')
					));
					$this->context->smarty->assign('HOOK_EXTRA_PRODUCT_COMPARISON', Module::hookExec('extraProductComparison', array('list_ids_product' => $ids)));
				}
			}
		}
		$this->context->smarty->assign('hasProduct', $hasProduct);
	}

	public function displayContent()
	{
		parent::displayContent();
		$this->context->smarty->display(_PS_THEME_DIR_.'products-comparison.tpl');
	}
}

