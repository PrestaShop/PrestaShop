<?php

namespace PrestaShop\PrestaShop\Core\Domain\Tax\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Tax\Command\ToggleTaxStatusCommand;

/**
 * Interface ToggleTaxStatusHandlerInterface responsible for changing tax status
 */
interface ToggleTaxStatusHandlerInterface
{
    public function handle(ToggleTaxStatusCommand $command);
}
