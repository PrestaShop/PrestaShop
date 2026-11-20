<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Query;

use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Exception\WithdrawalRequestConstraintException;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\ValueObject\WithdrawalRequestId;

final class GetWithdrawalRequestForViewing
{
    private WithdrawalRequestId $withdrawalRequestId;

    /**
     * @throws WithdrawalRequestConstraintException
     */
    public function __construct(int $withdrawalRequestId)
    {
        $this->withdrawalRequestId = new WithdrawalRequestId($withdrawalRequestId);
    }

    public function getWithdrawalRequestId(): WithdrawalRequestId
    {
        return $this->withdrawalRequestId;
    }
}
