<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

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
