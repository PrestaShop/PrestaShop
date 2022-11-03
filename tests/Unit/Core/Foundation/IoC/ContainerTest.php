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

namespace Tests\Unit\Core\Foundation\IoC;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Foundation\IoC\Container;
use Tests\Unit\Core\Foundation\IoC\Fixtures\DepBuiltByClosure;
use Tests\Unit\Core\Foundation\IoC\Fixtures\Dummy;

class ContainerTest extends TestCase
{
    /**
     * @var Container
     */
    private $container;

    protected function setUp(): void
    {
        $this->container = new Container();
    }

    public function testBindByClosure()
    {
        $this->container->bind('foo', function () {
            return 'FOO';
        });

        $this->assertEquals('FOO', $this->container->make('foo'));
    }

    public function testCannotBindTheSameServiceTwice()
    {
        $this->expectException(\PrestaShop\PrestaShop\Core\Foundation\IoC\Exception::class);

        $this->container->bind('foo', function () {});
        $this->container->bind('foo', function () {});
    }

    public function testBindByClosureInstanceNotSharedByDefault()
    {
        $this->container->bind('different', function () {
            return new Dummy();
        });

        $first = $this->container->make('different');
        $second = $this->container->make('different');

        $this->assertNotSame($first, $second);
    }

    public function testBindByClosureInstanceSharedIfExplicitelyRequired()
    {
        $this->container->bind('same', function () {
            return new Dummy();
        }, true);

        $first = $this->container->make('same');
        $second = $this->container->make('same');

        $this->assertSame($first, $second);
    }

    public function testBindClassName()
    {
        $this->container->bind('dummy', 'Tests\Unit\Core\Foundation\IoC\Fixtures\Dummy');

        $this->assertEquals('Tests\Unit\Core\Foundation\IoC\Fixtures\Dummy', get_class(
            $this->container->make('dummy')
        ));
    }

    public function testMakeWithoutBind()
    {
        $this->assertEquals('Tests\Unit\Core\Foundation\IoC\Fixtures\Dummy', get_class(
            $this->container->make('Tests\Unit\Core\Foundation\IoC\Fixtures\Dummy')
        ));
    }

    public function testClassesCanBeLoadedWithCustomNamespacePrefix()
    {
        $this->container->aliasNamespace('Fixtures', 'Tests\Unit\Core\Foundation\IoC\Fixtures');

        $this->assertEquals('Tests\Unit\Core\Foundation\IoC\Fixtures\Dummy', get_class(
            $this->container->make('Fixtures:Dummy')
        ));
    }

    public function testAnAliasCannotBeChanged()
    {
        $this->expectException(\PrestaShop\PrestaShop\Core\Foundation\IoC\Exception::class);

        $this->container->aliasNamespace('Fixtures', 'Tests\Unit\Core\Foundation\IoC\Fixtures');
        $this->container->aliasNamespace('Fixtures', 'Tests\Unit\Core\Foundation\Other');
    }

    public function testDepsAreFetchedAutomagically()
    {
        $this->assertEquals('Tests\Unit\Core\Foundation\IoC\Fixtures\ClassWithDep', get_class(
            $this->container->make('Tests\Unit\Core\Foundation\IoC\Fixtures\ClassWithDep')
        ));
    }

    public function testDepsAreFetchedAutomagicallyWhenDependsOnThingWithADefaultValue()
    {
        $this->assertEquals('Tests\Unit\Core\Foundation\IoC\Fixtures\ClassWithDepAndDefault', get_class(
            $this->container->make('Tests\Unit\Core\Foundation\IoC\Fixtures\ClassWithDepAndDefault')
        ));
    }

    public function testUnbuildableNotBuilt()
    {
        $this->expectException(\PrestaShop\PrestaShop\Core\Foundation\IoC\Exception::class);

        $this->container->make('Tests\Unit\Core\Foundation\IoC\Fixtures\UnBuildable');
    }

    public function testNonExistingClassNotBuilt()
    {
        $this->expectException(\PrestaShop\PrestaShop\Core\Foundation\IoC\Exception::class);

        $this->container->make('Tests\Unit\Core\Foundation\IoC\Fixtures\AClassThatDoesntExistAtAll');
    }

    public function testDependencyLoopDoesntCrashContainer()
    {
        $this->expectException(\PrestaShop\PrestaShop\Core\Foundation\IoC\Exception::class);

        /*
         * CycleA depends on CycleB,
         * CycleB depends on CycleA
         */
        $this->container->make('Tests\Unit\Core\Foundation\IoC\Fixtures\CycleA');
    }

    public function testCanBuildClassWhoseDependencyIsBuitByClosure()
    {
        $this->container->bind(
            'Tests\Unit\Core\Foundation\IoC\Fixtures\DepBuiltByClosure',
            function () {
                return new DepBuiltByClosure(42);
            }
        );

        $instance = $this->container->make(
            'Tests\Unit\Core\Foundation\IoC\Fixtures\ClassDependingOnClosureBuiltDep'
        );
        $this->assertEquals(42, $instance->getDep()->getValue());
    }

    /**
     * data provider for test_container_can_bind_values_directly
     */
    public function valuesToBind()
    {
        return [
            [new Dummy()],
            [42],
            [[1, 2, 3]],
        ];
    }

    /**
     * @dataProvider valuesToBind
     */
    public function testContainerCanBindValuesDirectly($value)
    {
        $this->container->bind('value', $value);
        $this->assertSame($value, $this->container->make('value'));
    }

    public function testContainerDoesntBindStringsAsLiteralValues()
    {
        $this->expectException(\Exception::class);

        $this->container->bind('value', 'a string which is not a class name');
        $this->container->make('value');
    }
}
