<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\BulkDuplicateProductCommand;

/**
 * Defines contract for bulk duplicating products.
 */
interface BulkDuplicateProductHandlerInterface
{
    /**
     * @param BulkDuplicateProductCommand $command
     *
     * @return void
     */
    public function handle(BulkDuplicateProductCommand $command);
}
