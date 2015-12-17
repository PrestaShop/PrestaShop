<?php
/**
 * 2007-2015 PrestaShop
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

use PrestaShop\PrestaShop\Core\Business\Checkout\TermsAndConditions;

class OrderControllerCore extends FrontController
{
    public $ssl = true;
    public $php_self = 'order';
    public $page_name = 'checkout';

    private $checkoutProcess;

    public function postProcess()
    {
        parent::postProcess();
        $this->bootstrap();
    }

    private function getCheckoutSession()
    {
        $session = new CheckoutSession;

        $session->setContext($this->context);

        return $session;
    }

    private function bootstrap()
    {
        $translator = $this->getTranslator();

        $session = $this->getCheckoutSession();

        $this->checkoutProcess = new CheckoutProcess($session);

        $this->checkoutProcess
            ->addStep(new CheckoutPersonalInformationStep(
                $this->context->smarty,
                $translator,
                $this->getLoginForm()
            ))
            ->addStep(new CheckoutAddressesStep(
                $this->context->smarty,
                $translator
            ))
            ->addStep(new CheckoutDeliveryStep(
                $this->context->smarty,
                $translator
            ))
            ->addStep(new CheckoutPaymentStep(
                $this->context->smarty,
                $translator
            ))
        ;

        $this->checkoutProcess->init(
            Tools::getAllValues()
        );
    }

    public function initContent()
    {
        parent::initContent();

        $rendered_checkout = $this->checkoutProcess->render();

        $this->context->smarty->assign([
            'rendered_checkout' => $rendered_checkout
        ]);
        $this->setTemplate('checkout/checkout.tpl');
    }
}
