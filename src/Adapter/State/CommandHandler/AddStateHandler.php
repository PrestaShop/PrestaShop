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
class AddStateHandler implements AddStateHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CannotAddStateException
     * @throws StateConstraintException
     * @throws StateException
     */
    public function handle(AddStateCommand $command): StateId
    {
        try {
            $state = new State();

            $state->name = $command->getName();
            $state->iso_code = $command->getIsoCode();
            $state->id_country = $command->getCountryId()->getValue();
            $state->id_zone = $command->getZoneId()->getValue();
            $state->active = $command->isActive();

            if ($state->validateFields(false, true) !== true) {
                throw new StateException('State contains invalid field values');
            }

            if (false === $state->add()) {
                throw new CannotAddStateException('Failed to add state');
            }
        } catch (PrestaShopException $e) {
            throw new StateException('An unexpected error occurred when adding state', 0, $e);
        }

        return new StateId((int) $state->id);
    }
}
