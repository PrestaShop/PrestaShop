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
  <div class="row product-actions">
    <div
      class="col-md-8 qty d-flex align-items-center"
      :class="{'active' : isFocused}"
    >
      <PSCheckbox
        id="bulk-action"
        ref="bulk-action"
        class="mt-3"
        :isIndeterminate="isIndeterminate"
        @checked="bulkChecked"
      />
      <div class="ml-2">
        <small>{{trans('title_bulk')}}</small>
        <PSNumber
          class="bulk-qty"
          :danger="danger"
          :value="bulkEditQty"
          :buttons="this.isFocused"
          @focus="focusIn"
          @blur="focusOut($event)"
          @change="onChange"
          @keyup="onKeyUp"
        />
      </div>
    </div>
    <div class="col-md-4">
      <PSButton
        type="button"
        class="update-qty float-sm-right my-4 mr-2"
        :class="{'btn-primary': disabled }"
        :disabled="disabled"
        :primary="true"
        @click="sendQty"
      >
        <i class="material-icons">edit</i>
        {{trans('button_movement_type')}}
      </PSButton>
    </div>
  </div>
</template>

<script>
  import PSNumber from '@app/widgets/ps-number';
  import PSCheckbox from '@app/widgets/ps-checkbox';
  import PSButton from '@app/widgets/ps-button';
  import { EventBus } from '@app/utils/event-bus';

  export default {
    computed: {
      disabled() {
        return !this.$store.state.hasQty;
      },
      bulkEditQty() {
        return this.$store.state.bulkEditQty;
      },
      isIndeterminate() {
        const selectedProductsLng = this.selectedProductsLng;
        const productsLng = this.$store.state.products.length;
        const isIndeterminate = (selectedProductsLng > 0 && selectedProductsLng < productsLng);
        if (isIndeterminate) {
          this.$refs['bulk-action'].checked = true;
        }
        return isIndeterminate;
      },
      selectedProductsLng() {
        return this.$store.getters.selectedProductsLng;
      },
    },
    watch: {
      selectedProductsLng(value) {
        if (value === 0 && this.$refs['bulk-action']) {
          this.$refs['bulk-action'].checked = false;
          this.isFocused = false;
        }
        if (value === 1 && this.$refs['bulk-action']) {
          this.isFocused = true;
        }
      },
    },
    methods: {
      focusIn() {
        this.danger = !this.selectedProductsLng;
        this.isFocused = !this.danger;
        if (this.danger) {
          EventBus.$emit('displayBulkAlert', 'error');
        }
      },
      focusOut(event) {
        this.isFocused = $(event.target).hasClass('ps-number');
        this.danger = false;
      },
      bulkChecked(checkbox) {
        if (!checkbox.checked) {
          this.$store.dispatch('updateBulkEditQty', null);
        }
        if (!this.isIndeterminate) {
          EventBus.$emit('toggleProductsCheck', checkbox.checked);
        }
      },
      sendQty() {
        this.$store.dispatch('updateQtyByProductsId');
      },
      onChange(value) {
        this.$store.dispatch('updateBulkEditQty', value);
      },
      onKeyUp(event) {
        this.isFocused = true;
        this.$store.dispatch('updateBulkEditQty', event.target.value);
      },
    },
    data: () => ({
      isFocused: false,
      danger: false,
    }),
    components: {
      PSNumber,
      PSCheckbox,
      PSButton,
    },
  };
</script>
