<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CreditSlip\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\CreditSlip\Query\GetCreditSlipIdsByDateRange;
use PrestaShop\PrestaShop\Core\Domain\CreditSlip\ValueObject\CreditSlipId;

/**
 * Interface for handling GetCreditSlipIdsByDateRange query
 */
interface GetCreditSlipIdsByDateRangeHandlerInterface
{
    /**
     * @param GetCreditSlipIdsByDateRange $query
     *
     * @return CreditSlipId[]
     */
    public function handle(GetCreditSlipIdsByDateRange $query);
}
