<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Cart\QueryHandler;

use Address;
use AddressFormat;
use Cart;
use CartRule;
use Configuration;
use Context;
use Customer;
use Message;
use PrestaShop\PrestaShop\Adapter\Cart\AbstractCartHandler;
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetCartSummary;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryHandler\GetCartSummaryHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartSummary;
use Product;
use Tools;

/**
 * @internal
 */
final class GetCartSummaryHandler extends AbstractCartHandler implements GetCartSummaryHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetCartSummary $query)
    {
        $cart = $this->getContextCartObject($query->getCartId());

        return new CartSummary(
            $this->getCartSummary($cart),
            $this->getDeliveryOptions($cart),
            $this->getAddresses($cart),
            $this->isFreeShippingApplicable($cart),
            $this->getOrderMessage($cart)
        );
    }

    /**
     * @param Cart $cart
     *
     * @return array
     */
    private function getCartSummary(Cart $cart)
    {
        $summary = $cart->getSummaryDetails(null, true);
        $currency = Context::getContext()->currency;

        if (count($summary['products'])) {
            foreach ($summary['products'] as &$product) {
                $product['numeric_price'] = $product['price'];
                $product['numeric_total'] = $product['total'];
                $product['price'] = str_replace($currency->sign, '', Tools::displayPrice($product['price'], $currency));
                $product['total'] = str_replace($currency->sign, '', Tools::displayPrice($product['total'], $currency));

                $product['image_link'] = Context::getContext()->link->getImageLink(
                    $product['link_rewrite'],
                    $product['id_image'],
                    'small_default'
                );

                if (!isset($product['attributes_small'])) {
                    $product['attributes_small'] = '';
                }

                $product['customized_datas'] = Product::getAllCustomizedDatas(
                    (int) $cart->id,
                    null,
                    true,
                    null,
                    (int) $product['id_customization']
                );
            }
        }

        if (count($summary['discounts'])) {
            foreach ($summary['discounts'] as &$voucher) {
                $voucher['value_real'] = Tools::displayPrice($voucher['value_real'], $currency);
            }
        }

        if (isset($summary['gift_products']) && count($summary['gift_products'])) {
            foreach ($summary['gift_products'] as &$product) {
                $product['image_link'] = Context::getContext()->link->getImageLink(
                    $product['link_rewrite'],
                    $product['id_image'],
                    'small_default'
                );

                if (!isset($product['attributes_small'])) {
                    $product['attributes_small'] = '';
                }
            }
        }

        return $summary;
    }

    /**
     * @param Cart $cart
     *
     * @return array
     */
    private function getDeliveryOptions(Cart $cart)
    {
        $deliveryOptionListFormatted = [];
        $deliveryOptionList = $cart->getDeliveryOptionList();

        if (!count($deliveryOptionList)) {
            return [];
        }

        $defaultCarrierId = (int) Configuration::get('PS_CARRIER_DEFAULT');

        foreach (current($deliveryOptionList) as $key => $deliveryOption) {
            $name = '';
            $first = true;
            $idDefaultCarrierDelivery = false;

            foreach ($deliveryOption['carrier_list'] as $carrier) {
                if (!$first) {
                    $name .= ', ';
                } else {
                    $first = false;
                }

                $name .= $carrier['instance']->name;

                if ($deliveryOption['unique_carrier']) {
                    $name .= ' - ' . $carrier['instance']->delay[Context::getContext()->employee->id_lang];
                }

                if (!$idDefaultCarrierDelivery) {
                    $idDefaultCarrierDelivery = (int) $carrier['instance']->id;
                }

                if ((int) $carrier['instance']->id === $defaultCarrierId) {
                    $idDefaultCarrierDelivery = $defaultCarrierId;
                }

                if (!$cart->id_carrier) {
                    $cart->setDeliveryOption([$cart->id_address_delivery => (int) $carrier['instance']->id . ',']);
                    $cart->save();
                }
            }

            $deliveryOptionListFormatted[] = ['name' => $name, 'key' => $key];
        }

        return $deliveryOptionListFormatted;
    }

    /**
     * @param Cart $cart
     *
     * @return array
     */
    private function getAddresses(Cart $cart)
    {
        $customer = new Customer($cart->id_customer);
        $addresses = $customer->getAddresses($cart->id_lang);

        foreach ($addresses as &$data) {
            $address = new Address((int) $data['id_address']);
            $data['formated_address'] = AddressFormat::generateAddress($address, [], '<br />');
        }

        return $addresses;
    }

    /**
     * @param Cart $cart
     *
     * @return bool
     */
    private function isFreeShippingApplicable(Cart $cart)
    {
        $cartRules = $cart->getCartRules(CartRule::FILTER_ACTION_SHIPPING);

        $isFreeShipping = false;
        if (count($cartRules)) {
            foreach ($cartRules as $cartRule) {
                if ($cartRule['id_cart_rule'] === CartRule::getIdByCode(CartRule::BO_ORDER_CODE_PREFIX . $cart->id)) {
                    $isFreeShipping = true;

                    break;
                }
            }
        }

        return $isFreeShipping;
    }

    /**
     * @param Cart $cart
     *
     * @return string
     */
    private function getOrderMessage(Cart $cart)
    {
        $messageContent = '';

        if ($message = Message::getMessageByCartId($cart->id)) {
            $messageContent = $message['message'];
        }

        return $messageContent;
    }
}
