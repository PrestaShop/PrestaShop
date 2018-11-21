<?php
/**
 * 2007-2018 PrestaShop.
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Core\Grid\Position;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionDataException;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionUpdateFactory;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionDefinition;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionModificationCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionModificationInterface;

/**
 * Class PositionUpdateFactoryTest.
 */
class PositionUpdateFactoryTest extends TestCase
{
    public function testHandleData()
    {
        $definition = $this->getDefinition();
        $data = ['positions' => [
            ['rowId' => 1, 'oldPosition' => 1, 'newPosition' => 2]
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
            ['row' => 1]
        ]];
        $this->checkDataValidation($data, PositionUpdateFactory::POSITION_KEY, [0, 'rowId']);

        $data = ['positions' => [
            ['rowId' => 1]
        ]];
        $this->checkDataValidation($data, PositionUpdateFactory::POSITION_KEY, [0, 'oldPosition']);

        $data = ['positions' => [
            ['rowId' => 1, 'oldPosition' => 1]
        ]];
        $this->checkDataValidation($data, PositionUpdateFactory::POSITION_KEY, [0, 'newPosition']);
    }

    public function testDataParentIdValidation()
    {
        $definition = $this->getDefinitionWithParent();
        $data = ['positions' => [
            ['rowId' => 1, 'oldPosition' => 1, 'newPosition' => 1]
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
