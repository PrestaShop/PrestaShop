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

namespace PrestaShop\PrestaShop\Adapter\Profile\Employee\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Employee\Command\ResetPasswordCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\CommandHandler\ResetPasswordHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\InvalidPasswordException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\ResetPasswordInformationMissingException;

/**
 * Handles the command which resets employee's password.
 *
 * @internal
 */
final class ResetPasswordHandler implements ResetPasswordHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(ResetPasswordCommand $command)
    {
        $this->assertCommandDataIsValid($command);

        //@todo finish implementation
    }

    private function assertCommandDataIsValid(ResetPasswordCommand $command)
    {
        if (empty($command->getResetToken()) || empty(trim($command->getEmail()->getValue()))) {
            throw new ResetPasswordInformationMissingException();
        }

        $newPasswordLength = strlen($command->getNewPlainPassword());

        // Legacy validation rules
        if ($newPasswordLength < 5 || $newPasswordLength > 72) {
            throw new InvalidPasswordException();
        }
    }
}
