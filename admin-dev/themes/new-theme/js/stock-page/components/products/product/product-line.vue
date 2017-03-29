<template>
  <tr>
    <td>
      <input type="checkbox" class="m-r-1">
      <ProductDesc :name="productName" :thumbnail="product.combination_thumbnail" />
    </td>
    <td>
      {{ product.product_reference }}
    </td>
    <td>
      {{ product.supplier_name }}
    </td>
    <td class="text-xs-center">
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
      <Spinner :product="product" class="pull-xs-right" />
    </td>
  </tr>
</template>
<script>
  import Spinner from './spinner';
  import ProductDesc from './product-desc';

  export default {
    props: ['product'],
    computed: {
      productName() {
        if(this.product.combination_id !== 0) {
          return this.product.combination_name;
        }
        return this.product.product_name;
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
      Spinner,
      ProductDesc
    }
  }
</script>

<style lang="sass?outputStyle=expanded" scoped>
  @import "~PrestaKit/scss/custom/_variables.scss";
  .qty-update {
    color: $brand-primary;
    .material-icons {
      vertical-align: middle;
    }
  }
</style>
