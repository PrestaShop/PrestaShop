<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Security\Command;

use PrestaShop\PrestaShop\Core\Domain\Security\ValueObject\EmployeeSessionId;

/**
 * Deletes employees sessions in bulk action
 */
class BulkDeleteEmployeeSessionsCommand
{
    /**
     * @var array<int, EmployeeSessionId>
     */
    private $sessionIds;

    /**
     * @param array<int, int> $sessionIds
     */
    public function __construct(array $sessionIds)
    {
        $this->setEmployeeSessionIds($sessionIds);
    }

    /**
     * @return array<int, EmployeeSessionId>
     */
    public function getEmployeeSessionIds(): array
    {
        return $this->sessionIds;
    }

    /**
     * @param array<int> $sessionIds
     */
    private function setEmployeeSessionIds(array $sessionIds): void
    {
        foreach ($sessionIds as $sessionId) {
            $this->sessionIds[] = new EmployeeSessionId((int) $sessionId);
        }
    }
}
