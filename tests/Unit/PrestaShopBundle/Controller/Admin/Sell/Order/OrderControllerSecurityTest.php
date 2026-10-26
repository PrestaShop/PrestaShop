<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Controller\Admin\Sell\Order;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Controller\Admin\Sell\Order\OrderController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use ReflectionMethod;

/**
 * The test container grants every permission, so the requirement is pinned on the attribute itself.
 */
class OrderControllerSecurityTest extends TestCase
{
    /**
     * Adding a product to an existing order modifies it, so the create permission alone must not be enough.
     */
    public function testAddProductToExistingOrderRequiresUpdatePermission(): void
    {
        $attributes = (new ReflectionMethod(OrderController::class, 'addProductAction'))->getAttributes(AdminSecurity::class);
        self::assertCount(1, $attributes);

        $expression = (string) $attributes[0]->newInstance()->getAttribute();
        self::assertStringContainsString("is_granted('update'", $expression);
        self::assertStringNotContainsString("is_granted('create'", $expression);
    }
}
