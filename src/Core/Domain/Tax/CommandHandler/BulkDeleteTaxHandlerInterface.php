<?php

namespace PrestaShop\PrestaShop\Core\Domain\Tax\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Tax\Command\BulkDeleteTaxCommand;

/**
 * Interface BulkDeleteTaxHandlerInterface defines contract for BulkDeleteTaxHandler
 */
interface BulkDeleteTaxHandlerInterface
{
    /**
     * @param BulkDeleteTaxCommand $command
     */
    public function handle(BulkDeleteTaxCommand $command);
}
