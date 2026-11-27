<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Service;

use DateTimeImmutable;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Exception\WithdrawalRequestConstraintException;

/**
 * Computes the end of the withdrawal period of a distance sales contract for goods.
 *
 * Directive 2011/83/EU, article 9: the period expires N days (14 by default) after the day the consumer
 * acquires physical possession of the goods. Recital 41 makes Regulation (EEC, Euratom) No 1182/71 apply to
 * the computation, which means:
 *  - the day of delivery is not counted, the period starts the following day (article 3.1);
 *  - the period ends at the end of its last day (article 3.2.b);
 *  - when the last day is a Saturday or a Sunday, the period ends at the end of the next working day
 *    (article 3.4). Public holidays are country specific and are not taken into account here: a merchant
 *    can configure a longer period to cover them.
 *
 * Until the goods are delivered, there is no deadline: the consumer can withdraw at any time.
 */
final class WithdrawalDeadlineCalculator
{
    public const DEFAULT_PERIOD_DAYS = 14;

    /**
     * @throws WithdrawalRequestConstraintException when the period is not a positive number of days
     */
    public function getDeadline(?DateTimeImmutable $deliveredAt, int $periodDays): ?DateTimeImmutable
    {
        if (0 >= $periodDays) {
            throw new WithdrawalRequestConstraintException(
                sprintf('Invalid withdrawal period "%d", it must be a positive number of days.', $periodDays),
                WithdrawalRequestConstraintException::INVALID_PERIOD
            );
        }

        if (null === $deliveredAt) {
            return null;
        }

        $lastDay = $deliveredAt->setTime(0, 0)->modify(sprintf('+%d days', $periodDays));

        // ISO-8601 day of the week: 6 is Saturday, 7 is Sunday
        $dayOfWeek = (int) $lastDay->format('N');
        if ($dayOfWeek >= 6) {
            $lastDay = $lastDay->modify(sprintf('+%d days', 8 - $dayOfWeek));
        }

        return $lastDay->setTime(23, 59, 59);
    }

    public function isWithinPeriod(?DateTimeImmutable $deliveredAt, int $periodDays, DateTimeImmutable $now): bool
    {
        $deadline = $this->getDeadline($deliveredAt, $periodDays);

        return null === $deadline || $now <= $deadline;
    }
}
