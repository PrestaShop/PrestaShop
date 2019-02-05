<?php

namespace PrestaShop\PrestaShop\Core\Domain\Tax\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Tax\Command\BulkUpdateTaxStatusCommand;

/**
 * Interface BulkUpdateTaxStatusHandlerInterface defines contract for BulkUpdateTaxStatus
 */
interface BulkUpdateTaxStatusHandlerInterface
{
    /**
     * @param BulkUpdateTaxStatusCommand $command
     */
    public function handle(BulkUpdateTaxStatusCommand $command);
}
