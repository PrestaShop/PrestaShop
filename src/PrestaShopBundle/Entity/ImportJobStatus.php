<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Entity;

/**
 * Status column of {@see ImportJob}.
 *
 * Deliberately a duplicate of the domain's ImportJobStatus: the persistence layer must not depend
 * on the CQRS layer, so the adapter converts between the two. Only what persistence needs lives
 * here — lifecycle rules stay in the domain enum.
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

    /**
     * @return list<self> the statuses a transition may start from: a terminal one is final
     */
    public static function nonTerminalCases(): array
    {
        return array_values(array_filter(self::cases(), static fn (self $status): bool => !$status->isTerminal()));
    }
}
