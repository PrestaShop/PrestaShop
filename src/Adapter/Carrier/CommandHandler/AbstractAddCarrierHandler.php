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
use PrestaShop\PrestaShop\Adapter\Carrier\AbstractCarrierHandler;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\AbstractAddCarrierCommand;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\Billing;
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
        $carrier->is_free = $command->isFreeShipping();
        $carrier->shipping_method = $command->getBilling()->getValue();

        $carrier->name = $command->getName()->getValue();

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
        $carrier->active = $command->isEnabled();
        $carrier->deleted = false;
    }

    /**
     * @param Carrier $carrier
     * @param Billing $billing
     * @param ShippingRange[] $shippingRanges
     *
     * @throws CarrierException
     * @throws PrestaShopException
     */
    protected function addShippingRanges(Carrier $carrier, Billing $billing, array $shippingRanges): void
    {
        $carrierId = (int) $carrier->id;

        foreach ($shippingRanges as $shippingRange) {
            $billing->accordingToWeight() ?
                $this->addRangeWeight($range = new RangeWeight(), $carrierId, $shippingRange) :
                $this->addRangePrice($range = new RangePrice(), $carrierId, $shippingRange);

            $this->addDeliveryPrice($shippingRange, $billing, $carrier, (int) $range->id);
        }
    }

    /**
     * @param ShippingRange $shippingRange
     * @param Billing $billing
     * @param Carrier $carrier
     * @param int $rangeId
     *
     * @throws CarrierException
     */
    protected function addDeliveryPrice(
        ShippingRange $shippingRange,
        Billing $billing,
        Carrier $carrier,
        int $rangeId
    ) {
        if ($billing->accordingToWeight()) {
            $rangePriceId = null;
            $rangeWeightId = $rangeId;
        } else {
            $rangePriceId = $rangeId;
            $rangeWeightId = null;
        }

        foreach ($shippingRange->getPricesByZoneId() as $zoneId => $price) {
            $priceList[] = [
                'id_range_price' => $rangePriceId,
                'id_range_weight' => $rangeWeightId,
                'id_carrier' => (int) $carrier->id,
                'id_zone' => (int) $zoneId,
                'price' => (float) $price,
            ];

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

    /**
     * @param RangePrice $rangePrice
     * @param int $carrierId
     * @param ShippingRange $shippingRange
     *
     * @throws CarrierException
     */
    private function addRangePrice(RangePrice $rangePrice, int $carrierId, ShippingRange $shippingRange)
    {
        $rangePrice->id_carrier = $carrierId;
        $rangePrice->delimiter1 = $shippingRange->getFrom();
        $rangePrice->delimiter2 = $shippingRange->getTo();

        if (false === $rangePrice->add()) {
            throw new CarrierException(sprintf(
                'Failed to create price range "%s - %s" for carrier with id "%s"',
                $shippingRange->getFrom(),
                $shippingRange->getTo(),
                $carrierId
            ));
        }
    }

    /**
     * @param RangeWeight $rangeWeight
     * @param int $carrierId
     * @param ShippingRange $shippingRange
     *
     * @throws CarrierException
     */
    private function addRangeWeight(RangeWeight $rangeWeight, int $carrierId, ShippingRange $shippingRange)
    {
        $rangeWeight->id_carrier = $carrierId;
        $rangeWeight->delimiter1 = $shippingRange->getFrom();
        $rangeWeight->delimiter2 = $shippingRange->getTo();

        if (false === $rangeWeight->add()) {
            throw new CarrierException(sprintf(
                'Failed to create weight range "%s - %s" for carrier with id "%s"',
                $shippingRange->getFrom(),
                $shippingRange->getTo(),
                $carrierId
            ));
        }
    }
}
