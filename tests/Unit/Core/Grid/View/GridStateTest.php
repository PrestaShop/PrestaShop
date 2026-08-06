<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Grid\View;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Grid\View\GridState;

class GridStateTest extends TestCase
{
    public function testItOnlyKeepsExpectedFieldsFromRawArrays(): void
    {
        $state = GridState::fromArray([
            'grid_id' => 'order',
            'filter_id' => 'order',
            'query' => 'SELECT * FROM ps_orders',
            'columns' => [
                ['id' => 'date_add', 'name' => 'Date', 'type' => 'date_time', 'callback' => 'evil'],
                ['id' => 'broken'],
                'not an array',
            ],
            'filters' => ['date_add' => ['from' => '2026-01-01', 'to' => '2026-01-31']],
        ]);

        $stateArray = $state->toArray();

        $this->assertArrayNotHasKey('query', $stateArray);
        $this->assertSame(
            [['id' => 'date_add', 'name' => 'Date', 'type' => 'date_time']],
            $stateArray['columns']
        );
    }

    public function testItDetectsActiveDateRangeFilters(): void
    {
        $state = GridState::fromArray([
            'grid_id' => 'order',
            'filter_id' => 'order',
            'columns' => [
                ['id' => 'date_add', 'name' => 'Date', 'type' => 'date_time'],
                ['id' => 'date_upd', 'name' => 'Updated', 'type' => 'date_time'],
                ['id' => 'total', 'name' => 'Total', 'type' => 'money'],
            ],
            'filters' => [
                'date_add' => ['from' => '2026-01-01', 'to' => '2026-01-31'],
                'date_upd' => 'not a range',
                'total' => ['from' => '10', 'to' => '20'],
            ],
        ]);

        $this->assertSame(
            ['date_add' => ['id' => 'date_add', 'name' => 'Date']],
            $state->getActiveDateRangeFilters()
        );
    }
}
