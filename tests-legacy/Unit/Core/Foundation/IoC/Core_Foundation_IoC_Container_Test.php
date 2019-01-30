<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace LegacyTests\Unit\Core\Foundation\IoC;

use LegacyTests\Unit\Core\Foundation\IoC\Fixtures\DepBuiltByClosure;
use LegacyTests\Unit\Core\Foundation\IoC\Fixtures\Dummy;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Foundation\IoC\Container;

class Core_Foundation_IoC_Container_Test extends TestCase
{
    /**
     * @var Container
     */
    private $container;

    protected function setUp()
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

    /**
     */
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
        $this->container->bind('dummy', 'LegacyTests\Unit\Core\Foundation\IoC\Fixtures\Dummy');

        $this->assertEquals('LegacyTests\Unit\Core\Foundation\IoC\Fixtures\Dummy', get_class(
            $this->container->make('dummy')
        ));
    }

    public function testMakeWithoutBind()
    {
        $this->assertEquals('LegacyTests\Unit\Core\Foundation\IoC\Fixtures\Dummy', get_class(
            $this->container->make('LegacyTests\Unit\Core\Foundation\IoC\Fixtures\Dummy')
        ));
    }

    public function testClassesCanBeLoadedWithCustomNamespacePrefix()
    {
        $this->container->aliasNamespace('Fixtures', 'LegacyTests\Unit\Core\Foundation\IoC\Fixtures');

        $this->assertEquals('LegacyTests\Unit\Core\Foundation\IoC\Fixtures\Dummy', get_class(
            $this->container->make('Fixtures:Dummy')
        ));
    }

    /**
     */
    public function testAnAliasCannotBeChanged()
    {
        $this->expectException(\PrestaShop\PrestaShop\Core\Foundation\IoC\Exception::class);

        $this->container->aliasNamespace('Fixtures', 'LegacyTests\Unit\Core\Foundation\IoC\Fixtures');
        $this->container->aliasNamespace('Fixtures', 'LegacyTests\Unit\Core\Foundation\Other');
    }

    public function testDepsAreFetchedAutomagically()
    {
        $this->assertEquals('LegacyTests\Unit\Core\Foundation\IoC\Fixtures\ClassWithDep', get_class(
            $this->container->make('LegacyTests\Unit\Core\Foundation\IoC\Fixtures\ClassWithDep')
        ));
    }

    public function testDepsAreFetchedAutomagicallyWhenDependsOnThingWithADefaultValue()
    {
        $this->assertEquals('LegacyTests\Unit\Core\Foundation\IoC\Fixtures\ClassWithDepAndDefault', get_class(
            $this->container->make('LegacyTests\Unit\Core\Foundation\IoC\Fixtures\ClassWithDepAndDefault')
        ));
    }

    /**
     */
    public function testUnbuildableNotBuilt()
    {
        $this->expectException(\PrestaShop\PrestaShop\Core\Foundation\IoC\Exception::class);

        $this->container->make('LegacyTests\Unit\Core\Foundation\IoC\Fixtures\UnBuildable');
    }

    /**
     */
    public function testNonExistingClassNotBuilt()
    {
        $this->expectException(\PrestaShop\PrestaShop\Core\Foundation\IoC\Exception::class);

        $this->container->make('LegacyTests\Unit\Core\Foundation\IoC\Fixtures\AClassThatDoesntExistAtAll');
    }

    /**
     */
    public function testDependencyLoopDoesntCrashContainer()
    {
        $this->expectException(\PrestaShop\PrestaShop\Core\Foundation\IoC\Exception::class);

        /**
         * CycleA depends on CycleB,
         * CycleB depends on CycleA
         */
        $this->container->make('LegacyTests\Unit\Core\Foundation\IoC\Fixtures\CycleA');
    }

    public function testCanBuildClassWhoseDependencyIsBuitByClosure()
    {
        $this->container->bind(
            'LegacyTests\Unit\Core\Foundation\IoC\Fixtures\DepBuiltByClosure',
            function () {
                return new DepBuiltByClosure(42);
            }
        );

        $instance = $this->container->make(
            'LegacyTests\Unit\Core\Foundation\IoC\Fixtures\ClassDependingOnClosureBuiltDep'
        );
        $this->assertEquals(42, $instance->getDep()->getValue());
    }

    /**
     * data provider for test_container_can_bind_values_directly
     */
    public function valuesToBind()
    {
        return array(
            array(new Dummy()),
            array(42),
            array(array(1, 2, 3)),
        );
    }

    /**
     * @dataProvider valuesToBind
     */
    public function testContainerCanBindValuesDirectly($value)
    {
        $this->container->bind('value', $value);
        $this->assertSame($value, $this->container->make('value'));
    }

    /**
     */
    public function testContainerDoesntBindStringsAsLiteralValues()
    {
        $this->expectException(\Exception::class);

        $this->container->bind('value', 'a string which is not a class name');
        $this->container->make('value');
    }
}
