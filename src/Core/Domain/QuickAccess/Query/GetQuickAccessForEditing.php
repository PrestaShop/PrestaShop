<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\QuickAccess\Query;

use PrestaShop\PrestaShop\Core\Domain\QuickAccess\ValueObject\QuickAccessId;

class GetQuickAccessForEditing
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
