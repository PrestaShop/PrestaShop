<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


class CheckoutSessionCore
{
    protected $context;
    protected $deliveryOptionsFinder;

    public function __construct(Context $context, DeliveryOptionsFinder $deliveryOptionsFinder)
    {
        $this->context = $context;
        $this->deliveryOptionsFinder = $deliveryOptionsFinder;
    }

    public function customerHasLoggedIn()
    {
        return $this->context->customer->isLogged();
    }

    public function getCustomer()
    {
        return $this->context->customer;
    }

    public function getCart()
    {
        return $this->context->cart;
    }

    public function getCustomerAddressesCount()
    {
        return count($this->getCustomer()->getSimpleAddresses(
            $this->context->language->id,
            true // no cache
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
        $this->_updateMessage(Tools::safeOutput($message));

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
            if ($oldMessage = Message::getMessageByCartId((int)$this->context->cart->id)) {
                $message = new Message((int)$oldMessage['id_message']);
                $message->message = $messageContent;
                $message->update();
            } else {
                $message = new Message();
                $message->message = $messageContent;
                $message->id_cart = (int)$this->context->cart->id;
                $message->id_customer = (int)$this->context->cart->id_customer;
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
        return $this->deliveryOptionsFinder->getSelectedDeliveryOption();
    }

    public function getDeliveryOptions()
    {
        return $this->deliveryOptionsFinder->getDeliveryOptions();
    }

    public function setRecyclable($option)
    {
        $this->context->cart->recyclable = (int) $option;

        return $this->context->cart->update();
    }

    public function isRecyclable()
    {
        return $this->context->cart->recyclable;
    }

    public function setGift($gift, $gift_message)
    {
        $this->context->cart->gift = (int) $gift;
        $this->context->cart->gift_message = $gift_message;

        return $this->context->cart->update();
    }

    public function getGift()
    {
        return array(
            'isGift' => $this->context->cart->gift,
            'message' => $this->context->cart->gift_message,
        );
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
