<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Presenter\Order;

use Cart;
use Configuration;
use Context;
use Currency;
use HistoryController;
use Order;
use PrestaShop\PrestaShop\Adapter\Presenter\AbstractLazyArray;
use PrestaShop\PrestaShop\Adapter\Presenter\LazyArrayAttribute;
use PrestaShop\PrestaShop\Core\Localization\LocaleInterface;
use PrestaShopBundle\Translation\TranslatorComponent;
use PrestaShopException;
use Tools;

class OrderDetailLazyArray extends AbstractLazyArray
{
    /**
     * @var LocaleInterface
     */
    private $locale;

    /**
     * @var Order
     */
    private $order;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var TranslatorComponent
     */
    private $translator;

    /**
     * OrderDetailLazyArray constructor.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->context = Context::getContext();
        $this->translator = Context::getContext()->getTranslator();
        $this->locale = $this->context->getCurrentLocale();
        parent::__construct();
    }

    /**
     * @return int
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getId()
    {
        return $this->order->id;
    }

    /**
     * @return string
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getReference()
    {
        return $this->order->reference;
    }

    /**
     * @return string
     *
     * @throws PrestaShopException
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getOrderDate()
    {
        return Tools::displayDate($this->order->date_add, false);
    }

    /**
     * @return string
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getDetailsUrl()
    {
        return $this->context->link->getPageLink('order-detail', null, null, 'id_order=' . $this->order->id);
    }

    /**
     * @return mixed
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getReorderUrl()
    {
        return HistoryController::getUrlToReorder((int) $this->order->id, $this->context);
    }

    /**
     * @return mixed
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getInvoiceUrl()
    {
        return HistoryController::getUrlToInvoice($this->order, $this->context);
    }

    /**
     * @return string
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getGiftMessage()
    {
        return nl2br($this->order->gift_message);
    }

    /**
     * @return int
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getIsReturnable()
    {
        return (int) $this->order->isReturnable();
    }

    /**
     * @return string
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getPayment()
    {
        return $this->order->payment;
    }

    /**
     * @return string
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getModule()
    {
        return $this->order->module;
    }

    /**
     * @return bool
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getRecyclable()
    {
        return (bool) $this->order->recyclable;
    }

    /**
     * @return bool
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getIsValid()
    {
        return $this->order->valid;
    }

    /**
     * @return bool
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getIsVirtual()
    {
        $cart = new Cart($this->order->id_cart);

        return $cart->isVirtualCart();
    }

    /**
     * @return array
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getShipping()
    {
        $order = $this->order;

        $shippingList = $order->getShipping();
        $orderShipping = [];

        foreach ($shippingList as $shippingId => $shipping) {
            if (isset($shipping['carrier_name']) && $shipping['carrier_name']) {
                $orderShipping[$shippingId] = $shipping;
                $orderShipping[$shippingId]['shipping_date'] =
                    Tools::displayDate($shipping['date_add'], false);
                $orderShipping[$shippingId]['shipping_weight'] =
                    ($shipping['weight'] > 0) ? sprintf('%.3f', $shipping['weight']) . ' ' .
                        Configuration::get('PS_WEIGHT_UNIT') : '-';
                $shippingCost =
                    ($order->getTaxCalculationMethod()) ? $shipping['shipping_cost_tax_excl']
                        : $shipping['shipping_cost_tax_incl'];
                $orderShipping[$shippingId]['shipping_cost'] =
                    ($shippingCost > 0) ? $this->locale->formatPrice($shippingCost, Currency::getIsoCodeById((int) $order->id_currency))
                        : $this->translator->trans('Free', [], 'Shop.Theme.Checkout');

                $tracking_line = '-';
                if ($shipping['tracking_number']) {
                    if ($shipping['url']) {
                        $tracking_line = '<a href="' . str_replace(
                            '@',
                            $shipping['tracking_number'],
                            $shipping['url']
                        ) . '" target="_blank">' . $shipping['tracking_number'] . '</a>';
                    } else {
                        $tracking_line = $shipping['tracking_number'];
                    }
                }

                $orderShipping[$shippingId]['tracking'] = $tracking_line;
            }
        }

        return $orderShipping;
    }
}
