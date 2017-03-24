<template>
  <table class="table">
    <thead>
      <tr>
        <th class="thead-title">Product<Sort order="product" :isDesc="isSorted" v-on:sort="toggleSort" /></th>
        <th class="p-l-0">Reference<Sort order="reference" :isDesc="isSorted" v-on:sort="toggleSort" /></th>
        <th class="p-l-0">Supplier<Sort order="supplier" :isDesc="isSorted" v-on:sort="toggleSort" /></th>
        <th class="text-xs-center">Physical<Sort order="physical_quantity" :isDesc="isSorted" v-on:sort="toggleSort" /></th>
        <th class="text-xs-center">Reserved</th>
        <th class="text-xs-center">Available<Sort order="available_quantity" :isDesc="isSorted" v-on:sort="toggleSort" /></th>
        <th><i class="material-icons">edit</i>Edit Quantity</th>
      </tr>
    </thead>
    <tbody>
      <ProductLine v-for="product in products" :key="product.product_key" :product="product" />
    </tbody>
  </table>
</template>

<script>
  import ProductLine from './product-line';
  import Sort from './sort';

  export default {
    components: {
      ProductLine,
      Sort
    },
    methods: {
      toggleSort() {
        this.isSorted = !this.isSorted;
      }
    },
    computed: {
      products() {
        let productId;
        return this.$store.state.products.filter((product)=> {
          if(productId !== product.product_id && product.combination_id !== 0) {
            productId = product.product_id;
            product.hasCombination = true;
          }
          else if(product.combination_id === 0) {
            product.hasCombination = false;
          }
          product.product_key = `${product.product_id}-${product.combination_id}`;
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

<style lang="sass?outputStyle=expanded" scoped>
  @import "~PrestaKit/scss/custom/_variables.scss";
  .table {
    font-size: .9em;
    thead {
      border:none;
      th {
        border:none;
        border-bottom: 2px solid $brand-primary;
        color: $gray-dark;
        .material-icons {
          margin-left: 5px;
          vertical-align: middle;
        }
        &.thead-title {
          padding-left: 97px;
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
