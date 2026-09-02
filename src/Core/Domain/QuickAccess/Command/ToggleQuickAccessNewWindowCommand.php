<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\QuickAccess\Command;

use PrestaShop\PrestaShop\Core\Domain\QuickAccess\ValueObject\QuickAccessId;

/**
 * Toggles the new_window flag. Partial update — only the new_window column is written.
 */
class ToggleQuickAccessNewWindowCommand
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
