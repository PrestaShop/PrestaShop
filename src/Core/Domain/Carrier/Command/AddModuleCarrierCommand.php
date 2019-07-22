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
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\OutOfRangeBehavior;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\ShippingMethod;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\SpeedGrade;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\TrackingUrl;

final class AddModuleCarrierCommand extends AbstractAddCarrierCommand
{
    /**
     * @var string
     */
    private $moduleName;

    /**
     * @var bool
     */
    private $moduleCalculatesShippingPrice;

    /**
     * @var bool
     */
    private $moduleNeedsCoreShippingPrice;

    /**
     * This class should be initialized using static factories
     */
    private function __construct()
    {
    }

    /**
     * @param string[] $localizedNames
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
     * @param string $moduleName
     *
     * @return AddModuleCarrierCommand
     *
     * @throws CarrierConstraintException
     */
    public static function withCoreShippingPrice(
        array $localizedNames,
        array $localizedShippingDelays,
        int $speedGrade,
        string $trackingUrl,
        bool $shippingCostIncluded,
        int $shippingMethod,
        int $taxRulesGroupId,
        int $outOfRangeBehavior,
        array $shippingRanges,
        int $maxPackageWidth,
        int $maxPackageHeight,
        int $maxPackageDepth,
        float $maxPackageWeight,
        array $associatedGroupIds,
        array $associatedShopIds,
        string $moduleName
    ) {
        $command = new self();
        $command->setLocalizedNames($localizedNames);
        $command->setLocalizedShippingDelays($localizedShippingDelays);
        $command->setMeasures($maxPackageWidth, $maxPackageHeight, $maxPackageDepth, $maxPackageWeight);
        $command->setShippingRanges($shippingRanges);
        $command->assertModuleName($moduleName);
        $command->moduleName = $moduleName;
        $command->speedGrade = new SpeedGrade($speedGrade);
        $command->shippingMethod = new ShippingMethod($shippingMethod);
        $command->trackingUrl = new TrackingUrl($trackingUrl);
        $command->outOfRangeBehavior = new OutOfRangeBehavior($outOfRangeBehavior);
        $command->shippingCostIncluded = $shippingCostIncluded;
        $command->taxRulesGroupId = $taxRulesGroupId;
        $command->associatedGroupIds = $associatedGroupIds;
        $command->associatedShopIds = $associatedShopIds;

        $command->freeShipping = false;
        $command->moduleCalculatesShippingPrice = false;
        $command->moduleNeedsCoreShippingPrice = false;

        return $command;
    }

    /**
     * @param string[] $localizedNames
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
     * @param string $moduleName
     * @param bool $moduleNeedsCoreShippingPrice
     *
     * @return AddModuleCarrierCommand
     *
     * @throws CarrierConstraintException
     */
    public static function withModuleShippingPrice(
        array $localizedNames,
        array $localizedShippingDelays,
        int $speedGrade,
        string $trackingUrl,
        bool $shippingCostIncluded,
        int $shippingMethod,
        int $taxRulesGroupId,
        int $outOfRangeBehavior,
        array $shippingRanges,
        int $maxPackageWidth,
        int $maxPackageHeight,
        int $maxPackageDepth,
        float $maxPackageWeight,
        array $associatedGroupIds,
        array $associatedShopIds,
        string $moduleName,
        bool $moduleNeedsCoreShippingPrice
    ) {
        $command = new self();
        $command->setLocalizedNames($localizedNames);
        $command->setLocalizedShippingDelays($localizedShippingDelays);
        $command->setMeasures($maxPackageWidth, $maxPackageHeight, $maxPackageDepth, $maxPackageWeight);
        $command->setShippingRanges($shippingRanges);
        $command->assertModuleName($moduleName);
        $command->moduleName = $moduleName;
        $command->speedGrade = new SpeedGrade($speedGrade);
        $command->shippingMethod = new ShippingMethod($shippingMethod);
        $command->trackingUrl = new TrackingUrl($trackingUrl);
        $command->outOfRangeBehavior = new OutOfRangeBehavior($outOfRangeBehavior);
        $command->freeShipping = false;
        $command->shippingCostIncluded = $shippingCostIncluded;
        $command->taxRulesGroupId = $taxRulesGroupId;
        $command->associatedGroupIds = $associatedGroupIds;
        $command->associatedShopIds = $associatedShopIds;
        $command->moduleCalculatesShippingPrice = true;
        $command->moduleNeedsCoreShippingPrice = $moduleNeedsCoreShippingPrice;

        return $command;
    }

    /**
     * @param string[] $localizedNames
     * @param string[] $localizedShippingDelays
     * @param int $speedGrade
     * @param string $trackingUrl
     * @param int $taxRulesGroupId
     * @param int $maxPackageWidth
     * @param int $maxPackageHeight
     * @param int $maxPackageDepth
     * @param float $maxPackageWeight
     * @param int[] $associatedGroupIds
     * @param int[] $associatedShopIds
     * @param string $moduleName
     *
     * @return AddModuleCarrierCommand
     *
     * @throws CarrierConstraintException
     */
    public static function withFreeShipping(
        array $localizedNames,
        array $localizedShippingDelays,
        int $speedGrade,
        string $trackingUrl,
        int $taxRulesGroupId,
        int $maxPackageWidth,
        int $maxPackageHeight,
        int $maxPackageDepth,
        float $maxPackageWeight,
        array $associatedGroupIds,
        array $associatedShopIds,
        string $moduleName
    ) {
        $command = new self();
        $command->setLocalizedNames($localizedNames);
        $command->setLocalizedShippingDelays($localizedShippingDelays);
        $command->setMeasures($maxPackageWidth, $maxPackageHeight, $maxPackageDepth, $maxPackageWeight);
        $command->assertModuleName($moduleName);
        $command->moduleName = $moduleName;
        $command->speedGrade = new SpeedGrade($speedGrade);
        $command->trackingUrl = new TrackingUrl($trackingUrl);
        $command->outOfRangeBehavior = new OutOfRangeBehavior(OutOfRangeBehavior::APPLY_HIGHEST_RANGE);
        $command->shippingMethod = new ShippingMethod(ShippingMethod::SHIPPING_METHOD_WEIGHT);
        $command->taxRulesGroupId = $taxRulesGroupId;
        $command->associatedGroupIds = $associatedGroupIds;
        $command->associatedShopIds = $associatedShopIds;

        $command->shippingRanges = [];
        $command->freeShipping = true;
        $command->shippingCostIncluded = false;
        $command->moduleCalculatesShippingPrice = false;
        $command->moduleNeedsCoreShippingPrice = false;

        return $command;
    }

    /**
     * @return string
     */
    public function getModuleName(): string
    {
        return $this->moduleName;
    }

    /**
     * @return bool
     */
    public function doModuleCalculateShippingPrice(): bool
    {
        return $this->moduleCalculatesShippingPrice;
    }

    /**
     * @return bool
     */
    public function doModuleNeedCoreShippingPrice(): bool
    {
        return $this->moduleNeedsCoreShippingPrice;
    }

    /**
     * @param string $name
     *
     * @throws CarrierConstraintException
     */
    private function assertModuleName(string $name)
    {
        $maxLength = 64;

        if ('' === $name || $maxLength < strlen($name)) {
            throw new CarrierConstraintException(
                sprintf('Carrier module name length is invalid. It must be 1 - %s characters long', $maxLength),
                CarrierConstraintException::INVALID_MODULE_NAME
            );
        }
    }
}
