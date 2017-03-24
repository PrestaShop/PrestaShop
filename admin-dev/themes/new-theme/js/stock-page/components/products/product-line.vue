<template>
  <tr v-if="product.hasCombination" class="has-combination">
    <td class="product-desc" colspan="4">
      <ProductDesc :name="product.product_name" :thumbnail="product.combination_thumbnail" />
    </td>
    <td colspan="3">
    </td>
  </tr>
  <tr v-else>
    <td>
      <input type="checkbox" class="m-r-1">
      <ProductDesc :name="productName" :thumbnail="product.combination_thumbnail" :class="productDescClass" :isCombination="isCombination" />
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
  import ProductDesc from './product/product-desc';

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
  .has-combination {
    background: $notice;
    .product-desc {
      padding-left : 35px;
    }
  }
</style>
