<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shipment;

use Context;
use DeliveryOptionsFinderCore;
use Hook;
use Module;
use PrestaShop\PrestaShop\Adapter\Presenter\Cart\CartPresenter;
use PrestaShop\PrestaShop\Adapter\Presenter\Object\ObjectPresenter;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use Symfony\Contracts\Translation\TranslatorInterface;

class DeliveryOptionsProvider extends DeliveryOptionsFinderCore
{
    private $context;

    /** @var CartPresenter */
    private $cartPresenter;

    public function __construct(
        Context $context,
        TranslatorInterface $translator,
        ObjectPresenter $objectPresenter,
        PriceFormatter $priceFormatter,
        CartPresenter $cartPresenter
    ) {
        parent::__construct($context, $translator, $objectPresenter, $priceFormatter);
        $this->context = $context;
        $this->cartPresenter = $cartPresenter;
    }

    public function getDeliveryOptions()
    {
        $parentOutput = parent::getDeliveryOptions();

        $deliveryOptions = $this->context->cart->getDeliveryOptionList();
        $currentAddressDeliveryOptions = $deliveryOptions[$this->context->cart->id_address_delivery];

        foreach ($currentAddressDeliveryOptions as $deliveryOption) {
            $carrierIdsAsString = implode(',', array_keys($deliveryOption['carrier_list'])) . ',';
            $carriersDetails = $this->getCarriersDetails($deliveryOption);

            // override parent output to mention all the carrier within the delivery option
            if (!empty($carriersDetails)) {
                $parentOutput[$carrierIdsAsString]['name'] = $carriersDetails['name'];
                $parentOutput[$carrierIdsAsString]['delay'] = $carriersDetails['delay'];
                $parentOutput[$carrierIdsAsString]['extraContent'] = $carriersDetails['extraContent'];
            }
        }

        return $parentOutput;
    }

    /**
     * @return array
     */
    public function getProductsByCarrier()
    {
        $deliveryOptions = $this->context->cart->getDeliveryOptionList();
        $currentAddressDeliveryOptions = $deliveryOptions[$this->context->cart->id_address_delivery];
        $result = [];

        foreach ($currentAddressDeliveryOptions as $deliveryOption) {
            foreach ($deliveryOption['carrier_list'] as $carrierId => $carrier) {
                $formatted = $this->formatCarrierWithProducts($carrier);

                $result[$carrierId] = [
                    'physical_products' => [],
                    'virtual_products' => [],
                ];

                if (!empty($formatted['products'])) {
                    $result[$carrierId]['physical_products'] = [
                        'carrier' => $formatted['carrier'],
                        'products' => array_values($formatted['products']),
                    ];
                }

                if (!empty($formatted['virtual_products'])) {
                    $result[$carrierId]['virtual_products'] = array_values($formatted['virtual_products']);
                }
            }
        }

        return $result;
    }

    private function formatCarrierWithProducts(array $carrierData): array
    {
        $carrierProductIds = array_map(function ($product) {
            return $product['id_product'];
        }, $carrierData['product_list']);

        $cartProducts = $this->cartPresenter->present($this->context->cart);
        $physicalProducts = [];
        $virtualProducts = [];

        foreach ($cartProducts->getProducts() as $product) {
            if (in_array($product['id_product'], $carrierProductIds)) {
                if ($product->getVirtual() == false) {
                    $physicalProducts[] = $product;
                } else {
                    $virtualProducts[] = $product;
                }
            }
        }

        return [
            'carrier' => [
                'name' => $carrierData['instance']->name,
                'delay' => $carrierData['instance']->delay[$this->context->language->id] ?? $carrierData['instance']->delay,
            ],
            'virtual_products' => $virtualProducts,
            'products' => $physicalProducts,
        ];
    }

    private function getCarriersDetails(array $deliveryOption): array
    {
        $carriers = $deliveryOption['carrier_list'];

        if (count($carriers) === 1) {
            return [];
        }

        $names = [];
        $delays = [];
        $extraContent = '';

        // If carrier related to a module, check for additionnal data to display
        foreach ($carriers as $carrier) {
            $names[] = $carrier['instance']->name;
            $delays[] = $carrier['instance']->delay[$this->context->language->id];

            // if more than on carrier are in the same delivery options then concatenate
            // all extracontent
            $extraContent .= Hook::exec('displayCarrierExtraContent', ['carrier' => $carrier['instance']], Module::getModuleIdByName($carrier['instance']->id));
        }

        return [
            'name' => implode(', ', $names),
            'delay' => implode(', ', $delays),
            'extraContent' => $extraContent,
        ];
    }
}
