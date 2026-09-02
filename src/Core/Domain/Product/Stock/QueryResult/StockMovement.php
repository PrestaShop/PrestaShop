<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Stock\QueryResult;

use DateTimeImmutable;
use RuntimeException;

class StockMovement
{
    public const EDITION_TYPE = 'edition';
    public const ORDERS_TYPE = 'orders';

    /**
     * @var string
     */
    protected $type;

    /**
     * @var array<string, DateTimeImmutable>
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
    protected $employeeName = null;

    /**
     * @var int[]
     */
    protected $apiClientIds = [];

    /**
     * @var string[]
     */
    protected $apiClientNames = [];

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
     * @param string|null $employeeName
     * @param int $deltaQuantity
     * @param int[] $apiClientIds
     * @param string[] $apiClientNames
     */
    protected function __construct(
        string $type,
        array $dates,
        array $stockMovementIds,
        array $stockIds,
        array $orderIds,
        array $employeeIds,
        ?string $employeeName,
        int $deltaQuantity,
        array $apiClientIds = [],
        array $apiClientNames = []
    ) {
        $this->type = $type;
        $this->dates = $this->initializeDates($dates);
        $this->stockMovementIds = $this->initializeIds($stockMovementIds);
        $this->stockIds = $this->initializeIds($stockIds);
        $this->orderIds = $this->initializeIds($orderIds);
        $this->employeeIds = $this->initializeIds($employeeIds);
        $this->employeeName = $employeeName;
        $this->deltaQuantity = $deltaQuantity;
        $this->apiClientIds = $this->initializeIds($apiClientIds);
        $this->apiClientNames = array_values(array_filter($apiClientNames));
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

    /**
     * @param int[] $apiClientIds
     * @param string[] $apiClientNames
     */
    public static function createEditionMovement(
        string $dateAdd,
        int $stockMovementId,
        int $stockId,
        ?int $orderId,
        int $employeeId,
        ?string $employeeName,
        int $deltaQuantity,
        array $apiClientIds = [],
        array $apiClientNames = []
    ): self {
        return new static(
            static::EDITION_TYPE,
            [
                'add' => $dateAdd,
            ],
            [$stockMovementId],
            [$stockId],
            $orderId !== null ? [$orderId] : [],
            [$employeeId],
            $employeeName,
            $deltaQuantity,
            $apiClientIds,
            $apiClientNames
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
     * @param int[] $apiClientIds
     * @param string[] $apiClientNames
     */
    public static function createOrdersMovement(
        string $fromDate,
        string $toDate,
        array $stockMovementIds,
        array $stockIds,
        array $orderIds,
        array $employeeIds,
        int $deltaQuantity,
        array $apiClientIds = [],
        array $apiClientNames = []
    ): self {
        return new static(
            static::ORDERS_TYPE,
            [
                'from' => $fromDate,
                'to' => $toDate,
            ],
            $stockMovementIds,
            $stockIds,
            $orderIds,
            $employeeIds,
            null,
            $deltaQuantity,
            $apiClientIds,
            $apiClientNames
        );
    }

    /**
     * Returns history type : "edition" or "orders"
     */
    public function getType(): string
    {
        return $this->type;
    }

    public function isEdition(): bool
    {
        return static::EDITION_TYPE === $this->getType();
    }

    public function isFromOrders(): bool
    {
        return static::ORDERS_TYPE === $this->getType();
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

    public function getEmployeeName(): ?string
    {
        return $this->employeeName;
    }

    /**
     * @return int[]
     */
    public function getApiClientIds(): array
    {
        return $this->apiClientIds;
    }

    /**
     * @return string[]
     */
    public function getApiClientNames(): array
    {
        return $this->apiClientNames;
    }

    public function getDeltaQuantity(): int
    {
        return $this->deltaQuantity;
    }

    /**
     * @return array<string, DateTimeImmutable>
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
