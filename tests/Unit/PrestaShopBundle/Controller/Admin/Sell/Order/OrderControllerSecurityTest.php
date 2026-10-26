<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Controller\Admin\Sell\Order;

use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Controller\Admin\Sell\Order\OrderController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use ReflectionMethod;

/**
 * The test container grants every permission, so the requirement is pinned on the annotation itself.
 */
class OrderControllerSecurityTest extends TestCase
{
    /**
     * Adding a product to an existing order modifies it, so the create permission alone must not be enough.
     */
    public function testAddProductToExistingOrderRequiresUpdatePermission(): void
    {
        $annotation = (new AnnotationReader())->getMethodAnnotation(
            new ReflectionMethod(OrderController::class, 'addProductAction'),
            AdminSecurity::class
        );
        self::assertInstanceOf(AdminSecurity::class, $annotation);

        $expression = (string) $annotation->getExpression();
        self::assertStringContainsString("is_granted('update'", $expression);
        self::assertStringNotContainsString("is_granted('create'", $expression);
    }
}
