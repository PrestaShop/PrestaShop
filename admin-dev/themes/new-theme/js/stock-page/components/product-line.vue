<template>
  <tr>
    <td>
      <input type="checkbox" :value="product.product_id">
      <img :src="imagePath" />
      <span>{{ product.product_name }}</span>
    </td>
    <td>
      {{ product.product_reference }}
    </td>
    <td>
      {{ product.supplier_name }}
    </td>
    <td>
      {{ product.product_available_quantity + product.product_reserved_quantity }}
    </td>
    <td>
      {{ product.product_reserved_quantity }}
    </td>
    <td>
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
