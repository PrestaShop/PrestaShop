<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Grid\Position;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionDataException;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionDefinition;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionModificationCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionModificationInterface;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionUpdateFactory;

/**
 * Class PositionUpdateFactoryTest.
 */
class PositionUpdateFactoryTest extends TestCase
{
    public function testHandleData()
    {
        $definition = $this->getDefinition();
        $data = ['positions' => [
            ['rowId' => 1, 'oldPosition' => 1, 'newPosition' => 2],
        ]];

        $positionUpdateFactory = $this->getPositionUpdateFactory();
        $positionUpdate = $positionUpdateFactory->buildPositionUpdate($data, $definition);
        /** @var PositionModificationCollectionInterface $collection */
        $collection = $positionUpdate->getPositionModificationCollection();
        $this->assertNotNull($collection);
        $this->assertEquals(1, $collection->count());
        /** @var PositionModificationInterface $positionModification */
        $positionModification = $collection->current();
        $this->assertEquals(1, $positionModification->getId());
        $this->assertEquals(1, $positionModification->getOldPosition());
        $this->assertEquals(2, $positionModification->getNewPosition());
        $this->assertNull($positionUpdate->getParentId());
    }

    public function testHandleDataWithParent()
    {
        $definition = $this->getDefinitionWithParent();
        $data = [
            'positions' => [
                ['rowId' => 1, 'oldPosition' => 1, 'newPosition' => 2],
            ],
            'parentId' => 42,
        ];

        $positionUpdateFactory = $this->getPositionUpdateFactory();
        $positionUpdate = $positionUpdateFactory->buildPositionUpdate($data, $definition);
        /** @var PositionModificationCollectionInterface $collection */
        $collection = $positionUpdate->getPositionModificationCollection();
        $this->assertNotNull($collection);
        $this->assertEquals(1, $collection->count());
        /** @var PositionModificationInterface $positionModification */
        $positionModification = $collection->current();
        $this->assertEquals(1, $positionModification->getId());
        $this->assertEquals(1, $positionModification->getOldPosition());
        $this->assertEquals(2, $positionModification->getNewPosition());
        $this->assertEquals(42, $positionUpdate->getParentId());
    }

    public function testDataPositionsValidation()
    {
        $this->checkDataValidation([], 'Missing positions in your data.');
    }

    public function testDataEmptyPositionValidation()
    {
        $this->checkDataValidation(['positions' => []], 'Missing positions in your data.');
    }

    public function testDataPositionValidation()
    {
        $data = ['positions' => [
            ['row' => 1],
        ]];
        $this->checkDataValidation($data, PositionUpdateFactory::POSITION_KEY, [0, 'rowId']);

        $data = ['positions' => [
            ['rowId' => 1, 'oldPosition' => 1],
        ]];
        $this->checkDataValidation($data, PositionUpdateFactory::POSITION_KEY, [0, 'newPosition']);
    }

    public function testDataParentIdValidation()
    {
        $definition = $this->getDefinitionWithParent();
        $data = ['positions' => [
            ['rowId' => 1, 'oldPosition' => 1, 'newPosition' => 1],
        ]];
        $this->checkDataValidation($data, 'Missing parentId in your data.', null, $definition);
    }

    /**
     * @param array $data
     * @param string|null $expectedErrorKey
     * @param array|null $expectedErrorParameters
     * @param PositionDefinition|null $definition
     */
    private function checkDataValidation(array $data, $expectedErrorKey = null, $expectedErrorParameters = null, $definition = null)
    {
        if (null === $definition) {
            $definition = $this->getDefinition();
        }
        $positionUpdateFactory = $this->getPositionUpdateFactory();

        /** @var PositionDataException $caughtException */
        $caughtException = null;

        try {
            $positionUpdateFactory->buildPositionUpdate($data, $definition);
        } catch (PositionDataException $e) {
            $caughtException = $e;
        }

        if (null === $expectedErrorKey) {
            $this->assertNull($caughtException);
        } else {
            $this->assertNotNull($caughtException);
            $this->assertInstanceOf(PositionDataException::class, $caughtException);
            $this->assertEquals($expectedErrorKey, $caughtException->getKey());
            $this->assertEquals('Admin.Notifications.Failure', $caughtException->getDomain());
            if (null !== $expectedErrorParameters) {
                $this->assertSame($expectedErrorParameters, $caughtException->getParameters());
            }
        }
    }

    /**
     * @return PositionDefinition
     */
    private function getDefinition()
    {
        return new PositionDefinition(
            'product',
            'id_product',
            'position'
        );
    }

    /**
     * @return PositionDefinition
     */
    private function getDefinitionWithParent()
    {
        return new PositionDefinition(
            'product',
            'id_product',
            'position',
            'id_category'
        );
    }

    /**
     * @return PositionUpdateFactory
     */
    private function getPositionUpdateFactory()
    {
        return new PositionUpdateFactory(
            'positions',
            'rowId',
            'oldPosition',
            'newPosition',
            'parentId'
        );
    }
}
