<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Entity\Enum;

/**
 * Enum status BusinessEntity.
 */
enum CustomerB2bStatus: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case REJECTED = 'rejected';
}
