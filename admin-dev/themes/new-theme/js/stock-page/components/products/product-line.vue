<template>
  <tr>
    <td>
      <input type="checkbox" class="m-r-1">
      <img v-if="imagePath" :src="imagePath" class="thumbnail" />
      <div v-else class="no-img">

      </div>
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
      <span class="qty-update" v-if="updatedQty">
        <i class="material-icons">trending_flat</i>
        {{physicalQtyUpdated}}
      </span>
    </td>
    <td class="text-xs-center">
      {{ product.product_reserved_quantity }}
    </td>
    <td class="text-xs-center">
      {{ product.product_available_quantity }}
      <span class="qty-update" v-if="updatedQty">
        <i class="material-icons">trending_flat</i>
        {{availableQtyUpdated}}
      </span>
    </td>
    <td>
      <Spinner :product="product" />
    </td>
  </tr>
</template>

<script>
  import Spinner from './spinner';

  export default {
    props: ['product'],
    computed: {
      imagePath() {
        if(this.product.combination_thumbnail !== 'N/A') {
          return `${data.baseUrl}/${this.product.combination_thumbnail}`;
        }
        return null;
      },
      updatedQty() {
        return !!this.product.qty;
      },
      physicalQtyUpdated () {
        return Number(this.physical) + Number(this.product.qty);
      },
      availableQtyUpdated() {
        return Number(this.product.product_available_quantity) + Number(this.product.qty);
      },
      physical() {
        return Number(this.product.product_available_quantity) + Number(this.product.product_reserved_quantity);
      }
    },
    components: {
      Spinner
    }
  }
</script>

<style lang="sass?outputStyle=expanded" scoped>
  @import "~PrestaKit/scss/custom/_variables.scss";
  .thumbnail, .no-img {
      border: $gray-light 1px solid;
  }
  .no-img {
    background: white;
    width: 47px;
    height: 47px;
    display: inline-block;
    vertical-align: middle;
  }
  .qty-update {
    color: $brand-primary;
    .material-icons {
      vertical-align: middle;
    }
  }
</style>
