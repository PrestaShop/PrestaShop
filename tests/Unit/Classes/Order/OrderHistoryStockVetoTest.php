<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\Order;

use Order;
use OrderHistory;
use OrderState;
use PHPUnit\Framework\TestCase;

/**
 * A merchant who cancels an order whose goods already shipped, and is not expecting them back, has
 * no way to keep the stock as it is. An override cannot serve two modules at once, which is why this
 * is a hook, and why the decision fails towards today's behaviour: only a module answering false
 * stops the stock update, so a shop with nothing listening is unaffected.
 */
class OrderHistoryStockVetoTest extends TestCase
{
    /**
     * @dataProvider hookResults
     *
     * @param mixed $hookResults
     */
    public function testOnlyAModuleAnsweringFalseStopsTheStockUpdate($hookResults, bool $expected): void
    {
        $history = $this->historyReturning($hookResults);

        $this->assertSame($expected, $history->askShouldUpdateStock(-2));
    }

    public static function hookResults(): iterable
    {
        yield 'no module listening' => [[], true];
        yield 'one module says false' => [['moduleA' => false], false];
        yield 'one module says true' => [['moduleA' => true], true];
        yield 'any false wins over true' => [['moduleA' => true, 'moduleB' => false], false];
        yield 'false first still wins' => [['moduleA' => false, 'moduleB' => true], false];
        // A module answering something meaningless must not read as a veto, or one careless module
        // would silently stop stock being given back on every cancellation.
        yield 'a module returns null' => [['moduleA' => null], true];
        yield 'a module returns a string' => [['moduleA' => 'no'], true];
        yield 'a module returns zero' => [['moduleA' => 0], true];
        // Hook::exec answers null when it cannot dispatch at all.
        yield 'the hook could not be dispatched' => [null, true];
    }

    public function testTheHookIsToldTheDeltaAndBothStates(): void
    {
        $history = $this->historyReturning([]);

        $history->askShouldUpdateStock(3);

        $this->assertSame($history->order, $history->seen['order']);
        $this->assertSame(['product_id' => 7], $history->seen['product']);
        $this->assertSame(3, $history->seen['delta_quantity']);
        $this->assertSame($history->newState, $history->seen['new_order_state']);
        $this->assertSame($history->oldState, $history->seen['old_order_state']);
    }

    /**
     * @param mixed $hookResults
     */
    private function historyReturning($hookResults): object
    {
        return new class($hookResults) extends OrderHistory {
            /** @var array<string, mixed> */
            public array $seen = [];

            public Order $order;

            public OrderState $newState;

            public OrderState $oldState;

            /** @var mixed */
            private $hookResults;

            /**
             * @param mixed $hookResults
             */
            public function __construct($hookResults)
            {
                $this->hookResults = $hookResults;
                // These are only handed to the hook, never read. They skip ObjectModel's constructor
                // because it reaches Configuration and therefore the database.
                $this->order = new class() extends Order {
                    public function __construct()
                    {
                    }
                };
                $this->newState = new class() extends OrderState {
                    public function __construct()
                    {
                    }
                };
                $this->oldState = new class() extends OrderState {
                    public function __construct()
                    {
                    }
                };
            }

            public function askShouldUpdateStock(int $deltaQuantity): bool
            {
                return $this->shouldUpdateStock(
                    $this->order,
                    ['product_id' => 7],
                    $deltaQuantity,
                    $this->newState,
                    $this->oldState
                );
            }

            protected function dispatchStockUpdateVeto(array $params)
            {
                $this->seen = $params;

                return $this->hookResults;
            }
        };
    }
}
