<!--*
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *-->
<template>
  <PSTable class="mt-1">
    <thead>
      <tr class="column-headers">
        <th
          scope="col"
        >
          <PSSort
            order="product_id"
            @sort="sort"
            :current-sort="currentSort"
          >
            {{ trans('title_product_id') }}
          </PSSort>
        </th>
        <th
          scope="col"
          width="27%"
          class="product-title"
        >
          <PSSort
            order="product_name"
            @sort="sort"
            :current-sort="currentSort"
          >
            {{ trans('title_product') }}
          </PSSort>
        </th>
        <th scope="col">
          <PSSort
            order="reference"
            @sort="sort"
            :current-sort="currentSort"
          >
            {{ trans('title_reference') }}
          </PSSort>
        </th>
        <th>
          <PSSort
            order="supplier"
            @sort="sort"
            :current-sort="currentSort"
          >
            {{ trans('title_supplier') }}
          </PSSort>
        </th>
        <th class="text-center">
          {{ trans('title_status') }}
        </th>
        <th class="text-center">
          <PSSort
            order="physical_quantity"
            @sort="sort"
            :current-sort="currentSort"
          >
            {{ trans('title_physical') }}
          </PSSort>
        </th>
        <th class="text-center">
          {{ trans('title_reserved') }}
        </th>
        <th class="text-center">
          <PSSort
            order="available_quantity"
            @sort="sort"
            :current-sort="currentSort"
          >
            {{ trans('title_available') }}
          </PSSort>
        </th>
        <th :title="trans('title_edit_quantity')">
          <i class="material-icons">edit</i>
          {{ trans('title_edit_quantity') }}
        </th>
      </tr>
    </thead>
    <tbody>
      <tr v-if="isLoading">
        <td colspan="9">
          <PSLoader
            v-for="(n, index) in 3"
            class="mt-1"
            :key="index"
          >
            <div class="background-masker header-top" />
            <div class="background-masker header-left" />
            <div class="background-masker header-bottom" />
            <div class="background-masker subheader-left" />
            <div class="background-masker subheader-bottom" />
          </PSLoader>
        </td>
      </tr>
      <tr v-else-if="emptyProducts">
        <td colspan="9">
          <PSAlert
            alert-type="ALERT_TYPE_WARNING"
            :has-close="false"
          >
            {{ trans('no_product') }}
          </PSAlert>
        </td>
      </tr>
      <ProductLine
        v-else
        v-for="(product, index) in products"
        :key="index"
        :product="product"
      />
    </tbody>
  </PSTable>
</template>

<script lang="ts">
  import PSAlert from '@app/widgets/ps-alert.vue';
  import PSTable from '@app/widgets/ps-table/ps-table.vue';
  import PSSort from '@app/widgets/ps-table/ps-sort.vue';
  import PSLoader from '@app/widgets/ps-loader.vue';
  import {defineComponent} from 'vue';
  import TranslationMixin from '@app/pages/stock/mixins/translate';
  import ProductLine from './product-line.vue';

  /* eslint-disable camelcase */
  export interface StockProduct {
    active: number;
    attribute_name: string;
    combination_cover_id: number;
    combination_id: number;
    combination_name: string;
    combination_reference: string;
    combination_thumbnail: string;
    combinations_product_url: string;
    edit_url: string;
    product_attributes: string;
    product_available_quantity: number;
    product_cover_id: number;
    product_features: string;
    product_id: number;
    product_low_stock_alert: number;
    product_low_stock_threshold: string;
    product_name: string;
    product_physical_quantity: number;
    product_reference: string;
    product_reserved_quantity: number;
    product_thumbnail: string;
    product_url: string;
    qty: number;
    supplier_id: number;
    supplier_name: string;
    total_combinations: number;
  }
  /* eslint-enable camelcase */

  export default defineComponent({
    props: {
      isLoading: {
        type: Boolean,
        required: true,
      },
    },
    mixins: [TranslationMixin],
    components: {
      ProductLine,
      PSSort,
      PSAlert,
      PSTable,
      PSLoader,
    },
    methods: {
      sort(order: string, sortDirection: string): void {
        this.$store.dispatch('updateOrder', order);
        this.$store.dispatch('updateSort', sortDirection);
        this.$emit('sort', sortDirection === 'desc' ? 'desc' : 'asc');
      },
    },
    computed: {
      products(): Array<StockProduct> {
        return this.$store.state.products;
      },
      emptyProducts(): boolean {
        return !this.$store.state.products.length;
      },
      currentSort(): string {
        return this.$store.state.order;
      },
    },
  });
</script>
