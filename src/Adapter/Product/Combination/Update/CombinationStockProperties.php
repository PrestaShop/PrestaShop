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

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\Update;

use DateTimeInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\StockModification;

class CombinationStockProperties
{
    /**
     * @var StockModification|null
     */
    private $stockModification;

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
     * @param StockModification|null $stockModification
     * @param int|null $minimalQuantity
     * @param string|null $location
     * @param int|null $lowStockThreshold
     * @param bool|null $lowStockAlertEnabled
     * @param DateTimeInterface|null $availableDate
     */
    public function __construct(
        ?StockModification $stockModification = null,
        ?int $minimalQuantity = null,
        ?string $location = null,
        ?int $lowStockThreshold = null,
        ?bool $lowStockAlertEnabled = null,
        ?DateTimeInterface $availableDate = null
    ) {
        $this->stockModification = $stockModification;
        $this->minimalQuantity = $minimalQuantity;
        $this->location = $location;
        $this->lowStockThreshold = $lowStockThreshold;
        $this->lowStockAlertEnabled = $lowStockAlertEnabled;
        $this->availableDate = $availableDate;
        $this->stockModification = $stockModification;
    }

    /**
     * @return StockModification|null
     */
    public function getStockModification(): ?StockModification
    {
        return $this->stockModification;
    }

    /**
     * @return int|null
     */
    public function getMinimalQuantity(): ?int
    {
        return $this->minimalQuantity;
    }

    /**
     * @return string|null
     */
    public function getLocation(): ?string
    {
        return $this->location;
    }

    /**
     * @return int|null
     */
    public function getLowStockThreshold(): ?int
    {
        return $this->lowStockThreshold;
    }

    /**
     * @return bool|null
     */
    public function isLowStockAlertEnabled(): ?bool
    {
        return $this->lowStockAlertEnabled;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getAvailableDate(): ?DateTimeInterface
    {
        return $this->availableDate;
    }
}
