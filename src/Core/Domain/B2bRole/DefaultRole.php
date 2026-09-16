<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\B2bRole;

/**
 * The B2B roles created on installation.
 */
enum DefaultRole: string
{
    public const PREFIX = 'ROLE_B2B_';

    case SUPER_ADMIN = self::PREFIX . 'SUPER_ADMIN';
    case ADMIN = self::PREFIX . 'ADMIN';
    case BUYER = self::PREFIX . 'BUYER';
}
