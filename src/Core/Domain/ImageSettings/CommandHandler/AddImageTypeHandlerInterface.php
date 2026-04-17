<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\ImageSettings\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Command\AddImageTypeCommand;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\ValueObject\ImageTypeId;

/**
 * Interface for service that creates new image type
 */
interface AddImageTypeHandlerInterface
{
    /**
     * @param AddImageTypeCommand $command
     *
     * @return ImageTypeId
     */
    public function handle(AddImageTypeCommand $command): ImageTypeId;
}
