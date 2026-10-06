<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Csp\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Csp\CspViolationRecorder;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Csp\Command\RecordCspViolationCommand;
use PrestaShop\PrestaShop\Core\Domain\Csp\CommandHandler\RecordCspViolationHandlerInterface;

/**
 * Thin wrapper over CspViolationRecorder so the command bus and the front-office collector share the same logic.
 *
 * @internal
 */
#[AsCommandHandler]
final class RecordCspViolationHandler implements RecordCspViolationHandlerInterface
{
    public function __construct(
        private readonly CspViolationRecorder $recorder,
    ) {
    }

    public function handle(RecordCspViolationCommand $command): void
    {
        $this->recorder->record(
            $command->getContext(),
            $command->getShopId(),
            $command->getDirective(),
            $command->getSource(),
            $command->getDocumentUri(),
        );
    }
}
