<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Command\AddOrderStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Command\EditOrderStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderState\ValueObject\OrderStateId;

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

        return $command;
    }
}
