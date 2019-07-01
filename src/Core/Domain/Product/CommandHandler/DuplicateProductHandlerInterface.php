<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\DuplicateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Defines contract for duplicating product.
 */
interface DuplicateProductHandlerInterface
{
    /**
     * @param DuplicateProductCommand $command
     *
     * @return ProductId
     */
    public function handle(DuplicateProductCommand $command);
}
