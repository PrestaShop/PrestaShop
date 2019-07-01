<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\DuplicateProductCommand;

/**
 * Defines contract for duplicating product.
 */
interface DuplicateProductHandlerInterface
{
    /**
     * @param DuplicateProductCommand $command
     *
     * @return void
     */
    public function handle(DuplicateProductCommand $command);
}
