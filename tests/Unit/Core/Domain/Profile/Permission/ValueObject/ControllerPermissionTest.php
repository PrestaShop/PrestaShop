<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Domain\Customer\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\Exception\InvalidPermissionValueException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\ValueObject\ControllerPermission;

class ControllerPermissionTest extends TestCase
{
    public function testExceptionIsThrownPermissionIsNotSupported()
    {
        $this->expectException(InvalidPermissionValueException::class);

        new ControllerPermission('This is not a good permission!');
    }

    public function testPermissionIsSupported()
    {
        $permission = new ControllerPermission('view');
        $this->assertEquals('view', $permission->getValue());
    }
}
