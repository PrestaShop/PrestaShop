<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\BulkDisableProductStatusCommand;

/**
 * Defines contract for bulk disabling product status.
 */
interface BulkDisableProductStatusHandlerInterface
{
    /**
     * @param BulkDisableProductStatusCommand $command
     *
     * @return void
     */
    public function handle(BulkDisableProductStatusCommand $command);
}
