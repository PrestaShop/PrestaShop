<template>
  <section>
    <ProductsActions />
    <ProductsTable />
    <Pagination />
  </section>
</template>

<script>
  import ProductsActions from './products-actions';
  import ProductsTable from './products-table';
  import Pagination from './pagination';

  export default {
    components: {
      ProductsActions,
      ProductsTable,
      Pagination
    }
  }
</script>