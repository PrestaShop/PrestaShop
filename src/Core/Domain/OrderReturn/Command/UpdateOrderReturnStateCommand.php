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

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturn\Command;

use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnConstraintException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\ValueObject\OrderReturnId;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\ValueObject\OrderReturnStateId;

/**
 * Edits provided order return.
 * It can edit either all or partial data.
 */
class UpdateOrderReturnStateCommand
{
    /**
     * @var OrderReturnId
     */
    private $orderReturnId;

    /**
     * @var OrderReturnStateId|null
     */
    private $orderReturnStateId;

    /**
     * @param int $orderReturnId
     *
     * @throws OrderReturnConstraintException
     */
    public function __construct(int $orderReturnId)
    {
        $this->orderReturnId = new OrderReturnId($orderReturnId);
    }

    /**
     * @return OrderReturnId
     */
    public function getOrderReturnId(): OrderReturnId
    {
        return $this->orderReturnId;
    }

    /**
     * @return OrderReturnStateId
     */
    public function getOrderReturnStateId(): OrderReturnStateId
    {
        return $this->orderReturnStateId;
    }

    /**
     * @param OrderReturnStateId $orderReturnStateId
     *
     * @return UpdateOrderReturnStateCommand
     */
    public function setOrderReturnStateId(OrderReturnStateId $orderReturnStateId): UpdateOrderReturnStateCommand
    {
        $this->orderReturnStateId = $orderReturnStateId;

        return $this;
    }
}
