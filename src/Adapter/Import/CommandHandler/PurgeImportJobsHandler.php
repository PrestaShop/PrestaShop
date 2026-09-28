<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Import\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Import\Job\ImportJobPurger;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Import\Command\PurgeImportJobsCommand;
use PrestaShop\PrestaShop\Core\Domain\Import\CommandHandler\PurgeImportJobsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\ImportJobPurgeSummary;

/**
 * Handles @see PurgeImportJobsCommand.
 *
 * The purger is shared with the Start handler, which collects garbage opportunistically, so a shop
 * nobody ever runs the command on still cleans up after itself.
 */
#[AsCommandHandler]
final class PurgeImportJobsHandler implements PurgeImportJobsHandlerInterface
{
    public function __construct(
        private readonly ImportJobPurger $purger,
    ) {
    }

    public function handle(PurgeImportJobsCommand $command): ImportJobPurgeSummary
    {
        return $this->purger->purge($command->getExpirationDate());
    }
}
