<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\BulkToggleCarrierStatusCommand;

/**
 * Defines contract for BulkToggleCarrierStatusHandler
 */
interface BulkToggleCarrierStatusHandlerInterface
{
    /**
     * @param BulkToggleCarrierStatusCommand $command
     */
    public function handle(BulkToggleCarrierStatusCommand $command);
}
