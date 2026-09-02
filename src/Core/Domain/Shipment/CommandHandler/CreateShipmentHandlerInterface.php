<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Shipment\Command\CreateShipment;

interface CreateShipmentHandlerInterface
{
    public function handle(CreateShipment $command): int;
}
