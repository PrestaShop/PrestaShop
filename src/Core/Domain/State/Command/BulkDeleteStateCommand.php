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

namespace PrestaShop\PrestaShop\Core\Domain\State\Command;

use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\NoStateId;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;

/**
 * Deletes states on bulk action
 */
class BulkDeleteStateCommand
{
    /**
     * @var array<int, StateId>
     */
    private $stateIds;

    /**
     * @param array<int, int> $stateIds
     */
    public function __construct(array $stateIds)
    {
        $this->setStateIds($stateIds);
    }

    /**
     * @return array<int, StateId>
     */
    public function getStateIds(): array
    {
        return $this->stateIds;
    }

    /**
     * @param array<int, int> $stateIds
     */
    private function setStateIds(array $stateIds): void
    {
        foreach ($stateIds as $stateId) {
            $this->stateIds[] = $stateId !== NoStateId::NO_STATE_ID_VALUE ? new StateId((int) $stateId) : new NoStateId();
        }
    }
}
