<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\BulkDeleteProductCommand;

/**
 * Defines contract for bulk product delete operation.
 */
interface BulkDeleteProductHandlerInterface
{
    /**
     * @param BulkDeleteProductCommand $command
     *
     * @return void
     */
    public function handle(BulkDeleteProductCommand $command);
}
