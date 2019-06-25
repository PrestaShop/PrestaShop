<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\ToggleProductStatusCommand;

/**
 * Defines contract for ToggleProductStatusHandler.
 */
interface ToggleProductStatusHandlerInterface
{
    /**
     * Allows to change product state to on or off.
     *
     * @param ToggleProductStatusCommand $command
     *
     * @return void
     */
    public function handle(ToggleProductStatusCommand $command);
}
