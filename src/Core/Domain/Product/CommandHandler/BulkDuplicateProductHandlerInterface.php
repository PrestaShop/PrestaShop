<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\BulkDuplicateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Defines contract to handle @see BulkDuplicateProductCommand
 */
interface BulkDuplicateProductHandlerInterface
{
    /**
     * @param BulkDuplicateProductCommand $command
     *
     * @return array<ProductId>
     */
    public function handle(BulkDuplicateProductCommand $command): array;
}
