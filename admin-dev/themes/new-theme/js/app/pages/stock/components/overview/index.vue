<!--*
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *-->
<template>
  <section class="stock-overview">
    <ProductsActions />
    <ProductsTable
      :is-loading="isLoading"
      @sort="sort"
    />
  </section>
</template>

<script lang="ts">
  import {defineComponent} from 'vue';
  import ProductsActions from './products-actions.vue';
  import ProductsTable from './products-table.vue';

  const DEFAULT_SORT = 'desc';

  export default defineComponent({
    computed: {
      isLoading(): boolean {
        return this.$store.state.isLoading;
      },
    },
    methods: {
      sort(sortDirection: string): void {
        this.$emit('fetch', sortDirection);
      },
    },
    mounted() {
      this.$store.dispatch('updatePageIndex', 1);
      this.$store.dispatch('updateKeywords', []);
      this.$store.dispatch('updateOrder', 'product_id');
      this.$store.dispatch('isLoading');
      this.$emit('resetFilters');
      this.$emit('fetch', DEFAULT_SORT);
    },
    components: {
      ProductsActions,
      ProductsTable,
    },
  });
</script>
