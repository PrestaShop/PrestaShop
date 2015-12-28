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
        $deliveryOptionsFinder = new DeliveryOptionsFinder(
            $this->context,
            $this->getTranslator(),
            $this->objectSerializer,
            new Adapter_PricePresenter
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

        $this->checkoutProcess = new CheckoutProcess($session);

        $checkoutDeliveryStep = new CheckoutDeliveryStep(
            $this->context->smarty,
            $translator
        );
        $checkoutDeliveryStep->setRecyclablePackAllowed((bool)Configuration::get('PS_RECYCLABLE_PACK'));
        $checkoutDeliveryStep->setGiftAllowed((bool)Configuration::get('PS_GIFT_WRAPPING'));
        $checkoutDeliveryStep->setIncludeTaxes(!Product::getTaxCalculationMethod((int)$this->context->cart->id_customer) && (int)Configuration::get('PS_TAX'));
        $checkoutDeliveryStep->setDisplayTaxesLabel((Configuration::get('PS_TAX') && !Configuration::get('AEUC_LABEL_TAX_INC_EXC')) && $this->context->smarty->tpl_vars['display_tax_label']->value);
        $checkoutDeliveryStep->setGiftCost($this->context->cart->getGiftWrappingPrice($checkoutDeliveryStep->getIncludeTaxes()));

        $this->checkoutProcess
            ->addStep(new CheckoutPersonalInformationStep(
                $this->context->smarty,
                $translator,
                $this->getLoginForm(),
                $this->getRegisterForm()
            ))
            ->addStep(new CheckoutAddressesStep(
                $this->context->smarty,
                $translator,
                $this->getAddressForm()
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

    private function persist(CheckoutProcess $process)
    {
        $data = $process->getDataToPersist();
        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'cart SET checkout_session_data = "'.pSQL(json_encode($data)).'" WHERE id_cart = '.(int)$this->context->cart->id);
    }

    private function load(CheckoutProcess $process)
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

        $this->load($this->checkoutProcess);
        $this->checkoutProcess->handleRequest(
            Tools::getAllValues()
        );

        $this->checkoutProcess->setNextStepReachable();

        $this->checkoutProcess->markCurrentStep();

        $this->persist($this->checkoutProcess);

        if (!$this->checkoutProcess->hasErrors()) {
            if ($_SERVER['REQUEST_METHOD'] !== 'GET' && !$this->ajax) {
                return $this->redirectWithNotifications(
                    $this->updateQueryString(null)
                );
            }
        }

        $this->context->smarty->assign([
            'rendered_checkout' => $this->checkoutProcess->render(),
            'rendered_cart_summary' => $this->renderCartSummary()
        ]);
        $this->setTemplate('checkout/checkout.tpl');
    }
}
