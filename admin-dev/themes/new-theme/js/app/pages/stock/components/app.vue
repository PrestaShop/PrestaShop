<template>
  <div v-show="isReady" id="app" class="stock-app">
    <StockHeader />
    <Search @search="onSearch" />
    <div class="card p-a-2">
      <router-view class="view" @fetch="fetch"></router-view>
    </div>
    <PSPagination
      pageNumber="3"
      activeMultiPagination="5"
      :current="currentPagination"
      :pagesCount="pagesCount"
      @pageChanged="onPageChanged"
    />
  </div>
</template>

<script>
  import StockHeader from './header/stock-header';
  import Search from './header/search';
  import PSPagination from 'app/widgets/ps-pagination/ps-pagination';

  export default {
    name: 'app',
    computed : {
      isReady() {
        return this.$store.getters.isReady;
      },
      pagesCount() {
        return this.$store.getters.totalPages;
      },
      currentPagination() {
         return this.$store.getters.pageIndex;
      }
    },
    methods: {
      onPageChanged(pageIndex) {
        let desc = this.$route.name === 'overview' ? '' : ' desc';
        this.$store.dispatch('updatePageIndex', pageIndex);
        this.fetch(desc);
      },
      fetch(desc) {
        let action = this.$route.name === 'overview' ? 'getStock' : 'getMovements';

        this.$store.dispatch(action, {
          order: `${this.$store.getters.order}${desc}`,
          page_size: this.$store.state.productsPerPage,
          page_index: this.$store.getters.pageIndex,
          keywords: this.$store.getters.keywords
        });
      },
      onSearch(keywords) {
        let desc = this.$route.name === 'overview' ? '' : ' desc';
        this.$store.dispatch('updateKeywords', keywords);
        this.fetch(desc);
      },
    },
    components: {
      StockHeader,
      Search,
      PSPagination
    },
  }
</script>

<style lang="sass">
  @import "~PrestaKit/scss/custom/_variables.scss";
  .header-toolbar {
    z-index: 0;
    height: 128px;
  }
  .stock-app {
    padding-top: 3em;
  }
  .table tr td {
    padding: 5px 0;
    vertical-align: top;
    &:not(.qty-spinner) {
      padding-top:14px;
    }
  }
  .ui-spinner {
    .ui-spinner-button {
      right: 30px;
      cursor: pointer;
      display: none;
      z-index: 3;
      transition: all 0.2s ease;
      height: 20px;
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
