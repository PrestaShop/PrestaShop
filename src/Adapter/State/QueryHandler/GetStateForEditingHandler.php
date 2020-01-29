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

namespace PrestaShop\PrestaShop\Adapter\State\QueryHandler;

use PrestaShop\PrestaShop\Adapter\State\AbstractStateHandler;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\State\Query\GetStateForEditing;
use PrestaShop\PrestaShop\Core\Domain\State\QueryHandler\GetStateForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\State\QueryResult\EditableState;
use PrestaShop\PrestaShop\Core\Domain\Zone\ValueObject\ZoneId;

/**
 * Handles query which gets state for editing
 */
final class GetStateForEditingHandler extends AbstractStateHandler implements GetStateForEditingHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @param GetStateForEditing $query
     *
     * @return EditableState
     */
    public function handle(GetStateForEditing $query): EditableState
    {
        $stateId = $query->getStateId();
        $state = $this->getState($stateId);

        return new EditableState(
            $stateId,
            new CountryId($state->id_country),
            new ZoneId($state->id_zone),
            $state->name,
            $state->iso_code,
            (bool) $state->active
        );
    }
}
