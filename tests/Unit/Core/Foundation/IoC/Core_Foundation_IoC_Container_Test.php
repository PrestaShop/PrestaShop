<?php
/**
 * 2007-2017 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Core\Foundation\IoC;

use Exception;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Foundation\IoC\Container;
use Tests\Unit\Core\Foundation\IoC\Fixtures\Dummy;
use Tests\Unit\Core\Foundation\IoC\Fixtures\DepBuiltByClosure;

class Core_Foundation_IoC_Container_Test extends TestCase
{
    /**
     * @var Container
     */
    private $container;

    public function setUp()
    {
        $this->container = new Container();
    }

    public function test_bind_by_closure()
    {
        $this->container->bind('foo', function () {
            return 'FOO';
        });

        $this->assertEquals('FOO', $this->container->make('foo'));
    }

    /**
     * @expectedException \PrestaShop\PrestaShop\Core\Foundation\IoC\Exception
     */
    public function test_cannot_bind_the_same_service_twice()
    {
        $this->container->bind('foo', function () {});
        $this->container->bind('foo', function () {});
    }

    public function test_bind_by_closure_instance_not_shared_by_default()
    {
        $this->container->bind('different', function () {
            return new Dummy;
        });

        $first = $this->container->make('different');
        $second = $this->container->make('different');

        $this->assertNotSame($first, $second);
    }

    public function test_bind_by_closure_instance_shared_if_explicitely_required()
    {
        $this->container->bind('same', function () {
            return new Dummy;
        }, true);

        $first = $this->container->make('same');
        $second = $this->container->make('same');

        $this->assertSame($first, $second);
    }

    public function test_bind_className()
    {
        $this->container->bind('dummy', 'Tests\Unit\Core\Foundation\IoC\Fixtures\Dummy');

        $this->assertEquals('Tests\Unit\Core\Foundation\IoC\Fixtures\Dummy', get_class(
            $this->container->make('dummy')
        ));
    }

    public function test_make_without_bind()
    {
        $this->assertEquals('Tests\Unit\Core\Foundation\IoC\Fixtures\Dummy', get_class(
            $this->container->make('Tests\Unit\Core\Foundation\IoC\Fixtures\Dummy')
        ));
    }

    public function test_classes_can_be_loaded_with_custom_namespace_prefix()
    {
        $this->container->aliasNamespace('Fixtures', 'Tests\Unit\Core\Foundation\IoC\Fixtures');

        $this->assertEquals('Tests\Unit\Core\Foundation\IoC\Fixtures\Dummy', get_class(
            $this->container->make('Fixtures:Dummy')
        ));
    }

    /**
     * @expectedException \PrestaShop\PrestaShop\Core\Foundation\IoC\Exception
     */
    public function test_an_alias_cannot_be_changed()
    {
        $this->container->aliasNamespace('Fixtures', 'Tests\Unit\Core\Foundation\IoC\Fixtures');
        $this->container->aliasNamespace('Fixtures', 'Tests\Unit\Core\Foundation\Other');
    }

    public function test_deps_are_fetched_automagically()
    {
        $this->assertEquals('Tests\Unit\Core\Foundation\IoC\Fixtures\ClassWithDep', get_class(
            $this->container->make('Tests\Unit\Core\Foundation\IoC\Fixtures\ClassWithDep')
        ));
    }

    public function test_deps_are_fetched_automagically_When_dependsOnThingWithADefaultValue()
    {
        $this->assertEquals('Tests\Unit\Core\Foundation\IoC\Fixtures\ClassWithDepAndDefault', get_class(
            $this->container->make('Tests\Unit\Core\Foundation\IoC\Fixtures\ClassWithDepAndDefault')
        ));
    }

    /**
     * @expectedException \PrestaShop\PrestaShop\Core\Foundation\IoC\Exception
     */
    public function test_unbuildable_not_built()
    {
        $this->container->make('Tests\Unit\Core\Foundation\IoC\Fixtures\UnBuildable');
    }

    /**
     * @expectedException \PrestaShop\PrestaShop\Core\Foundation\IoC\Exception
     */
    public function test_non_existing_class_not_built()
    {
        $this->container->make('Tests\Unit\Core\Foundation\IoC\Fixtures\AClassThatDoesntExistAtAll');
    }

    /**
     * @expectedException \PrestaShop\PrestaShop\Core\Foundation\IoC\Exception
     */
    public function test_dependency_loop_doesnt_crash_container()
    {
        /**
         * CycleA depends on CycleB,
         * CycleB depends on CycleA
         */
        $this->container->make('Tests\Unit\Core\Foundation\IoC\Fixtures\CycleA');
    }

    public function test_can_build_class_whose_dependency_is_buit_by_closure()
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
        return array(
            array(new Dummy),
            array(42),
            array(array(1, 2, 3))
        );
    }

    /**
     * @dataProvider valuesToBind
     */
    public function test_container_can_bind_values_directly($value)
    {
        $this->container->bind('value', $value);
        $this->assertSame($value, $this->container->make('value'));
    }

    /**
     * @expectedException Exception
     */
    public function test_container_doesnt_bind_strings_as_literal_values()
    {
        $this->container->bind('value', 'a string which is not a class name');
        $this->container->make('value');
    }
}
