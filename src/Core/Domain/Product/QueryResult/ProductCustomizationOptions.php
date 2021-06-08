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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Product\ProductCustomizabilitySettings;

/**
 * Transfers data of product customization options
 */
class ProductCustomizationOptions
{
    /**
     * @var int value representing if product requires, allows, disallows customizations
     *          see @var ProductCustomizabilitySettings for more info
     */
    private $customizabilityValue;

    /**
     * @var int
     */
    private $availableTextCustomizationsCount;

    /**
     * @var int
     */
    private $availableFileCustomizationsCount;

    /**
     * @return ProductCustomizationOptions
     */
    public static function createNotCustomizable(): ProductCustomizationOptions
    {
        return new self(ProductCustomizabilitySettings::NOT_CUSTOMIZABLE, 0, 0);
    }

    /**
     * @param int $availableTextCustomizationsCount
     * @param int $availableFileCustomizationsCount
     *
     * @return ProductCustomizationOptions
     */
    public static function createAllowsCustomization(
        int $availableTextCustomizationsCount,
        int $availableFileCustomizationsCount
    ): ProductCustomizationOptions {
        return new self(
            ProductCustomizabilitySettings::ALLOWS_CUSTOMIZATION,
            $availableTextCustomizationsCount,
            $availableFileCustomizationsCount
        );
    }

    /**
     * @param int $availableTextCustomizationsCount
     * @param int $availableFileCustomizationsCount
     *
     * @return ProductCustomizationOptions
     */
    public static function createRequiresCustomization(
        int $availableTextCustomizationsCount,
        int $availableFileCustomizationsCount
    ): ProductCustomizationOptions {
        return new self(
            ProductCustomizabilitySettings::REQUIRES_CUSTOMIZATION,
            $availableTextCustomizationsCount,
            $availableFileCustomizationsCount
        );
    }

    /**
     * @return int
     */
    public function getCustomizabilityValue(): int
    {
        return $this->customizabilityValue;
    }

    /**
     * @return int
     */
    public function getAvailableTextCustomizationsCount(): int
    {
        return $this->availableTextCustomizationsCount;
    }

    /**
     * @return int
     */
    public function getAvailableFileCustomizationsCount(): int
    {
        return $this->availableFileCustomizationsCount;
    }

    /**
     * @return bool true if product does not have any customizations
     */
    public function isNotCustomizable(): bool
    {
        return $this->customizabilityValue === ProductCustomizabilitySettings::NOT_CUSTOMIZABLE;
    }

    /**
     * @return bool true if product has customizations, but none of them are required
     */
    public function allowsCustomization(): bool
    {
        return $this->customizabilityValue === ProductCustomizabilitySettings::ALLOWS_CUSTOMIZATION;
    }

    /**
     * @return bool true if product has at least one required customization
     */
    public function requiresCustomization(): bool
    {
        return $this->customizabilityValue === ProductCustomizabilitySettings::REQUIRES_CUSTOMIZATION;
    }

    /**
     * Use static factories to instantiate this class
     *
     * @param int $value
     * @param int $availableTextCustomizations
     * @param int $availableFileCustomizations
     */
    private function __construct(int $value, int $availableTextCustomizations, int $availableFileCustomizations)
    {
        $this->customizabilityValue = $value;
        $this->availableTextCustomizationsCount = $availableTextCustomizations;
        $this->availableFileCustomizationsCount = $availableFileCustomizations;
    }
}
