<?php
/*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class OrderReturnControllerCore extends FrontController
{
	public $auth = true;
	public $php_self = 'order-return';
	public $authRedirection = 'order-follow';
	public $ssl = true;

	/**
	 * Initialize order return controller
	 * @see FrontController::init()
	 */
	public function init()
	{
		parent::init();

		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

		if (!isset($_GET['id_order_return']) || !Validate::isUnsignedId($_GET['id_order_return']))
			$this->errors[] = Tools::displayError('Order ID required');
		else
		{
			$orderRet = new OrderReturn((int)$_GET['id_order_return']);
			if (Validate::isLoadedObject($orderRet) && $orderRet->id_customer == $this->context->cookie->id_customer)
			{
				$order = new Order((int)($orderRet->id_order));
				if (Validate::isLoadedObject($order))
				{
					$state = new OrderReturnState((int)$orderRet->state);
					$this->context->smarty->assign(array(
						'orderRet' => $orderRet,
						'order' => $order,
						'state_name' => $state->name[(int)$this->context->language->id],
						'return_allowed' => false,
						'products' => OrderReturn::getOrdersReturnProducts((int)$orderRet->id, $order),
						'returnedCustomizations' => OrderReturn::getReturnedCustomizedProducts((int)$orderRet->id_order),
						'customizedDatas' => Product::getAllCustomizedDatas((int)$order->id_cart)
					));
				}
				else
					$this->errors[] = Tools::displayError('Cannot find the order return.');
			}
			else
				$this->errors[] = Tools::displayError('Cannot find the order return.');
		}
	}

	/**
	 * Assign template vars related to page content
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();

		$this->context->smarty->assign(array(
			'errors' => $this->errors,
			'nbdaysreturn' => (int)Configuration::get('PS_ORDER_RETURN_NB_DAYS')
		));
		$this->setTemplate(_PS_THEME_DIR_.'order-return.tpl');
	}

	public function displayAjax()
	{
		$this->smartyOutputContent($this->template);
	}
}

