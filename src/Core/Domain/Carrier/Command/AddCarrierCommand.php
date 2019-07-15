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
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\ShippingMethod;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\ShippingRange;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\SpeedGrade;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\TrackingUrl;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;

/**
 * Adds new carrier
 */
final class AddCarrierCommand
{
    /**
     * @var CarrierName[]
     */
    private $localizedCarrierNames;

    /**
     * @var ShippingDelay[]
     */
    private $localizedShippingDelays;

    /**
     * @var SpeedGrade
     */
    private $speedGrade;

    /**
     * @var TrackingUrl
     */
    private $trackingUrl;

    /**
     * @var bool
     */
    private $shippingCostIncluded;

    /**
     * @var ShippingMethod
     */
    private $shippingMethod;

    /**
     * @var ShippingRange[]
     */
    private $shippingRanges;

    /**
     * @var int
     */
    private $taxRulesGroupId;

    /**
     * @var OutOfRangeBehavior
     */
    private $outOfRangeBehavior;

    /**
     * @var int
     */
    private $maxPackageWidth;

    /**
     * @var int
     */
    private $maxPackageHeight;

    /**
     * @var int
     */
    private $maxPackageDepth;

    /**
     * @var float
     */
    private $maxPackageWeight;

    /**
     * @var int[]
     */
    private $associatedGroupIds;

    /**
     * @var int[]
     */
    private $associatedShopIds;

    /**
     * @param string[] $localizedCarrierNames
     * @param string[] $localizedShippingDelays
     * @param int $speedGrade
     * @param string $trackingUrl
     * @param bool $shippingCostIncluded
     * @param int $shippingMethod
     * @param int $taxRulesGroupId
     * @param int $outOfRangeBehavior
     * @param array $shippingRanges
     * @param int $maxPackageWidth
     * @param int $maxPackageHeight
     * @param int $maxPackageDepth
     * @param float $maxPackageWeight
     * @param int[] $associatedGroupIds
     * @param int[] $associatedShopIds
     *
     * @throws CarrierConstraintException
     */
    public function __construct(
        array $localizedCarrierNames,
        array $localizedShippingDelays,
        $speedGrade,
        $trackingUrl,
        $shippingCostIncluded,
        $shippingMethod,
        $taxRulesGroupId,
        int $outOfRangeBehavior,
        array $shippingRanges,
        int $maxPackageWidth,
        int $maxPackageHeight,
        int $maxPackageDepth,
        float $maxPackageWeight,
        array $associatedGroupIds,
        array $associatedShopIds
    ) {
        $this->setLocalizedCarrierNames($localizedCarrierNames);
        $this->setLocalizedShippingDelays($localizedShippingDelays);
        $this->setMeasures($maxPackageWidth, $maxPackageHeight, $maxPackageDepth, $maxPackageWeight);
        $this->setShippingRanges($shippingRanges);
        $this->speedGrade = new SpeedGrade($speedGrade);
        $this->shippingMethod = new ShippingMethod($shippingMethod);
        $this->trackingUrl = new TrackingUrl($trackingUrl);
        $this->outOfRangeBehavior = new OutOfRangeBehavior($outOfRangeBehavior);
        $this->taxRulesGroupId = $taxRulesGroupId;
        $this->shippingCostIncluded = $shippingCostIncluded;
        $this->associatedGroupIds = $associatedGroupIds;
        $this->associatedShopIds = $associatedShopIds;
    }

    /**
     * @return CarrierName[]
     */
    public function getLocalizedCarrierNames(): array
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
     * @return ShippingMethod
     */
    public function getShippingMethod(): ShippingMethod
    {
        return $this->shippingMethod;
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
    private function setShippingRanges(array $shippingRanges)
    {
        foreach ($shippingRanges as $range) {
            $this->shippingRanges[] = ShippingRange::buildFromArray($range);
        }
    }

    /**
     * @param array $localizedCarrierNames
     *
     * @throws CarrierConstraintException
     */
    private function setLocalizedCarrierNames(array $localizedCarrierNames)
    {
        foreach ($localizedCarrierNames as $langId => $name) {
            $this->localizedCarrierNames[(new LanguageId($langId))->getValue()] = new CarrierName($name);
        }
    }

    /**
     * @param array $localizedShippingDelays
     *
     * @throws CarrierConstraintException
     */
    private function setLocalizedShippingDelays(array $localizedShippingDelays)
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
    private function setMeasures(int $width, int $height, int $depth, float $weight)
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
    private function assertMeasureIsNonNegative($measure)
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
