<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Search;

/**
 * Resolves paginated offsets that no longer point at any record.
 *
 * A grid keeps its offset in the URL and in the employee's saved filters, so deleting the last rows of
 * the page being viewed leaves an offset past the end of the result set. The query then returns nothing
 * and the grid reports that there is no record at all, while the earlier pages still hold some.
 */
final class Pagination
{
    /**
     * Whether the offset points past the last record.
     */
    public static function isOffsetOutOfRange(int $recordsTotal, int $offset): bool
    {
        return $offset > 0 && $offset >= $recordsTotal;
    }

    /**
     * Offset of the last page that still holds records, or 0 when there are none left.
     *
     * The result is always a multiple of the limit and strictly lower than $recordsTotal whenever there
     * is at least one record, so applying it cannot be out of range in turn.
     */
    public static function computeValidOffset(int $recordsTotal, int $limit): int
    {
        if ($recordsTotal <= 0 || $limit <= 0) {
            return 0;
        }

        return (int) (floor(($recordsTotal - 1) / $limit) * $limit);
    }
}
