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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult;

use DateTimeInterface;

class CombinationStock
{
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
    private $localtion;

    /**
     * @var int
     */
    private $lowStockThreshold;

    /**
     * @var bool
     */
    private $lowStockAlertOn;

    /**
     * @var DateTimeInterface
     */
    private $availableDate;

    /**
     * @param int $quantity
     * @param int $minimalQuantity
     * @param string $localtion
     * @param int $lowStockThreshold
     * @param bool $lowStockAlertOn
     * @param DateTimeInterface $availableDate
     */
    public function __construct(
        int $quantity,
        int $minimalQuantity,
        string $localtion,
        int $lowStockThreshold,
        bool $lowStockAlertOn,
        DateTimeInterface $availableDate
    ) {
        $this->quantity = $quantity;
        $this->minimalQuantity = $minimalQuantity;
        $this->localtion = $localtion;
        $this->lowStockThreshold = $lowStockThreshold;
        $this->lowStockAlertOn = $lowStockAlertOn;
        $this->availableDate = $availableDate;
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
    public function getLocaltion(): string
    {
        return $this->localtion;
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
    public function isLowStockAlertOn(): bool
    {
        return $this->lowStockAlertOn;
    }

    /**
     * @return DateTimeInterface
     */
    public function getAvailableDate(): DateTimeInterface
    {
        return $this->availableDate;
    }
}
