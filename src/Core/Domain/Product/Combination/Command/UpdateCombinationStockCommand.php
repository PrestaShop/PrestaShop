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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command;

use DateTimeInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;

/**
 * Updates combination stock information
 */
class UpdateCombinationStockCommand
{
    /**
     * @var CombinationId
     */
    private $combinationId;

    /**
     * @var int|null
     */
    private $quantity;

    /**
     * @var int|null
     */
    private $minimalQuantity;

    /**
     * @var string|null
     */
    private $location;

    /**
     * @var int|null
     */
    private $lowStockThreshold;

    /**
     * @var bool|null
     */
    private $lowStockAlertEnabled;

    /**
     * @var DateTimeInterface|null
     */
    private $availableDate;

    /**
     * @param int $combinationId
     */
    public function __construct(
        int $combinationId
    ) {
        $this->combinationId = new CombinationId($combinationId);
    }

    /**
     * @return CombinationId
     */
    public function getCombinationId(): CombinationId
    {
        return $this->combinationId;
    }

    /**
     * @return int|null
     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     *
     * @return UpdateCombinationStockCommand
     */
    public function setQuantity(int $quantity): UpdateCombinationStockCommand
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getMinimalQuantity(): ?int
    {
        return $this->minimalQuantity;
    }

    /**
     * @param int $minimalQuantity
     *
     * @return UpdateCombinationStockCommand
     */
    public function setMinimalQuantity(int $minimalQuantity): UpdateCombinationStockCommand
    {
        $this->minimalQuantity = $minimalQuantity;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLocation(): ?string
    {
        return $this->location;
    }

    /**
     * @param string $location
     *
     * @return UpdateCombinationStockCommand
     */
    public function setLocation(string $location): UpdateCombinationStockCommand
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLowStockThreshold(): ?int
    {
        return $this->lowStockThreshold;
    }

    /**
     * @param int $lowStockThreshold
     *
     * @return UpdateCombinationStockCommand
     */
    public function setLowStockThreshold(int $lowStockThreshold): UpdateCombinationStockCommand
    {
        $this->lowStockThreshold = $lowStockThreshold;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isLowStockAlertEnabled(): ?bool
    {
        return $this->lowStockAlertEnabled;
    }

    /**
     * @param bool $enabled
     *
     * @return UpdateCombinationStockCommand
     */
    public function setLowStockAlert(bool $enabled): UpdateCombinationStockCommand
    {
        $this->lowStockAlertEnabled = $enabled;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getAvailableDate(): ?DateTimeInterface
    {
        return $this->availableDate;
    }

    /**
     * @param DateTimeInterface $availableDate
     *
     * @return UpdateCombinationStockCommand
     */
    public function setAvailableDate(DateTimeInterface $availableDate): UpdateCombinationStockCommand
    {
        $this->availableDate = $availableDate;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getLowStockAlertEnabled(): ?bool
    {
        return $this->lowStockAlertEnabled;
    }
}
