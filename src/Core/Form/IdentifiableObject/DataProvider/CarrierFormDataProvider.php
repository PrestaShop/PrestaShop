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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider\ZoneByIdChoiceProvider;
use PrestaShop\PrestaShop\Adapter\Group\GroupDataProvider;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Currency\CurrencyDataProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Query\GetCarrierForEditing;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Query\GetCarrierRanges;
use PrestaShop\PrestaShop\Core\Domain\Carrier\QueryResult\CarrierRangesCollection;
use PrestaShop\PrestaShop\Core\Domain\Carrier\QueryResult\EditableCarrier;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\ShippingMethod;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

class CarrierFormDataProvider implements FormDataProviderInterface
{
    public function __construct(
        private readonly CommandBusInterface $queryBus,
        private readonly ShopContext $shopContext,
        private readonly CurrencyDataProviderInterface $currencyDataProvider,
        private readonly ConfigurationInterface $configuration,
        private readonly ZoneByIdChoiceProvider $zonesChoiceProvider,
        private readonly GroupDataProvider $groupDataProvider,
    ) {
    }

    public function getData($id)
    {
        /** @var EditableCarrier $carrier */
        $carrier = $this->queryBus->handle(new GetCarrierForEditing((int) $id, ShopConstraint::allShops()));
        $carrierRanges = $this->queryBus->handle(new GetCarrierRanges((int) $id, ShopConstraint::allShops()));

        return [
            'general_settings' => [
                'name' => $carrier->getName(),
                'localized_delay' => $carrier->getLocalizedDelay(),
                'active' => $carrier->isActive(),
                'grade' => $carrier->getGrade(),
                'group_access' => $carrier->getAssociatedGroupIds(),
                'associated_shops' => $carrier->getAssociatedShopIds(),
                'logo_preview' => $carrier->getLogoPath(),
                'tracking_url' => $carrier->getTrackingUrl(),
            ],
            'shipping_settings' => [
                'has_additional_handling_fee' => $carrier->hasAdditionalHandlingFee(),
                'is_free' => $carrier->isFree(),
                'shipping_method' => $carrier->getShippingMethod(),
                'id_tax_rule_group' => $carrier->getIdTaxRuleGroup(),
                'range_behavior' => $carrier->getRangeBehavior(),
                'zones' => $carrier->getZones(),
                'ranges' => $this->formatRangesData($carrierRanges),
                'ranges_costs' => $this->formatRangesCostsData($carrier, $carrierRanges),
            ],
            'size_weight_settings' => [
                'max_width' => $carrier->getMaxWidth(),
                'max_height' => $carrier->getMaxHeight(),
                'max_depth' => $carrier->getMaxDepth(),
                'max_weight' => $carrier->getMaxWeight(),
            ],
        ];
    }

    public function getDefaultData()
    {
        return [
            'general_settings' => [
                'grade' => 0,
                'associated_shops' => $this->shopContext->getAssociatedShopIds(),
                'group_access' => $this->groupDataProvider->getAllGroupIds(),
            ],
        ];
    }

    /**
     * Function to format ranges data.
     *
     * @param CarrierRangesCollection $carrierRangesCollection
     *
     * @return array
     */
    private function formatRangesData(CarrierRangesCollection $carrierRangesCollection): array
    {
        $ranges = [];

        // For each zones, we need to get all ranges
        foreach ($carrierRangesCollection->getZones() as $zone) {
            foreach ($zone->getRanges() as $range) {
                $ranges[] = [
                    'from' => (float) (string) $range->getFrom(),
                    'to' => (float) (string) $range->getTo(),
                ];
            }
        }

        // Then, we remove duplicates and sort ranges by from value.
        $ranges = array_unique($ranges, SORT_REGULAR);
        $from_values = array_column($ranges, 'from');
        array_multisort($from_values, SORT_ASC, $ranges);

        return ['data' => json_encode($ranges)];
    }

    /**
     * Function to format ranges costs data.
     *
     * @param CarrierRangesCollection $carrierRangesCollection
     *
     * @return array
     */
    private function formatRangesCostsData(EditableCarrier $carrier, CarrierRangesCollection $carrierRangesCollection): array
    {
        $ranges = [];

        // We retrieve zones to get the correct zone name
        $zones = $this->zonesChoiceProvider->getChoices([]);
        $zones = array_flip($zones);

        // We choose the right symbol for the range in function of the ShippingMethod
        switch ($carrier->getShippingMethod()) {
            default:
                $rangeSymbol = '';
                break;
            case ShippingMethod::BY_PRICE:
                $rangeSymbol = $this->currencyDataProvider->getDefaultCurrencySymbol();
                break;
            case ShippingMethod::BY_WEIGHT:
                $rangeSymbol = $this->configuration->get('PS_WEIGHT_UNIT');
                break;
        }

        // For each zones, we need to get all ranges
        foreach ($carrierRangesCollection->getZones() as $zone) {
            $zoneRanges = [];
            foreach ($zone->getRanges() as $range) {
                $zoneRanges[] = [
                    'range' => $range->getFrom()->__toString() . $rangeSymbol . ' - ' . $range->getTo()->__toString() . $rangeSymbol,
                    'from' => $range->getFrom(),
                    'to' => $range->getTo(),
                    'price' => $range->getPrice()->__toString(),
                ];
            }

            $ranges[] = [
                'zoneId' => $zone->getZoneId(),
                'zoneName' => $zones[$zone->getZoneId()] ?? $zone->getZoneId(),
                'ranges' => $zoneRanges,
            ];
        }

        return $ranges;
    }
}
