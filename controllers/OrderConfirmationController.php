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

class OrderConfirmationControllerCore extends FrontController
{
	public $id_cart;
	public $id_module;
	public $id_order;
	public $secure_key;
	
	public function __construct()
	{
		$this->php_self = 'order-confirmation.php';
	
		parent::__construct();
	}
	
	public function preProcess()
	{
		parent::preProcess();
		
		$this->id_cart = (int)(Tools::getValue('id_cart', 0));
		
		/* check if the cart has been made by a Guest customer, for redirect link */
		if (Cart::isGuestCartByCartId($this->id_cart))
			$redirectLink = 'guest-tracking.php';
		else
			$redirectLink = 'history.php';
		
		$this->id_module = (int)(Tools::getValue('id_module', 0));
		$this->id_order = Order::getOrderByCartId((int)($this->id_cart));
		$this->secure_key = Tools::getValue('key', false);
		if (!$this->id_order OR !$this->id_module OR !$this->secure_key OR empty($this->secure_key))
			Tools::redirect($redirectLink.(Tools::isSubmit('slowvalidation') ? '?slowvalidation' : ''));

		$order = new Order((int)($this->id_order));
		if (!Validate::isLoadedObject($order) OR $order->id_customer != self::$cookie->id_customer OR $this->secure_key != $order->secure_key)
			Tools::redirect($redirectLink);
		$module = Module::getInstanceById((int)($this->id_module));
		if ($order->payment != $module->displayName)
			Tools::redirect($redirectLink);
	}
	
	public function process()
	{
		parent::process();
		self::$smarty->assign(array(
			'is_guest' => self::$cookie->is_guest,
			'HOOK_ORDER_CONFIRMATION' => Hook::orderConfirmation((int)($this->id_order)),
			'HOOK_PAYMENT_RETURN' => Hook::paymentReturn((int)($this->id_order), (int)($this->id_module))
		));
		
		if (self::$cookie->is_guest)
		{
			self::$smarty->assign(array(
				'id_order' => $this->id_order,
				'id_order_formatted' => sprintf('#%06d', $this->id_order)
			));
			/* If guest we clear the cookie for security reason */
			self::$cookie->logout();
		}
	}
	
	public function displayContent()
	{
		parent::displayContent();
		self::$smarty->display(_PS_THEME_DIR_.'order-confirmation.tpl');
	}
}

