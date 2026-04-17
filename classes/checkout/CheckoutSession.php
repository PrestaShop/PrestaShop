<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

use PrestaShop\PrestaShop\Adapter\Shipment\DeliveryOptionsInterface;
use PrestaShop\PrestaShop\Adapter\Shipment\DeliveryOptionsProvider;

class CheckoutSessionCore
{
    /** @var Context */
    protected $context;
    /** @var DeliveryOptionsInterface */
    protected $deliveryOptions;

    /**
     * @param Context $context
     * @param DeliveryOptionsInterface $deliveryOptions
     */
    public function __construct(Context $context, DeliveryOptionsInterface $deliveryOptions)
    {
        $this->context = $context;
        $this->deliveryOptions = $deliveryOptions;
    }

    /**
     * @return bool
     */
    public function customerHasLoggedIn()
    {
        return $this->context->customer->isLogged();
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->context->customer;
    }

    /**
     * @return Cart
     */
    public function getCart()
    {
        return $this->context->cart;
    }

    /**
     * @return int
     */
    public function getCustomerAddressesCount()
    {
        return count($this->getCustomer()->getSimpleAddresses(
            $this->context->language->id
        ));
    }

    public function setIdAddressDelivery($id_address)
    {
        $this->context->cart->updateAddressId($this->context->cart->id_address_delivery, $id_address);
        $this->context->cart->id_address_delivery = $id_address;
        $this->context->cart->save();

        return $this;
    }

    public function setIdAddressInvoice($id_address)
    {
        $this->context->cart->id_address_invoice = $id_address;
        $this->context->cart->save();

        return $this;
    }

    public function getIdAddressDelivery()
    {
        return $this->context->cart->id_address_delivery;
    }

    public function getIdAddressInvoice()
    {
        return $this->context->cart->id_address_invoice;
    }

    public function setMessage($message)
    {
        $this->_updateMessage($message);

        return $this;
    }

    public function getMessage()
    {
        if ($message = Message::getMessageByCartId($this->context->cart->id)) {
            return $message['message'];
        }

        return false;
    }

    private function _updateMessage($messageContent)
    {
        if ($messageContent) {
            if ($oldMessage = Message::getMessageByCartId((int) $this->context->cart->id)) {
                $message = new Message((int) $oldMessage['id_message']);
                $message->message = $messageContent;
                $message->update();
            } else {
                $message = new Message();
                $message->message = $messageContent;
                $message->id_cart = (int) $this->context->cart->id;
                $message->id_customer = (int) $this->context->cart->id_customer;
                $message->add();
            }
        } else {
            if ($oldMessage = Message::getMessageByCartId($this->context->cart->id)) {
                $message = new Message($oldMessage['id_message']);
                $message->delete();
            }
        }

        return true;
    }

    public function setDeliveryOption($option)
    {
        $this->context->cart->setDeliveryOption($option);

        return $this->context->cart->update();
    }

    public function getSelectedDeliveryOption()
    {
        return $this->deliveryOptions->getSelectedDeliveryOption();
    }

    public function getProductsByCarrier()
    {
        if ($this->deliveryOptions instanceof DeliveryOptionsProvider) {
            return $this->deliveryOptions->getProductsByCarrier();
        }

        return [];
    }

    public function getDeliveryOptions()
    {
        return $this->deliveryOptions->getDeliveryOptions();
    }

    public function setRecyclable($option)
    {
        $this->context->cart->recyclable = (bool) $option;

        return $this->context->cart->update();
    }

    public function isRecyclable()
    {
        return $this->context->cart->recyclable;
    }

    public function setGift($gift, $gift_message)
    {
        $this->context->cart->gift = (bool) $gift;
        $this->context->cart->gift_message = $gift_message;

        return $this->context->cart->update();
    }

    public function getGift()
    {
        return [
            'isGift' => $this->context->cart->gift,
            'message' => $this->context->cart->gift_message,
        ];
    }

    public function isGuestAllowed()
    {
        return Configuration::get('PS_GUEST_CHECKOUT_ENABLED');
    }

    public function getCheckoutURL()
    {
        return $this->context->link->getPageLink('order');
    }
}
