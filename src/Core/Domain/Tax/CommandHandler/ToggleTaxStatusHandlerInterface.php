<?php

namespace PrestaShop\PrestaShop\Core\Domain\Tax\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Tax\Command\ToggleTaxStatusCommand;

/**
 * Interface ToggleTaxStatusHandlerInterface defines contract for ToggleTaxStatusHandler
 */
interface ToggleTaxStatusHandlerInterface
{
    public function handle(ToggleTaxStatusCommand $command);
}
