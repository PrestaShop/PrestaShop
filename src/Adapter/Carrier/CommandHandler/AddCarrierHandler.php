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
use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\AddCarrierCommand;
use PrestaShop\PrestaShop\Core\Domain\Carrier\CommandHandler\AddCarrierHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\ShippingMethod;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\ShippingRange;
use PrestaShopException;
use RangePrice;
use RangeWeight;

/**
 * Handles AddCarrierCommand using legacy object model
 */
final class AddCarrierHandler extends AbstractObjectModelHandler implements AddCarrierHandlerInterface
{
    /**
     * @var int
     */
    private $defaultLangId;

    /**
     * @var int
     */
    private $defaultShippingMethod;

    /**
     * @param int $defaultLangId
     * @param int $defaultShippingMethod
     */
    public function __construct(
        int $defaultLangId,
        int $defaultShippingMethod
    ) {
        $this->defaultLangId = $defaultLangId;
        $this->defaultShippingMethod = $defaultShippingMethod;
    }

    /**
     * @param AddCarrierCommand $command
     *
     * @return CarrierId
     *
     * @throws CarrierException
     */
    public function handle(AddCarrierCommand $command)
    {
        $carrier = new Carrier();
        $this->fillLegacyCarrierWithData($carrier, $command);

        try {
            if (false === $carrier->validateFields(false) || false === $carrier->validateFieldsLang(false)) {
                throw new CarrierException('Carrier contains invalid field values');
            }

            if (false === $carrier->add()) {
                throw new CarrierException(
                    sprintf('Failed to add new carrier')
                );
            }
            $this->associateWithShops($carrier, $command->getAssociatedShopIds());
            $carrier->setTaxRulesGroup($command->getTaxRulesGroupId());
            $carrier->setGroups($command->getAssociatedGroupIds());
            $this->handleRanges($carrier, $command->getShippingMethod(), $command->getShippingRanges());
        } catch (PrestaShopException $e) {
            throw new CarrierException(
                sprintf('An error occurred when trying to add new carrier')
            );
        }

        return new CarrierId((int) $carrier->id);
    }

    /**
     * @param Carrier $carrier
     * @param AddCarrierCommand $command
     */
    private function fillLegacyCarrierWithData(Carrier $carrier, AddCarrierCommand $command)
    {
        $shippingMethod = $command->getShippingMethod()->getValue();

        /*
         * Backwards compatibility.
         * SHIPPING_METHOD_DEFAULT @deprecated 1.5.5
         */
        if (Carrier::SHIPPING_METHOD_DEFAULT === $shippingMethod) {
            $shippingMethod = $this->defaultShippingMethod ?
                ShippingMethod::SHIPPING_METHOD_WEIGHT : ShippingMethod::SHIPPING_METHOD_PRICE;
        }
        $carrier->shipping_method = $shippingMethod;
        $carrier->is_free = ShippingMethod::SHIPPING_METHOD_FREE === $shippingMethod ? true : false;

        $localizedNames = $command->getLocalizedCarrierNames();
        foreach ($localizedNames as $langId => $carrierName) {
            $carrier->localized_name[$langId] = $carrierName->getValue();
        }
        $carrier->name = $localizedNames[$this->defaultLangId]->getValue();

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
    }

    /**
     * @param Carrier $carrier
     * @param ShippingMethod $shippingMethod
     * @param ShippingRange[] $shippingRanges
     *
     * @throws CarrierException
     * @throws PrestaShopException
     * @throws \PrestaShopDatabaseException
     */
    private function handleRanges(Carrier $carrier, ShippingMethod $shippingMethod, array $shippingRanges): void
    {
        $methodValue = $shippingMethod->getValue();
        $carrierId = (int) $carrier->id;

        if ($methodValue === ShippingMethod::SHIPPING_METHOD_PRICE) {
            foreach ($shippingRanges as $shippingRange) {
                $range = new RangePrice();
                $range->id_carrier = $carrierId;
                $range->delimiter1 = $shippingRange->getFrom();
                $range->delimiter2 = $shippingRange->getTo();
                $this->addRange($range, $carrierId, $shippingRange);
                $this->addDeliveryPrice($shippingRange, $shippingMethod, $carrier, (int) $range->id);
            }

            return;
        }

        if ($methodValue === ShippingMethod::SHIPPING_METHOD_WEIGHT) {
            foreach ($shippingRanges as $shippingRange) {
                $range = new RangeWeight();
                $range->id_carrier = $carrierId;
                $range->delimiter1 = $shippingRange->getFrom();
                $range->delimiter2 = $shippingRange->getTo();
                $this->addRange($range, $carrierId, $shippingRange);
                $this->addDeliveryPrice($shippingRange, $shippingMethod, $carrier, (int) $range->id);
            }

            return;
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
    private function addRange(ObjectModel $range, int $carrierId, ShippingRange $shippingRange)
    {
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
    private function addDeliveryPrice(
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
