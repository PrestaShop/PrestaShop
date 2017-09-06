<!--**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *-->
<template>
  <div v-if="isReady" id="app" class="stock-app">
    <StockHeader />
    <Search @search="onSearch" @applyFilter="applyFilter" />
    <div class="card pa-2">
      <router-view class="view" @resetFilters="resetFilters" @fetch="fetch"></router-view>
    </div>
    <PSPagination
      :currentIndex="currentPagination"
      :pagesCount="pagesCount"
      @pageChanged="onPageChanged"
    />
  </div>
</template>

<script>
  import StockHeader from './header/stock-header';
  import Search from './header/search';
  import PSPagination from 'app/widgets/ps-pagination';

  export default {
    name: 'app',
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
    },
    methods: {
      onPageChanged(pageIndex) {
        const desc = this.$route.name === 'overview' ? '' : ' desc';
        this.$store.dispatch('updatePageIndex', pageIndex);
        this.fetch(desc);
      },
      fetch(desc) {
        let sorting = desc;
        const action = this.$route.name === 'overview' ? 'getStock' : 'getMovements';
        if (typeof desc !== 'string') {
          sorting = ' desc';
        }
        this.$store.dispatch('isLoading');

        this.$store.dispatch(action, Object.assign(this.filters, {
          order: `${this.$store.state.order}${sorting}`,
          page_size: this.$store.state.productsPerPage,
          page_index: this.$store.state.pageIndex,
          keywords: this.$store.state.keywords,
        }));
      },
      onSearch(keywords) {
        this.$store.dispatch('updateKeywords', keywords);
        this.fetch();
      },
      applyFilter(filters) {
        this.filters = filters;
        this.fetch();
      },
      resetFilters() {
        this.filters = {};
      },
    },
    components: {
      StockHeader,
      Search,
      PSPagination,
    },
    data: () => ({
      filters: {},
    }),
  };
</script>

<style lang="sass">
  @import "../../../../../scss/config/_settings.scss";
  .header-toolbar {
    z-index: 0;
    height: 128px;
  }
  .stock-app {
    padding-top: 3em;
  }
  .table tr td {
    padding: 5px 5px 5px;
    vertical-align: top;
    &:not(.qty-spinner) {
      padding-top:14px;
    }
    word-wrap: break-word;
    white-space: normal;
  }
  .ui-spinner {
    .ui-spinner-button {
      right: 30px;
      cursor: pointer;
      display: none;
      z-index: 3;
      transition: all 0.2s ease;
      height: 20px;
      .product-actions & {
        right: 7px;
      }
    }
    .ui-spinner-up::before {
      font-family: 'Material Icons';
      content: "\E5C7";
      font-size: 20px;
      color: $gray-dark;
      position: relative;
      top: -3px;
    }
    .ui-spinner-down::before {
      font-family: 'Material Icons';
      content: "\E5C5";
      font-size: 20px;
      color: $gray-dark;
      bottom: 5px;
      position: relative;
    }
    span {
      display: none;
    }
  }
  .qty.active .ui-spinner-button{
    display: block;
  }
  #growls.default {
    top: 20px;
  }
  .growl.growl-notice {
    background: white;
    border: 2px solid $success;
    border-radius: 0;
    padding: 0;
    min-height: 50px;
    .growl-message {
      color: $gray-dark;
      line-height: 46px;
      &::before {
        color: white;
        text-align: center;
        background: $success;
        height: 48px;
        width: 48px;
      }
    }
    .growl-close {
      color: $success;
      font-size: 20px
    }
  }
  .d-inline {
    display: inline;
  }
  .flex {
    display: flex;
    align-items: center;
    &.column {
      flex-direction: column;
    }
  }
  .btn:disabled {
    background-color: $gray-light;
    border-color: $gray-light;
  }
</style>
