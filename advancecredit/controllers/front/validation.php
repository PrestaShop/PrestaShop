<?php

require_once __DIR__ . '/../../src/Domain/CustomerCreditRepository.php';

class AdvancecreditValidationModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        $cart = $this->context->cart;

        // Zabezpieczenia: koszyk istnieje, moduł włączony, klient zalogowany
        if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer)) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        $creditRepository = new CustomerCreditRepository();
        $cartTotal = (float)$cart->getOrderTotal(true, Cart::BOTH);
        $availableCredit = $creditRepository->getAvailableCredit((int)$customer->id);

        // Ostatnia walidacja czy środki nadal wystarczają
        if ($availableCredit < $cartTotal) {
            $this->errors[] = $this->module->l('Niewystarczające środki w ramach kredytu kupieckiego.');
            $this->redirectWithNotifications('index.php?controller=order');
            return;
        }

        // Zwiększenie długu klienta (odjęcie środków z limitu)
        $creditRepository->addDebt((int)$customer->id, $cartTotal);

        // Status płatności: standardowo PS_OS_PREPARATION oznacza, że zamówienie zostało przyjęte i jest w przygotowaniu
        $orderStatusId = (int)Configuration::get('PS_OS_PREPARATION');

        // Finalizacja zamówienia
        $this->module->validateOrder(
            (int)$cart->id,
            $orderStatusId,
            $cartTotal,
            $this->module->displayName,
            null,
            [],
            (int)$this->context->currency->id,
            false,
            $customer->secure_key
        );

        // Przekierowanie na standardową stronę podziękowania za zamówienie
        Tools::redirect('index.php?controller=order-confirmation&id_cart=' . (int)$cart->id . '&id_module=' . (int)$this->module->id . '&id_order=' . $this->module->currentOrder . '&key=' . $customer->secure_key);
    }
}
