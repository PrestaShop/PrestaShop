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

namespace Tests\Unit\Core\Util;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Util\ArrayFinder;

class ArrayFinderTest extends TestCase
{
    /** @var array */
    private $sampleArray;

    /** @var ArrayFinder */
    private $arrayFinder;

    /**
     * @before
     */
    public function setupComplexArrayFinder(): void
    {
        $this->sampleArray = [
            'hello',
            'world',
            'a' => [
                'b' => [
                    'c' => 'end',
                ],
                'value_without_key',
                [
                    'value_without_key_bis',
                ],
            ],
            'here' => 'is_a_key',
        ];
        $this->arrayFinder = new ArrayFinder($this->sampleArray);
    }

    public function testArrayAccessReading(): void
    {
        $this->assertEquals('hello', $this->arrayFinder[0]);
        $this->assertEquals('world', $this->arrayFinder[1]);
    }

    public function testArrayAccessInsertValueWithoutKey(): void
    {
        $this->arrayFinder[] = 't';
        $this->assertEquals('t', $this->arrayFinder[2]);
    }

    public function testArrayAccessInsertValueWithKey(): void
    {
        $this->arrayFinder['last'] = '!';
        $this->assertEquals('!', $this->arrayFinder['last']);
    }

    public function testArrayAccessUnset(): void
    {
        unset($this->arrayFinder[1]);
        $this->assertNull($this->arrayFinder[1]);
    }

    public function testArrayAccessIsset(): void
    {
        $this->assertEquals(true, isset($this->arrayFinder['here']));
        $this->assertEquals(false, isset($this->arrayFinder['out']));
    }

    public function testArrayAccessIssetRecursive(): void
    {
        $this->assertEquals(true, isset($this->arrayFinder['a.b']));
        $this->assertEquals(true, isset($this->arrayFinder['a.b.c']));
    }

    public function testArrayAccessIssetRecursiveOnNonExistingPath(): void
    {
        $this->assertEquals(false, isset($this->arrayFinder['a.b.c.d.e']));
    }

    public function testSimpleGet(): void
    {
        $this->assertEquals('hello', $this->arrayFinder->get('0'));
        $this->assertEquals('is_a_key', $this->arrayFinder->get('here'));
    }

    public function testGetWithStringPath(): void
    {
        $this->assertEquals('end', $this->arrayFinder->get('a.b.c'));
    }

    public function testGetRoot(): void
    {
        $this->assertEquals('hello', $this->arrayFinder->get('[]'));
    }

    public function testGetWithoutArgument(): void
    {
        $this->assertEquals($this->sampleArray, $this->arrayFinder->get());
    }

    public function testGetWithIndexPath(): void
    {
        $this->assertEquals('value_without_key', $this->arrayFinder->get('a.0'));
        $this->assertEquals('value_without_key_bis', $this->arrayFinder->get('a.1.0'));
    }

    public function testArrayAccessReadingWithPath(): void
    {
        $this->assertEquals('end', $this->arrayFinder['a.b.c']);
    }

    public function testGetReturnNullIfDoesNotExist(): void
    {
        $this->assertNull($this->arrayFinder->get('a.b.d'));
    }

    public function testSetCorrectlyAddValueToRoot(): void
    {
        $this->arrayFinder->set('new', 'yeah');
        $this->assertEquals('yeah', $this->arrayFinder['new']);
    }

    public function testSetCorrectlyAddValueIfPathExist(): void
    {
        $this->arrayFinder->set('a.b.c', 'c_replaced');
        $this->assertEquals('c_replaced', $this->arrayFinder['a.b.c']);
    }

    public function testSetCorrectlyReturnTheInstance(): void
    {
        $this->assertInstanceOf('\PrestaShop\PrestaShop\Core\Util\ArrayFinder', $this->arrayFinder->set('a', 'b'));
    }

    public function testSetCorrectlyAddValueIfPathDoesNotExist(): void
    {
        $this->arrayFinder->set('d.e.f', 'f_setted');
        $this->assertEquals('f_setted', $this->arrayFinder['d.e.f']);
    }

    public function testSetCorrectlyAddAnArray(): void
    {
        $this->arrayFinder->set('b', ['it' => ['works' => ['cool']]]);
        $this->assertEquals('cool', $this->arrayFinder['b.it.works.0']);
    }

    public function testSetCorrectlyReplaceArray(): void
    {
        $this->arrayFinder->set('a', 'value');
        $this->assertEquals('value', $this->arrayFinder['a']);
    }

    public function testSetEmptyValue(): void
    {
        $this->arrayFinder->set('a', null);
        $this->assertNull($this->arrayFinder['a']);
    }

    public function testSetEmptyArray(): void
    {
        $this->arrayFinder->set('this.is.empty', []);
        $this->assertEmpty($this->arrayFinder['this.is.empty']);
    }

    public function testCount(): void
    {
        $this->assertEquals(4, count($this->arrayFinder));
    }

    public function testArrayAccessReference(): void
    {
        $this->arrayFinder['access'] = 'work';
        $this->assertEquals('work', $this->arrayFinder->get('access'));
    }

    public function testArrayAccessUnsetWithPath(): void
    {
        unset($this->arrayFinder['a.b']);
        $this->assertNull($this->arrayFinder['a.b']);
    }

    public function testArrayAccessUnsetWithInt(): void
    {
        unset($this->arrayFinder['a.1']);
        $this->assertNull($this->arrayFinder['a.1']);
    }

    public function testCountOnFirstAccess(): void
    {
        $this->assertCount(1, $this->arrayFinder['a.b']);
    }

    public function testArrayAccessAddValueToRoot(): void
    {
        $arrayFinder = new ArrayFinder();
        $arrayFinder[] = 'added';

        $this->assertCount(1, $arrayFinder);
        $this->assertEquals('added', $arrayFinder[0]);
    }

    public function testGetWithoutParamWillReturnAllTheArray(): void
    {
        $content = $this->arrayFinder->get();

        $this->assertEquals('end', $content['a']['b']['c']);
    }

    public function testGetAcceptsADefaultArgument(): void
    {
        // ensure the default value is not returned when
        // the array does have the key
        $this->assertEquals(
            'is defined',
            (new ArrayFinder(['some' => ['key' => 'is defined']]))->get('some.key', 'default value')
        );

        // ensure that when the key is not defined, the default
        // value is used
        $this->assertEquals(
            'default value',
            (new ArrayFinder([]))->get('some.key', 'default value')
        );
    }
}
