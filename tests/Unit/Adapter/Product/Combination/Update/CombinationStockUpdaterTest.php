<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Adapter\Product\Combination\Update;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Update\CombinationStockUpdater;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\MovementReasonRepository;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\StockAvailableRepository;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\MovementReasonId;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\StockModification;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Stock\StockManager;
use ReflectionMethod;
use StockAvailable;

class CombinationStockUpdaterTest extends TestCase
{
    /**
     * The employee-edition movement reason must reflect whether the stock actually increased,
     * which depends on the sign of the delta - not on a comparison with the previous quantity.
     *
     * @dataProvider getStockModifications
     */
    public function testSaveMovementPicksEditionReasonFromDeltaSign(
        StockModification $stockModification,
        int $previousQuantity,
        bool $expectedIncrease
    ): void {
        $capturedIncrease = null;
        $movementReasonRepository = $this->createMock(MovementReasonRepository::class);
        $movementReasonRepository
            ->expects($this->once())
            ->method('getEmployeeEditionReasonId')
            ->willReturnCallback(function (bool $increased) use (&$capturedIncrease): MovementReasonId {
                $capturedIncrease = $increased;

                return new MovementReasonId(1);
            });

        $updater = new CombinationStockUpdater(
            $this->createMock(StockAvailableRepository::class),
            $this->createMock(CombinationRepository::class),
            $movementReasonRepository,
            $this->createMock(StockManager::class),
            $this->createMock(ShopConfigurationInterface::class),
            $this->createMock(HookDispatcherInterface::class)
        );

        $stockAvailable = $this->getMockBuilder(StockAvailable::class)
            ->disableOriginalConstructor()
            ->getMock();
        $stockAvailable->id_product = 1;
        $stockAvailable->id_product_attribute = 2;
        $stockAvailable->id_shop = 1;
        $stockAvailable->quantity = 1;

        $saveMovement = new ReflectionMethod(CombinationStockUpdater::class, 'saveMovement');
        $saveMovement->setAccessible(true);
        $saveMovement->invoke($updater, $stockAvailable, $stockModification, $previousQuantity, 1);

        $this->assertSame($expectedIncrease, $capturedIncrease);
    }

    public static function getStockModifications(): iterable
    {
        // Increases from a non-zero base: the (positive) delta is smaller than the previous
        // quantity, so a comparison against the previous quantity would wrongly report a decrease.
        yield 'delta increase from non-zero base' => [StockModification::buildDeltaQuantity(5), 10, true];
        yield 'fixed increase from non-zero base' => [StockModification::buildFixedQuantity(12), 10, true];
        // Increase from empty stock (the only increase case the buggy comparison happened to get right).
        yield 'delta increase from empty stock' => [StockModification::buildDeltaQuantity(100), 0, true];
        // Decreases must keep reporting a decrease.
        yield 'delta decrease' => [StockModification::buildDeltaQuantity(-3), 10, false];
        yield 'fixed decrease' => [StockModification::buildFixedQuantity(8), 10, false];
    }
}
