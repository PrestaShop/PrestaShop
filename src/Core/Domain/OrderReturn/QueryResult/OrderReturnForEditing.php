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

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturn\QueryResult;

use DateTimeImmutable;

class OrderReturnForEditing
{
    /**
     * @var int
     */
    private $orderReturnId;

    /**
     * @var int
     */
    private $customerId;

    /**
     * @var string
     */
    private $customerFirstName;

    /**
     * @var string
     */
    private $customerLastName;

    /**
     * @var int
     */
    private $orderId;

    /**
     * @var DateTimeImmutable
     */
    private $orderDate;

    /**
     * @var int
     */
    private $orderReturnStateId;

    /**
     * @var string
     */
    private $question;

    /**
     * OrderReturnForEditing constructor.
     *
     * @param int $orderReturnId
     * @param int $customerId
     * @param string $customerFirstName
     * @param string $customerLastName
     * @param int $orderId
     * @param DateTimeImmutable $orderDate
     * @param int $orderReturnStateId
     * @param string $question
     */
    public function __construct(
        int $orderReturnId,
        int $customerId,
        string $customerFirstName,
        string $customerLastName,
        int $orderId,
        DateTimeImmutable $orderDate,
        int $orderReturnStateId,
        string $question
    ) {
        $this->orderReturnId = $orderReturnId;
        $this->customerId = $customerId;
        $this->customerFirstName = $customerFirstName;
        $this->customerLastName = $customerLastName;
        $this->orderId = $orderId;
        $this->orderDate = $orderDate;
        $this->orderReturnStateId = $orderReturnStateId;
        $this->question = $question;
    }

    /**
     * @return int
     */
    public function getOrderReturnId(): int
    {
        return $this->orderReturnId;
    }

    /**
     * @return int
     */
    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    /**
     * @return int
     */
    public function getOrderId(): int
    {
        return $this->orderId;
    }

    /**
     * @return int
     */
    public function getOrderReturnStateId(): int
    {
        return $this->orderReturnStateId;
    }

    /**
     * @return string
     */
    public function getQuestion(): string
    {
        return $this->question;
    }

    /**
     * @return string
     */
    public function getCustomerFullName(): string
    {
        return sprintf('%s %s', $this->customerFirstName, $this->customerLastName);
    }

    /**
     * @return string
     */
    public function getCustomerFirstName(): string
    {
        return $this->customerFirstName;
    }

    /**
     * @return string
     */
    public function getCustomerLastName(): string
    {
        return $this->customerLastName;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getOrderDate(): DateTimeImmutable
    {
        return $this->orderDate;
    }
}
