<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\IssueStandardRefundCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\VoucherRefundType;

/**
 * Class StandardRefundFormDataHandler
 */
final class StandardRefundFormDataHandler implements FormDataHandlerInterface
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

        $command = new IssueStandardRefundCommand(
            $id,
            $refunds,
            $data['shipping'],
            $data['credit_slip'],
            $data['voucher'],
            $data['voucher_refund_type'] ?? VoucherRefundType::PRODUCT_PRICES_EXCLUDING_VOUCHER_REFUND
        );

        $this->commandBus->handle($command);
    }
}
