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

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

class OrderMessageForViewing
{
    /**
     * @var int
     */
    private $messageId;

    /**
     * @var string
     */
    private $message;

    /**
     * @var OrderMessageDateForViewing
     */
    private $messageDate;

    /**
     * @var string
     */
    private $employeeFirstName;

    /**
     * @var string
     */
    private $employeeLastName;

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
    private $employeeId;
    /**
     * @var bool
     */
    private $isPrivate;
    /**
     * @var bool
     */
    private $isCurrentEmployeesMessage;

    /**
     * @param int $messageId
     * @param string $message
     * @param OrderMessageDateForViewing $messageDate
     * @param int $employeeId
     * @param bool $isCurrentEmployeesMessage
     * @param string $employeeFirstName
     * @param string $employeeLastName
     * @param string $customerFirstName
     * @param string $customerLastName
     * @param bool $isPrivate
     */
    public function __construct(
        int $messageId,
        string $message,
        OrderMessageDateForViewing $messageDate,
        int $employeeId,
        bool $isCurrentEmployeesMessage,
        ?string $employeeFirstName,
        ?string $employeeLastName,
        string $customerFirstName,
        string $customerLastName,
        bool $isPrivate
    ) {
        $this->messageId = $messageId;
        $this->message = $message;
        $this->messageDate = $messageDate;
        $this->employeeFirstName = $employeeFirstName;
        $this->employeeLastName = $employeeLastName;
        $this->customerFirstName = $customerFirstName;
        $this->customerLastName = $customerLastName;
        $this->employeeId = $employeeId;
        $this->isPrivate = $isPrivate;
        $this->isCurrentEmployeesMessage = $isCurrentEmployeesMessage;
    }

    /**
     * @return int
     */
    public function getMessageId(): int
    {
        return $this->messageId;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return OrderMessageDateForViewing
     */
    public function getMessageDate(): OrderMessageDateForViewing
    {
        return $this->messageDate;
    }

    /**
     * @return string
     */
    public function getEmployeeFirstName(): ?string
    {
        return $this->employeeFirstName;
    }

    /**
     * @return string
     */
    public function getEmployeeLastName(): ?string
    {
        return $this->employeeLastName;
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
     * @return int
     */
    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }

    /**
     * @return bool
     */
    public function isCurrentEmployeesMessage(): bool
    {
        return $this->isCurrentEmployeesMessage;
    }

    /**
     * @return bool
     */
    public function isPrivate(): bool
    {
        return $this->isPrivate;
    }
}
