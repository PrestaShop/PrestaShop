<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

/**
 * Optional add-on interface a grid definition factory may implement to enable
 * server-side SQL export (see admin_common_grid_sql). Grids that implement it no
 * longer expose their SQL query in the page HTML; the query is regenerated on demand
 * from an authorized endpoint instead.
 *
 * This interface is intentionally opt-in: existing grids (core and third-party) that
 * do not implement it keep working through the legacy data-query flow, so no module is
 * forced to migrate.
 */
interface SqlExportableGridDefinitionFactoryInterface
{
    /**
     * FQCN of the concrete Filters (SearchCriteriaInterface) class for this grid,
     * e.g. OrderFilters::class. Supplying the concrete class keeps shop-scoped grids
     * (ShopFilters) correct when the query is rebuilt server-side.
     *
     * @return string
     */
    public function getFiltersClass(): string;

    /**
     * Service id of this grid's GridFactory, e.g. 'prestashop.core.grid.factory.order'.
     *
     * @return string
     */
    public function getGridFactoryServiceId(): string;
}
