<?php
/**
 * 2007-2019 PrestaShop and Contributors
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

namespace PrestaShop\PrestaShop\Adapter\State;

use PrestaShop\PrestaShop\Core\Domain\State\Exception\DeleteStateException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;
use PrestaShopException;
use State;

/**
 * Abstract state handler
 */
abstract class AbstractStateHandler
{
    /**
     * @param StateId $stateId
     *
     * @return State
     *
     * @throws StateNotFoundException
     */
    protected function getState(StateId $stateId)
    {
        $stateIdValue = $stateId->getValue();

        try {
            $state = new State($stateIdValue);
        } catch (PrestaShopException $e) {
            throw new StateNotFoundException(
                sprintf('State with id "%s" was not found.', $stateId->getValue())
            );
        }

        if ($state->id !== $stateId->getValue()) {
            throw new StateNotFoundException(
                sprintf('State with id "%s" was not found.', $stateId->getValue())
            );
        }

        return $state;
    }

    /**
     * Deletes legacy State
     *
     * @param State $state
     *
     * @return bool
     *
     * @throws DeleteStateException
     */
    protected function deleteState(State $state): bool
    {
        try {
            return $state->delete();
        } catch (PrestaShopException $e) {
            throw new DeleteStateException(
                sprintf('An error occurred when deleting State object with id "%s".', $state->id)
            );
        }
    }

    /**
     * Toggles legacy state status
     *
     * @param State $state
     * @param bool $newStatus
     *
     * @return bool
     *
     * @throws StateException
     */
    protected function toggleStateStatus(State $state, bool $newStatus)
    {
        $state->active = $newStatus;

        try {
            return $state->save();
        } catch (PrestaShopException $e) {
            throw new StateException(sprintf(
                'An error occurred when updating state status with id "%s"',
                $state->id
            ));
        }
    }
}
