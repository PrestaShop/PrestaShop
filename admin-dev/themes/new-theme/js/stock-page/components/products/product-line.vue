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
    <td class="p-r-1">
      {{ product.supplier_name }}
    </td>
    <td class="text-xs-center p-r-1">
      {{ physical }}
      <span class="qty-update" v-if="hasQty">
        <i class="material-icons">trending_flat</i>
        {{physicalQtyUpdated}}
      </span>
    </td>
    <td class="text-xs-center">
      {{ product.product_reserved_quantity }}
    </td>
    <td class="text-xs-center">
      {{ product.product_available_quantity }}
      <span class="qty-update" v-if="hasQty">
        <i class="material-icons">trending_flat</i>
        {{availableQtyUpdated}}
      </span>
    </td>
    <td>
      <Spinner :product="product" v-on:valueChanged="onValueChanged" />
    </td>
  </tr>
</template>

<script>
  import Spinner from './spinner';

  export default {
    props: ['product'],
    computed: {
      imagePath() {
        return `${data.baseUrl}/${this.product.combination_thumbnail}`;
      },
      hasQty() {
        return !!this.value;
      },
      physicalQtyUpdated () {
        return Number(this.physical) + Number(this.value);
      },
      availableQtyUpdated() {
        return Number(this.product.product_available_quantity) + Number(this.value);
      },
      physical() {
        return Number(this.product.product_available_quantity) + Number(this.product.product_reserved_quantity);
      }
    },
    data() {
      return {
        value: 0
      }
    },
    methods: {
      onValueChanged(val) {
        this.value = val;
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
  .qty-update {
    color: $brand-primary;
    .material-icons {
      vertical-align: middle;
    }
  }
</style>
