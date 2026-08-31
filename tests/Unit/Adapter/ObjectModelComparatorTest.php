<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter;

use Category;
use ObjectModel;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\ObjectModelComparator;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\ExtraProperty\Value\ExtraPropertiesBag;
use Product;
use Tests\Resources\classes\ExampleObjectModel;

final class ObjectModelComparatorTest extends TestCase
{
    /**
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public function testObjectComparatorWithDiff(): void
    {
        $oldObject = $this->getExampleObjectModel();
        $newObject = clone $oldObject;

        $newObject->int_field = 5;
        $newObject->float_field = 3.5;
        $newObject->bool_field = 1;
        $newObject->id_shop_list = [1, 3];
        $newObject->string_multilang_field = [
            1 => 'lang_1_new_value',
        ];

        $comparator = new ObjectModelComparator($oldObject, $newObject);
        $diff = $comparator->getDiff();

        $expectedDiff = [
            'int_field' => ['old' => 10, 'new' => 5],
            'float_field' => ['old' => 14.5, 'new' => 3.5],
            'string_multilang_field' => [
                'old' => [1 => 'lang_1'],
                'new' => [1 => 'lang_1_new_value'],
            ],
            'id_shop_list' => [
                'old' => [1, 2],
                'new' => [1, 3],
            ],
        ];

        $this->assertEquals($expectedDiff, $diff);
    }

    /**
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public function testObjectComparatorWithoutDiff(): void
    {
        $oldObject = $this->getExampleObjectModel();
        $newObject = clone $oldObject;
        $comparator = new ObjectModelComparator($oldObject, $newObject);

        $this->assertFalse($comparator->hasChanges());
    }

    /**
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public function testObjectComparatorNoStrict(): void
    {
        $oldObject = $this->getExampleObjectModel();
        $newObject = clone $oldObject;

        $newObject->int_field = '10';
        $newObject->bool_field = 1;
        $newObject->float_field = '14.5';

        $comparator = new ObjectModelComparator($oldObject, $newObject);

        $this->assertFalse($comparator->hasChanges());
    }

    /**
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public function testObjectComparatorException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Only objects of the same class can be compared.');

        new ObjectModelComparator(
            $this->getMockObjectModel(Product::class),
            $this->getMockObjectModel(Category::class)
        );
    }

    /**
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public function testObjectComparatorWithExtraPropertiesDiff(): void
    {
        $storedValues = ['mymodule' => ['my_field' => 'stored_value']];
        $oldObject = $this->getExampleObjectModelWithExtraProperties(new ExtraPropertiesBag(static fn (): array => $storedValues));
        $newObject = $this->getExampleObjectModelWithExtraProperties(new ExtraPropertiesBag(static fn (): array => $storedValues));

        $newObject->extra_properties['mymodule']['my_field'] = 'new_value';

        $comparator = new ObjectModelComparator($oldObject, $newObject);

        $this->assertTrue($comparator->hasChanges());
        $this->assertSame(
            [
                'old' => ['mymodule' => ['my_field' => 'stored_value']],
                'new' => ['mymodule' => ['my_field' => 'new_value']],
            ],
            $comparator->getDiff()['extra_properties']
        );
    }

    /**
     * An extra property written with its already stored value must not be reported as a change.
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public function testObjectComparatorWithExtraPropertyModifiedWithSameValue(): void
    {
        $storedValues = ['mymodule' => ['my_field' => 'stored_value']];
        $oldObject = $this->getExampleObjectModelWithExtraProperties(new ExtraPropertiesBag(static fn (): array => $storedValues));
        $newObject = $this->getExampleObjectModelWithExtraProperties(new ExtraPropertiesBag(static fn (): array => $storedValues));

        $newObject->extra_properties['mymodule']['my_field'] = 'stored_value';

        $comparator = new ObjectModelComparator($oldObject, $newObject);

        $this->assertFalse($comparator->hasChanges());
        $this->assertArrayNotHasKey('extra_properties', $comparator->getDiff());
    }

    /**
     * Without any pending extra property write, the comparison must not trigger
     * the lazy load of either bag (no database read in real conditions).
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public function testObjectComparatorDoesNotLoadExtraPropertiesWhenNothingWasWritten(): void
    {
        $loaderCalls = 0;
        $loader = function () use (&$loaderCalls): array {
            ++$loaderCalls;

            return [];
        };

        $oldObject = $this->getExampleObjectModelWithExtraProperties(new ExtraPropertiesBag($loader));
        $newObject = $this->getExampleObjectModelWithExtraProperties(new ExtraPropertiesBag($loader));

        $comparator = new ObjectModelComparator($oldObject, $newObject);

        $this->assertFalse($comparator->hasChanges());
        $this->assertSame(0, $loaderCalls);
    }

    /**
     * @param ExtraPropertiesBag $extraPropertiesBag
     *
     * @return ExampleObjectModel
     */
    private function getExampleObjectModelWithExtraProperties(ExtraPropertiesBag $extraPropertiesBag): ExampleObjectModel
    {
        $exampleObjectModel = $this->getMockBuilder(ExampleObjectModel::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $exampleObjectModel->method('__get')->willReturnMap([['extra_properties', $extraPropertiesBag]]);

        return $exampleObjectModel;
    }

    /**
     * @return ExampleObjectModel
     */
    protected function getExampleObjectModel(): ExampleObjectModel
    {
        $exampleObjectModel = $this->getMockObjectModel(ExampleObjectModel::class);

        $exampleObjectModel->int_field = 10;
        $exampleObjectModel->float_field = 14.5;
        $exampleObjectModel->bool_field = true;
        $exampleObjectModel->string_field = 'my_string';
        $exampleObjectModel->id_shop_list = [1, 2];
        $exampleObjectModel->string_multilang_field = [
            1 => 'lang_1',
            2 => 'lang_2',
            3 => 'lang_3',
        ];

        return $exampleObjectModel;
    }

    /**
     * @param string $objectModelClassName
     *
     * @return ObjectModel
     */
    protected function getMockObjectModel(string $objectModelClassName): ObjectModel
    {
        return $this->getMockBuilder($objectModelClassName)
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }
}
