<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\B2bRole;

final class Role
{
    public const PREFIX = 'ROLE_B2B_';

    public const SUPER_ADMIN = self::PREFIX . 'SUPER_ADMIN';
    public const ADMIN = self::PREFIX . 'ADMIN';
    public const BUYER = self::PREFIX . 'BUYER';

    private function __construct()
    {
    }
}
