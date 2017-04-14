<template>
  <PSTable>
    <thead>
      <tr>
        <th width="40%" class="thead-title">Product<Sort order="product" :isDesc="isSorted" v-on:sort="toggleSort" /></th>
        <th>Reference<Sort order="reference" :isDesc="isSorted" v-on:sort="toggleSort" /></th>
        <th>Supplier<Sort order="supplier" :isDesc="isSorted" v-on:sort="toggleSort" /></th>
        <th class="text-xs-center">Physical<Sort order="physical_quantity" :isDesc="isSorted" v-on:sort="toggleSort" /></th>
        <th class="text-xs-center">Reserved</th>
        <th class="text-xs-center">Available<Sort order="available_quantity" :isDesc="isSorted" v-on:sort="toggleSort" /></th>
        <th class="text-xs-right"><i class="material-icons">edit</i>Edit Quantity</th>
      </tr>
    </thead>
    <tbody>
      <Alert v-if="emptyProducts" />
      <ProductLine v-for="(product, index) in products" key=${index} :product="product" />
    </tbody>
  </PSTable>
</template>

<script>
  import ProductLine from './product/product-line';
  import Alert from './alert';
  import Sort from './sort';
  import PSTable from '../utils/ps-table';

  export default {
    components: {
      ProductLine,
      Sort,
      Alert,
      PSTable
    },
    methods: {
      toggleSort() {
        this.isSorted = !this.isSorted;
      }
    },
    computed: {
      products() {
       return this.$store.getters.products;
      },
      emptyProducts() {
        return !this.$store.getters.products.length;
      }
    },
    data() {
      return {
        isSorted: true
      }
    }
  }
</script>

<style lang="sass?outputStyle=expanded">
  @import "~PrestaKit/scss/custom/_variables.scss";
  .table {
    font-size: .9em;
    table-layout: fixed;
    width: 100%;
    white-space: nowrap;
    thead {
      border:none;
      th {
        border:none;
        border-bottom: 2px solid $brand-primary;
        color: $gray-dark;
        padding: 10px 0;
        .material-icons {
          margin-left: 5px;
          vertical-align: middle;
        }
        &.thead-title {
          padding-left: 98px;
        }
        &:last-child {
          .material-icons {
            color: $gray-medium;
            margin-right: 5px;
          }
        }
      }
    }
    tbody {
      border: none;
      tr {
        border-bottom: $gray-light 1px solid;
      }
    }
  }
</style>
