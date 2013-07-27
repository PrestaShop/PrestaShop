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

/**
 * @since 1.5.0
 */
class BlockWishListMyWishListModuleFrontController extends ModuleFrontController
{
	public function __construct()
	{
		parent::__construct();

		$this->context = Context::getContext();
		$this->ssl = true;

		include_once($this->module->getLocalPath().'WishList.php');
	}

	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		$this->display_column_left = false;
		parent::initContent();

		$this->assign();
	}

	/**
	 * Assign wishlist template
	 */
	public function assign()
	{
		$errors = array();

		if ($this->context->customer->isLogged())
		{
			$add = Tools::getIsset('add');
			$add = (empty($add) === false ? 1 : 0);
			$delete = Tools::getIsset('deleted');
			$delete = (empty($delete) === false ? 1 : 0);
			$id_wishlist = Tools::getValue('id_wishlist');
			if (Tools::isSubmit('submitWishlist'))
			{
				if (Configuration::get('PS_TOKEN_ACTIVATED') == 1 && strcmp(Tools::getToken(), Tools::getValue('token')))
					$errors[] = $this->module->l('Invalid token', 'mywishlist');
				if (!count($errors))
				{
					$name = Tools::getValue('name');
					if (empty($name))
						$errors[] = $this->module->l('You must specify a name.', 'mywishlist');
					if (WishList::isExistsByNameForUser($name))
						$errors[] = $this->module->l('This name is already used by another list.', 'mywishlist');

					if (!count($errors))
					{
						$wishlist = new WishList();
						$wishlist->id_shop = $this->context->shop->id;
						$wishlist->id_shop_group = $this->context->shop->id_shop_group;
						$wishlist->name = $name;
						$wishlist->id_customer = (int)$this->context->customer->id;
						list($us, $s) = explode(' ', microtime());
						srand($s * $us);
						$wishlist->token = strtoupper(substr(sha1(uniqid(rand(), true)._COOKIE_KEY_.$this->context->customer->id), 0, 16));
						$wishlist->add();
						Mail::Send(
							$this->context->language->id,
							'wishlink',
							Mail::l('Your wishlist\'s link', $this->context->language->id),
							array(
							'{wishlist}' => $wishlist->name,
							'{message}' => Tools::getProtocol().htmlentities($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'modules/blockwishlist/view.php?token='.$wishlist->token),
							$this->context->customer->email,
							$this->context->customer->firstname.' '.$this->context->customer->lastname,
							null,
							strval(Configuration::get('PS_SHOP_NAME')),
							null,
							null,
							$this->module->getLocalPath().'mails/');
					}
				}
			}
			else if ($add)
				WishList::addCardToWishlist($this->context->customer->id, Tools::getValue('id_wishlist'), $this->context->language->id);
			elseif ($delete && empty($id_wishlist) === false)
			{
				$wishlist = new WishList((int)($id_wishlist));
				if (Validate::isLoadedObject($wishlist))
					$wishlist->delete();
				else
					$errors[] = $this->module->l('Cannot delete this wishlist', 'mywishlist');
			}
			$this->context->smarty->assign('wishlists', WishList::getByIdCustomer($this->context->customer->id));
			$this->context->smarty->assign('nbProducts', WishList::getInfosByIdCustomer($this->context->customer->id));
		}
		else
			Tools::redirect('index.php?controller=authentication&back='.urlencode($this->context->link->getModuleLink('blockwishlist', 'mywishlist')));

		$this->context->smarty->assign(array(
			'id_customer' => (int)$this->context->customer->id,
			'errors' => $errors,
			'form_link' => $errors,
		));

		$this->setTemplate('mywishlist.tpl');
	}
}
