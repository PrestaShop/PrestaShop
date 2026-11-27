<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Group\QueryResult;

final class DefaultCustomerGroups
{
    public function __construct(
        private readonly int $visitorsGroupId,
        private readonly int $guestsGroupId,
        private readonly int $customersGroupId
    ) {
    }

    public function getVisitorsGroupId(): int
    {
        return $this->visitorsGroupId;
    }

    public function getGuestsGroupId(): int
    {
        return $this->guestsGroupId;
    }

    public function getCustomersGroupId(): int
    {
        return $this->customersGroupId;
    }
}
