<template>
  <div id="app" class="stock-app">
    <StockHeader />
    <Search />
    <div class="container card p-t-2">
      <ProductsActions />
      <Products />
      <Pagination />
    </div>
  </div>
</template>

<script>
  import StockHeader from './stock-header';
  import Search from './search';
  import ProductsActions from './products-actions';
  import Products from './products-table';
  import Pagination from './pagination';
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

<style>
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
</style>
