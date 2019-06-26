<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\BulkEnableProductStatusCommand;

/**
 * Defines contract for bulk enabling product status.
 */
interface BulkEnableProductStatusHandlerInterface
{
    /**
     * @param BulkEnableProductStatusCommand $command
     *
     * @return void
     */
    public function handle(BulkEnableProductStatusCommand $command);
}
