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

namespace PrestaShop\PrestaShop\Adapter\OrderReturn\QueryHandler;

use Customer;
use DateTime;
use Order;
use PrestaShop\PrestaShop\Adapter\OrderReturn\AbstractOrderReturnHandler;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Query\GetOrderReturnForEditing;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\QueryHandler\GetOrderReturnForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\QueryResult\EditableOrderReturn;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\ValueObject\OrderReturnStateId;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

/**
 * Handles query which gets merchandise return for editing
 */
final class GetOrderReturnForEditingHandler extends AbstractOrderReturnHandler implements GetOrderReturnForEditingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetOrderReturnForEditing $query): EditableOrderReturn
    {
        $orderReturnId = $query->getOrderReturnId();
        $orderReturn = $this->getOrderReturn($orderReturnId);
        $customer = new Customer($orderReturn->id_customer);
        $order = new Order($orderReturn->id_order);

        return new EditableOrderReturn(
            $orderReturnId->getValue(),
            (int) $orderReturn->id_customer,
            $customer->firstname,
            $customer->lastname,
            (int) $orderReturn->id_order,
            new DateTime($order->date_add),
            (int) $orderReturn->state,
            $orderReturn->question
        );
    }
}
