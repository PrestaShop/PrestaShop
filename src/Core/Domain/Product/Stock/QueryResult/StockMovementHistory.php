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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Stock\QueryResult;

use DateTimeImmutable;
use RuntimeException;

class StockMovementHistory
{
    public const SINGLE_TYPE = 'single';
    public const RANGE_TYPE = 'range';

    /**
     * @var string
     */
    protected $type;

    /**
     * @var DateTimeImmutable[]
     */
    protected $dates;

    /**
     * @var int[]
     */
    protected $stockMovementIds;

    /**
     * @var int[]
     */
    protected $stockIds;

    /**
     * @var int[]
     */
    protected $orderIds;

    /**
     * @var int[]
     */
    protected $employeeIds;

    /**
     * @var string|null
     */
    protected $employeeFirstname = null;

    /**
     * @var string|null
     */
    protected $employeeLastname = null;

    /**
     * @var int
     */
    protected $deltaQuantity;

    /**
     * @param string $type
     * @param string[] $dates
     * @param int[] $stockMovementIds
     * @param int[] $stockIds
     * @param int[] $orderIds
     * @param int[] $employeeIds
     * @param string|null $employeeFirstName
     * @param string|null $employeeLastName
     * @param int $deltaQuantity
     */
    protected function __construct(
        string $type,
        array $dates,
        array $stockMovementIds,
        array $stockIds,
        array $orderIds,
        array $employeeIds,
        ?string $employeeFirstName,
        ?string $employeeLastName,
        int $deltaQuantity
    ) {
        $this->type = $type;
        $this->dates = $this->initializeDates($dates);
        $this->stockMovementIds = $this->initializeIds($stockMovementIds);
        $this->stockIds = $this->initializeIds($stockIds);
        $this->orderIds = $this->initializeIds($orderIds);
        $this->employeeIds = $this->initializeIds($employeeIds);
        $this->stockMovementIds = $this->initializeIds($stockMovementIds);
        $this->employeeFirstname = $employeeFirstName;
        $this->employeeLastname = $employeeLastName;
        $this->deltaQuantity = $deltaQuantity;
    }

    /**
     * @return int[]
     */
    protected function initializeIds(array $ids): array
    {
        // Falsy values should get removed from array
        return array_filter(
            array_map(
                static function ($id): int {
                    return (int) $id;
                },
                $ids
            )
        );
    }

    /**
     * @param string[] $dates
     *
     * @return DateTimeImmutable[]
     */
    protected function initializeDates(array $dates): array
    {
        return array_map(
            static function (string $date): DateTimeImmutable {
                return new DateTimeImmutable($date);
            },
            $dates
        );
    }

    public static function createSingleHistory(
        string $dateAdd,
        int $stockMovementId,
        int $stockId,
        ?int $orderId,
        int $employeeId,
        ?string $employeeFirstName,
        ?string $employeeLastName,
        int $deltaQuantity
    ): self {
        return new static(
            static::SINGLE_TYPE,
            [
                'add' => $dateAdd,
            ],
            [$stockMovementId],
            [$stockId],
            $orderId !== null ? [$orderId] : [],
            [$employeeId],
            $employeeFirstName,
            $employeeLastName,
            $deltaQuantity
        );
    }

    /**
     * @param string $fromDate
     * @param string $toDate
     * @param string[]|int[] $stockMovementIds
     * @param string[]|int[] $stockIds
     * @param string[]|int[] $orderIds
     * @param string[]|int[] $employeeIds
     * @param int $deltaQuantity
     */
    public static function createRangeHistory(
        string $fromDate,
        string $toDate,
        array $stockMovementIds,
        array $stockIds,
        array $orderIds,
        array $employeeIds,
        int $deltaQuantity
    ): self {
        return new static(
            static::RANGE_TYPE,
            [
                'from' => $fromDate,
                'to' => $toDate,
            ],
            $stockMovementIds,
            $stockIds,
            $orderIds,
            $employeeIds,
            null,
            null,
            $deltaQuantity
        );
    }

    /**
     * Returns history type : "single" or "range"
     */
    public function getType(): string
    {
        return $this->type;
    }

    public function isSingle(): bool
    {
        return static::SINGLE_TYPE === $this->getType();
    }

    public function isRange(): bool
    {
        return static::RANGE_TYPE === $this->getType();
    }

    /**
     * @return int[]
     */
    public function getStockMovementIds(): array
    {
        return $this->stockMovementIds;
    }

    /**
     * @return int[]
     */
    public function getStockIds(): array
    {
        return $this->stockIds;
    }

    /**
     * @return int[]
     */
    public function getOrderIds(): array
    {
        return $this->orderIds;
    }

    /**
     * @return int[]
     */
    public function getEmployeeIds(): array
    {
        return $this->employeeIds;
    }

    public function getEmployeeFirstname(): ?string
    {
        return $this->employeeFirstname;
    }

    public function getEmployeeLastname(): ?string
    {
        return $this->employeeLastname;
    }

    public function getDeltaQuantity(): int
    {
        return $this->deltaQuantity;
    }

    /**
     * @return DateTimeImmutable[]
     */
    public function getDates(): array
    {
        return $this->dates;
    }

    public function getDate(string $key): DateTimeImmutable
    {
        $dates = $this->getDates();

        if (!array_key_exists($key, $dates)) {
            throw new RuntimeException(
                sprintf(
                    'Invalid date key "%s" provided, available keys: %s',
                    $key,
                    implode(', ', array_keys($dates))
                )
            );
        }

        return $dates[$key];
    }
}
