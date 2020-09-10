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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Command\AddOrderReturnStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Command\EditOrderReturnStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\ValueObject\OrderReturnStateId;

/**
 * Saves or updates order return state data submitted in form
 */
final class OrderReturnStateFormDataHandler implements FormDataHandlerInterface
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
        $command = $this->buildOrderReturnStateAddCommandFromFormData($data);

        /** @var OrderReturnStateId $orderReturnStateId */
        $orderReturnStateId = $this->bus->handle($command);

        return $orderReturnStateId->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function update($orderReturnStateId, array $data)
    {
        $command = $this->buildOrderReturnStateEditCommand($orderReturnStateId, $data);

        $this->bus->handle($command);
    }

    /**
     * @return AddOrderReturnStateCommand
     */
    private function buildOrderReturnStateAddCommandFromFormData(array $data)
    {
        $command = new AddOrderReturnStateCommand(
            $data['name'],
            $data['color']
        );

        return $command;
    }

    /**
     * @param int $orderReturnStateId
     *
     * @return EditOrderReturnStateCommand
     */
    private function buildOrderReturnStateEditCommand($orderReturnStateId, array $data)
    {
        $command = (new EditOrderReturnStateCommand($orderReturnStateId))
            ->setName($data['name'])
            ->setColor($data['color'])
        ;

        return $command;
    }
}
