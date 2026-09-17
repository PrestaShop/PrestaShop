<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Import\Command\ContinueImportJobCommand;
use PrestaShop\PrestaShop\Core\Domain\Import\QueryResult\ImportJobState;

/**
 * Defines the contract for handling @see ContinueImportJobCommand.
 *
 * Returns the query's state shape so a polling client needs no second round trip.
 */
interface ContinueImportJobHandlerInterface
{
    public function handle(ContinueImportJobCommand $command): ImportJobState;
}
