<?php

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Image\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\SetProductImagesForAllShopCommand;

interface SetProductImagesForAllShopHandlerInterface
{
    /**
     * @param SetProductImagesForAllShopCommand $command
     */
    public function handle(SetProductImagesForAllShopCommand $command): void;
}
