<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Grid\View;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Grid\View\DynamicDateRangeComputer;
use PrestaShop\PrestaShop\Core\Grid\View\DynamicDateRuleApplier;

class DynamicDateRuleApplierTest extends TestCase
{
    private DynamicDateRuleApplier $applier;

    protected function setUp(): void
    {
        $this->applier = new DynamicDateRuleApplier(new DynamicDateRangeComputer());
    }

    public function testItReplacesTheSavedRangeOfRuledFields(): void
    {
        $searchCriteria = [
            'limit' => 50,
            'filters' => [
                'date_add' => ['from' => '2020-01-01', 'to' => '2020-01-31'],
                'name' => 'john',
            ],
        ];

        $appliedCriteria = $this->applier->apply($searchCriteria, [
            'date_add' => ['date_rule' => 'today'],
        ]);

        $today = date('Y-m-d');
        $this->assertSame(['from' => $today, 'to' => $today], $appliedCriteria['filters']['date_add']);
        $this->assertSame('john', $appliedCriteria['filters']['name']);
        $this->assertSame(50, $appliedCriteria['limit']);
    }

    public function testItIgnoresInvalidOrNeutralRules(): void
    {
        $searchCriteria = [
            'filters' => [
                'date_add' => ['from' => '2020-01-01', 'to' => '2020-01-31'],
            ],
        ];

        $appliedCriteria = $this->applier->apply($searchCriteria, [
            'date_add' => ['date_rule' => 'keep_as_is'],
            'date_upd' => ['date_rule' => 'today'],
            'other' => ['date_rule' => 'not_a_rule'],
        ]);

        $this->assertSame($searchCriteria, $appliedCriteria);
    }
}
