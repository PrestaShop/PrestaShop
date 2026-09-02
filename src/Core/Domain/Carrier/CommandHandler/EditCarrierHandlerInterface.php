<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\EditCarrierCommand;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;

/**
 * Defines contract for EditCarrierHandler
 */
interface EditCarrierHandlerInterface
{
    /**
     * @param EditCarrierCommand $command
     */
    public function handle(EditCarrierCommand $command): CarrierId;
}
