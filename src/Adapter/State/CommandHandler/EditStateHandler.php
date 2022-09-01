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

namespace PrestaShop\PrestaShop\Adapter\State\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\State\Command\EditStateCommand;
use PrestaShop\PrestaShop\Core\Domain\State\CommandHandler\EditStateHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\CannotUpdateStateException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateConstraintException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;
use PrestaShopException;
use State;

/**
 * Handles state editing
 */
class EditStateHandler implements EditStateHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CannotUpdateStateException
     * @throws StateConstraintException
     * @throws StateException
     * @throws StateNotFoundException
     */
    public function handle(EditStateCommand $command): void
    {
        $state = $this->getState($command->getStateId());

        try {
            if (null !== $command->getZoneId()->getValue()) {
                $state->id_zone = $command->getZoneId()->getValue();
            }

            if (null !== $command->getCountryId()->getValue()) {
                $state->id_country = $command->getCountryId()->getValue();
            }

            if (null !== $command->getIsoCode()) {
                $state->iso_code = $command->getIsoCode();
            }

            if (null !== $command->getName()) {
                $state->name = $command->getName();
            }

            if (null !== $command->getActive()) {
                $state->active = $command->getActive();
            }

            $isValid = $state->validateFields(true);
            if ($isValid !== true) {
                throw new StateConstraintException('State contains invalid field values: ' . $isValid);
            }

            if (false === $state->update()) {
                throw new CannotUpdateStateException('Failed to update state');
            }
        } catch (PrestaShopException $e) {
            throw new StateException('An unexpected error occurred when updating state', 0, $e);
        }
    }

    /**
     * @param StateId $stateId
     *
     * @return State
     *
     * @throws StateNotFoundException
     * @throws StateException
     */
    private function getState(StateId $stateId): State
    {
        $stateIdValue = $stateId->getValue();

        try {
            $state = new State($stateIdValue);
        } catch (PrestaShopException $e) {
            throw new StateException(sprintf('Failed to get state with id: "%s"', $stateIdValue), 0, $e);
        }

        if ($state->id !== $stateIdValue) {
            throw new StateNotFoundException(sprintf('State with id "%s" was not found.', $stateIdValue));
        }

        return $state;
    }
}
