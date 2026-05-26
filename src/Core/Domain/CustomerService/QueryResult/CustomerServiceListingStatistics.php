<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryResult;

final class CustomerServiceListingStatistics
{
    public function __construct(
        private readonly int $totalThreads,
        private readonly int $openThreads,
        private readonly int $pendingThreads,
        private readonly int $closedThreads,
        private readonly int $customerMessages,
        private readonly int $employeeMessages,
    ) {
    }

    public function getTotalThreads(): int
    {
        return $this->totalThreads;
    }

    public function getOpenThreads(): int
    {
        return $this->openThreads;
    }

    public function getPendingThreads(): int
    {
        return $this->pendingThreads;
    }

    public function getClosedThreads(): int
    {
        return $this->closedThreads;
    }

    public function getCustomerMessages(): int
    {
        return $this->customerMessages;
    }

    public function getEmployeeMessages(): int
    {
        return $this->employeeMessages;
    }
}
