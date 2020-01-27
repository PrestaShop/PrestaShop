<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

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
            $data['logable'],
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
            ->setLogable($data['logable'])
            ->setInvoiceOn($data['invoice'])
            ->setHiddenOn($data['hidden'])
            ->setSendEmailOn($data['send_email'])
            ->setPdfInvoiceOn($data['pdf_invoice'])
            ->setPdfDeliveryOn($data['pdf_delivery'])
            ->setShippedOn($data['shipped'])
            ->setPaidOn($data['paid'])
            ->setDeliveryOn($data['delivery'])
            ->setTemplate($data['template'])
        ;

        return $command;
    }
}
