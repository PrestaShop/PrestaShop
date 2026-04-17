<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Currency\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Currency\Command\BulkToggleCurrenciesStatusCommand;

/**
 * Interface for service that toggles status for multiple currencies
 */
interface BulkToggleCurrenciesStatusHandlerInterface
{
    /**
     * @param BulkToggleCurrenciesStatusCommand $command
     */
    public function handle(BulkToggleCurrenciesStatusCommand $command);
}
