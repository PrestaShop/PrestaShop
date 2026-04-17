<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Image\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\DeleteProductImageCommand;

/**
 * Defines contract to handle @see DeleteProductImageCommand
 */
interface DeleteProductImageHandlerInterface
{
    /**
     * @param DeleteProductImageCommand $command
     */
    public function handle(DeleteProductImageCommand $command): void;
}
