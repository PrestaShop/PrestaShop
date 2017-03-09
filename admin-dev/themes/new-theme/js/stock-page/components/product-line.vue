<template>
  <tr>
    <td>
      <input type="checkbox" :value="product.product_id" class="m-r-1">
      <img :src="imagePath" class="thumbnail" />
      <span class="m-l-1">{{ product.product_name }}</span>
    </td>
    <td>
      {{ product.product_reference }}
    </td>
    <td>
      {{ product.supplier_name }}
    </td>
    <td class="text-xs-center">
      {{ product.product_available_quantity + product.product_reserved_quantity }}
    </td>
    <td class="text-xs-center">
      {{ product.product_reserved_quantity }}
    </td>
    <td class="text-xs-center">
      {{ product.product_available_quantity }}
    </td>
    <td>
      <input>
    </td>
  </tr>
</template>

<script>
  export default {
    props: ['product'],
    computed: {
      imagePath: function() {
        return `${data.baseUrl}/${this.product.image_thumbnail_path}`;
      }
    }
  }
</script>

<style lang="sass?outputStyle=expanded" scoped>
  @import "~PrestaKit/scss/custom/_variables.scss";
  .thumbnail {
      border: $gray-light 1px solid;
  }
</style>
