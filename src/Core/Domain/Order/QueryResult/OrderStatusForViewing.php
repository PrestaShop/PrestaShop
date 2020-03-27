<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
        ?string $employeeLastName
    ) {
        $this->orderStatusId = $orderStatusId;
        $this->name = $name;
        $this->color = $color;
        $this->createdAt = $createdAt;
        $this->withEmail = $withEmail;
        $this->employeeFirstName = $employeeFirstName;
        $this->employeeLastName = $employeeLastName;
        $this->orderHistoryId = $orderHistoryId;
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
}
