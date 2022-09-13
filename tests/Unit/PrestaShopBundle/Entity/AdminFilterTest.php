<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
