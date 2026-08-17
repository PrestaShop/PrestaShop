<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Entity\Enum;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Enum status BusinessEntity.
 */
enum BusinessEntityStatus: string implements TranslatableInterface
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case REJECTED = 'rejected';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return match ($this) {
            self::PENDING => $translator->trans('Pending', [], 'Admin.Global', $locale),
            self::ACTIVE => $translator->trans('Active', [], 'Admin.Global', $locale),
            self::INACTIVE => $translator->trans('Inactive', [], 'Admin.Global', $locale),
            self::REJECTED => $translator->trans('Rejected', [], 'Admin.Global', $locale),
        };
    }

    /**
     * Badge type expected by BadgeColumn and by the detail page status badge.
     */
    public function badgeType(): string
    {
        return match ($this) {
            self::PENDING => 'info',
            self::ACTIVE => 'success',
            self::INACTIVE => 'light-info',
            self::REJECTED => 'danger',
        };
    }
}
