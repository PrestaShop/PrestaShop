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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Query\GetOrderReturnForEditing;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\QueryResult\OrderReturnForEditing;
use PrestaShopBundle\Service\Routing\Router;
use Symfony\Component\Translation\TranslatorInterface;

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
     * @var Router
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
        Router $router,
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

    private function buildOrderReturnInformation(OrderReturnForEditing $orderReturnForEditing)
    {
        return sprintf(
            '#%s %s %s',
            $orderReturnForEditing->getOrderId(),
            $this->translator->trans('from', [], 'Admin.Global'),
            $orderReturnForEditing->getOrderDate()->format($this->dateFormat)
        );
    }
}
