<?php

namespace PrestaShop\PrestaShop\Tests\Unit\Core\Foundation\IoC;

use PHPUnit_Framework_TestCase;

use Core_Foundation_IoC_Container as Container;

use PrestaShop\PrestaShop\Tests\Unit\Core\Foundation\IoC\Fixtures\Dummy;
use PrestaShop\PrestaShop\Tests\Unit\Core\Foundation\IoC\Fixtures\ClassWithDep;
use PrestaShop\PrestaShop\Tests\Unit\Core\Foundation\IoC\Fixtures\DepBuiltByClosure;

class Core_Foundation_IoC_Container_Test extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->container = new Container;
    }

    public function test_bind_by_closure()
    {
        $this->container->bind('foo', function () {
            return 'FOO';
        });

        $this->assertEquals('FOO', $this->container->make('foo'));
    }

    /**
     * @expectedException Core_Foundation_IoC_Exception
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
        $this->container->bind('dummy', 'PrestaShop\PrestaShop\Tests\Unit\Core\Foundation\IoC\Fixtures\Dummy');

        $this->assertEquals('PrestaShop\PrestaShop\Tests\Unit\Core\Foundation\IoC\Fixtures\Dummy', get_class(
            $this->container->make('dummy')
        ));
    }

    public function test_make_without_bind()
    {
        $this->assertEquals('PrestaShop\PrestaShop\Tests\Unit\Core\Foundation\IoC\Fixtures\Dummy', get_class(
            $this->container->make('PrestaShop\PrestaShop\Tests\Unit\Core\Foundation\IoC\Fixtures\Dummy')
        ));
    }

    public function test_classes_can_be_loaded_with_custom_namespace_prefix()
    {
        $this->container->aliasNamespace('Fixtures', 'PrestaShop\PrestaShop\Tests\Unit\Core\Foundation\IoC\Fixtures');

        $this->assertEquals('PrestaShop\PrestaShop\Tests\Unit\Core\Foundation\IoC\Fixtures\Dummy', get_class(
            $this->container->make('Fixtures:Dummy')
        ));
    }

    /**
     * @expectedException Core_Foundation_IoC_Exception
     */
    public function test_an_alias_cannot_be_changed()
    {
        $this->container->aliasNamespace('Fixtures', 'PrestaShop\PrestaShop\Tests\Unit\Core\Foundation\IoC\Fixtures');
        $this->container->aliasNamespace('Fixtures', 'PrestaShop\PrestaShop\Tests\Unit\Core\Foundation\Other');
    }

    public function test_deps_are_fetched_automagically()
    {
        $this->assertEquals('PrestaShop\PrestaShop\Tests\Unit\Core\Foundation\IoC\Fixtures\ClassWithDep', get_class(
            $this->container->make('PrestaShop\PrestaShop\Tests\Unit\Core\Foundation\IoC\Fixtures\ClassWithDep')
        ));
    }

    public function test_deps_are_fetched_automagically_When_dependsOnThingWithADefaultValue()
    {
        $this->assertEquals('PrestaShop\PrestaShop\Tests\Unit\Core\Foundation\IoC\Fixtures\ClassWithDepAndDefault', get_class(
            $this->container->make('PrestaShop\PrestaShop\Tests\Unit\Core\Foundation\IoC\Fixtures\ClassWithDepAndDefault')
        ));
    }

    /**
     * @expectedException Core_Foundation_IoC_Exception
     */
    public function test_unbuildable_not_built()
    {
        $this->container->make('PrestaShop\PrestaShop\Tests\Unit\Core\Foundation\IoC\Fixtures\UnBuildable');
    }

    /**
     * @expectedException Core_Foundation_IoC_Exception
     */
    public function test_non_existing_class_not_built()
    {
        $this->container->make('PrestaShop\PrestaShop\Tests\Unit\Core\Foundation\IoC\Fixtures\AClassThatDoesntExistAtAll');
    }

    /**
     * @expectedException Core_Foundation_IoC_Exception
     */
    public function test_dependency_loop_doesnt_crash_container()
    {
        /**
         * CycleA depends on CycleB,
         * CycleB depends on CycleA
         */
        $this->container->make('PrestaShop\PrestaShop\Tests\Unit\Core\Foundation\IoC\Fixtures\CycleA');
    }

    public function test_can_build_class_whose_dependency_is_buit_by_closure()
    {
        $this->container->bind(
            'PrestaShop\PrestaShop\Tests\Unit\Core\Foundation\IoC\Fixtures\DepBuiltByClosure',
            function () {
                return new DepBuiltByClosure(42);
            }
        );

        $instance = $this->container->make(
            'PrestaShop\PrestaShop\Tests\Unit\Core\Foundation\IoC\Fixtures\ClassDependingOnClosureBuiltDep'
        );
        $this->assertEquals(42, $instance->getDep()->getValue());
    }
}
