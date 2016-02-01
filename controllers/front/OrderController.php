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

use PrestaShop\PrestaShop\Core\Foundation\Templating\RenderableProxy;
use PrestaShop\PrestaShop\Adapter\Product\PricePresenter;

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
        $deliveryOptionsFinder = new DeliveryOptionsFinder(
            $this->context,
            $this->getTranslator(),
            $this->objectSerializer,
            new PricePresenter
        );

        $session = new CheckoutSession(
            $this->context,
            $deliveryOptionsFinder
        );

        return $session;
    }

    private function bootstrap()
    {
        $translator = $this->getTranslator();

        $session = $this->getCheckoutSession();

        $this->checkoutProcess = new CheckoutProcess(
            $this->context->smarty,
            $session
        );

        $checkoutDeliveryStep = new CheckoutDeliveryStep(
            $this->context->smarty,
            $translator
        );

        $checkoutDeliveryStep->setRecyclablePackAllowed(
            (bool)Configuration::get('PS_RECYCLABLE_PACK')
        )->setGiftAllowed(
            (bool)Configuration::get('PS_GIFT_WRAPPING')
        )->setIncludeTaxes(
            !Product::getTaxCalculationMethod((int)$this->context->cart->id_customer) && (int)Configuration::get('PS_TAX')
        )->setDisplayTaxesLabel(
            (Configuration::get('PS_TAX')
            && !Configuration::get('AEUC_LABEL_TAX_INC_EXC'))
            && $this->context->smarty->tpl_vars['display_tax_label']->value
        )->setGiftCost(
            $this->context->cart->getGiftWrappingPrice(
                $checkoutDeliveryStep->getIncludeTaxes()
            )
        );

        $this->checkoutProcess
            ->addStep(new CheckoutPersonalInformationStep(
                $this->context->smarty,
                $translator,
                $this->makeLoginForm(),
                $this->makeCustomerForm()
            ))
            ->addStep(new CheckoutAddressesStep(
                $this->context->smarty,
                $translator,
                $this->makeAddressForm()
            ))
            ->addStep($checkoutDeliveryStep)
            ->addStep(new CheckoutPaymentStep(
                $this->context->smarty,
                $translator,
                new PaymentOptionsFinder,
                new ConditionsToApproveFinder(
                    $this->context,
                    $translator
                )
            ))
        ;
    }

    private function saveDataToPersist(CheckoutProcess $process)
    {
        $data = $process->getDataToPersist();
        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'cart SET checkout_session_data = "'.pSQL(json_encode($data)).'" WHERE id_cart = '.(int)$this->context->cart->id);
    }

    private function restorePersistedData(CheckoutProcess $process)
    {
        $rawData = Db::getInstance()->getValue('SELECT checkout_session_data FROM '._DB_PREFIX_.'cart WHERE id_cart = '.(int)$this->context->cart->id);
        $data = json_decode($rawData, true);
        if (!is_array($data)) {
            $data = [];
        }
        $process->restorePersistedData($data);
    }

    private function renderCartSummary()
    {
        $cart = $this->cart_presenter->present(
            $this->context->cart
        );
        return $this->render('checkout/_partials/cart-summary.tpl', [
            'cart' => $cart,
        ]);
    }

    public function initContent()
    {
        parent::initContent();

        $this->restorePersistedData($this->checkoutProcess);
        $this->checkoutProcess->handleRequest(
            Tools::getAllValues()
        );

        $this->checkoutProcess->setNextStepReachable();

        $this->checkoutProcess->markCurrentStep();

        $this->saveDataToPersist($this->checkoutProcess);

        if (!$this->checkoutProcess->hasErrors()) {
            if ($_SERVER['REQUEST_METHOD'] !== 'GET' && !$this->ajax) {
                return $this->redirectWithNotifications(
                    $this->getCurrentURL()
                );
            }
        }

        $this->context->smarty->assign([
            'checkout_process'  => new RenderableProxy($this->checkoutProcess),
            'cart'              => $this->cart_presenter->present(
                                        $this->context->cart
                                    )
        ]);
        $this->setTemplate('checkout/checkout.tpl');
    }
}
