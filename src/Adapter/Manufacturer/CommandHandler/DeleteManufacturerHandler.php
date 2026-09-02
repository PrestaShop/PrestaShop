<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Manufacturer\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\DeleteManufacturerCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\CommandHandler\DeleteManufacturerHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\DeleteManufacturerException;

/**
 * Handles command which deletes manufacturer using legacy object model
 */
#[AsCommandHandler]
final class DeleteManufacturerHandler extends AbstractManufacturerCommandHandler implements DeleteManufacturerHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(DeleteManufacturerCommand $command)
    {
        $manufacturer = $this->getManufacturer($command->getManufacturerId());

        if (!$this->deleteManufacturer($manufacturer)) {
            throw new DeleteManufacturerException(sprintf('Cannot delete Manufacturer object with id "%s".', $manufacturer->id), DeleteManufacturerException::FAILED_DELETE);
        }
    }
}
