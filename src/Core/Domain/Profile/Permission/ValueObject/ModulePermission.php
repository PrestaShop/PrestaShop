<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Profile\Permission\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\Exception\InvalidPermissionValueException;

class ModulePermission implements PermissionInterface
{
    public const VIEW = 'view';
    public const CONFIGURE = 'configure';
    public const UNINSTALL = 'uninstall';

    public const SUPPORTED_PERMISSIONS = [
        self::VIEW,
        self::CONFIGURE,
        self::UNINSTALL,
    ];

    /**
     * @var string
     */
    private $permission;

    /**
     * @param string $permission
     */
    public function __construct(string $permission)
    {
        $this->assertPermissionIsSupported($permission);

        $this->permission = $permission;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->permission;
    }

    protected function assertPermissionIsSupported(string $permission): void
    {
        if (!in_array($permission, static::SUPPORTED_PERMISSIONS)) {
            throw new InvalidPermissionValueException(
                sprintf('Invalid permission "%s" provided', $permission)
            );
        }
    }
}
