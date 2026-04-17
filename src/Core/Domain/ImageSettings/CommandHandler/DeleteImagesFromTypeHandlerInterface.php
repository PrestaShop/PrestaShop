<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\ImageSettings\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Command\DeleteImagesFromTypeCommand;

/**
 * Defines contract for DeleteImagesFromTypeHandler
 */
interface DeleteImagesFromTypeHandlerInterface
{
    /**
     * @param DeleteImagesFromTypeCommand $command
     */
    public function handle(DeleteImagesFromTypeCommand $command): void;
}
