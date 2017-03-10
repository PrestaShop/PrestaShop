<template>
  <tr>
    <td>
      <input type="checkbox" :value="product.product_id" class="m-r-1">
      <img :src="imagePath" class="thumbnail" />
      <span class="m-l-1">{{ product.product_name }}</span>
    </td>
    <td class="text-xs-center p-r-1">
      {{ product.product_reference }}
    </td>
    <td class="p-r-1">
      {{ product.supplier_name }}
    </td>
    <td class="text-xs-center p-r-1">
      {{ product.product_available_quantity + product.product_reserved_quantity }}
    </td>
    <td class="text-xs-center">
      {{ product.product_reserved_quantity }}
    </td>
    <td class="text-xs-center">
      {{ product.product_available_quantity }}
    </td>
    <td>
      <Spinner :value="product.product_id" />
    </td>
  </tr>
</template>

<script>
  import Spinner from './spinner';

  export default {
    props: ['product'],
    computed: {
      imagePath: function() {
        return `${data.baseUrl}/${this.product.image_thumbnail_path}`;
      }
    },
    components: {
      Spinner
    }
  }
</script>

<style lang="sass?outputStyle=expanded" scoped>
  @import "~PrestaKit/scss/custom/_variables.scss";
  .thumbnail {
      border: $gray-light 1px solid;
  }
  .edit-qty {
    width: 90px;
    height: 34px;
    border: $gray-medium 1px solid;
  }
</style>
