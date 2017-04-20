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
    mounted() {
      this.$store.dispatch('getStock', {
        order: this.$store.state.order,
        page_size: this.$store.state.productsPerPage,
        page_index: 1
      });
    },
    components: {
      ProductsActions,
      ProductsTable,
      Pagination
    }
  }
</script>