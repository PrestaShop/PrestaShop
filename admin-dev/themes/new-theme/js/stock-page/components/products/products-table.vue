<template>
  <table class="table">
    <thead>
      <tr>
        <th class="thead-title">Product<Sort order="product" /></th>
        <th class="p-l-0">Reference<Sort order="reference" /></th>
        <th class="p-l-0">Supplier<Sort order="supplier" /></th>
        <th class="text-xs-center">Physical<Sort order="physical_quantity" /></th>
        <th class="text-xs-center">Reserved</th>
        <th class="text-xs-center">Available<Sort order="available_quantity" /></th>
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
    computed: {
      products () {
        return this.$store.state.products.filter((product)=> {
          product.product_key = `${product.product_id}-${product.product_attribute_id}`;
          return product;
        });
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
          padding-left: 100px;
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
