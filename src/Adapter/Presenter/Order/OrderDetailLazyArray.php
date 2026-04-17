<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
use PrestaShopBundle\Entity\Repository\ShipmentRepository;
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

    private $shipmentRepository;

    /**
     * OrderDetailLazyArray constructor.
     *
     * @param Order $order
     */
    public function __construct(Order $order, ShipmentRepository $shipmentRepository)
    {
        $this->order = $order;
        $this->context = Context::getContext();
        $this->translator = Context::getContext()->getTranslator();
        $this->locale = $this->context->getCurrentLocale();
        $this->shipmentRepository = $shipmentRepository;
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

    #[LazyArrayAttribute(arrayAccess: true)]
    public function hasShipments(): bool
    {
        return !empty($this->shipmentRepository->findByOrderId($this->order->id));
    }

    /**
     * @return array
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getShipping()
    {
        if ($this->hasShipments()) {
            return $this->getShipments();
        }

        return $this->getCarrier();
    }

    /**
     * Used when the feature flag FEATURE_FLAG_IMPROVED_SHIPMENT is enabled.
     * Since the feature flag introduce a new "shipment" concept in PrestaShop, we now need to get
     * all shipments for the given order.
     */
    private function getShipments()
    {
        $shipments = $this->shipmentRepository->getShipmentWithWeightByOrderId($this->order->id);

        foreach ($shipments as &$shipment) {
            if ($shipment['carrier_tracking_url']) {
                $shipment['carrier_tracking_url'] = str_replace('@', $shipment['tracking_number'], $shipment['carrier_tracking_url']);
            }

            $shipment['date_add'] = Tools::displayDate($shipment['date_add'], false);

            $shipment['package_weight'] = ($shipment['package_weight'] > 0) ? sprintf('%.3f', $shipment['package_weight']) . ' ' .
                Configuration::get('PS_WEIGHT_UNIT') : '-';
            $packageCost = ($this->order->getTaxCalculationMethod()) ? $shipment['shipping_cost_tax_excl'] : $shipment['shipping_cost_tax_incl'];
            $shipment['package_cost'] = ($packageCost > 0) ? $this->locale->formatPrice($packageCost, Currency::getIsoCodeById((int) $this->order->id_currency))
                : $this->translator->trans('Free', [], 'Shop.Theme.Checkout');
        }

        return $shipments;
    }

    /**
     * Used when the feature flag FEATURE_FLAG_IMPROVED_SHIPMENT is disabled.
     * Get carrier details used for the given order.
     */
    private function getCarrier()
    {
        $shippingList = $this->order->getShipping();
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
                    ($this->order->getTaxCalculationMethod()) ? $shipping['shipping_cost_tax_excl']
                    : $shipping['shipping_cost_tax_incl'];
                $orderShipping[$shippingId]['shipping_cost'] =
                    ($shippingCost > 0) ? $this->locale->formatPrice($shippingCost, Currency::getIsoCodeById((int) $this->order->id_currency))
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
