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
        :is-indeterminate="isIndeterminate()"
        @checked="bulkChecked"
      />
      <div class="ml-2">
        <small>{{ trans('title_bulk') }}</small>
        <PSNumber
          class="bulk-qty"
          :danger="danger"
          :value="bulkValue"
          :buttons="isFocused"
          :hover-buttons="isFocused"
          @keyup="onKeyup($event)"
          @keydown="onKeydown($event)"
          @change="onChange($event)"
          @focus="focusIn($event)"
          @blur="focusOut"
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
        {{ trans('button_movement_type') }}
      </PSButton>
    </div>
  </div>
</template>

<script lang="ts">
  import PSNumber from '@app/widgets/ps-number.vue';
  import PSCheckbox from '@app/widgets/ps-checkbox.vue';
  import PSButton from '@app/widgets/ps-button.vue';
  import {EventEmitter} from '@components/event-emitter';
  import {defineComponent} from 'vue';
  import TranslationMixin from '@app/pages/stock/mixins/translate';
  import isNumber from 'lodash/isNumber';

  export default defineComponent({
    computed: {
      disabled(): boolean {
        return !this.$store.state.hasQty || this.bulkValue === 0;
      },
      selectedProductsLng(): any {
        return this.$store.getters.selectedProductsLng;
      },
    },
    mixins: [TranslationMixin],
    watch: {
      bulkValue(val: number): void {
        if (isNumber(val)) {
          this.$store.dispatch('updateBulkEditQty', val);
        }
      },
      selectedProductsLng(value: number): void {
        if (value === 0 && this.$refs['bulk-action']) {
          (<HTMLInputElement> this.$refs['bulk-action']).checked = false;
          this.isFocused = false;
        }
        if (value === 1 && this.$refs['bulk-action']) {
          this.isFocused = true;
        }
      },
    },
    methods: {
      isChecked(): boolean {
        return (<HTMLInputElement> this.$refs['bulk-action']).checked;
      },
      isIndeterminate(): boolean {
        const {selectedProductsLng} = this;
        const productsLng = this.$store.state.products.length;
        const isIndeterminate = (selectedProductsLng > 0 && selectedProductsLng < productsLng);

        if (isIndeterminate) {
          (<HTMLInputElement> this.$refs['bulk-action']).checked = true;
        }
        return isIndeterminate;
      },
      focusIn(event: Event): void {
        this.danger = !this.selectedProductsLng;
        this.isFocused = !this.danger;
        if (this.danger) {
          EventEmitter.emit('displayBulkAlert', 'error');
        } else {
          (<HTMLInputElement>event.target).select();
        }
      },
      focusOut(): void {
        this.isFocused = this.isChecked();
        this.danger = false;
      },
      bulkChecked(checkbox: HTMLInputElement): void {
        if (!checkbox.checked) {
          this.bulkValue = '';
        }
        if (!this.isIndeterminate()) {
          EventEmitter.emit('toggleProductsCheck', checkbox.checked);
        }
      },
      sendQty(): void {
        this.$store.state.hasQty = false;
        this.$store.dispatch('updateQtyByProductsId');
      },
      onChange(event: Event): void {
        if (this.isChecked()) {
          const value = (<HTMLInputElement>event.target).value !== ''
            ? parseInt((<HTMLInputElement>event.target).value, 10)
            : 0;
          this.bulkValue = value;
          this.disabled = !!value;
        }
      },
      onKeydown(event: KeyboardEvent): void {
        if (event.key === '.' || event.key === ',') {
          event.preventDefault();
        }
      },
      onKeyup(event: KeyboardEvent): void {
        if (this.isChecked() && event.key !== '-') {
          const value = (<HTMLInputElement>event.target).value !== ''
            ? parseInt((<HTMLInputElement>event.target).value, 10)
            : 0;
          this.bulkValue = value;
          this.disabled = !!value;
        }
      },
    },
    data() {
      return {
        bulkValue: '' as string | number,
        isFocused: false,
        danger: false,
      };
    },
    components: {
      PSNumber,
      PSCheckbox,
      PSButton,
    },
  });
</script>
