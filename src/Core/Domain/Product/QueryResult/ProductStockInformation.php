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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\QueryResult;

use DateTimeInterface;

/**
 * Transfers data about Product stock information
 */
class ProductStockInformation
{
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
     * @var int
     */
    private $lowStockThreshold;

    /**
     * @var bool
     */
    private $lowStockAlertEnabled;

    /**
     * @var string[] key value pairs where key is the id of language
     */
    private $localizedAvailableNowLabels;

    /**
     * @var string[] key value pairs where key is the id of language
     */
    private $localizedAvailableLaterLabels;

    /**
     * @var string
     */
    private $location;

    /**
     * @var DateTimeInterface|null
     */
    private $availableDate;

    /**
     * @param int $packStockType
     * @param int $outOfStockType
     * @param int $quantity
     * @param int $minimalQuantity
     * @param int $lowStockThreshold
     * @param bool $lowStockAlertEnabled
     * @param array $localizedAvailableNowLabels
     * @param array $localizedAvailableLaterLabels
     * @param string $location
     * @param DateTimeInterface|null $availableDate
     */
    public function __construct(
        int $packStockType,
        int $outOfStockType,
        int $quantity,
        int $minimalQuantity,
        int $lowStockThreshold,
        bool $lowStockAlertEnabled,
        array $localizedAvailableNowLabels,
        array $localizedAvailableLaterLabels,
        string $location,
        ?DateTimeInterface $availableDate
    ) {
        $this->packStockType = $packStockType;
        $this->outOfStockType = $outOfStockType;
        $this->quantity = $quantity;
        $this->minimalQuantity = $minimalQuantity;
        $this->location = $location;
        $this->lowStockThreshold = $lowStockThreshold;
        $this->lowStockAlertEnabled = $lowStockAlertEnabled;
        $this->localizedAvailableNowLabels = $localizedAvailableNowLabels;
        $this->localizedAvailableLaterLabels = $localizedAvailableLaterLabels;
        $this->availableDate = $availableDate;
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
     * @return int
     */
    public function getLowStockThreshold(): int
    {
        return $this->lowStockThreshold;
    }

    /**
     * @return bool
     */
    public function isLowStockAlertEnabled(): bool
    {
        return $this->lowStockAlertEnabled;
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
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getAvailableDate(): ?DateTimeInterface
    {
        return $this->availableDate;
    }
}
