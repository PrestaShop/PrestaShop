<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Query\GetWithdrawalRequestForViewing;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\QueryResult\WithdrawalRequestForViewing;

interface GetWithdrawalRequestForViewingHandlerInterface
{
    public function handle(GetWithdrawalRequestForViewing $query): WithdrawalRequestForViewing;
}
