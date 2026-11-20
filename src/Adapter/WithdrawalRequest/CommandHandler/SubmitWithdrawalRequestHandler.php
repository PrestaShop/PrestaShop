<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\WithdrawalRequest\CommandHandler;

use PrestaShop\PrestaShop\Adapter\WithdrawalRequest\WithdrawalRequestSubmitter;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Command\SubmitWithdrawalRequestCommand;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\CommandHandler\SubmitWithdrawalRequestHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Exception\CannotSaveWithdrawalRequestException;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Exception\OrderNotEligibleForWithdrawalException;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\ValueObject\WithdrawalRequestId;

#[AsCommandHandler]
final class SubmitWithdrawalRequestHandler implements SubmitWithdrawalRequestHandlerInterface
{
    public function __construct(
        private readonly WithdrawalRequestSubmitter $submitter,
    ) {
    }

    /**
     * @throws OrderNotFoundException
     * @throws OrderNotEligibleForWithdrawalException
     * @throws CannotSaveWithdrawalRequestException
     */
    public function handle(SubmitWithdrawalRequestCommand $command): WithdrawalRequestId
    {
        return $this->submitter->submit($command);
    }
}
