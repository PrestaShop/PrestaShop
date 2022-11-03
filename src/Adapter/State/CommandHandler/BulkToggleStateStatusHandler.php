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

namespace PrestaShop\PrestaShop\Adapter\State\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\State\Command\BulkToggleStateStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\State\CommandHandler\BulkToggleStateStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\CannotToggleStateStatusException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateNotFoundException;
use PrestaShopException;
use State;

/**
 * Handles command that toggles states status in bulk action
 */
class BulkToggleStateStatusHandler implements BulkToggleStateStatusHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(BulkToggleStateStatusCommand $command): void
    {
        foreach ($command->getStateIds() as $stateId) {
            $state = new State($stateId->getValue());

            if (0 >= $state->id) {
                throw new StateNotFoundException(sprintf('State object with id "%d" has not been found for status changing', $stateId->getValue()));
            }

            $state->active = $command->getExpectedStatus();

            try {
                if (!$state->save()) {
                    throw new CannotToggleStateStatusException(sprintf('Unable to toggle status for state with id "%d"', $stateId->getValue()));
                }
            } catch (PrestaShopException $e) {
                throw new StateException(
                    sprintf('An error occurred while updating state status with id "%d"', $stateId->getValue()),
                    0,
                    $e
                );
            }
        }
    }
}
