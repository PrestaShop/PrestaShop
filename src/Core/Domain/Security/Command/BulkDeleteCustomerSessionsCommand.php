<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Security\Command;

use PrestaShop\PrestaShop\Core\Domain\Security\ValueObject\CustomerSessionId;

/**
 * Deletes customer sessions in bulk action
 */
class BulkDeleteCustomerSessionsCommand
{
    /**
     * @var array<int, CustomerSessionId>
     */
    private $sessionIds;

    /**
     * @param array<int, int> $sessionIds
     */
    public function __construct(array $sessionIds)
    {
        $this->setCustomerSessionIds($sessionIds);
    }

    /**
     * @return array<int, CustomerSessionId>
     */
    public function getCustomerSessionIds(): array
    {
        return $this->sessionIds;
    }

    /**
     * @param array<int, int> $sessionIds
     */
    private function setCustomerSessionIds(array $sessionIds): void
    {
        foreach ($sessionIds as $sessionId) {
            $this->sessionIds[] = new CustomerSessionId((int) $sessionId);
        }
    }
}
