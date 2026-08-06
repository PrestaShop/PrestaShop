<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Feature\Enum;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum ShopModeEnum: string implements TranslatableInterface
{
    case SHOP_MODE_B2C_ONLY = 'b2c_only';
    case SHOP_MODE_B2B_ONLY = 'b2b_only';
    case SHOP_MODE_B2B_AND_B2C = 'b2b_and_b2c';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return match ($this) {
            self::SHOP_MODE_B2C_ONLY => $translator->trans('B2C only', [], 'Admin.Shopparameters.Feature', $locale),
            self::SHOP_MODE_B2B_ONLY => $translator->trans('B2B only', [], 'Admin.Shopparameters.Feature', $locale),
            self::SHOP_MODE_B2B_AND_B2C => $translator->trans('B2B and B2C', [], 'Admin.Shopparameters.Feature', $locale),
        };
    }
}
