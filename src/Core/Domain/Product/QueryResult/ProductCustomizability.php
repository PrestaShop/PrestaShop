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
class ProductCustomizability
{
    /**
     * @var int
     */
    private $value;

    /**
     * @var int
     */
    private $availableTextCustomizations;

    /**
     * @var int
     */
    private $availableFileCustomizations;

    /**
     * @return ProductCustomizability
     */
    public static function createNotCustomizable(): ProductCustomizability
    {
        return new self(
            ProductCustomizabilitySettings::NOT_CUSTOMIZABLE,
            0,
            0
        );
    }

    /**
     * @param int $availableTextCustomizations
     * @param int $availableFileCustomizations
     *
     * @return ProductCustomizability
     */
    public static function createAllowsCustomization(
        int $availableTextCustomizations,
        int $availableFileCustomizations
    ): ProductCustomizability {
        return new self(
            ProductCustomizabilitySettings::ALLOWS_CUSTOMIZATION,
            $availableTextCustomizations,
            $availableFileCustomizations
        );
    }

    /**
     * @param int $availableTextCustomizations
     * @param int $availableFileCustomizations
     *
     * @return ProductCustomizability
     */
    public static function createRequiresCustomization(
        int $availableTextCustomizations,
        int $availableFileCustomizations
    ): ProductCustomizability {
        return new self(
            ProductCustomizabilitySettings::REQUIRES_CUSTOMIZATION,
            $availableTextCustomizations,
            $availableFileCustomizations
        );
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @return int
     */
    public function getAvailableTextCustomizations(): int
    {
        return $this->availableTextCustomizations;
    }

    /**
     * @return int
     */
    public function getAvailableFileCustomizations(): int
    {
        return $this->availableFileCustomizations;
    }

    /**
     * @return bool
     */
    public function isNotCustomizable(): bool
    {
        return $this->value === ProductCustomizabilitySettings::NOT_CUSTOMIZABLE;
    }

    /**
     * @return bool
     */
    public function allowsCustomization(): bool
    {
        return $this->value === ProductCustomizabilitySettings::ALLOWS_CUSTOMIZATION;
    }

    /**
     * @return bool
     */
    public function requiresCustomization(): bool
    {
        return $this->value === ProductCustomizabilitySettings::REQUIRES_CUSTOMIZATION;
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
        $this->value = $value;
        $this->availableTextCustomizations = $availableTextCustomizations;
        $this->availableFileCustomizations = $availableFileCustomizations;
    }
}
