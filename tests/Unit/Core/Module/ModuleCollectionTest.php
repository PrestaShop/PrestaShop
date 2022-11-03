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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Module;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Module\Module;
use PrestaShop\PrestaShop\Core\Module\ModuleCollection;
use PrestaShopException;
use stdClass;

class ModuleCollectionTest extends TestCase
{
    private $moduleCollection;

    public function setUp(): void
    {
        $this->moduleCollection = ModuleCollection::createFrom([
            $this->createMock(Module::class),
            $this->createMock(Module::class),
        ]);
    }

    public function testNewModuleCollection(): void
    {
        new ModuleCollection();
        $validArray = [
            $this->createMock(Module::class),
            $this->createMock(Module::class),
        ];
        new ModuleCollection($validArray);
        $this->expectException(PrestaShopException::class);
        $invalidArray = [
            [],
            [],
        ];
        new ModuleCollection($invalidArray);
    }

    public function testCreateModuleCollection(): void
    {
        $validArray = [
            $this->createMock(Module::class),
            $this->createMock(Module::class),
        ];
        ModuleCollection::createFrom($validArray);
        $this->expectException(PrestaShopException::class);
        $invalidArray = [
            [],
            [],
        ];
        /* @phpstan-ignore-next-line */
        ModuleCollection::createFrom($invalidArray);
    }

    public function testCount(): void
    {
        $moduleCollection = new ModuleCollection();
        $this->assertSame(0, count($moduleCollection));

        $moduleCollection = ModuleCollection::createFrom([
            $this->createMock(Module::class),
            $this->createMock(Module::class),
        ]);
        $this->assertSame(2, count($moduleCollection));
    }

    public function testArrayAccess(): void
    {
        $this->assertTrue(isset($this->moduleCollection[0]));
        $this->assertFalse(isset($this->moduleCollection[2]));

        $this->assertFalse(empty($this->moduleCollection[0]));
        $this->assertTrue(empty($this->moduleCollection[2]));

        unset($this->moduleCollection[1]);
        $this->assertFalse(isset($this->moduleCollection[1]));

        $this->moduleCollection[] = $this->createMock(Module::class);
        $this->assertSame(2, count($this->moduleCollection));

        $this->expectException(PrestaShopException::class);
        $this->moduleCollection[] = new stdClass();
    }

    public function testIterator(): void
    {
        $i = 0;
        foreach ($this->moduleCollection as $module) {
            ++$i;
        }
        $this->assertSame(2, $i);
    }

    public function testFilter(): void
    {
        $halfCallback = new class() {
            private static $i = 0;

            public function __invoke()
            {
                return ++self::$i % 2 === 0;
            }
        };
        $halfCollection = $this->moduleCollection->filter($halfCallback);
        $this->assertSame(1, count($halfCollection));
    }
}
