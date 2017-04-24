<template>
  <section>
    <ProductsActions />
    <ProductsTable @sort="fetch" />
    <Pagination />
  </section>
</template>

<script>
  import ProductsActions from './products-actions';
  import ProductsTable from './products-table';
  import Pagination from 'app/pages/stock/components/product/pagination';

  const DEFAULT_SORT = '';

  export default {
    methods: {
      fetch(desc) {
        this.$store.dispatch('getStock', {
          order: `${this.$store.getters.order}${desc}`,
          page_size: this.$store.state.productsPerPage,
          page_index: this.$store.getters.pageIndex,
        });
      }
    },
    mounted() {
      this.$store.dispatch('updateOrder', 'product');
      this.fetch(DEFAULT_SORT);
    },
    components: {
      ProductsActions,
      ProductsTable,
      Pagination
    }
  }
</script>