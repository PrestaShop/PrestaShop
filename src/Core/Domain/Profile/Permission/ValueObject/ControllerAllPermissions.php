<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Profile\Permission\ValueObject;

class ControllerAllPermissions implements PermissionInterface
{
    public const ALL = 'all';

    public function getValue(): string
    {
        return static::ALL;
    }
}
