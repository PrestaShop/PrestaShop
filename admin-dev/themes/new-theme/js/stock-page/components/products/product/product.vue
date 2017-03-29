<template>
  <tr>
    <td width="45%">
      <input type="checkbox" class="m-r-1">
      <ProductDesc :name="productName" :thumbnail="product.combination_thumbnail" :class="productDescClass" />
    </td>
    <td width="15%">
      {{ product.product_reference }}
    </td>
    <td width="15%">
      {{ product.supplier_name }}
    </td>
    <td width="10%" class="text-xs-center">
      {{ physical }}
      <span class="qty-update" v-if="updatedQty">
        <i class="material-icons">trending_flat</i>
        {{physicalQtyUpdated}}
      </span>
    </td>
    <td width="10%" class="text-xs-center">
      {{ product.product_reserved_quantity }}
    </td>
    <td width="10%" class="text-xs-center">
      {{ product.product_available_quantity }}
      <span class="qty-update" v-if="updatedQty">
        <i class="material-icons">trending_flat</i>
        {{availableQtyUpdated}}
      </span>
    </td>
    <td width="10%">
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
      isCombination() {
        return !!this.product.combination_id
      },
      productDescClass() {
        return  {
          'is-combination': this.isCombination
        }
      },
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