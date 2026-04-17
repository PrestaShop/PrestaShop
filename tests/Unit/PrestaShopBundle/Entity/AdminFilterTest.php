<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Entity;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Entity\AdminFilter;

class AdminFilterTest extends TestCase
{
    private $emptyFilter;

    protected function setUp(): void
    {
        $this->emptyFilter = AdminFilter::getProductCatalogEmptyFilter();
    }

    public function testGetProductCatalogEmptyFilter(): void
    {
        $this->assertEmpty($this->emptyFilter['filter_category']);
        $this->assertEmpty($this->emptyFilter['filter_column_id_product']);
        $this->assertEmpty($this->emptyFilter['filter_column_name']);
        $this->assertEmpty($this->emptyFilter['filter_column_reference']);
        $this->assertEmpty($this->emptyFilter['filter_column_price']);
        $this->assertEmpty($this->emptyFilter['filter_column_sav_quantity']);
        $this->assertEmpty($this->emptyFilter['filter_column_active']);
    }

    /**
     * @dataProvider productFilterProviderByName
     */
    public function testSetProductCatalogFilterByName(array $filter, string $expected): void
    {
        $setFilter = (new AdminFilter())
            ->setProductCatalogFilter($filter)
            ->getProductCatalogFilter();

        $this->assertSame($expected, $setFilter['filter_column_name']);
    }

    public function productFilterProviderByName(): array
    {
        return [
            'quote_in_name' => [['filter_column_name' => 't\'est'], 't\'est'],
            'double_quote_in_name' => [['filter_column_name' => 't"est'], 't"est'],
            'lot_of_double_quote' => [['filter_column_name' => 't""e""s""t""""'], 't""e""s""t""""'],
            'lot_of_quote' => [['filter_column_name' => "t'''e''s't''"], "t'''e''s't''"],
        ];
    }
}
