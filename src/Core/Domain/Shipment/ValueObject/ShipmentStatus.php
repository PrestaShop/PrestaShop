<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\ValueObject;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * A shipment has no status column: its state is derived from the timestamps carried by ps_shipment.
 *
 * The cases are ordered from the most to the least advanced state, which is also the order in which
 * they must be evaluated — a cancelled shipment may very well carry a shipped_at date.
 */
enum ShipmentStatus: string implements TranslatableInterface
{
    case CANCELLED = 'cancelled';
    case DELIVERED = 'delivered';
    case SHIPPED = 'shipped';
    case PACKED = 'packed';
    case PENDING = 'pending';

    /**
     * SQL expression deriving the status of a shipment, to be reused by both the SELECT and the WHERE
     * clauses of the grid query so that sorting, filtering and display can never drift apart.
     *
     * @param string $alias table alias of ps_shipment in the query
     */
    public static function getSqlExpression(string $alias = 's'): string
    {
        return sprintf(
            'CASE
                WHEN %1$s.cancelled_at IS NOT NULL THEN \'%2$s\'
                WHEN %1$s.delivered_at IS NOT NULL THEN \'%3$s\'
                WHEN %1$s.shipped_at IS NOT NULL THEN \'%4$s\'
                WHEN %1$s.packed_at IS NOT NULL THEN \'%5$s\'
                ELSE \'%6$s\'
            END',
            $alias,
            self::CANCELLED->value,
            self::DELIVERED->value,
            self::SHIPPED->value,
            self::PACKED->value,
            self::PENDING->value
        );
    }

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return match ($this) {
            self::CANCELLED => $translator->trans('Cancelled', [], 'Admin.Global', $locale),
            self::DELIVERED => $translator->trans('Delivered', [], 'Admin.Orderscustomers.Feature', $locale),
            self::SHIPPED => $translator->trans('Shipped', [], 'Admin.Orderscustomers.Feature', $locale),
            self::PACKED => $translator->trans('Packed', [], 'Admin.Orderscustomers.Feature', $locale),
            self::PENDING => $translator->trans('Pending', [], 'Admin.Global', $locale),
        };
    }

    /**
     * Bootstrap badge modifier used to render the status in the grid.
     */
    public function getBadgeType(): string
    {
        return match ($this) {
            self::CANCELLED => 'danger',
            self::DELIVERED => 'success',
            self::SHIPPED => 'info',
            self::PACKED => 'light-info',
            self::PENDING => 'warning',
        };
    }
}
