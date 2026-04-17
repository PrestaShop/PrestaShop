<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\OrderState\CommandHandler;

use OrderState;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Command\AddOrderStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderState\CommandHandler\AddOrderStateHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Exception\OrderStateException;
use PrestaShop\PrestaShop\Core\Domain\OrderState\OrderStateFileUploaderInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderState\ValueObject\OrderStateId;

/**
 * Handles command that adds new order state
 *
 * @internal
 */
#[AsCommandHandler]
final class AddOrderStateHandler extends AbstractOrderStateHandler implements AddOrderStateHandlerInterface
{
    /**
     * @var OrderStateFileUploaderInterface
     */
    protected $fileUploader;

    /**
     * @param OrderStateFileUploaderInterface $fileUploader
     */
    public function __construct(OrderStateFileUploaderInterface $fileUploader)
    {
        $this->fileUploader = $fileUploader;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AddOrderStateCommand $command)
    {
        $orderState = new OrderState();

        $this->fillOrderStateWithCommandData($orderState, $command);
        $this->assertRequiredFieldsAreNotMissing($orderState);

        if (false === $orderState->validateFields(false)) {
            throw new OrderStateException('Order status contains invalid field values');
        }

        $orderState->add();
        if ($command->getFilePathName()) {
            $this->fileUploader->upload($command->getFilePathName(), (int) $orderState->id, $command->getFileSize());
        }

        return new OrderStateId((int) $orderState->id);
    }

    private function fillOrderStateWithCommandData(OrderState $orderState, AddOrderStateCommand $command)
    {
        $orderState->name = $command->getLocalizedNames();
        $orderState->color = $command->getColor();
        $orderState->logable = $command->isLoggable();
        $orderState->invoice = $command->isInvoice();
        $orderState->hidden = $command->isHidden();
        $orderState->send_email = $command->isSendEmailEnabled();
        $orderState->pdf_invoice = $command->isPdfInvoice();
        $orderState->pdf_delivery = $command->isPdfDelivery();
        $orderState->shipped = $command->isShipped();
        $orderState->paid = $command->isPaid();
        $orderState->delivery = $command->isDelivery();
        if ($command->isSendEmailEnabled()) {
            $orderState->template = $command->getLocalizedTemplates();
        }
    }
}
