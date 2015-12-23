<?php

use Symfony\Component\Translation\TranslatorInterface;

class CheckoutAddressesStepCore extends AbstractCheckoutStep
{
    private $addressForm;
    private $use_same_address = true;
    private $show_delivery_address_form = false;
    private $show_invoice_address_form  = false;

    public function __construct(
        Smarty $smarty,
        TranslatorInterface $translator,
        CustomerAddressForm $addressForm
    ) {
        parent::__construct($smarty, $translator);
        $this->addressForm = $addressForm;
    }

    public function getDataToPersist()
    {
        return [
            'use_same_address' => $this->use_same_address
        ];
    }

    public function restorePersistedData(array $data)
    {
        if (array_key_exists('use_same_address', $data)) {
            $this->use_same_address = $data['use_same_address'];
        }
        return $this;
    }

    public function handleRequest(array $requestParams = [])
    {
        if (array_key_exists('use_same_address', $requestParams)) {
            $this->use_same_address = (bool)$requestParams['use_same_address'];
        }

        if (isset($requestParams['cancelAddress']) && $requestParams['cancelAddress'] === 'invoice') {
            if ($this->getCheckoutSession()->getCustomerAddressesCount() < 2) {
                $this->use_same_address = true;
            }
        }

        // Can't really hurt to set the firstname and lastname.
        $this->addressForm->fillWith([
            'firstname' => $this->getCheckoutSession()->getCustomer()->firstname,
            'lastname'  => $this->getCheckoutSession()->getCustomer()->lastname
        ]);

        if (isset($requestParams['saveAddress'])) {
            $saved = $this->addressForm->fillWith($requestParams)->submit();
            if (!$saved) {
                $this->step_is_current = true;
                $this->getCheckoutProcess()->setHasErrors(true);
                if ($requestParams['saveAddress'] === 'delivery') {
                    $this->show_delivery_address_form = true;
                } else {
                    $this->show_invoice_address_form = true;
                }
            } else {
                $id_address = $this->addressForm->getAddress()->id;
                if ($requestParams['saveAddress'] === 'delivery') {
                    $this->getCheckoutSession()->setIdAddressDelivery($id_address);
                    if ($this->use_same_address) {
                        $this->getCheckoutSession()->setIdAddressInvoice($id_address);
                    }
                } else {
                    $this->getCheckoutSession()->setIdAddressInvoice($id_address);
                }
            }
            if ($requestParams['saveAddress'] === 'delivery') {
                $this->use_same_address = isset($requestParams['use_same_address']);
            }
        } elseif (isset($requestParams['id_address_delivery'])) {
            $id_address = $requestParams['id_address_delivery'];
            $this->getCheckoutSession()->setIdAddressDelivery($id_address);
            if ($this->use_same_address) {
                $this->getCheckoutSession()->setIdAddressInvoice($id_address);
            }
        } elseif (isset($requestParams['id_address_invoice'])) {
            $id_address = $requestParams['id_address_invoice'];
            $this->getCheckoutSession()->setIdAddressInvoice($id_address);
        } elseif (isset($requestParams['newAddress'])) {
            // while a form is open, do not go to next step
            $this->step_is_current = true;
            if ($requestParams['newAddress'] === 'delivery') {
                $this->show_delivery_address_form = true;
            } else {
                $this->show_invoice_address_form = true;
            }
            $this->addressForm->fillWith($requestParams);
        } elseif (isset($requestParams['editAddress']) && isset($requestParams['id_address'])) {
            // while a form is open, do not go to next step
            $this->step_is_current = true;
            if ($requestParams['editAddress'] === 'delivery') {
                $this->show_delivery_address_form = true;
            } else {
                $this->show_invoice_address_form = true;
            }
            $this->addressForm->setIdAddress($requestParams['id_address']);
        }

        $this->step_is_complete = $this->getCheckoutSession()->getIdAddressInvoice() && $this->getCheckoutSession()->getIdAddressDelivery();

        if (!$this->use_same_address && $this->getCheckoutSession()->getCustomerAddressesCount() < 2) {
            $this->step_is_complete = false;
        }

        $this->setTitle(
            $this->getTranslator()->trans(
                'Addresses',
                [],
                'Checkout'
            )
        );
    }

    public function render(array $extraParams = [])
    {
        return $this->renderTemplate(
            'checkout/addresses-step.tpl', $extraParams, [
                'address_form'          => $this->addressForm->getProxy(),
                'use_same_address'      => $this->use_same_address,
                'id_address_delivery'   => $this
                                            ->getCheckoutSession()
                                            ->getIdAddressDelivery(),
                'id_address_invoice'    => $this
                                            ->getCheckoutSession()
                                            ->getIdAddressInvoice(),
                'show_delivery_address_form' => $this->show_delivery_address_form,
                'show_invoice_address_form'  => $this->show_invoice_address_form
            ]
        );
    }
}
