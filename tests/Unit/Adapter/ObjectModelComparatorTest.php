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
