<?php

namespace PrestaShop\PrestaShop\Core\Domain\Tax\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Tax\Command\BulkUpdateTaxStatusCommand;

/**
 * Interface BulkUpdateTaxStatusHandlerInterface responsible for updating Taxes status in bulk action
 */
interface BulkUpdateTaxStatusHandlerInterface
{
    public function handle(BulkUpdateTaxStatusCommand $command);
}
