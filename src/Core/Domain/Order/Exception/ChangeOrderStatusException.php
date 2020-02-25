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

namespace PrestaShop\PrestaShop\Core\Domain\Order\Exception;

use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

/**
 * Throw when failed changing order status
 */
class ChangeOrderStatusException extends OrderException
{
    /**
     * @var OrderId[]
     */
    private $ordersWithFailedToUpdateStatus;

    /**
     * @var OrderId[]
     */
    private $ordersWithFailedToSendEmail;

    /**
     * @var OrderId[]
     */
    private $ordersWithAssignedStatus;

    /**
     * @param OrderId[] $ordersWithFailedToUpdateStatus
     * @param OrderId[] $ordersWithFailedToSendEmail
     * @param OrderId[] $ordersWithAssignedStatus
     * @param string $message
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct(
        array $ordersWithFailedToUpdateStatus,
        array $ordersWithFailedToSendEmail,
        array $ordersWithAssignedStatus,
        $message = '',
        $code = 0,
        $previous = null
    ) {
        $this->ordersWithFailedToUpdateStatus = $ordersWithFailedToUpdateStatus;
        $this->ordersWithFailedToSendEmail = $ordersWithFailedToSendEmail;
        $this->ordersWithAssignedStatus = $ordersWithAssignedStatus;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return OrderId[]
     */
    public function getOrdersWithFailedToUpdateStatus()
    {
        return $this->ordersWithFailedToUpdateStatus;
    }

    /**
     * @return OrderId[]
     */
    public function getOrdersWithFailedToSendEmail()
    {
        return $this->ordersWithFailedToSendEmail;
    }

    /**
     * @return OrderId[]
     */
    public function getOrdersWithAssignedStatus()
    {
        return $this->ordersWithAssignedStatus;
    }
}
