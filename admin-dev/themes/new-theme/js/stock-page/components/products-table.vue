<template>
  <table class="table">
    <thead>
      <tr>
        <th>Product</th>
        <th>Reference</th>
        <th>Supplier</th>
        <th>Physical</th>
        <th>Reserved</th>
        <th>Available</th>
        <th>Edit Quantity</th>
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
