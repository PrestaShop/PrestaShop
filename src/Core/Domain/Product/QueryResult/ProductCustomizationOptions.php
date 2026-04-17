<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
