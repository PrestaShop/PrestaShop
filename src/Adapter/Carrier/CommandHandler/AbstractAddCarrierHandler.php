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

namespace PrestaShop\PrestaShop\Adapter\Carrier\CommandHandler;

use Carrier;
use ObjectModel;
use PrestaShop\PrestaShop\Adapter\Carrier\AbstractCarrierHandler;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\AbstractAddCarrierCommand;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\ShippingMethod;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\ShippingRange;
use PrestaShopException;
use RangePrice;
use RangeWeight;

/**
 * Provides reusable methods for AddCarrier handlers
 */
abstract class AbstractAddCarrierHandler extends AbstractCarrierHandler
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param ConfigurationInterface $configuration
     */
    public function __construct(
        ConfigurationInterface $configuration
    ) {
        $this->configuration = $configuration;
    }

    /**
     * @param Carrier $carrier
     * @param AbstractAddCarrierCommand $command
     */
    protected function fillCarrierCommonFieldsWithData(Carrier $carrier, AbstractAddCarrierCommand $command)
    {
        $shippingMethod = $command->getShippingMethod()->getValue();

        /*
         * Backwards compatibility.
         * SHIPPING_METHOD_DEFAULT @deprecated 1.5.5
         */
        if (Carrier::SHIPPING_METHOD_DEFAULT === $shippingMethod) {
            $shippingMethod = $this->configuration->get('PS_SHIPPING_METHOD') ?
                ShippingMethod::SHIPPING_METHOD_WEIGHT : ShippingMethod::SHIPPING_METHOD_PRICE;
        }
        $carrier->is_free = $command->isFreeShipping();
        $carrier->shipping_method = $shippingMethod;

        $localizedNames = $command->getLocalizedNames();
        foreach ($localizedNames as $langId => $carrierName) {
            $carrier->localized_name[$langId] = $carrierName->getValue();
        }
        $carrier->name = $localizedNames[$this->configuration->get('PS_LANG_DEFAULT')]->getValue();

        foreach ($command->getLocalizedShippingDelays() as $langId => $shippingDelay) {
            $carrier->delay[$langId] = $shippingDelay->getValue();
        }
        $carrier->grade = $command->getSpeedGrade()->getValue();
        $carrier->url = $command->getTrackingUrl()->getValue();
        $carrier->shipping_handling = $command->isShippingCostIncluded();
        $carrier->range_behavior = $command->getOutOfRangeBehavior()->getValue();
        $carrier->max_width = $command->getMaxPackageWidth();
        $carrier->max_height = $command->getMaxPackageHeight();
        $carrier->max_depth = $command->getMaxPackageDepth();
        $carrier->max_weight = $command->getMaxPackageWeight();
        $carrier->deleted = false;
    }

    /**
     * @param Carrier $carrier
     * @param ShippingMethod $shippingMethod
     * @param ShippingRange[] $shippingRanges
     *
     * @throws CarrierException
     * @throws PrestaShopException
     */
    protected function addShippingRanges(Carrier $carrier, ShippingMethod $shippingMethod, array $shippingRanges): void
    {
        $methodValue = $shippingMethod->getValue();
        $carrierId = (int) $carrier->id;
        $range = new RangePrice();

        if ($methodValue === ShippingMethod::SHIPPING_METHOD_WEIGHT) {
            $range = new RangeWeight();
        }

        foreach ($shippingRanges as $shippingRange) {
            $this->addRange($range, $carrierId, $shippingRange);
            $this->addDeliveryPrice($shippingRange, $shippingMethod, $carrier, (int) $range->id);
        }
    }

    /**
     * @param ObjectModel $range RangePrice|RangeWeight
     * @param int $carrierId
     * @param ShippingRange $shippingRange
     *
     * @throws CarrierException
     * @throws PrestaShopException
     */
    protected function addRange(ObjectModel $range, int $carrierId, ShippingRange $shippingRange)
    {
        $range->id_carrier = $carrierId;
        $range->delimiter1 = $shippingRange->getFrom();
        $range->delimiter2 = $shippingRange->getTo();

        if (false === $range->add()) {
            throw new CarrierException(sprintf(
                'Failed to create range "%s - %s" for carrier with id "%s"',
                $shippingRange->getFrom(),
                $shippingRange->getTo(),
                $carrierId
            ));
        }
    }

    /**
     * @param ShippingRange $shippingRange
     * @param ShippingMethod $shippingMethod
     * @param Carrier $carrier
     * @param int $rangeId
     *
     * @throws CarrierException
     */
    protected function addDeliveryPrice(
        ShippingRange $shippingRange,
        ShippingMethod $shippingMethod,
        Carrier $carrier,
        int $rangeId
    ) {
        $rangePriceId = $rangeId;
        $rangeWeightId = null;

        if ($shippingMethod->getValue() === ShippingMethod::SHIPPING_METHOD_WEIGHT) {
            $rangePriceId = null;
            $rangeWeightId = $rangeId;
        }

        foreach ($shippingRange->getPricesByZoneId() as $zoneId => $price) {
            $priceList[] = array(
                'id_range_price' => $rangePriceId,
                'id_range_weight' => $rangeWeightId,
                'id_carrier' => (int) $carrier->id,
                'id_zone' => (int) $zoneId,
                'price' => (float) $price,
            );

            if (false === $carrier->addDeliveryPrice($priceList)) {
                throw new CarrierException(sprintf(
                    'Failed to add delivery price for carrier with id "%s" within range "%s - %s"',
                    $carrier->id,
                    $shippingRange->getFrom(),
                    $shippingRange->getTo()
                ));
            }
        }
    }
}
