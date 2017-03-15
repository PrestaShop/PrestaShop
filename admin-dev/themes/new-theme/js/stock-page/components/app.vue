<template>
  <div id="app" class="stock-app">
    <StockHeader />
    <Search />
    <div class="card p-a-2">
      <ProductsActions />
      <Products />
      <Pagination />
    </div>
  </div>
</template>

<script>
  import StockHeader from './header/stock-header';
  import Search from './header/search';
  import ProductsActions from './products/products-actions';
  import Products from './products/products-table';
  import Pagination from './products/pagination';
  export default {
    name: 'app',
    components: {
      StockHeader,
      Search,
      ProductsActions,
      Products,
      Pagination
    },
  }
</script>

<style lang="sass?outputStyle=expanded">
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
  }
  .ui-spinner {
    .ui-spinner-button {
      right: 52px;
      cursor: pointer;
      display: none;
      z-index: 3;
    }
    .ui-spinner-up::before {
      font-family: 'Material Icons';
      content: "\E5C7";
      font-size: 20px;
      color: $gray-dark;
      position: relative;
      top: -4px;
    }
    .ui-spinner-down::before {
      font-family: 'Material Icons';
      content: "\E5C5";
      font-size: 20px;
      color: $gray-dark;
      bottom: 8px;
      position: relative;
    }
    span {
      display: none;
    }
  }
  .qty.active .ui-spinner-button{
    display: block;
  }
</style>
