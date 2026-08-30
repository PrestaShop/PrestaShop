<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\QuickAccess\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Command\EditQuickAccessCommand;

interface EditQuickAccessHandlerInterface
{
    public function handle(EditQuickAccessCommand $command): void;
}
