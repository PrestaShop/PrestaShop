<?php
/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/* SSL Management */
$useSSL = true;

require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../header.php');
require_once(dirname(__FILE__).'/WishList.php');

$context = Context::getContext();
$token = Tools::getValue('token');

// Instance of module class for translations
$module = new BlockWishList();

if (empty($token) === false)
{
	$wishlist = WishList::getByToken($token);
	if (empty($result) === true || $result === false)
		$errors[] = $module->l('Invalid wishlist token', 'view');
	WishList::refreshWishList($wishlist['id_wishlist']);
	$products = WishList::getProductByIdCustomer((int)($wishlist['id_wishlist']), (int)($wishlist['id_customer']), $context->language->id, null, true);
	for ($i = 0; $i < sizeof($products); ++$i)
	{
		$obj = new Product($products[$i]['id_product'], false, $context->language->id);
		if (!Validate::isLoadedObject($obj))
			continue;
		else
		{
			if ($products[$i]['id_product_attribute'] != 0 && isset($combination_imgs[$products[$i]['id_product_attribute']][0]))
			{
				$combination_imgs = $obj->getCombinationImages($context->language->id);
				$products[$i]['cover'] = $obj->id.'-'.$combination_imgs[$products[$i]['id_product_attribute']][0]['id_image'];
			}
			else
			{
				$images = $obj->getImages($context->language->id);
				foreach ($images AS $k => $image)
				{
					if ($image['cover'])
					{
						$products[$i]['cover'] = $obj->id.'-'.$image['id_image'];
						break;
					}
				}
				if (!isset($products[$i]['cover']))
					$products[$i]['cover'] = $context->language->iso_code.'-default';
			}
		}
	}
	WishList::incCounter((int)($wishlist['id_wishlist']));
	$ajax = Configuration::get('PS_BLOCK_CART_AJAX');
	$context->smarty->assign(array (
		'current_wishlist' => $wishlist,
		'token' => $token,
		'ajax' => ((isset($ajax) AND (int)($ajax) == 1) ? '1' : '0'),
		'wishlists' => WishList::getByIdCustomer((int)($wishlist['id_customer'])),
		'products' => $products));
}

if (Tools::file_exists_cache(_PS_THEME_DIR_.'modules/blockwishlist/view.tpl'))
	$context->smarty->display(_PS_THEME_DIR_.'modules/blockwishlist/view.tpl');
elseif (Tools::file_exists_cache(dirname(__FILE__).'/view.tpl'))
	$context->smarty->display(dirname(__FILE__).'/view.tpl');
else
	echo $module->l('No template found', 'view');

require(dirname(__FILE__).'/../../footer.php');
