<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Security\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Security\Command\ClearOutdatedEmployeeSessionCommand;

/**
 * Interface ClearOutdatedEmployeeSessionHandlerInterface defines session deletion handler.
 */
interface ClearOutdatedEmployeeSessionHandlerInterface
{
    /**
     * Delete session.
     *
     * @param ClearOutdatedEmployeeSessionCommand $command
     */
    public function handle(ClearOutdatedEmployeeSessionCommand $command): void;
}
