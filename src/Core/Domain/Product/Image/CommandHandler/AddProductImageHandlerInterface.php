<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Image\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\AddProductImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;

/**
 * Defines contract to handle @see AddProductImageCommand
 */
interface AddProductImageHandlerInterface
{
    /**
     * @param AddProductImageCommand $command
     *
     * @return ImageId
     */
    public function handle(AddProductImageCommand $command): ImageId;
}
