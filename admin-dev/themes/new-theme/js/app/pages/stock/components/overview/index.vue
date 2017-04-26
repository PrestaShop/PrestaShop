<template>
  <section>
    <ProductsActions />
    <ProductsTable @sort="sort" />
  </section>
</template>

<script>
  import ProductsActions from './products-actions';
  import ProductsTable from './products-table';

  const DEFAULT_SORT = '';

  export default {
    methods: {
      sort(desc) {
        this.$emit('fetch', desc);
      }
    },
    mounted() {
      this.$store.dispatch('updateKeywords', []);
      this.$store.dispatch('updateOrder', 'product');
      this.$emit('fetch', DEFAULT_SORT);
    },
    components: {
      ProductsActions,
      ProductsTable
    }
  }
</script>