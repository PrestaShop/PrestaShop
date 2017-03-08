<template>
  <div id="app">
    <StockHeader />
    <ProductsActions />
    <Products />
    <Pagination />
  </div>
</template>

<script>
  import StockHeader from './stock-header';
  import ProductsActions from './products-actions';
  import Products from './products-table';
  import Pagination from './pagination';
  export default {
    name: 'app',
    components: {
      StockHeader,
      ProductsActions,
      Products,
      Pagination
    },
  }
</script>
