<?php
/**
 * 2007-2015 PrestaShop.
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
use PrestaShop\PrestaShop\Adapter\Order\OrderPresenter;

class GuestTrackingControllerCore extends FrontController
{
    public $ssl = true;
    public $auth = false;
    public $php_self = 'guest-tracking';
    private $order;

    /**
     * Initialize guest tracking controller.
     *
     * @see FrontController::init()
     */
    public function init()
    {
        if ($this->context->customer->isLogged()) {
            Tools::redirect('history.php');
        }

        parent::init();
    }

    /**
     * Start forms process.
     *
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        $order_reference = current(explode('#', Tools::getValue('order_reference')));
        $email = Tools::getValue('email');

        if ($email !== false && $order_reference !== false) {
            $this->order = Order::getByReferenceAndEmail($order_reference, $email);
            if (!Validate::isLoadedObject($this->order)) {
                $this->errors[] = $this->getTranslator()->trans(
                    'We couldn\'t find your order with the information provided, please try again',
                    array(),
                    'Shop.Notifications.Error'
                );
            }
        }

        // TODO: Error message for the form

        // TODO: TRANSFORM TO CUSTOMER
    }

    /**
     * Assign template vars related to page content.
     *
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        if (!Validate::isLoadedObject($this->order)) {
            return $this->setTemplate('customer/guest-login.tpl');
        }

        if ((int) $this->order->isReturnable()) {
            $this->info[] = $this->trans('You cannot return merchandise with a guest account.', array(), 'Shop.Notifications.Warning');
        }

        $presented_order = (new OrderPresenter())->present($this->order);

        $this->context->smarty->assign(array(
            'order' => $presented_order,
            'hook_orderdetaildisplayed' => Hook::exec('displayOrderDetail', array('order' => $this->order)),
        ));

        return $this->setTemplate('customer/guest-tracking.tpl');
    }
}
