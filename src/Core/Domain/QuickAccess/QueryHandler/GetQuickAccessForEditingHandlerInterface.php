<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\QuickAccess\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Query\GetQuickAccessForEditing;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\QueryResult\EditableQuickAccess;

interface GetQuickAccessForEditingHandlerInterface
{
    public function handle(GetQuickAccessForEditing $query): EditableQuickAccess;
}
