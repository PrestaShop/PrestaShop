<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\IssuePartialRefundCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\VoucherRefundType;

/**
 * Class PartialRefundFormDataHandler
 */
final class PartialRefundFormDataHandler implements FormDataHandlerInterface
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
            if (!empty($data['quantity_' . $orderDetailId]) || !empty((float) $data['amount_' . $orderDetailId])) {
                $refunds[$orderDetailId]['quantity'] = $data['quantity_' . $orderDetailId] ?? 0;
                $refunds[$orderDetailId]['amount'] = $data['amount_' . $orderDetailId] ?? 0;
            }
        }

        $command = new IssuePartialRefundCommand(
            $id,
            $refunds,
            $data['shipping_amount'],
            $data['restock'],
            $data['credit_slip'],
            $data['voucher'],
            $data['voucher_refund_type'] ?? VoucherRefundType::PRODUCT_PRICES_EXCLUDING_VOUCHER_REFUND
        );

        $this->commandBus->handle($command);
    }
}
