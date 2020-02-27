<?php
/**
 * 2007-2020 Friends of PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\IssueReturnProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\VoucherRefundType;

class ReturnProductFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @param CommandBusInterface $commandBus
     */
    public function __construct(CommandBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data)
    {
        $refunds = [];
        foreach ($data['products'] as $product) {
            $orderDetailId = $product->getOrderDetailId();
            if (!isset($data['selected_' . $orderDetailId]) || !(bool) $data['selected_' . $orderDetailId]) {
                continue;
            }
            $refunds[$orderDetailId]['quantity'] = $data['quantity_' . $orderDetailId] ?? 0;
        }

        $command = new IssueReturnProductCommand(
            $id,
            $refunds,
            $data['restock'],
            $data['shipping'],
            $data['credit_slip'],
            $data['voucher'],
            $data['voucher_refund_type'] ?? VoucherRefundType::PRODUCT_PRICES_EXCLUDING_VOUCHER_REFUND
        );

        $this->commandBus->handle($command);
    }
}
