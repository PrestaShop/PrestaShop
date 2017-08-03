<!--**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *-->
<template>
  <tr>
    <td class="flex p-r-1">
      <PSCheckbox
        id=""
        model=""
      />
      <PSMedia
        class="ml-1"
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
      {{ reference }}
    </td>
    <td>
      {{ product.supplier_name }}
    </td>
    <td v-if="product.active" class="text-xs-center">
      <i class="material-icons enable">check</i>
    </td>
    <td v-else class="text-xs-center">
      <i class="material-icons disable">close</i>
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
      <Spinner :product="product" class="float-xs-right" />
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
      reference() {
        if (this.product.combination_reference !== 'N/A') {
          return this.product.combination_reference;
        }
        return this.product.product_reference;
      },
      updatedQty() {
        return !!this.product.qty;
      },
      physicalQtyUpdated() {
        return Number(this.physical) + Number(this.product.qty);
      },
      availableQtyUpdated() {
        return Number(this.product.product_available_quantity) + Number(this.product.qty);
      },
      physical() {
        const productAvailableQty = Number(this.product.product_available_quantity);
        const productReservedQty = Number(this.product.product_reserved_quantity);
        return productAvailableQty + productReservedQty;
      },
    },
    components: {
      Spinner,
      PSMedia,
      PSCheckbox,
    },
  };
</script>

<style lang="sass" scoped>
  @import "../../../../../../scss/config/_settings.scss";
  .qty-update {
    color: $brand-primary;
    .material-icons {
      vertical-align: middle;
    }
  }
  .checkbox {
    width: 5%;
  }
  .enable {
    color: $success;
  }
  .disable {
    color: $danger;
  }
</style>
