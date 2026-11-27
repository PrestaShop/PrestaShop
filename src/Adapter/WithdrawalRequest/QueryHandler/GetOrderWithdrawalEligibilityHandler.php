<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\WithdrawalRequest\QueryHandler;

use PrestaShop\PrestaShop\Adapter\WithdrawalRequest\WithdrawalEligibilityChecker;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Query\GetOrderWithdrawalEligibility;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\QueryHandler\GetOrderWithdrawalEligibilityHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\QueryResult\OrderWithdrawalEligibility;

#[AsQueryHandler]
final class GetOrderWithdrawalEligibilityHandler implements GetOrderWithdrawalEligibilityHandlerInterface
{
    public function __construct(
        private readonly WithdrawalEligibilityChecker $eligibilityChecker,
    ) {
    }

    public function handle(GetOrderWithdrawalEligibility $query): OrderWithdrawalEligibility
    {
        return $this->eligibilityChecker->check($query->getOrderId(), $query->getCheckedAt());
    }
}
