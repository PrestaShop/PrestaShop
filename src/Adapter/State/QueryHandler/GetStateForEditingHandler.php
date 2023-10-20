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

namespace PrestaShop\PrestaShop\Adapter\State\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\State\Query\GetStateForEditing;
use PrestaShop\PrestaShop\Core\Domain\State\QueryHandler\GetStateForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\State\QueryResult\EditableState;
use PrestaShop\PrestaShop\Core\Domain\Zone\ValueObject\ZoneId;
use State;

/**
 * Handles command that gets state for editing
 *
 * @internal
 */
class GetStateForEditingHandler implements GetStateForEditingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetStateForEditing $query): EditableState
    {
        $stateId = $query->getStateId();
        $state = new State($stateId->getValue());

        if ($state->id !== $stateId->getValue()) {
            throw new StateNotFoundException(sprintf('State with id "%d" not found', $stateId->getValue()));
        }

        return new EditableState(
            $stateId,
            new CountryId((int) $state->id_country),
            new ZoneId((int) $state->id_zone),
            (string) $state->name,
            $state->iso_code,
            (bool) $state->active,
            $state->getAssociatedShops()
        );
    }
}
