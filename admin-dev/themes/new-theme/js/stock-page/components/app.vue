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
