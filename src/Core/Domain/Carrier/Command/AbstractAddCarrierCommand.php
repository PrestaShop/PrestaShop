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

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\Command;

use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierName;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\OutOfRangeBehavior;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\ShippingDelay;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\Billing;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\ShippingRange;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\SpeedGrade;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\TrackingUrl;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;

/**
 * Provides reusable properties/methods for AddCarrierCommands
 */
abstract class AbstractAddCarrierCommand
{
    /**
     * @var CarrierName[]
     */
    protected $localizedCarrierNames;

    /**
     * @var ShippingDelay[]
     */
    protected $localizedShippingDelays;

    /**
     * @var SpeedGrade
     */
    protected $speedGrade;

    /**
     * @var TrackingUrl
     */
    protected $trackingUrl;

    /**
     * @var bool
     */
    protected $shippingCostIncluded;

    /**
     * @var Billing
     */
    protected $billing;

    /**
     * @var ShippingRange[]
     */
    protected $shippingRanges;

    /**
     * @var int
     */
    protected $taxRulesGroupId;

    /**
     * @var OutOfRangeBehavior
     */
    protected $outOfRangeBehavior;

    /**
     * @var int
     */
    protected $maxPackageWidth;

    /**
     * @var int
     */
    protected $maxPackageHeight;

    /**
     * @var int
     */
    protected $maxPackageDepth;

    /**
     * @var float
     */
    protected $maxPackageWeight;

    /**
     * @var int[]
     */
    protected $associatedGroupIds;

    /**
     * @var int[]
     */
    protected $associatedShopIds;
    /**
     * @var bool
     */
    protected $freeShipping;

    /**
     * @return bool
     */
    public function isFreeShipping(): bool
    {
        return $this->freeShipping;
    }

    /**
     * @return CarrierName[]
     */
    public function getLocalizedNames(): array
    {
        return $this->localizedCarrierNames;
    }

    /**
     * @return ShippingDelay[]
     */
    public function getLocalizedShippingDelays(): array
    {
        return $this->localizedShippingDelays;
    }

    /**
     * @return SpeedGrade
     */
    public function getSpeedGrade(): SpeedGrade
    {
        return $this->speedGrade;
    }

    /**
     * @return TrackingUrl
     */
    public function getTrackingUrl(): TrackingUrl
    {
        return $this->trackingUrl;
    }

    /**
     * @return bool
     */
    public function isShippingCostIncluded(): bool
    {
        return $this->shippingCostIncluded;
    }

    /**
     * @return Billing
     */
    public function getBilling(): Billing
    {
        return $this->billing;
    }

    /**
     * @return ShippingRange[]
     */
    public function getShippingRanges(): array
    {
        return $this->shippingRanges;
    }

    /**
     * @return int
     */
    public function getTaxRulesGroupId(): int
    {
        return $this->taxRulesGroupId;
    }

    /**
     * @return OutOfRangeBehavior
     */
    public function getOutOfRangeBehavior(): OutOfRangeBehavior
    {
        return $this->outOfRangeBehavior;
    }

    /**
     * @return int
     */
    public function getMaxPackageWidth(): int
    {
        return $this->maxPackageWidth;
    }

    /**
     * @return int
     */
    public function getMaxPackageHeight(): int
    {
        return $this->maxPackageHeight;
    }

    /**
     * @return int
     */
    public function getMaxPackageDepth(): int
    {
        return $this->maxPackageDepth;
    }

    /**
     * @return float
     */
    public function getMaxPackageWeight(): float
    {
        return $this->maxPackageWeight;
    }

    /**
     * @return int[]
     */
    public function getAssociatedGroupIds(): array
    {
        return $this->associatedGroupIds;
    }

    /**
     * @return int[]
     */
    public function getAssociatedShopIds(): array
    {
        return $this->associatedShopIds;
    }

    /**
     * @param array $shippingRanges
     *
     * @throws CarrierConstraintException
     */
    protected function setShippingRanges(array $shippingRanges)
    {
        $this->assertShippingRangesIsNotEmptySequentialArray($shippingRanges);

        foreach ($shippingRanges as $i => $range) {
            $this->assertShippingRangeArrayKeysExists($range);

            if (isset($shippingRanges[$i - 1])) {
                $this->assertRangesSequence($range, $shippingRanges[$i - 1]);
            }
            $this->shippingRanges[] = new ShippingRange($range['from'], $range['to'], $range['prices_by_zone_id']);
        }
    }

    /**
     * Checks whether the provided array is not empty and contains sequential keys (is not associative)
     *
     * @param array $shippingRanges
     *
     * @throws CarrierConstraintException
     */
    protected function assertShippingRangesIsNotEmptySequentialArray(array $shippingRanges)
    {
        if (empty($shippingRanges) || array_keys($shippingRanges) !== range(0, count($shippingRanges) - 1)) {
            throw new CarrierConstraintException(
                'Invalid carrier shipping ranges. The shipping ranges array cannot be empty or associative',
                CarrierConstraintException::INVALID_SHIPPING_RANGE
            );
        }
    }

    /**
     * Checks whether the provided array of single range contains required keys
     *
     * @param array $range
     *
     * @throws CarrierConstraintException
     */
    protected function assertShippingRangeArrayKeysExists(array $range)
    {
        if (!isset($range['from'], $range['to'], $range['prices_by_zone_id'])) {
            throw new CarrierConstraintException(sprintf(
                'Invalid data provided for shipping ranges. Each range must contain keys: %s',
                'from, to, prices_by_zone_id.'),
                CarrierConstraintException::INVALID_SHIPPING_RANGE
            );
        }
    }

    /**
     * Checks whether the sequence of ranges is correct.
     * The previously provided 'range to' value cannot be greater than current 'range from' value
     * as this way ranges would overlap each other.
     *
     * @param array $currentRange
     * @param array $previousRange
     *
     * @throws CarrierConstraintException
     */
    protected function assertRangesSequence(array $currentRange, array $previousRange)
    {
        if ($currentRange['from'] < $previousRange['to']) {
            throw new CarrierConstraintException(sprintf(
                'Shipping ranges %s - %s and %s - %s are overlapping.',
                $previousRange['from'],
                $previousRange['to'],
                $currentRange['from'],
                $currentRange['to']),
                CarrierConstraintException::INVALID_SHIPPING_RANGE
            );
        }
    }

    /**
     * @param array $localizedNames
     *
     * @throws CarrierConstraintException
     */
    protected function setLocalizedNames(array $localizedNames)
    {
        foreach ($localizedNames as $langId => $name) {
            $this->localizedCarrierNames[(new LanguageId($langId))->getValue()] = new CarrierName($name);
        }
    }

    /**
     * @param array $localizedShippingDelays
     *
     * @throws CarrierConstraintException
     */
    protected function setLocalizedShippingDelays(array $localizedShippingDelays)
    {
        foreach ($localizedShippingDelays as $langId => $delay) {
            $this->localizedShippingDelays[(new LanguageId($langId))->getValue()] = new ShippingDelay($delay);
        }
    }

    /**
     * @param int $width
     * @param int $height
     * @param int $depth
     * @param float $weight
     *
     * @throws CarrierConstraintException
     */
    protected function setMeasures(int $width, int $height, int $depth, float $weight)
    {
        foreach ([$width, $height, $depth, $weight] as $measure) {
            $this->assertMeasureIsNonNegative($measure);
        }

        $this->maxPackageWidth = $width;
        $this->maxPackageHeight = $height;
        $this->maxPackageDepth = $depth;
        $this->maxPackageWeight = $weight;
    }

    /**
     * @param int|float $measure
     *
     * @throws CarrierConstraintException
     */
    protected function assertMeasureIsNonNegative($measure)
    {
        if (0 > $measure) {
            throw new CarrierConstraintException(sprintf(
                'Carrier package measure "%s" is invalid. It cannot be negative.',
                $measure),
                CarrierConstraintException::INVALID_PACKAGE_MEASURE
            );
        }
    }
}
