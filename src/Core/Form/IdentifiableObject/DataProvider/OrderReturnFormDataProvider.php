<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Query\GetOrderReturnForEditing;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\QueryResult\OrderReturnForEditing;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Provides data for order return edit form
 */
class OrderReturnFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $dateFormat;

    /**
     * @param CommandBusInterface $queryBus
     */
    public function __construct(
        CommandBusInterface $queryBus,
        RouterInterface $router,
        TranslatorInterface $translator,
        string $dateFormat
    ) {
        $this->queryBus = $queryBus;
        $this->router = $router;
        $this->translator = $translator;
        $this->dateFormat = $dateFormat;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($orderReturnId): array
    {
        /** @var OrderReturnForEditing $orderReturnForEditing */
        $orderReturnForEditing = $this->queryBus->handle(new GetOrderReturnForEditing($orderReturnId));

        return [
            'question' => $orderReturnForEditing->getQuestion(),
            'customer_name' => $orderReturnForEditing->getCustomerFullName(),
            'customer_link' => $this->router->generate('admin_customers_view', [
                'customerId' => $orderReturnForEditing->getCustomerId(),
            ]),
            'order' => $this->buildOrderReturnInformation($orderReturnForEditing),
            'order_link' => $this->router->generate('admin_orders_view', [
                'orderId' => $orderReturnForEditing->getOrderId(),
            ]),
            'order_return_state' => $orderReturnForEditing->getOrderReturnStateId(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData(): array
    {
        return [];
    }

    /**
     * @param OrderReturnForEditing $orderReturnForEditing
     *
     * @return string
     */
    private function buildOrderReturnInformation(OrderReturnForEditing $orderReturnForEditing): string
    {
        return $this->translator->trans(
            '#%order_id% from %order_date%',
            [
                '%order_id%' => $orderReturnForEditing->getOrderId(),
                '%order_date%' => $orderReturnForEditing->getOrderDate()->format($this->dateFormat),
            ],
            'Admin.Orderscustomers.Feature'
        );
    }
}
