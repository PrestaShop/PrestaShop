<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Employee\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Employee\Command\SendEmployeePasswordResetEmailCommand;

interface SendEmployeePasswordResetEmailHandlerInterface
{
    /**
     * @param SendEmployeePasswordResetEmailCommand $command
     *
     * @return string The url to reset the password
     */
    public function handle(SendEmployeePasswordResetEmailCommand $command): string;
}
