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

namespace PrestaShop\PrestaShop\Core\Domain\CustomerMessage\Command;

use PrestaShop\PrestaShop\Core\Domain\CustomerMessage\Exception\CustomerMessageConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

/**
 * This command adds/sends message to the customer related with the order.
 */
class AddOrderCustomerMessageCommand
{
    /**
     * @var string
     */
    private $message;

    /**
     * @var OrderId
     */
    private $orderId;
    /**
     * @var bool
     */
    private $isPrivate;

    /**
     * @param int $orderId
     * @param string $message
     * @param bool $isPrivate
     *
     * @throws OrderException
     * @throws CustomerMessageConstraintException
     */
    public function __construct(int $orderId, string $message, bool $isPrivate)
    {
        $this->orderId = new OrderId($orderId);
        $this->setMessage($message);
        $this->isPrivate = $isPrivate;
    }

    /**
     * @return OrderId
     */
    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return bool
     */
    public function isPrivate(): bool
    {
        return $this->isPrivate;
    }

    /**
     * @param string $message
     *
     * @throws CustomerMessageConstraintException
     */
    private function setMessage(string $message): void
    {
        if (!$message) {
            throw new CustomerMessageConstraintException('Missing required message', CustomerMessageConstraintException::MISSING_MESSAGE);
        }

        $this->message = $message;
    }
}
