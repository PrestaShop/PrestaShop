<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Command\AddOrderStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Command\EditOrderStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderState\ValueObject\OrderStateId;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Saves or updates order state data submitted in form
 */
final class OrderStateFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $bus;

    public function __construct(
        CommandBusInterface $bus
    ) {
        $this->bus = $bus;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        $command = $this->buildOrderStateAddCommandFromFormData($data);

        /** @var OrderStateId $orderStateId */
        $orderStateId = $this->bus->handle($command);

        return $orderStateId->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function update($orderStateId, array $data)
    {
        $command = $this->buildOrderStateEditCommand($orderStateId, $data);

        $this->bus->handle($command);
    }

    /**
     * @return AddOrderStateCommand
     */
    private function buildOrderStateAddCommandFromFormData(array $data)
    {
        $command = new AddOrderStateCommand(
            $data['name'],
            $data['color'],
            $data['loggable'],
            $data['invoice'],
            $data['hidden'],
            $data['send_email'],
            $data['pdf_invoice'],
            $data['pdf_delivery'],
            $data['shipped'],
            $data['paid'],
            $data['delivery'],
            $data['template']
        );

        if (isset($data['icon'])) {
            /** @var UploadedFile $fileObject */
            $fileObject = $data['icon'];

            $command->setFileInformation(
                $fileObject->getPathname(),
                $fileObject->getSize(),
                $fileObject->getMimeType(),
                $fileObject->getClientOriginalName()
            );
        }

        return $command;
    }

    /**
     * @param int $orderStateId
     *
     * @return EditOrderStateCommand
     */
    private function buildOrderStateEditCommand($orderStateId, array $data)
    {
        $command = (new EditOrderStateCommand($orderStateId))
            ->setName($data['name'])
            ->setColor($data['color'])
            ->setLoggable($data['loggable'])
            ->setInvoice($data['invoice'])
            ->setHidden($data['hidden'])
            ->setSendEmail($data['send_email'])
            ->setPdfInvoice($data['pdf_invoice'])
            ->setPdfDelivery($data['pdf_delivery'])
            ->setShipped($data['shipped'])
            ->setPaid($data['paid'])
            ->setDelivery($data['delivery'])
            ->setTemplate($data['template'])
        ;

        /** @var UploadedFile|null $fileObject */
        $fileObject = $data['icon'];

        if ($fileObject instanceof UploadedFile) {
            $command->setFileInformation(
                $fileObject->getPathname(),
                $fileObject->getSize(),
                $fileObject->getMimeType(),
                $fileObject->getClientOriginalName()
            );
        }

        return $command;
    }
}
