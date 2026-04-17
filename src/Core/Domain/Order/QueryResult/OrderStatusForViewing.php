<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

use DateTimeImmutable;

class OrderStatusForViewing
{
    /**
     * @var int
     */
    private $orderStatusId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $color;

    /**
     * @var DateTimeImmutable
     */
    private $createdAt;

    /**
     * @var bool
     */
    private $withEmail;

    /**
     * @var string|null First name of employee who updated order status or null otherwise
     */
    private $employeeFirstName;

    /**
     * @var string|null Last name of employee who updated order status or null otherwise
     */
    private $employeeLastName;

    /**
     * @var int
     */
    private $orderHistoryId;

    private ?string $apiClientId;

    /**
     * @param int $orderHistoryId
     * @param int $orderStatusId
     * @param string $name
     * @param string $color
     * @param DateTimeImmutable $createdAt
     * @param bool $withEmail
     * @param string|null $employeeFirstName
     * @param string|null $employeeLastName
     */
    public function __construct(
        int $orderHistoryId,
        int $orderStatusId,
        string $name,
        string $color,
        DateTimeImmutable $createdAt,
        bool $withEmail,
        ?string $employeeFirstName,
        ?string $employeeLastName,
        ?string $apiClientId,
    ) {
        $this->orderStatusId = $orderStatusId;
        $this->name = $name;
        $this->color = $color;
        $this->createdAt = $createdAt;
        $this->withEmail = $withEmail;
        $this->employeeFirstName = $employeeFirstName;
        $this->employeeLastName = $employeeLastName;
        $this->orderHistoryId = $orderHistoryId;
        $this->apiClientId = $apiClientId;
    }

    /**
     * @return int
     */
    public function getOrderHistoryId(): int
    {
        return $this->orderHistoryId;
    }

    /**
     * @return int
     */
    public function getOrderStatusId(): int
    {
        return $this->orderStatusId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return bool
     */
    public function withEmail(): bool
    {
        return $this->withEmail;
    }

    /**
     * @return string|null
     */
    public function getEmployeeFirstName(): ?string
    {
        return $this->employeeFirstName;
    }

    /**
     * @return string|null
     */
    public function getEmployeeLastName(): ?string
    {
        return $this->employeeLastName;
    }

    public function getApiClientId(): ?string
    {
        return $this->apiClientId;
    }
}
