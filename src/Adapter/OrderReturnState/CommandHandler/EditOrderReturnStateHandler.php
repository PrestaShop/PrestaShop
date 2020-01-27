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

namespace PrestaShop\PrestaShop\Adapter\OrderReturnState\CommandHandler;

use OrderReturnState;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Command\EditOrderReturnStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\CommandHandler\EditOrderReturnStateHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Exception\OrderReturnStateException;

/**
 * Handles commands which edits given order return state with provided data.
 *
 * @internal
 */
final class EditOrderReturnStateHandler extends AbstractOrderReturnStateHandler implements EditOrderReturnStateHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(EditOrderReturnStateCommand $command)
    {
        $orderReturnStateId = $command->getOrderReturnStateId();
        $orderReturnState = new OrderReturnState($orderReturnStateId->getValue());

        $this->assertOrderReturnStateWasFound($orderReturnStateId, $orderReturnState);

        $this->updateOrderReturnStateWithCommandData($orderReturnState, $command);

        $this->assertRequiredFieldsAreNotMissing($orderReturnState);

        if (false === $orderReturnState->validateFields(false)) {
            throw new OrderReturnStateException('OrderReturnState contains invalid field values');
        }

        if (false === $orderReturnState->update()) {
            throw new OrderReturnStateException('Failed to update order return state');
        }
    }

    private function updateOrderReturnStateWithCommandData(OrderReturnState $orderReturnState, EditOrderReturnStateCommand $command)
    {
        if (null !== $command->getName()) {
            $orderReturnState->name = $command->getName();
        }

        if (null !== $command->getColor()) {
            $orderReturnState->color = $command->getColor();
        }
    }
}
