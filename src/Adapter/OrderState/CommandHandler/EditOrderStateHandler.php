<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\OrderState\CommandHandler;

use OrderState;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Command\EditOrderStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderState\CommandHandler\EditOrderStateHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Exception\MissingOrderStateRequiredFieldsException;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Exception\OrderStateException;
use PrestaShop\PrestaShop\Core\Domain\OrderState\OrderStateFileUploaderInterface;

/**
 * Handles commands which edits given order state with provided data.
 *
 * @internal
 */
#[AsCommandHandler]
final class EditOrderStateHandler extends AbstractOrderStateHandler implements EditOrderStateHandlerInterface
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
    public function handle(EditOrderStateCommand $command)
    {
        $orderStateId = $command->getOrderStateId();
        $orderState = new OrderState($orderStateId->getValue());

        $this->assertOrderStateWasFound($orderStateId, $orderState);

        $this->updateOrderStateWithCommandData($orderState, $command);

        $this->assertRequiredFieldsAreNotMissing($orderState);

        if (false === $orderState->validateFields(false)) {
            throw new OrderStateException('OrderState contains invalid field values');
        }

        if (false === $orderState->update()) {
            throw new OrderStateException('Failed to update order state');
        }

        if ($command->getFilePathName()) {
            $this->fileUploader->upload($command->getFilePathName(), $orderStateId->getValue(), $command->getFileSize());
        }
    }

    /**
     * @throws MissingOrderStateRequiredFieldsException
     */
    protected function assertRequiredFieldsAreNotMissing(OrderState $orderState)
    {
        // Check that we have templates for all languages when send_email is on
        $haveMissingTemplates = (
            !is_array($orderState->template)
            || count($orderState->template) != count(array_filter($orderState->template, function ($v) {
                return (bool) strlen($v);
            }))
        );

        if (true === $orderState->send_email && true === $haveMissingTemplates) {
            throw new MissingOrderStateRequiredFieldsException(['template'], 'One or more required fields for order state are missing. Missing fields are: template');
        }

        parent::assertRequiredFieldsAreNotMissing($orderState);
    }

    private function updateOrderStateWithCommandData(OrderState $orderState, EditOrderStateCommand $command)
    {
        if (null !== $command->getName()) {
            $orderState->name = $command->getName();
        }

        if (null !== $command->getColor()) {
            $orderState->color = $command->getColor();
        }

        if (null !== $command->isLoggable()) {
            $orderState->logable = $command->isLoggable();
        }

        if (null !== $command->isHidden()) {
            $orderState->hidden = $command->isHidden();
        }

        if (null !== $command->isInvoice()) {
            $orderState->invoice = $command->isInvoice();
        }

        if (null !== $command->isSendEmailEnabled()) {
            $orderState->send_email = $command->isSendEmailEnabled();

            if ($orderState->send_email && null !== $command->getTemplate()) {
                $orderState->template = $command->getTemplate();
            }
        }

        if (null !== $command->isPdfInvoice()) {
            $orderState->pdf_invoice = $command->isPdfInvoice();
        }

        if (null !== $command->isPdfDelivery()) {
            $orderState->pdf_delivery = $command->isPdfDelivery();
        }

        if (null !== $command->isShipped()) {
            $orderState->shipped = $command->isShipped();
        }

        if (null !== $command->isPaid()) {
            $orderState->paid = $command->isPaid();
        }

        if (null !== $command->isDelivery()) {
            $orderState->delivery = $command->isDelivery();
        }
    }
}
