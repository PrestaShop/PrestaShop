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

use DateTimeInterface;

class ProductStock
{
    /**
     * @var bool
     */
    private $useAdvancedStockManagement;

    /**
     * @var bool
     */
    private $dependsOnStock;

    /**
     * @var int
     */
    private $packStockType;

    /**
     * @var int
     */
    private $outOfStockType;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var int
     */
    private $minimalQuantity;

    /**
     * @var string
     */
    private $location;

    /**
     * @var int
     */
    private $lowStockThreshold;

    /**
     * @var bool
     */
    private $lowStockAlert;

    /**
     * @var string[] key value pairs where key is the id of language
     */
    private $localizedAvailableNowLabels;

    /**
     * @var string[] key value pairs where key is the id of language
     */
    private $localizedAvailableLaterLabels;

    /**
     * @var DateTimeInterface
     */
    private $availableDate;

    public function __construct(
        bool $useAdvancedStockManagement,
        bool $dependsOnStock,
        int $packStockType,
        int $outOfStockType,
        int $quantity,
        int $minimalQuantity,
        string $location,
        int $lowStockThreshold,
        bool $lowStockAlert,
        array $localizedAvailableNowLabels,
        array $localizedAvailableLaterLabels,
        DateTimeInterface $availableDate
    ) {
        $this->useAdvancedStockManagement = $useAdvancedStockManagement;
        $this->dependsOnStock = $dependsOnStock;
        $this->packStockType = $packStockType;
        $this->outOfStockType = $outOfStockType;
        $this->quantity = $quantity;
        $this->minimalQuantity = $minimalQuantity;
        $this->location = $location;
        $this->lowStockThreshold = $lowStockThreshold;
        $this->lowStockAlert = $lowStockAlert;
        $this->localizedAvailableNowLabels = $localizedAvailableNowLabels;
        $this->localizedAvailableLaterLabels = $localizedAvailableLaterLabels;
        $this->availableDate = $availableDate;
    }

    /**
     * @return bool
     */
    public function useAdvancedStockManagement(): bool
    {
        return $this->useAdvancedStockManagement;
    }

    /**
     * @return bool
     */
    public function dependsOnStock(): bool
    {
        return $this->dependsOnStock;
    }

    /**
     * @return int
     */
    public function getPackStockType(): int
    {
        return $this->packStockType;
    }

    /**
     * @return int
     */
    public function getOutOfStockType(): int
    {
        return $this->outOfStockType;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return int
     */
    public function getMinimalQuantity(): int
    {
        return $this->minimalQuantity;
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * @return int
     */
    public function getLowStockThreshold(): int
    {
        return $this->lowStockThreshold;
    }

    /**
     * @return bool
     */
    public function hasLowStockAlert(): bool
    {
        return $this->lowStockAlert;
    }

    /**
     * @return string[]
     */
    public function getLocalizedAvailableNowLabels(): array
    {
        return $this->localizedAvailableNowLabels;
    }

    /**
     * @return string[]
     */
    public function getLocalizedAvailableLaterLabels(): array
    {
        return $this->localizedAvailableLaterLabels;
    }

    /**
     * @return DateTimeInterface
     */
    public function getAvailableDate(): DateTimeInterface
    {
        return $this->availableDate;
    }
}
