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
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\PackageSizeMeasure;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\PackageWeightMeasure;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\ShippingMethod;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\ShippingRange;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\SpeedGrade;

/**
 * Adds new carrier
 */
final class AddCarrierCommand
{
    /**
     * @var string[]
     */
    private $localizedName;

    /**
     * @var string[]
     */
    private $localizedDelay;

    /**
     * @var SpeedGrade
     */
    private $speedGrade;

    /**
     * @var string
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
    private $taxId;

    /**
     * @var int
     */
    private $outOfRangeBehavior;

    /**
     * @var PackageSizeMeasure
     */
    private $maxPackageWidth;

    /**
     * @var PackageSizeMeasure
     */
    private $maxPackageHeight;

    /**
     * @var PackageSizeMeasure
     */
    private $maxPackageDepth;

    /**
     * @var PackageWeightMeasure
     */
    private $maxPackageWeight;

    /**
     * @var int[]
     */
    private $associatedGroupIds;

    /**
     * @var int[]|null
     */
    private $associatedShopIds;

    /**
     * @param string[] $localizedName
     * @param string[] $localizedDelay
     * @param int $speedGrade
     * @param string $trackingUrl
     * @param bool $shippingCostIncluded
     * @param int $shippingMethod
     * @param int $taxId
     * @param int $outOfRangeBehavior
     * @param array $shippingRanges
     * @param int $maxPackageWidth
     * @param int $maxPackageHeight
     * @param int $maxPackageDepth
     * @param float $maxPackageWeight
     * @param int[] $associatedGroupIds
     * @param int[]|null $associatedShopIds
     *
     * @throws CarrierConstraintException
     */
    public function __construct(
        array $localizedName,
        array $localizedDelay,
        $speedGrade,
        $trackingUrl,
        $shippingCostIncluded,
        $shippingMethod,
        $taxId,
        $outOfRangeBehavior,
        array $shippingRanges,
        $maxPackageWidth,
        $maxPackageHeight,
        $maxPackageDepth,
        $maxPackageWeight,
        array $associatedGroupIds,
        array $associatedShopIds
    ) {
        $this->assertOutOfRangeBehaviorValueIsValid($outOfRangeBehavior);
        $this->maxPackageWidth = new PackageSizeMeasure($maxPackageWidth);
        $this->maxPackageHeight = new PackageSizeMeasure($maxPackageHeight);
        $this->maxPackageDepth = new PackageSizeMeasure($maxPackageDepth);
        $this->maxPackageWeight = new PackageWeightMeasure($maxPackageWeight);
        $this->speedGrade = new SpeedGrade($speedGrade);
        $this->shippingMethod = new ShippingMethod($shippingMethod);
        $this->setShippingRanges($shippingRanges);
        $this->outOfRangeBehavior = $outOfRangeBehavior;
        $this->localizedName = $localizedName;
        $this->localizedDelay = $localizedDelay;
        $this->trackingUrl = $trackingUrl;
        $this->shippingCostIncluded = $shippingCostIncluded;
        $this->taxId = $taxId;
        $this->associatedGroupIds = $associatedGroupIds;
        $this->associatedShopIds = $associatedShopIds;
    }

    /**
     * @return string[]
     */
    public function getLocalizedName()
    {
        return $this->localizedName;
    }

    /**
     * @return string[]
     */
    public function getLocalizedDelay()
    {
        return $this->localizedDelay;
    }

    /**
     * @return SpeedGrade
     */
    public function getSpeedGrade()
    {
        return $this->speedGrade;
    }

    /**
     * @return string
     */
    public function getTrackingUrl()
    {
        return $this->trackingUrl;
    }

    /**
     * @return bool
     */
    public function isShippingCostIncluded()
    {
        return $this->shippingCostIncluded;
    }

    /**
     * @return ShippingMethod
     */
    public function getShippingMethod()
    {
        return $this->shippingMethod;
    }

    /**
     * @return ShippingRange[]
     */
    public function getShippingRanges()
    {
        return $this->shippingRanges;
    }

    /**
     * @return int
     */
    public function getTaxId()
    {
        return $this->taxId;
    }

    /**
     * @return int
     */
    public function getOutOfRangeBehavior()
    {
        return $this->outOfRangeBehavior;
    }

    /**
     * @return PackageSizeMeasure
     */
    public function getMaxPackageWidth()
    {
        return $this->maxPackageWidth;
    }

    /**
     * @return PackageSizeMeasure
     */
    public function getMaxPackageHeight()
    {
        return $this->maxPackageHeight;
    }

    /**
     * @return PackageSizeMeasure
     */
    public function getMaxPackageDepth()
    {
        return $this->maxPackageDepth;
    }

    /**
     * @return PackageWeightMeasure
     */
    public function getMaxPackageWeight()
    {
        return $this->maxPackageWeight;
    }

    /**
     * @return int[]
     */
    public function getAssociatedGroupIds()
    {
        return $this->associatedGroupIds;
    }

    /**
     * @return int[]|null
     */
    public function getAssociatedShopIds()
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
     * @param int $value
     *
     * @throws CarrierConstraintException
     */
    private function assertOutOfRangeBehaviorValueIsValid($value)
    {
        $definedValues = [
            ShippingRange::WHEN_OUT_OF_RANGE_APPLY_HIGHEST,
            ShippingRange::WHEN_OUT_OF_RANGE_DISABLE_CARRIER,
        ];

        if (!in_array($value, $definedValues, true)) {
            throw new CarrierConstraintException(sprintf(
                'Invalid out of range behavior value "%s". Defined values are: %s',
                var_export($value, true),
                implode(', ', $definedValues)
            ));
        }
    }
}
