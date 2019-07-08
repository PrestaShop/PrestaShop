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

use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\PackageSizeMeasure;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\PackageWeightMeasure;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\ShippingPrice;
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
     * @var int
     */
    private $grade;

    /**
     * @var string
     */
    private $trackingUrl;

    /**
     * @var bool
     */
    private $includeShippingCost;

    /**
     * @var ShippingPrice[]
     */
    private $shippingPrices;

    /**
     * @var int
     */
    private $taxId;

    /**
     * @var bool
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
     * @param int $grade
     * @param string $trackingUrl
     * @param bool $includeShippingCost
     * @param int $shippingMethod
     * @param int $taxId
     * @param bool $outOfRangeBehavior
     * @param array $rangeZonePrices
     * @param int $maxPackageWidth
     * @param int $maxPackageHeight
     * @param int $maxPackageDepth
     * @param float $maxPackageWeight
     * @param int[] $associatedGroupIds
     * @param int[]|null $associatedShopIds
     */
    public function __construct(
        array $localizedName,
        array $localizedDelay,
        $grade,
        $trackingUrl,
        $includeShippingCost,
        $shippingMethod,
        $taxId,
        $outOfRangeBehavior,
        array $rangeZonePrices,
        $maxPackageWidth,
        $maxPackageHeight,
        $maxPackageDepth,
        $maxPackageWeight,
        array $associatedGroupIds,
        array $associatedShopIds
    ) {
        $this->localizedName = $localizedName;
        $this->localizedDelay = $localizedDelay;
        $this->grade = new SpeedGrade($grade);
        $this->trackingUrl = $trackingUrl;
        $this->includeShippingCost = $includeShippingCost;
        $this->shippingPrices = $this->fillShippingRanges($shippingMethod);
        $this->taxId = $taxId;
        $this->outOfRangeBehavior = $outOfRangeBehavior;
        $this->maxPackageWidth = new PackageSizeMeasure($maxPackageWidth);
        $this->maxPackageHeight = new PackageSizeMeasure($maxPackageHeight);
        $this->maxPackageDepth = new PackageSizeMeasure($maxPackageDepth);
        $this->maxPackageWeight = new PackageWeightMeasure($maxPackageWeight);
        $this->associatedGroupIds = $associatedGroupIds;
        $this->associatedShopIds = $associatedShopIds;
    }

    private function fillShippingPrices(
        $shippingMethod,
        $rangeZonePrices
    ) {
        foreach ($rangeZonePrices as $range => $zonePrices) {
            $this->shippingPrices = new ShippingPrice(
                $shippingMethod,
                $rangeZonePrices
            );
        }
    }
}
