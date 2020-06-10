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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\QueryResult;

use DateTime;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\ValueObject\MerchandiseReturnId;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\ValueObject\MerchandiseReturnStateId;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

class EditableMerchandiseReturn
{
    /**
     * @var MerchandiseReturnId
     */
    private $merchandiseReturnId;

    /**
     * @var CustomerId
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
     * @var OrderId
     */
    private $orderId;

    /**
     * @var DateTime
     */
    private $orderDate;

    /**
     * @var MerchandiseReturnStateId
     */
    private $merchandiseReturnStateId;

    /**
     * @var string
     */
    private $question;

    /**
     * EditableMerchandiseReturn constructor.
     * @param MerchandiseReturnId $merchandiseReturnId
     * @param CustomerId $customerId
     * @param string $customerFirstName
     * @param string $customerLastName
     * @param OrderId $orderId
     * @param DateTime $orderDate
     * @param MerchandiseReturnStateId $merchandiseReturnStateId
     * @param string $question
     */
    public function __construct(
        MerchandiseReturnId $merchandiseReturnId,
        CustomerId $customerId,
        string $customerFirstName,
        string $customerLastName,
        OrderId $orderId,
        DateTime $orderDate,
        MerchandiseReturnStateId $merchandiseReturnStateId,
        string $question
    ) {
        $this->merchandiseReturnId = $merchandiseReturnId;
        $this->customerId = $customerId;
        $this->customerFirstName = $customerFirstName;
        $this->customerLastName = $customerLastName;
        $this->orderId = $orderId;
        $this->orderDate = $orderDate;
        $this->merchandiseReturnStateId = $merchandiseReturnStateId;
        $this->question = $question;
    }

    /**
     * @return MerchandiseReturnId
     */
    public function getMerchandiseReturnId(): MerchandiseReturnId
    {
        return $this->merchandiseReturnId;
    }

    /**
     * @return CustomerId
     */
    public function getCustomerId(): CustomerId
    {
        return $this->customerId;
    }

    /**
     * @return OrderId
     */
    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }

    /**
     * @return MerchandiseReturnStateId
     */
    public function getMerchandiseReturnStateId(): MerchandiseReturnStateId
    {
        return $this->merchandiseReturnStateId;
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
     * @return DateTime
     */
    public function getOrderDate(): DateTime
    {
        return $this->orderDate;
    }
}
