<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\QuickAccess\Command;

use PrestaShop\PrestaShop\Core\Domain\QuickAccess\ValueObject\QuickAccessId;

class DeleteQuickAccessCommand
{
    private QuickAccessId $quickAccessId;

    public function __construct(int $quickAccessId)
    {
        $this->quickAccessId = new QuickAccessId($quickAccessId);
    }

    public function getQuickAccessId(): QuickAccessId
    {
        return $this->quickAccessId;
    }
}
