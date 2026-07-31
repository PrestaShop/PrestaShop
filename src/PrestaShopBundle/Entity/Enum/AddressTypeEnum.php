<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Entity\Enum;

enum AddressTypeEnum: string
{
    case BOTH = 'both';
    case INVOICE = 'invoice';
    case DELIVERY = 'delivery';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
