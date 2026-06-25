<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\QuickAccess\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Command\AddQuickAccessCommand;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\ValueObject\QuickAccessId;

interface AddQuickAccessHandlerInterface
{
    public function handle(AddQuickAccessCommand $command): QuickAccessId;
}
