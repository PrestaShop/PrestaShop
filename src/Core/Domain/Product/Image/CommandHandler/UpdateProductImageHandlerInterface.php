<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Image\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\UpdateProductImageCommand;

/**
 * Defines contract to handle @see UpdateProductImageCommand
 */
interface UpdateProductImageHandlerInterface
{
    /**
     * @param UpdateProductImageCommand $command
     */
    public function handle(UpdateProductImageCommand $command): void;
}
