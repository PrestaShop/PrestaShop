<template>
  <tr>
    <td class="flex p-r-1">
      <PSCheckbox :id="id" />
      <PSMedia
        class="m-l-1"
        :thumbnail="thumbnail"
      >
        <p>
          {{ product.product_name }}
          <small v-if="hasCombination"><br />
            {{ combinationName }}
          </small>
        </p>
      </PSMedia>
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
    <td class="qty-spinner">
      <Spinner :product="product" class="pull-xs-right" />
    </td>
  </tr>
</template>
<script>
  import Spinner from './spinner';
  import PSCheckbox from 'app/widgets/ps-checkbox';
  import PSMedia from 'app/widgets/ps-media';
  import ProductDesc from 'app/pages/stock/mixins/product-desc';

  export default {
    props: ['product'],
    mixins: [ProductDesc],
    computed: {
      id() {
        return `${this.product.product_id}-${this.product.combination_id}`;
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
      PSMedia,
      PSCheckbox
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
