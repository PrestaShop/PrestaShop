<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Query\GetOrderStateForEditing;
use PrestaShop\PrestaShop\Core\Domain\OrderState\QueryResult\EditableOrderState;

/**
 * Provides data for order state forms
 */
final class OrderStateFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    public function __construct(
        CommandBusInterface $queryBus
    ) {
        $this->queryBus = $queryBus;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($orderStateId)
    {
        /** @var EditableOrderState $editableOrderState */
        $editableOrderState = $this->queryBus->handle(new GetOrderStateForEditing((int) $orderStateId));

        return [
            'name' => $editableOrderState->getLocalizedNames(),
            'color' => $editableOrderState->getColor(),
            'loggable' => $editableOrderState->isLoggable(),
            'invoice' => $editableOrderState->isInvoice(),
            'hidden' => $editableOrderState->isHidden(),
            'send_email' => $editableOrderState->isSendEmailEnabled(),
            'pdf_invoice' => $editableOrderState->isPdfInvoice(),
            'pdf_delivery' => $editableOrderState->isPdfDelivery(),
            'shipped' => $editableOrderState->isShipped(),
            'paid' => $editableOrderState->isPaid(),
            'delivery' => $editableOrderState->isDelivery(),
            'template' => $editableOrderState->getLocalizedTemplates(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        return [
            'color' => '#ffffff',
        ];
    }
}
