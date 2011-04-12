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

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../header.php');
include_once(dirname(__FILE__).'/WishList.php');

$errors = array();

if ($cookie->isLogged())
{
	$add = Tools::getIsset('add');
	$add = (empty($add) === false ? 1 : 0);
	$delete = Tools::getIsset('deleted');
	$delete = (empty($delete) === false ? 1 : 0);
	$id_wishlist = Tools::getValue('id_wishlist');
	if (Tools::isSubmit('submitWishlist'))
	{
		if (Configuration::get('PS_TOKEN_ACTIVATED') == 1 AND
			strcmp(Tools::getToken(), Tools::getValue('token')))
			$errors[] = Tools::displayError('Invalid token');
		if (!sizeof($errors))
		{
			$name = Tools::getValue('name');
			if (empty($name))
				$errors[] = Tools::displayError('You must specify a name.');
			if (WishList::isExistsByNameForUser($name))
				$errors[] = Tools::displayError('This name is already used by another list.');
			
			if(!sizeof($errors))
			{
				$wishlist = new WishList();
				$wishlist->name = $name;
				$wishlist->id_customer = $cookie->id_customer;
				list($us, $s) = explode(' ', microtime());
				srand($s * $us);
				$wishlist->token = strtoupper(substr(sha1(uniqid(rand(), true)._COOKIE_KEY_.$cookie->id_customer), 0, 16));
				$wishlist->add();
				Mail::Send((int)($cookie->id_lang), 'wishlink', Mail::l('Your wishlist\'s link'), 
					array(
					'{wishlist}' => $wishlist->name,
					'{message}' => Tools::getProtocol().htmlentities($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'modules/blockwishlist/view.php?token='.$wishlist->token),
					$cookie->email, $cookie->firstname.' '.$cookie->lastname, NULL, strval(Configuration::get('PS_SHOP_NAME')), NULL, NULL, dirname(__FILE__).'/mails/');
			}
		}
	}
	else if ($add)
		WishList::addCardToWishlist((int)($cookie->id_customer), (int)(Tools::getValue('id_wishlist')), (int)($cookie->id_lang));
	else if ($delete AND empty($id_wishlist) === false)
	{
		$wishlist = new WishList((int)($id_wishlist));
		if (Validate::isLoadedObject($wishlist))
			$wishlist->delete();
		else
			$errors[] = Tools::displayError('Cannot delete this wishlist');
	}
	$smarty->assign('wishlists', WishList::getByIdCustomer((int)($cookie->id_customer)));
	$smarty->assign('nbProducts', WishList::getInfosByIdCustomer((int)($cookie->id_customer)));
}
else
{
	Tools::redirect('authentication.php?back=modules/blockwishlist/mywishlist.php');
}

$smarty->assign(array(
	'id_customer' => (int)($cookie->id_customer),
	'errors' => $errors
));

if (Tools::file_exists_cache(_PS_THEME_DIR_.'modules/blockwishlist/mywishlist.tpl'))
	$smarty->display(_PS_THEME_DIR_.'modules/blockwishlist/mywishlist.tpl');
elseif (Tools::file_exists_cache(dirname(__FILE__).'/mywishlist.tpl'))
	$smarty->display(dirname(__FILE__).'/mywishlist.tpl');
else
	echo Tools::displayError('No template found');

include(dirname(__FILE__).'/../../footer.php');


