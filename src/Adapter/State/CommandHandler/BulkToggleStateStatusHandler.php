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
use PrestaShop\PrestaShop\Core\Domain\State\Command\BulkToggleStateStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\State\CommandHandler\BulkToggleStateStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\UpdateStateException;

/**
 * Handles bulk states status toggle
 */
final class BulkToggleStateStatusHandler extends AbstractStateHandler implements BulkToggleStateStatusHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(BulkToggleStateStatusCommand $command)
    {
        foreach ($command->getStateIds() as $stateId) {
            $state = $this->getState($stateId);

            if (!$this->toggleStateStatus($state, $command->getExpectedStatus())) {
                throw new UpdateStateException(
                    sprintf('Unable to toggle state status with id "%s"', $state->id),
                    UpdateStateException::FAILED_BULK_UPDATE_STATUS
                );
            }
        }
    }
}
