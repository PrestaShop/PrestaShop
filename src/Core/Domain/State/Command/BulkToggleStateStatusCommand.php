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

namespace PrestaShop\PrestaShop\Core\Domain\State\Command;

use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;

/**
 * Toggles state status in bulk action
 */
class BulkToggleStateStatusCommand
{
    /**
     * @var StateId[]
     */
    private $stateIds;

    /**
     * @var bool
     */
    private $expectedStatus;

    /**
     * @param array $stateIds
     * @param bool $expectedStatus
     */
    public function __construct(array $stateIds, bool $expectedStatus)
    {
        $this->expectedStatus = $expectedStatus;
        $this->setStateIds($stateIds);
    }

    /**
     * @return bool
     */
    public function getExpectedStatus(): bool
    {
        return $this->expectedStatus;
    }

    /**
     * @return StateId[]
     */
    public function getStateIds(): array
    {
        return $this->stateIds;
    }

    /**
     * @param int[] $stateIds
     */
    private function setStateIds(array $stateIds)
    {
        foreach ($stateIds as $stateId) {
            $this->stateIds[] = new StateId($stateId);
        }
    }
}
