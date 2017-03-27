<template>
  <tr v-if="product.hasCombination">
    <td colspan="7" class="p-y-0">
      <table width="100%">
        <tr class="has-combination">
          <td class="product-desc" colspan="4">
            <ProductDesc :name="product.product_name" :thumbnail="product.combination_thumbnail" />
          </td>
          <td colspan="3 text-xs-right">
            <button type="button" class="m-r-3 pull-xs-right btn btn-tertiary-outline m-l-1"><i class="material-icons m-r-1">mode_edit</i>Edit all combinations</button>
            <strong class="pull-xs-right">{{ totalCombinations }}</strong>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <Product v-else :product="product" />
</template>

<script>
  import Spinner from './spinner';
  import ProductDesc from './product-desc';
  import Product from './product';

  export default {
    props: ['product'],
    computed: {
      totalCombinations() {
        if(this.product.total_combinations > 1) {
          let combinationsPerPage = this.$store.state.combinationsPerPage;

          if(this.product.total_combinations > combinationsPerPage) {
            return `${combinationsPerPage}/${this.product.total_combinations} combinations`;
          }
          return `${this.product.total_combinations} combinations`;
        }
        else {
          return `${this.product.total_combinations} combination`;
        }
      }
    },
    components: {
      Product,
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
  .has-combination {
    background: $notice;
    .product-desc {
      padding-left : 35px;
    }
  }
</style>
