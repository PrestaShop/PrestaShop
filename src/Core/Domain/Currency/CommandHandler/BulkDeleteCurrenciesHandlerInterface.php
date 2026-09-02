<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Currency\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Currency\Command\BulkDeleteCurrenciesCommand;

/**
 * Interface for service that deletes currencies in bulk action
 */
interface BulkDeleteCurrenciesHandlerInterface
{
    /**
     * @param BulkDeleteCurrenciesCommand $command
     */
    public function handle(BulkDeleteCurrenciesCommand $command);
}
