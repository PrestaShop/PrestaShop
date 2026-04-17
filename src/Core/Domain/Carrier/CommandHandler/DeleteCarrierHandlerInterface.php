<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\DeleteCarrierCommand;

/**
 * Defines contract for DeleteCarrierHandler
 */
interface DeleteCarrierHandlerInterface
{
    /**
     * @param DeleteCarrierCommand $command
     */
    public function handle(DeleteCarrierCommand $command);
}
