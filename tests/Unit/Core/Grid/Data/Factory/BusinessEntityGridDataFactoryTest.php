<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Grid\Data\Factory;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Grid\Data\Factory\BusinessEntityGridDataFactory;
use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class BusinessEntityGridDataFactoryTest extends TestCase
{
    public function testItAddsTranslatedStatusLabelAndBadgeTypeToEachRecordAndKeepsRawStatus(): void
    {
        $records = new RecordCollection([
            ['id_business_entity' => 1, 'status' => 'active'],
            ['id_business_entity' => 2, 'status' => 'pending'],
            ['id_business_entity' => 3, 'status' => 'unknown_value'],
            ['id_business_entity' => 4, 'status' => 'inactive'],
            ['id_business_entity' => 5, 'status' => 'rejected'],
        ]);

        $query = 'SELECT be.id_business_entity FROM ps_business_entity be';

        $inner = $this->createMock(GridDataFactoryInterface::class);
        $inner->method('getData')->willReturn(new GridData($records, 5, $query));

        $data = $this->buildFactory($inner)->getData($this->createMock(SearchCriteriaInterface::class));
        $result = iterator_to_array($data->getRecords());

        $this->assertSame('Active', $result[0]['status_label']);
        $this->assertSame('active', $result[0]['status'], 'raw status is preserved');
        $this->assertSame('success', $result[0]['status_badge_type']);

        $this->assertSame('Pending', $result[1]['status_label']);
        $this->assertSame('info', $result[1]['status_badge_type']);

        // An unknown status falls back to the raw value and no badge type rather than throwing.
        $this->assertSame('unknown_value', $result[2]['status_label']);
        $this->assertSame('', $result[2]['status_badge_type']);

        $this->assertSame('Inactive', $result[3]['status_label']);
        $this->assertSame('light-info', $result[3]['status_badge_type']);

        $this->assertSame('Rejected', $result[4]['status_label']);
        $this->assertSame('danger', $result[4]['status_badge_type']);

        $this->assertSame(5, $data->getRecordsTotal());
    }

    public function testItForwardsTheInnerRecordsTotalAndQueryUnchanged(): void
    {
        $query = 'SELECT be.id_business_entity FROM ps_business_entity be WHERE be.deleted = 0';

        $inner = $this->createMock(GridDataFactoryInterface::class);
        $inner->method('getData')->willReturn(new GridData(new RecordCollection([]), 42, $query));

        $data = $this->buildFactory($inner)->getData($this->createMock(SearchCriteriaInterface::class));

        $this->assertSame(42, $data->getRecordsTotal());
        $this->assertSame($query, $data->getQuery(), 'the SQL query must reach "Show SQL query" untouched');
    }

    private function buildFactory(GridDataFactoryInterface $inner): BusinessEntityGridDataFactory
    {
        $translator = $this->createMock(TranslatorInterface::class);
        // trans() echoes the source string so we can assert the label went through translation.
        $translator->method('trans')->willReturnCallback(static fn (string $id): string => $id);

        return new BusinessEntityGridDataFactory($inner, $translator);
    }
}
