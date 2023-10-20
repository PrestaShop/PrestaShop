<!--**
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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *-->
<template>
  <div
    v-if="isReady"
    id="app"
    class="stock-app container-fluid"
  >
    <StockHeader />
    <Search
      ref="search"
      @search="onSearch"
      @applyFilter="applyFilter"
    />
    <LowFilter
      v-if="isOverview"
      :filters="filters"
      @lowStockChecked="onLowStockChecked"
    />
    <div class="card container-fluid pa-2 clearfix">
      <router-view
        class="view"
        @resetFilters="resetFilters"
        @fetch="fetch"
      />
      <PSPagination
        :current-index="currentPagination"
        :pages-count="pagesCount"
        @pageChanged="onPageChanged"
      />
    </div>
  </div>
</template>

<script lang="ts">
  import {defineComponent} from 'vue';
  import PSPagination from '@app/widgets/ps-pagination.vue';
  import StockHeader from './header/stock-header.vue';
  import Search, {SearchInstanceType} from './header/search.vue';
  import LowFilter from './header/filters/low-filter.vue';
  import {FiltersInstanceType} from './header/filters.vue';

  /* eslint-disable camelcase */
  export interface StockFilters {
    active?: string;
    suppliers?: Array<number>;
    categories?: Array<number>;
    date_add?: Array<any>;
    id_employee?: Array<number>;
    id_stock_mvt_reason?: Array<number>;
    order?: string;
    page_size?: number,
    page_index?: number;
    keywords?: any;
    low_stock?: number | boolean | string;
  }
  /* eslint-enable camelcase */

  const FIRST_PAGE = 1;

  export default defineComponent({
    name: 'App',
    computed: {
      isReady(): boolean {
        return this.$store.state.isReady;
      },
      pagesCount(): number {
        return this.$store.state.totalPages;
      },
      currentPagination(): number {
        return this.$store.state.pageIndex;
      },
      isOverview(): boolean {
        return this.$route.name === 'overview';
      },
      isMovements(): boolean {
        return this.$route.name === 'movements';
      },
      searchRef(): SearchInstanceType {
        return <SearchInstanceType>(this.$refs.search);
      },
      filtersRef(): FiltersInstanceType {
        return this.searchRef?.filtersRef;
      },
    },
    beforeMount() {
      this.$store.dispatch('getTranslations');
    },
    methods: {
      onPageChanged(pageIndex: number): void {
        this.$store.dispatch('updatePageIndex', pageIndex);
        this.fetch(this.$store.state.sort);
      },
      fetch(sortDirection?: string): void {
        const action = this.isOverview ? 'getStock' : 'getMovements';
        const sorting = sortDirection === 'desc' ? ' desc' : '';

        this.$store.dispatch('isLoading');

        this.filters = {
          ...this.filters,
          order: `${this.$store.state.order}${sorting}`,
          page_size: this.$store.state.productsPerPage,
          page_index: this.$store.state.pageIndex,
          keywords: this.$store.state.keywords,
        };

        this.$store.dispatch(action, this.filters);
      },
      onSearch(keywords: any): void {
        this.$store.dispatch('updateKeywords', keywords);
        this.resetPagination();
        this.fetch();
      },
      applyFilter(filters: StockFilters): void {
        this.filters = filters;
        this.resetPagination();
        this.fetch();
      },
      resetFilters(): void {
        this.filtersRef?.reset();
        this.filters = {};
      },
      resetPagination(): void {
        this.$store.dispatch('updatePageIndex', FIRST_PAGE);
      },
      onLowStockChecked(isChecked: boolean): void {
        this.filters = {...this.filters, low_stock: isChecked};
        this.fetch();
      },
    },
    components: {
      StockHeader,
      Search,
      PSPagination,
      LowFilter,
    },
    data: (): {filters: StockFilters} => ({
      filters: {},
    }),
  });
</script>

<style lang="scss" type="text/scss">
// hide the layout header
#main-div > .header-toolbar {
  height: 0;
  display: none;
}
</style>
