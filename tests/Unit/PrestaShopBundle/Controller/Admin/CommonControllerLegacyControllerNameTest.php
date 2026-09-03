<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Controller\Admin;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Controller\Admin\CommonController;

/**
 * Pins CommonController::legacyControllerFromEntityName() — the permission subject of the
 * extra-property grid toggle, derived server-side from the entity name. The mapping is
 * security-relevant: an override map handles irregular entities ('order' → AdminOrders),
 * the inflector convention covers the rest, and a derived name matching no existing tab is
 * deny-safe (Access::isGranted() grants nothing for unknown subjects).
 */
class CommonControllerLegacyControllerNameTest extends TestCase
{
    /**
     * @dataProvider entityNameProvider
     */
    public function testLegacyControllerFromEntityName(string $entityName, string $expected): void
    {
        $this->assertSame($expected, CommonController::legacyControllerFromEntityName($entityName));
    }

    public static function entityNameProvider(): iterable
    {
        yield 'product' => ['product', 'AdminProducts'];
        // The override map: 'order' is the canonical entity name of the orders table.
        yield 'order (override map)' => ['order', 'AdminOrders'];
        yield 'category (ies pluralization)' => ['category', 'AdminCategories'];
        yield 'address (es pluralization)' => ['address', 'AdminAddresses'];
        // snake_case entities classify correctly (the old hand-rolled pluralizer
        // produced the broken AdminManufacturer_addresss).
        yield 'manufacturer_address' => ['manufacturer_address', 'AdminManufacturerAddresses'];
        // No AdminCombinations tab exists: deny-safe unknown subject (and moot — the
        // combination list is Vue-based, no grid toggle can target it).
        yield 'combination (deny-safe unknown tab)' => ['combination', 'AdminCombinations'];
    }
}
