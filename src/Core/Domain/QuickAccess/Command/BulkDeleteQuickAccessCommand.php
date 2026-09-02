<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\QuickAccess\Command;

use PrestaShop\PrestaShop\Core\Domain\QuickAccess\ValueObject\QuickAccessId;

class BulkDeleteQuickAccessCommand
{
    /** @var QuickAccessId[] */
    private array $quickAccessIds;

    /** @param int[] $quickAccessIds */
    public function __construct(array $quickAccessIds)
    {
        foreach ($quickAccessIds as $id) {
            $this->quickAccessIds[] = new QuickAccessId((int) $id);
        }
    }

    /** @return QuickAccessId[] */
    public function getQuickAccessIds(): array
    {
        return $this->quickAccessIds;
    }
}
