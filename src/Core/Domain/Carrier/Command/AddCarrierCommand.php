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
use Symfony\Component\OptionsResolver\OptionsResolver;

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
        $grade,
        $trackingUrl,
        $includeShippingCost,
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
        $this->localizedName = $localizedName;
        $this->localizedDelay = $localizedDelay;
        $this->grade = new SpeedGrade($grade);
        $this->trackingUrl = $trackingUrl;
        $this->includeShippingCost = $includeShippingCost;
        $this->shippingMethod = new ShippingMethod($shippingMethod);
        $this->setShippingRanges($shippingRanges);
        $this->taxId = $taxId;
        $this->outOfRangeBehavior = $outOfRangeBehavior;
        $this->maxPackageWidth = new PackageSizeMeasure($maxPackageWidth);
        $this->maxPackageHeight = new PackageSizeMeasure($maxPackageHeight);
        $this->maxPackageDepth = new PackageSizeMeasure($maxPackageDepth);
        $this->maxPackageWeight = new PackageWeightMeasure($maxPackageWeight);
        $this->associatedGroupIds = $associatedGroupIds;
        $this->associatedShopIds = $associatedShopIds;
    }

    /**
     * @param array $shippingRanges
     */
    private function setShippingRanges(array $shippingRanges)
    {
        foreach ($shippingRanges as $range) {
            $resolvedRange = $this->resolveRangeParams($range);
            $this->shippingRanges[] = new ShippingRange(
                $resolvedRange['from'],
                $resolvedRange['to'],
                $resolvedRange['prices_by_zone_id']
            );
        }
    }

    /**
     * Resolves array parameters to contain valid structure
     *
     * @param array $params
     *
     * @return array
     */
    private function resolveRangeParams(array $params)
    {
        $resolver = new OptionsResolver();

        $resolver->setRequired(['from', 'to', 'prices_by_zone_id']);
        $resolver->setAllowedTypes('from', 'int');
        $resolver->setAllowedTypes('to', 'int');
        $resolver->setAllowedTypes('prices_by_zone_id', 'array');

        return $resolver->resolve($params);
    }
}
