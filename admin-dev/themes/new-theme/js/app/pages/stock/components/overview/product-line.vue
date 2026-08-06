<!--*
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *-->
<template>
  <tr :class="{'low-stock':lowStock}">
    <td data-role="product-id">
      <div class="d-flex align-items-left">
        <PSCheckbox
          :id="id"
          :ref="id"
          :model="product"
          @checked="productChecked"
        />
        <p
          class="d-flex align-items-center ml-2"
        >
          {{ product.product_id }}
        </p>
      </div>
    </td>
    <td data-role="product-name">
      <div class="d-flex align-items-center">
        <PSMedia
          class="d-flex align-items-center ml-2"
          :thumbnail="thumbnail"
        >
          <a
            :href="product.product_url"
            class="product-name-link"
          >{{ product.product_name }}</a>
          <small
            v-if="hasCombination"
            class="product-combinations"
          >
            <br>
            {{ product.combination_name }}
          </small>
        </PSMedia>
      </div>
    </td>
    <td data-role="product-reference">
      {{ reference }}
    </td>
    <td data-role="product-supplier-name">
      {{ product.supplier_name }}
    </td>
    <td
      v-if="product.active"
      class="text-sm-center"
      data-role="product-active"
    >
      <i class="material-icons enable">check</i>
    </td>
    <td
      v-else
      class="text-sm-center"
    >
      <i class="material-icons disable">close</i>
    </td>
    <td
      class="text-sm-center"
      :class="{'stock-warning':lowStock}"
      data-role="physical-quantity"
    >
      {{ physical }}
      <span
        v-if="updatedQty"
        class="qty-update"
        :class="{'stock-warning':lowStock}"
      >
        <i class="material-icons rtl-flip">trending_flat</i>
        {{ physicalQtyUpdated }}
      </span>
    </td>
    <td
      class="text-sm-center"
      :class="{'stock-warning':lowStock}"
      data-role="reserved-quantity"
    >
      {{ product.product_reserved_quantity }}
    </td>
    <td
      class="text-sm-center"
      :class="{'stock-warning':lowStock}"
      data-role="available-quantity"
    >
      {{ product.product_available_quantity }}
      <span
        v-if="updatedQty"
        class="qty-update"
        :class="{'stock-warning':lowStock}"
      >
        <i class="material-icons rtl-flip">trending_flat</i>
        {{ availableQtyUpdated }}
      </span>
      <span
        v-if="lowStock"
        class="stock-warning ico ml-2"
        data-toggle="pstooltip"
        data-placement="top"
        data-html="true"
        :title="lowStockLevel"
      >!</span>
    </td>
    <td
      class="qty-spinner text-right"
      data-role="update-quantity"
    >
      <Spinner
        :product="product"
        @updateProductQty="updateProductQty"
      />
    </td>
  </tr>
</template>

<script lang="ts">
  import {defineComponent} from 'vue';
  import PSCheckbox from '@app/widgets/ps-checkbox.vue';
  import PSMedia from '@app/widgets/ps-media.vue';
  import {StockProduct} from '@app/pages/stock/components/overview/products-table.vue';
  import ProductDesc from '@app/pages/stock/mixins/product-desc';
  import {EventEmitter} from '@components/event-emitter';
  import Spinner from '@app/pages/stock/components/overview/spinner.vue';
  import TranslationMixin from '@app/pages/stock/mixins/translate';

  export interface StockProductToUpdate {
    product: StockProduct;
    delta: number;
  }

  export default defineComponent({
    props: {
      product: {
        type: Object,
        required: true,
      },
    },
    mixins: [TranslationMixin, ProductDesc],
    computed: {
      reference(): string {
        if (this.product.combination_reference !== 'N/A') {
          return this.product.combination_reference;
        }
        return this.product.product_reference;
      },
      updatedQty(): boolean {
        return !!this.product.qty;
      },
      physicalQtyUpdated(): number {
        return Number(this.physical) + Number(this.product.qty);
      },
      availableQtyUpdated(): number {
        return Number(this.product.product_available_quantity) + Number(this.product.qty);
      },
      physical(): number {
        const productAvailableQty = Number(this.product.product_available_quantity);
        const productReservedQty = Number(this.product.product_reserved_quantity);

        return productAvailableQty + productReservedQty;
      },
      lowStock(): boolean {
        return this.product.product_low_stock_alert;
      },
      lowStockLevel(): string {
        return `<div class="text-sm-left">
          <p>${this.trans('product_low_stock')}</p>
          <p><strong>${this.trans('product_low_stock_level')} ${this.product.product_low_stock_threshold}</strong></p>
        </div>`;
      },
      lowStockAlert(): string {
        return `<div class="text-sm-left">
          <p><strong>${this.trans('product_low_stock_alert')} ${this.product.product_low_stock_alert}</strong></p>
        </div>`;
      },
      id(): string {
        return `product-${this.product.product_id}${this.product.combination_id}`;
      },
    },
    methods: {
      productChecked(checkbox: any): void {
        if (checkbox.checked) {
          this.$store.dispatch('addSelectedProduct', checkbox.item);
        } else {
          this.$store.dispatch('removeSelectedProduct', checkbox.item);
        }
      },
      updateProductQty(productToUpdate: StockProductToUpdate): void {
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
      EventEmitter.on('toggleProductsCheck', (checked: boolean) => {
        const ref = this.id;

        if (this.$refs[ref]) {
          (<VCheckboxDatas> this.$refs[ref]).checked = checked;
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
  });
</script>

<style lang="scss" scoped>
@import '~@scss/config/_settings.scss';

.product-combinations {
  padding-top: var(--#{$cdk}size-4);
  color: var(--#{$cdk}primary-500);
}

.product-name-link {
  color: inherit;
  text-decoration: none;

  &:hover {
    color: var(--#{$cdk}primary-500);
    text-decoration: underline;
  }
}
</style>
