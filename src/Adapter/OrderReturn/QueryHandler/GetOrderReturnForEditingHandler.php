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

namespace PrestaShop\PrestaShop\Adapter\OrderReturn\QueryHandler;

use DateTimeImmutable;
use PrestaShop\PrestaShop\Adapter\Customer\Repository\CustomerRepository;
use PrestaShop\PrestaShop\Adapter\Order\Repository\OrderRepository;
use PrestaShop\PrestaShop\Adapter\OrderReturn\Repository\OrderReturnRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Query\GetOrderReturnForEditing;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\QueryHandler\GetOrderReturnForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\QueryResult\OrderReturnForEditing;

/**
 * Handles query which gets order return for editing
 */
#[AsQueryHandler]
class GetOrderReturnForEditingHandler implements GetOrderReturnForEditingHandlerInterface
{
    /**
     * @var OrderReturnRepository
     */
    private $orderReturnRepository;

    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * GetOrderReturnForEditingHandler constructor.
     *
     * @param OrderReturnRepository $orderReturnRepository
     * @param CustomerRepository $customerRepository
     * @param OrderRepository $orderRepository
     */
    public function __construct(
        OrderReturnRepository $orderReturnRepository,
        CustomerRepository $customerRepository,
        OrderRepository $orderRepository
    ) {
        $this->orderReturnRepository = $orderReturnRepository;
        $this->customerRepository = $customerRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetOrderReturnForEditing $query): OrderReturnForEditing
    {
        $orderReturn = $this->orderReturnRepository->get($query->getOrderReturnId());
        $customer = $this->customerRepository->get(new CustomerId((int) $orderReturn->id_customer));
        $order = $this->orderRepository->get(new OrderId((int) $orderReturn->id_order));

        return new OrderReturnForEditing(
            $query->getOrderReturnId()->getValue(),
            (int) $orderReturn->id_customer,
            $customer->firstname,
            $customer->lastname,
            (int) $orderReturn->id_order,
            new DateTimeImmutable($order->date_add),
            (int) $orderReturn->state,
            $orderReturn->question
        );
    }
}
