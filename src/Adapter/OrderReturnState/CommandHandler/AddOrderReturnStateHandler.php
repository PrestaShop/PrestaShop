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

namespace PrestaShop\PrestaShop\Adapter\OrderReturnState\CommandHandler;

use OrderReturnState;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Command\AddOrderReturnStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\CommandHandler\AddOrderReturnStateHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Exception\OrderReturnStateException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\ValueObject\OrderReturnStateId;

/**
 * Handles command that adds new order return state
 *
 * @internal
 */
#[AsCommandHandler]
final class AddOrderReturnStateHandler extends AbstractOrderReturnStateHandler implements AddOrderReturnStateHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(AddOrderReturnStateCommand $command)
    {
        $orderReturnState = new OrderReturnState();

        $this->fillOrderReturnStateWithCommandData($orderReturnState, $command);
        $this->assertRequiredFieldsAreNotMissing($orderReturnState);

        if (false === $orderReturnState->validateFields(false)) {
            throw new OrderReturnStateException('Order status contains invalid field values');
        }

        $orderReturnState->add();

        return new OrderReturnStateId((int) $orderReturnState->id);
    }

    private function fillOrderReturnStateWithCommandData(OrderReturnState $orderReturnState, AddOrderReturnStateCommand $command)
    {
        $orderReturnState->name = $command->getLocalizedNames();
        $orderReturnState->color = $command->getColor();
    }
}
