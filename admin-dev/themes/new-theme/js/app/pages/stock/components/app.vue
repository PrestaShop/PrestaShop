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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
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

<script>
  import PSPagination from '@app/widgets/ps-pagination';
  import StockHeader from './header/stock-header';
  import Search from './header/search';
  import LowFilter from './header/filters/low-filter';

  const FIRST_PAGE = 1;

  export default {
    name: 'App',
    computed: {
      isReady() {
        return this.$store.state.isReady;
      },
      pagesCount() {
        return this.$store.state.totalPages;
      },
      currentPagination() {
        return this.$store.state.pageIndex;
      },
      isOverview() {
        return this.$route.name === 'overview';
      },
    },
    methods: {
      onPageChanged(pageIndex) {
        this.$store.dispatch('updatePageIndex', pageIndex);
        this.fetch('asc');
      },
      fetch(sortDirection) {
        const action = this.$route.name === 'overview' ? 'getStock' : 'getMovements';
        const sorting = (sortDirection === 'desc') ? ' desc' : '';
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
      onSearch(keywords) {
        this.$store.dispatch('updateKeywords', keywords);
        this.resetPagination();
        this.fetch();
      },
      applyFilter(filters) {
        this.filters = filters;
        this.resetPagination();
        this.fetch();
      },
      resetFilters() {
        this.filters = {};
      },
      resetPagination() {
        this.$store.dispatch('updatePageIndex', FIRST_PAGE);
      },
      onLowStockChecked(isChecked) {
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
    data: () => ({
      filters: {},
    }),
  };
</script>

<style lang="scss" type="text/scss">
  // hide the layout header
  #main-div > .header-toolbar {
    height: 0;
    display: none;
  }
</style>
