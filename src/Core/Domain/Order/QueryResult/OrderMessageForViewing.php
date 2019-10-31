<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

use DateTimeImmutable;

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
     * @var DateTimeImmutable
     */
    private $date;

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
     * @param int $messageId
     * @param string $message
     * @param DateTimeImmutable $date
     * @param int $employeeId
     * @param string $employeeFirstName
     * @param string $employeeLastName
     * @param string $customerFirstName
     * @param string $customerLastName
     */
    public function __construct(
        int $messageId,
        string $message,
        DateTimeImmutable $date,
        int $employeeId,
        ?string $employeeFirstName,
        ?string $employeeLastName,
        string $customerFirstName,
        string $customerLastName
    ) {
        $this->messageId = $messageId;
        $this->message = $message;
        $this->date = $date;
        $this->employeeFirstName = $employeeFirstName;
        $this->employeeLastName = $employeeLastName;
        $this->customerFirstName = $customerFirstName;
        $this->customerLastName = $customerLastName;
        $this->employeeId = $employeeId;
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
     * @return DateTimeImmutable
     */
    public function getDate(): DateTimeImmutable
    {
        return $this->date;
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
}
