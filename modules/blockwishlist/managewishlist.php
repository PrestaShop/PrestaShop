<?php
/*
* 2007-2011 PrestaShop 
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/* SSL Management */
$useSSL = true;

require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');
require_once(dirname(__FILE__).'/WishList.php');

if ($cookie->isLogged())
{
	$action = Tools::getValue('action');
	$id_wishlist = Tools::getValue('id_wishlist');
	$id_product = Tools::getValue('id_product');
	$id_product_attribute = Tools::getValue('id_product_attribute');
	$quantity = Tools::getValue('quantity');
	$priority = Tools::getValue('priority');
	$wishlist = new WishList((int)($id_wishlist));
	$refresh = (($_GET['refresh'] == 'true') ? 1 : 0);
	if (empty($id_wishlist) === false)
	{
		 if (!strcmp($action, 'update'))
		{
			WishList::updateProduct($id_wishlist, $id_product, $id_product_attribute, $priority, $quantity);
		}
		else
		{
			if (!strcmp($action, 'delete'))
				WishList::removeProduct($id_wishlist, (int)($cookie->id_customer), $id_product, $id_product_attribute);
	
			$products = WishList::getProductByIdCustomer($id_wishlist, $cookie->id_customer, $cookie->id_lang);
			$bought = WishList::getBoughtProduct($id_wishlist);
		
			for ($i = 0; $i < sizeof($products); ++$i)
			{
				$obj = new Product((int)($products[$i]['id_product']), false, (int)($cookie->id_lang));
				if (!Validate::isLoadedObject($obj))
					continue;
				else
				{
					if ($products[$i]['id_product_attribute'] != 0)
					{
						$combination_imgs = $obj->getCombinationImages((int)($cookie->id_lang));
						$products[$i]['cover'] = $obj->id.'-'.$combination_imgs[$products[$i]['id_product_attribute']][0]['id_image'];
					}
					else
					{
						$images = $obj->getImages((int)($cookie->id_lang));
						foreach ($images AS $k => $image)
							if ($image['cover'])
							{
								$products[$i]['cover'] = $obj->id.'-'.$image['id_image'];
								break;
							}
					}
					if (!isset($products[$i]['cover']))
						$products[$i]['cover'] = Language::getIsoById($cookie->id_lang).'-default';
				}
				$products[$i]['bought'] = false;
				for ($j = 0, $k = 0; $j < sizeof($bought); ++$j)
				{
					if ($bought[$j]['id_product'] == $products[$i]['id_product'] AND
						$bought[$j]['id_product_attribute'] == $products[$i]['id_product_attribute'])
						$products[$i]['bought'][$k++] = $bought[$j];
				}
			}
		
			$productBoughts = array();
		
			foreach ($products as $product)
				if (sizeof($product['bought']))
					$productBoughts[] = $product;
			$smarty->assign(array(
				'products' => $products,
				'productsBoughts' => $productBoughts,
				'id_wishlist' => $id_wishlist,
				'refresh' => $refresh,
				'token_wish' => $wishlist->token
			));
			
			if (Tools::file_exists_cache(_PS_THEME_DIR_.'modules/blockwishlist/managewishlist.tpl'))
				$smarty->display(_PS_THEME_DIR_.'modules/blockwishlist/managewishlist.tpl');
			elseif (Tools::file_exists_cache(dirname(__FILE__).'/managewishlist.tpl'))
				$smarty->display(dirname(__FILE__).'/managewishlist.tpl');
			else
				echo Tools::displayError('No template found');
		}
	}
}

