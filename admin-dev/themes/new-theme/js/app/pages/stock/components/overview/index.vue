<template>
  <section>
    <ProductsActions />
    <ProductsTable :isLoading="isLoading" @sort="sort" />
  </section>
</template>

<script>
  import ProductsActions from './products-actions';
  import ProductsTable from './products-table';

  const DEFAULT_SORT = '';

  export default {
    computed: {
      isLoading() {
        return this.$store.getters.isLoading;
      },
    },
    methods: {
      sort(desc) {
        this.$emit('fetch', desc);
      },
    },
    mounted() {
      this.$store.dispatch('updatePageIndex', 1);
      this.$store.dispatch('updateKeywords', []);
      this.$store.dispatch('updateOrder', 'product');
      this.$store.dispatch('isLoading');
      this.$emit('fetch', DEFAULT_SORT);
    },
    components: {
      ProductsActions,
      ProductsTable,
    },
  };
</script>
