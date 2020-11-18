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

namespace Tests\Unit\Core\Data;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Data\AbstractTypedCollection;

class AbstractTypedCollectionTest extends TestCase
{
    public function testConstructor()
    {
        $collection = new TestCollection();
        $this->assertNotNull($collection);
        $this->assertCount(0, $collection);

        $collection = new TestCollection([
            new CollectionTestElement(),
            new CollectionTestElementChild(),
        ]);
        $this->assertNotNull($collection);
        $this->assertCount(2, $collection);
    }

    /**
     * @expectedException \PrestaShop\PrestaShop\Core\Exception\TypeException
     * @expectedExceptionMessage Invalid element type Tests\Unit\Core\Data\InvalidCollectionTestElement, expected Tests\Unit\Core\Data\CollectionTestElement
     */
    public function testInvalidConstructor()
    {
        new TestCollection([
            new InvalidCollectionTestElement(),
        ]);
    }

    /**
     * @expectedException \PrestaShop\PrestaShop\Core\Exception\TypeException
     * @expectedExceptionMessage Invalid element type Tests\Unit\Core\Data\InvalidCollectionTestElement, expected Tests\Unit\Core\Data\CollectionTestElement
     */
    public function testPartialInvalidConstructor()
    {
        new TestCollection([
            new CollectionTestElement(),
            new InvalidCollectionTestElement(),
        ]);
    }

    public function testRemoveElement()
    {
        $element = new CollectionTestElement();
        $collection = new TestCollection([$element]);
        $this->assertNotNull($collection);
        $this->assertCount(1, $collection);

        $elementRemoved = $collection->removeElement($element);
        $this->assertCount(0, $collection);
        $this->assertTrue($elementRemoved);

        $elementRemoved = $collection->removeElement($element);
        $this->assertCount(0, $collection);
        $this->assertFalse($elementRemoved);
    }

    /**
     * @expectedException \PrestaShop\PrestaShop\Core\Exception\TypeException
     * @expectedExceptionMessage Invalid element type Tests\Unit\Core\Data\InvalidCollectionTestElement, expected Tests\Unit\Core\Data\CollectionTestElement
     */
    public function testInvalidRemoveElement()
    {
        $element = new CollectionTestElement();
        $collection = new TestCollection([$element]);
        $this->assertNotNull($collection);
        $this->assertCount(1, $collection);

        $collection->removeElement(new InvalidCollectionTestElement());
    }

    public function testOffsetSet()
    {
        $collection = new TestCollection();
        $this->assertNotNull($collection);
        $this->assertCount(0, $collection);

        $element = new CollectionTestElement();
        $collection->offsetSet(null, $element);
        $this->assertEquals($element, $collection->offsetGet(0));

        $collection->offsetSet(1, $element);
        $this->assertEquals($element, $collection->offsetGet(1));
    }

    /**
     * @expectedException \PrestaShop\PrestaShop\Core\Exception\TypeException
     * @expectedExceptionMessage Invalid element type Tests\Unit\Core\Data\InvalidCollectionTestElement, expected Tests\Unit\Core\Data\CollectionTestElement
     */
    public function testInvalidOffsetSet()
    {
        $collection = new TestCollection();
        $this->assertNotNull($collection);
        $this->assertCount(0, $collection);

        $collection->offsetSet(0, new InvalidCollectionTestElement());
    }

    public function testContains()
    {
        $collection = new TestCollection();
        $this->assertNotNull($collection);
        $this->assertCount(0, $collection);

        $element = new CollectionTestElement();
        $this->assertFalse($collection->contains($element));

        $collection->add($element);
        $this->assertTrue($collection->contains($element));
    }

    /**
     * @expectedException \PrestaShop\PrestaShop\Core\Exception\TypeException
     * @expectedExceptionMessage Invalid element type Tests\Unit\Core\Data\InvalidCollectionTestElement, expected Tests\Unit\Core\Data\CollectionTestElement
     */
    public function testInvalidContains()
    {
        $collection = new TestCollection();
        $this->assertNotNull($collection);
        $this->assertCount(0, $collection);

        $element = new InvalidCollectionTestElement();
        $collection->contains($element);
    }

    public function testIndexOf()
    {
        $collection = new TestCollection();
        $this->assertNotNull($collection);
        $this->assertCount(0, $collection);

        $element = new CollectionTestElement();
        $this->assertFalse($collection->indexOf($element));

        $collection->set('element', $element);
        $this->assertCount(1, $collection);
        $this->assertEquals('element', $collection->indexOf($element));

        $childElement = new CollectionTestElementChild();
        $collection->add($childElement);
        $this->assertCount(2, $collection);
        $this->assertEquals(0, $collection->indexOf($childElement));
        $this->assertEquals('element', $collection->indexOf($element));
    }

    /**
     * @expectedException \PrestaShop\PrestaShop\Core\Exception\TypeException
     * @expectedExceptionMessage Invalid element type Tests\Unit\Core\Data\InvalidCollectionTestElement, expected Tests\Unit\Core\Data\CollectionTestElement
     */
    public function testInvalidIndexOf()
    {
        $collection = new TestCollection();
        $this->assertNotNull($collection);
        $this->assertCount(0, $collection);

        $collection->indexOf(new InvalidCollectionTestElement());
    }

    public function testSet()
    {
        $collection = new TestCollection();
        $this->assertNotNull($collection);
        $this->assertCount(0, $collection);

        $element = new CollectionTestElement();
        $collection->set('element', $element);
        $this->assertCount(1, $collection);
        $retrievedElement = $collection->get('element');
        $this->assertEquals($element, $retrievedElement);
        $this->assertEquals($element, $collection['element']);
    }

    /**
     * @expectedException \PrestaShop\PrestaShop\Core\Exception\TypeException
     * @expectedExceptionMessage Invalid element type Tests\Unit\Core\Data\InvalidCollectionTestElement, expected Tests\Unit\Core\Data\CollectionTestElement
     */
    public function testInvalidSet()
    {
        $collection = new TestCollection();
        $this->assertNotNull($collection);
        $this->assertCount(0, $collection);

        $collection->set('invalidElement', new InvalidCollectionTestElement());
    }

    public function testAdd()
    {
        $collection = new TestCollection();
        $this->assertNotNull($collection);
        $this->assertCount(0, $collection);

        $element = new CollectionTestElement();
        $collection->add($element);
        $this->assertCount(1, $collection);
        $this->assertEquals($element, $collection->get(0));
        $this->assertEquals($element, $collection[0]);
    }

    /**
     * @expectedException \PrestaShop\PrestaShop\Core\Exception\TypeException
     * @expectedExceptionMessage Invalid element type Tests\Unit\Core\Data\InvalidCollectionTestElement, expected Tests\Unit\Core\Data\CollectionTestElement
     */
    public function testInvalidAdd()
    {
        $collection = new TestCollection();
        $this->assertNotNull($collection);
        $this->assertCount(0, $collection);

        $collection->add(new InvalidCollectionTestElement());
    }

    public function testAddMock()
    {
        $collection = new TestCollection();
        $this->assertNotNull($collection);
        $this->assertCount(0, $collection);

        $elementMock = $this->getMockBuilder(CollectionTestElement::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $collection->add($elementMock);
        $this->assertCount(1, $collection);
        $this->assertEquals($elementMock, $collection->get(0));
        $this->assertEquals($elementMock, $collection[0]);
    }

    /**
     * @expectedException \PrestaShop\PrestaShop\Core\Exception\TypeException
     * @expectedExceptionMessage Invalid element type string, expected Tests\Unit\Core\Data\CollectionTestElement
     */
    public function testInvalidString()
    {
        $collection = new TestCollection();
        $this->assertNotNull($collection);
        $this->assertCount(0, $collection);

        $collection->add('test');
    }
}

class TestCollection extends AbstractTypedCollection
{
    /**
     * {@inheritdoc}
     */
    protected function getType()
    {
        return CollectionTestElement::class;
    }
}

class InvalidCollectionTestElement
{
}

class CollectionTestElement
{
}

class CollectionTestElementChild extends CollectionTestElement
{
}
