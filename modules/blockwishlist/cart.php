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

require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');
require_once(dirname(__FILE__).'/WishList.php');

$errors = array();

$action = Tools::getValue('action');
$add = (!strcmp($action, 'add') ? 1 : 0);
$delete = (!strcmp($action, 'delete') ? 1 : 0);
$id_wishlist = (int)(Tools::getValue('id_wishlist'));
$id_product = (int)(Tools::getValue('id_product'));
$quantity = (int)(Tools::getValue('quantity'));
$id_product_attribute = (int)(Tools::getValue('id_product_attribute'));
if (Configuration::get('PS_TOKEN_ENABLE') == 1 AND
	strcmp(Tools::getToken(false), Tools::getValue('token')) AND
	$cookie->isLogged() === true)
	$errors[] = Tools::displayError('Invalid token');
if ($cookie->isLogged())
{
	if ($id_wishlist AND WishList::exists($id_wishlist, $cookie->id_customer) === true)
		$cookie->id_wishlist = (int)($id_wishlist);
	if (empty($cookie->id_wishlist) === true OR $cookie->id_wishlist == false)
		$smarty->assign('error', true);
	if (($add OR $delete) AND empty($id_product) === false)
	{
		if(!isset($cookie->id_wishlist) OR $cookie->id_wishlist == '')
		{
			$wishlist = new WishList();
			$wishlist->name = 'My WishList';
			$wishlist->id_customer = (int)($cookie->id_customer);
			list($us, $s) = explode(' ', microtime());
			srand($s * $us);
			$wishlist->token = strtoupper(substr(sha1(uniqid(rand(), true)._COOKIE_KEY_.$cookie->id_customer), 0, 16));
			$wishlist->add();
			$cookie->id_wishlist = (int)($wishlist->id);
		}
		if ($add AND $quantity)
			WishList::addProduct($cookie->id_wishlist, $cookie->id_customer, $id_product, $id_product_attribute, $quantity);
		else if ($delete)
			WishList::removeProduct($cookie->id_wishlist, $cookie->id_customer, $id_product, $id_product_attribute);
	}
	$smarty->assign('products', WishList::getProductByIdCustomer($cookie->id_wishlist, $cookie->id_customer, $cookie->id_lang, null, true));
	
	if (Tools::file_exists_cache(_PS_THEME_DIR_.'modules/blockwishlist/blockwishlist-ajax.tpl'))
		$smarty->display(_PS_THEME_DIR_.'modules/blockwishlist/blockwishlist-ajax.tpl');
	elseif (Tools::file_exists_cache(dirname(__FILE__).'/blockwishlist-ajax.tpl'))
		$smarty->display(dirname(__FILE__).'/blockwishlist-ajax.tpl');
	else
		echo Tools::displayError('No template found');
}
else
	$errors[] = Tools::displayError('You must be logged in to manage your wishlist.');
	
if (sizeof($errors))
{
	$smarty->assign('errors', $errors);
	$smarty->display(_PS_THEME_DIR_.'errors.tpl');
}
