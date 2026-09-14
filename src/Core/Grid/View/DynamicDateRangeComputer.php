<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\View;

use DateTimeImmutable;

class DynamicDateRangeComputer
{
    private const DATE_FORMAT = 'Y-m-d';

    /**
     * @return array{from: string, to: string}|null
     */
    public function compute(DynamicDateRule $rule, ?int $lastDaysCount = null, ?DateTimeImmutable $now = null): ?array
    {
        $now = $now ?? new DateTimeImmutable();
        $end = $now;

        switch ($rule) {
            case DynamicDateRule::TODAY:
                $start = $now;
                break;
            case DynamicDateRule::YESTERDAY:
                $start = $end = $now->modify('-1 day');
                break;
            case DynamicDateRule::CURRENT_WEEK:
                $start = $now->modify('monday this week');
                break;
            case DynamicDateRule::CURRENT_MONTH:
                $start = $now->modify('first day of this month');
                break;
            case DynamicDateRule::CURRENT_QUARTER:
                $quarterStartMonth = (int) (floor(((int) $now->format('n') - 1) / 3) * 3 + 1);
                $start = $now->setDate((int) $now->format('Y'), $quarterStartMonth, 1);
                break;
            case DynamicDateRule::CURRENT_SEMESTER:
                $semesterStartMonth = (int) $now->format('n') <= 6 ? 1 : 7;
                $start = $now->setDate((int) $now->format('Y'), $semesterStartMonth, 1);
                break;
            case DynamicDateRule::CURRENT_YEAR:
                $start = $now->setDate((int) $now->format('Y'), 1, 1);
                break;
            case DynamicDateRule::LAST_DAYS:
                if (null === $lastDaysCount || $lastDaysCount < 1) {
                    return null;
                }
                $start = $now->modify(sprintf('-%d days', $lastDaysCount));
                break;
            default:
                return null;
        }

        return [
            'from' => $start->format(self::DATE_FORMAT),
            'to' => $end->format(self::DATE_FORMAT),
        ];
    }
}
