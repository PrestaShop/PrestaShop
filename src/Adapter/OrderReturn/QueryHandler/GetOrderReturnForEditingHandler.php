<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
