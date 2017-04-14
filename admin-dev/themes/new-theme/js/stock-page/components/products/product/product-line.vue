<template>
  <tr>
    <td class="flex p-r-1">
      <Checkbox :id="id" />
      <ProductDesc
        class="m-l-1"
        :has-combination="hasCombination"
        :name="productName"
        :thumbnail="product.combination_thumbnail"
        :combinationName="product.combination_name"
      />
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
  import Checkbox from '../../utils/checkbox';

  export default {
    props: ['product'],
    computed: {
      id() {
        return `${this.product.product_id}-${this.product.combination_id}`;
      },
      productName() {
        return this.product.product_name;
      },
      hasCombination() {
        return !!this.product.combination_id;
      },
      updatedQty() {
         if(isNaN(this.product.qty)) {
          return false;
        }
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
      ProductDesc,
      Checkbox
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
  .checkbox {
    width: 5%;
  }
</style>
