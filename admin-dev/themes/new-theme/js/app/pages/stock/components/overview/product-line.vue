<!--**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *-->
<template>
  <tr :class="{'low-stock':lowStock}">
    <td>
      <div class="d-flex align-items-center">
        <PSCheckbox
          :id="id"
          :ref="id"
          :model="product"
          @checked="productChecked"
        />
        <PSMedia
          class="d-flex align-items-center ml-2"
          :thumbnail="thumbnail"
        >
          <p>
            {{ product.product_name }}
            <small v-if="hasCombination"><br />
              {{ combinationName }}
            </small>
          </p>
        </PSMedia>
      </div>
    </td>
    <td>
      {{ reference }}
    </td>
    <td>
      {{ product.supplier_name }}
    </td>
    <td v-if="product.active" class="text-sm-center">
      <i class="material-icons enable">check</i>
    </td>
    <td v-else class="text-sm-center">
      <i class="material-icons disable">close</i>
    </td>
    <td class="text-sm-center" :class="{'stock-warning':lowStock}">
      {{ physical }}
      <span v-if="updatedQty" class="qty-update" :class="{'stock-warning':lowStock}">
        <i class="material-icons">trending_flat</i>
        {{physicalQtyUpdated}}
      </span>
    </td>
    <td class="text-sm-center" :class="{'stock-warning':lowStock}">
      {{ product.product_reserved_quantity }}
    </td>
    <td class="text-sm-center" :class="{'stock-warning':lowStock}">
      {{ product.product_available_quantity }}
      <span v-if="updatedQty" class="qty-update" :class="{'stock-warning':lowStock}">
        <i class="material-icons">trending_flat</i>
        {{availableQtyUpdated}}
      </span>
      <span v-if="lowStock" class="stock-warning ico ml-2" data-toggle="pstooltip" data-placement="top" data-html="true" :title="lowStockLevel">!</span>
    </td>
    <td class="qty-spinner text-right">
      <Spinner :product="product" @updateProductQty="updateProductQty" />
    </td>
  </tr>
</template>

<script>
  import PSCheckbox from '@app/widgets/ps-checkbox';
  import PSMedia from '@app/widgets/ps-media';
  import ProductDesc from '@app/pages/stock/mixins/product-desc';
  import { EventBus } from '@app/utils/event-bus';
  import Spinner from '@app/pages/stock/components/overview/spinner';
  import _ from 'lodash';

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
      lowStock() {
        return this.product.product_low_stock_alert;
      },
      lowStockLevel() {
        return `<div class="text-sm-left">
                  <p>${this.trans('product_low_stock')}</p>
                  <p><strong>${this.trans('product_low_stock_level')} ${this.product.product_low_stock_threshold}</strong></p>
                </div>`;
      },
      lowStockAlert() {
        return `<div class="text-sm-left">
                  <p><strong>${this.trans('product_low_stock_alert')} ${this.product.product_low_stock_alert}</strong></p>
                </div>`;
      },
      id() {
        return `product-${this.product.product_id}${this.product.combination_id}`;
      },
    },
    methods: {
      productChecked(checkbox) {
        if (checkbox.checked) {
          this.$store.dispatch('addSelectedProduct', checkbox.item);
        } else {
          this.$store.dispatch('removeSelectedProduct', checkbox.item);
        }
      },
      updateProductQty(productToUpdate) {
        const updatedProduct = {
          product_id: productToUpdate.product.product_id,
          combination_id: productToUpdate.product.combination_id,
          delta: productToUpdate.delta,
        };
        this.$store.dispatch('updateProductQty', updatedProduct);
        if (productToUpdate.delta) {
          this.$store.dispatch('addProductToUpdate', updatedProduct);
        } else {
          this.$store.dispatch('removeProductToUpdate', updatedProduct);
        }
      },
    },
    mounted() {
      EventBus.$on('toggleProductsCheck', (checked) => {
        const ref = this.id;
        if (this.$refs[ref]) {
          this.$refs[ref].checked = checked;
        }
      });
      $('[data-toggle="pstooltip"]').pstooltip();
    },
    data: () => ({
      bulkEdition: false,
    }),
    components: {
      Spinner,
      PSMedia,
      PSCheckbox,
    },
  };
</script>
