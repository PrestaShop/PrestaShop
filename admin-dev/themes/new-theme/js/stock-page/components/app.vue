<template>
  <div id="app">
    <ul class="products-list">
      <li v-for="product in products">
       {{ product.product_name }}
     </li>
    </ul>
  </div>
</template>

<script>
  export default {
    name: 'app',
    computed: {
      products : function() {
        return this.$store.state.products;
      }
    }
  }
</script>
