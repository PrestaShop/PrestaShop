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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturn\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderDetailId;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnConstraintException;

/**
 * Provides order return id
 */
class OrderReturnDetailId
{
    /**
     * @var OrderReturnId
     */
    private $orderReturnId;

    /**
     * @var OrderDetailId
     */
    private $orderDetailId;

    /**
     * @param int $orderReturnId
     * @param int $orderDetailId
     *
     * @throws OrderReturnConstraintException
     */
    public function __construct(int $orderReturnId, int $orderDetailId)
    {
        $this->assertIsIntegerGreaterThanZero($orderReturnId);
        $this->assertIsIntegerGreaterThanZero($orderDetailId);

        $this->orderReturnId = new OrderReturnId($orderReturnId);
        $this->orderDetailId = new OrderDetailId($orderDetailId);
    }

    /**
     * Validates that the value is integer and is greater than zero
     *
     * @param int $value
     *
     * @throws OrderReturnConstraintException
     */
    private function assertIsIntegerGreaterThanZero(int $value): void
    {
        if (0 >= $value) {
            throw new OrderReturnConstraintException(
                sprintf('Invalid id "%s".', $value),
                OrderReturnConstraintException::INVALID_ID
            );
        }
    }

    /**
     * @return OrderReturnId
     */
    public function getOrderReturnId(): OrderReturnId
    {
        return $this->orderReturnId;
    }

    /**
     * @return OrderDetailId
     */
    public function getOrderDetailId(): OrderDetailId
    {
        return $this->orderDetailId;
    }
}
