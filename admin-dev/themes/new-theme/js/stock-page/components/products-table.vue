<template>
  <table class="table">
    <thead>
      <tr>
        <th class="text-xs-center">Product<i class="material-icons">swap_vert</i></th>
        <th>Reference<i class="material-icons">swap_vert</i></th>
        <th>Supplier<i class="material-icons">swap_vert</i></th>
        <th class="text-xs-center">Physical<i class="material-icons">swap_vert</i></th>
        <th class="text-xs-center">Reserved</th>
        <th class="text-xs-center">Available<i class="material-icons">swap_vert</i></th>
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

  export default {
    components: {
      ProductLine
    },
    computed: {
      products : function() {
        return this.$store.state.products;
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
