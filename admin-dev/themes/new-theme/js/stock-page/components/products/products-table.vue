<template>
  <table class="table">
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
      <ProductLine v-for="product in products" :key="product.id" :product="product" />
    </tbody>
  </table>
</template>

<script>
  import ProductLine from './product/product-line';
  import Product from './product/product';
  import Sort from './sort';

  export default {
    components: {
      ProductLine,
      Product,
      Sort
    },
    methods: {
      toggleSort() {
        this.isSorted = !this.isSorted;
      }
    },
    computed: {
      products() {
       return this.$store.state.products.filter((product)=> {
         product.hasCombination = false;
         if(product.list.length > 1) {
          product.product_name = product.list[0].product_name;
          product.total_combinations = product.list[0].total_combinations;
          product.product_thumbnail = product.list[0].product_thumbnail;
          product.hasCombination = true;
         }
         return product;
       });
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
        padding: 5px 0;
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
