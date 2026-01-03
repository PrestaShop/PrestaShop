<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Employee\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Employee\Command\SetEmployeeTwoFactorSecretCommand;

/**
 * Interface for services that handle the command responsible for
 * storing a two-factor authentication secret.
 */
interface SetEmployeeTwoFactorSecretHandlerInterface
{
    /**
     * @param SetEmployeeTwoFactorSecretCommand $command
     */
    public function handle(SetEmployeeTwoFactorSecretCommand $command);
}
