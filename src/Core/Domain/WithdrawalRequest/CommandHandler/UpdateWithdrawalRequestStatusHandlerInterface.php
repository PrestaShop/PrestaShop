<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Command\UpdateWithdrawalRequestStatusCommand;

interface UpdateWithdrawalRequestStatusHandlerInterface
{
    public function handle(UpdateWithdrawalRequestStatusCommand $command): void;
}
