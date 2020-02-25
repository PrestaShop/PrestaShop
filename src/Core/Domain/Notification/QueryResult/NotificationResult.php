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

namespace PrestaShop\PrestaShop\Core\Domain\Notification\QueryResult;

/**
 * NotificationResult contains the notification data
 */
class NotificationResult
{
    /**
     * @var int
     */
    private $orderId;

    /**
     * @var int
     */
    private $customerId;

    /**
     * @var string
     */
    private $customerName;

    /**
     * @var int
     */
    private $customerMessageId;

    /**
     * @var int
     */
    private $customerThreadId;

    /**
     * @var string
     */
    private $customerViewUrl;

    /**
     * @var string
     */
    private $totalPaid;

    /**
     * @var string
     */
    private $carrier;

    /**
     * @var string
     */
    private $isoCode;

    /**
     * @var string
     */
    private $company;

    /**
     * @var string
     */
    private $status;

    /**
     * @var string
     */
    private $dateAdd;

    /**
     * NotificationResult constructor.
     *
     * @param int $orderId
     * @param int $customerId
     * @param string $customerName
     * @param int $customerMessageId
     * @param int $customerThreadId
     * @param string $customerViewUrl
     * @param string $totalPaid
     * @param string $carrier
     * @param string $isoCode
     * @param string $company
     * @param string $status
     * @param string $dateAdd
     */
    public function __construct(
        int $orderId,
        int $customerId,
        string $customerName,
        int $customerMessageId,
        int $customerThreadId,
        string $customerViewUrl,
        string $totalPaid,
        string $carrier,
        string $isoCode,
        string $company,
        string $status,
        string $dateAdd
    ) {
        $this->orderId = $orderId;
        $this->customerId = $customerId;
        $this->customerName = $customerName;
        $this->customerMessageId = $customerMessageId;
        $this->customerThreadId = $customerThreadId;
        $this->customerViewUrl = $customerViewUrl;
        $this->totalPaid = $totalPaid;
        $this->carrier = $carrier;
        $this->isoCode = $isoCode;
        $this->company = $company;
        $this->status = $status;
        $this->dateAdd = $dateAdd;
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
    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    /**
     * @return string
     */
    public function getCustomerName(): string
    {
        return $this->customerName;
    }

    /**
     * @return int
     */
    public function getCustomerMessageId(): int
    {
        return $this->customerMessageId;
    }

    /**
     * @return int
     */
    public function getCustomerThreadId(): int
    {
        return $this->customerThreadId;
    }

    /**
     * @return string
     */
    public function getCustomerViewUrl(): string
    {
        return $this->customerViewUrl;
    }

    /**
     * @return string
     */
    public function getTotalPaid(): string
    {
        return $this->totalPaid;
    }

    /**
     * @return string
     */
    public function getCarrier(): string
    {
        return $this->carrier;
    }

    /**
     * @return string
     */
    public function getIsoCode(): string
    {
        return $this->isoCode;
    }

    /**
     * @return string
     */
    public function getCompany(): string
    {
        return $this->company;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getDateAdd(): string
    {
        return $this->dateAdd;
    }
}
