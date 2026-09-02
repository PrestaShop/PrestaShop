<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Manufacturer\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\DeleteManufacturerLogoImageCommand;

/**
 * Defines contract for DeleteManufacturerLogoImageHandler
 */
interface DeleteManufacturerLogoImageHandlerInterface
{
    /**
     * @param DeleteManufacturerLogoImageCommand $command
     */
    public function handle(DeleteManufacturerLogoImageCommand $command): void;
}
