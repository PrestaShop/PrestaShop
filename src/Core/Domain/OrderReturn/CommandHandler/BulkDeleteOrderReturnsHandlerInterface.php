<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturn\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Command\BulkDeleteOrderReturnsCommand;

interface BulkDeleteOrderReturnsHandlerInterface
{
    public function handle(BulkDeleteOrderReturnsCommand $command): void;
}
