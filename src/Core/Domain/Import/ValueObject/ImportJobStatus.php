<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\ValueObject;

/**
 * Lifecycle status of an import job.
 *
 * AWAITING_CONFIRMATION means a pausing phase ended with messages; continuing the job is the
 * confirmation, so it is not terminal.
 */
enum ImportJobStatus: string
{
    case AWAITING_CONFIRMATION = 'awaiting_confirmation';
    case CANCELLED = 'cancelled';
    case FAILED = 'failed';
    case FINISHED = 'finished';
    case PENDING = 'pending';
    case RUNNING = 'running';

    public function isTerminal(): bool
    {
        return in_array($this, [self::CANCELLED, self::FAILED, self::FINISHED], true);
    }

    public function canContinue(): bool
    {
        return !$this->isTerminal();
    }

    /**
     * @return list<string> for the purge query
     */
    public static function terminalValues(): array
    {
        return array_values(array_map(
            static fn (self $status): string => $status->value,
            array_filter(self::cases(), static fn (self $status): bool => $status->isTerminal())
        ));
    }
}
