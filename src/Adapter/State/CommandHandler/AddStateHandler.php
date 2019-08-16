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

namespace PrestaShop\PrestaShop\Adapter\State\CommandHandler;

use PrestaShop\PrestaShop\Adapter\State\AbstractStateHandler;
use PrestaShop\PrestaShop\Core\Domain\State\Command\AddStateCommand;
use PrestaShop\PrestaShop\Core\Domain\State\CommandHandler\AddStateHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\CannotAddStateException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateConstraintException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateException;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;
use PrestaShopException;
use State;

/**
 * Handles creation of state
 */
final class AddStateHandler extends AbstractStateHandler implements AddStateHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CannotAddStateException
     * @throws StateConstraintException
     * @throws StateException
     */
    public function handle(AddStateCommand $command)
    {
        try {
            $state = new State();

            $state->name = $command->getName();
            $state->iso_code = $command->getIsoCode();
            $state->id_country = $command->getCountryId()->getValue();
            $state->id_zone = $command->getZoneId()->getValue();
            $state->active = $command->isActive();

            if (!$state->validateFields(false)) {
                throw new StateConstraintException(
                    'State contains invalid field values',
                    StateConstraintException::INVALID_FIELD_VALUES
                );
            }

            if (false === $state->add()) {
                throw new CannotAddStateException(
                    'Failed to add state'
                );
            }
        } catch (PrestaShopException $e) {
            throw new StateException(
                'An unexpected error occurred when adding state',
                0,
                $e
            );
        }

        return new StateId($state->id);
    }
}
