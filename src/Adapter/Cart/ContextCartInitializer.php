<?php
/**
 * 2007-2019 PrestaShop and Contributors
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

namespace PrestaShop\PrestaShop\Adapter\Cart;

use Cart;
use Configuration;
use Context;
use Currency;
use Customer;
use Tools;
use Validate;

/**
 * Adapter service for initializing context cart
 */
final class ContextCartInitializer
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function init(int $cartId, int $customerId)
    {
        //@todo: clean up
        $customer = new Customer($customerId);
        $this->context->customer = $customer;
        $this->context->cart = new Cart((int) $cartId);

        if (!$this->context->cart->id) {
            $this->context->cart->recyclable = 0;
            $this->context->cart->gift = 0;
        }

        if (!$this->context->cart->id_customer) {
            $this->context->cart->id_customer = $customerId;
        }
        if (Validate::isLoadedObject($this->context->cart) && $this->context->cart->OrderExists()) {
            return;
        }
        if (!$this->context->cart->secure_key) {
            $this->context->cart->secure_key = $this->context->customer->secure_key;
        }
        if (!$this->context->cart->id_shop) {
            $this->context->cart->id_shop = (int) $this->context->shop->id;
        }
        if (!$this->context->cart->id_lang) {
            $this->context->cart->id_lang = (($id_lang = (int) Tools::getValue('id_lang')) ? $id_lang : Configuration::get('PS_LANG_DEFAULT'));
        }
        if (!$this->context->cart->id_currency) {
            $this->context->cart->id_currency = (($id_currency = (int) Tools::getValue('id_currency')) ? $id_currency : Configuration::get('PS_CURRENCY_DEFAULT'));
        }

        $addresses = $customer->getAddresses((int) $this->context->cart->id_lang);
        $id_address_delivery = (int) Tools::getValue('id_address_delivery');
        $id_address_invoice = (int) Tools::getValue('id_address_delivery');

        if (!$this->context->cart->id_address_invoice && isset($addresses[0])) {
            $this->context->cart->id_address_invoice = (int) $addresses[0]['id_address'];
        } elseif ($id_address_invoice) {
            $this->context->cart->id_address_invoice = (int) $id_address_invoice;
        }
        if (!$this->context->cart->id_address_delivery && isset($addresses[0])) {
            $this->context->cart->id_address_delivery = $addresses[0]['id_address'];
        } elseif ($id_address_delivery) {
            $this->context->cart->id_address_delivery = (int) $id_address_delivery;
        }
        $this->context->cart->setNoMultishipping();
        $this->context->cart->save();
        $currency = new Currency((int) $this->context->cart->id_currency);
        $this->context->currency = $currency;
    }
}
