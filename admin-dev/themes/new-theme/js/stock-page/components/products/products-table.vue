<template>
  <table class="table">
    <thead>
      <tr>
        <th class="text-xs-center">Product<Sort v-on:sort="sortProducts" /></th>
        <th class="text-xs-center">Reference<Sort v-on:sort="sortProducts" /></th>
        <th>Supplier<Sort /></th>
        <th class="text-xs-center">Physical<Sort v-on:sort="sortProducts" /></th>
        <th class="text-xs-center">Reserved</th>
        <th class="text-xs-center">Available<Sort v-on:sort="sortProducts" /></th>
        <th><i class="material-icons">edit</i>Edit Quantity</th>
      </tr>
    </thead>
    <tbody>
        <ProductLine v-for="product in products" :key="product.product_id" :product="product" />
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
    computed: {
      products : function() {
        let mainProducts = [];
        let productId = null;
        this.$store.state.products.filter(function(product) {
          product.qty = 0;
          if(productId !== product.product_id) {
            productId = product.product_id;
            mainProducts.push(product);
          }
        });
        return mainProducts;
      }
    },
    methods: {
      sortProducts: function() {

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
