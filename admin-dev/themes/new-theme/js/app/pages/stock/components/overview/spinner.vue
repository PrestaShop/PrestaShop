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
  <form
    class="qty"
    :class="classObject"
    @mouseover="focusIn"
    @mouseleave="focusOut($event)"
    @submit.prevent="sendQty"
  >
    <PSNumber
      name="qty"
      class="edit-qty"
      placeholder="0"
      pattern="\d*"
      step="1"
      :buttons="true"
      :hover-buttons="true"
      :value="getQuantity()"
      @change="onChange"
      @keyup="onKeyup($event)"
      @focus="focusIn"
      @blur="focusOut($event)"
    />
    <transition name="fade">
      <button
        v-if="isActive"
        class="check-button"
      >
        <i class="material-icons">check</i>
      </button>
    </transition>
  </form>
</template>

<script lang="ts">
  import PSNumber from '@app/widgets/ps-number.vue';
  import Vue from 'vue';

  const {$} = window;

  export default Vue.extend({
    props: {
      product: {
        type: Object,
        required: true,
      },
    },
    computed: {
      id(): string {
        return `qty-${this.product.product_id}-${this.product.combination_id}`;
      },
      classObject(): Record<string, any> {
        return {
          active: this.isActive,
          disabled: !this.isEnabled,
        };
      },
    },
    methods: {
      getQuantity(): number {
        if (!this.product.qty) {
          this.isEnabled = false;
          this.value = 0;
        }
        return Math.round(<number> this.value);
      },
      onChange(val: number): void {
        this.value = val;
        this.isEnabled = !!val;
      },
      deActivate(): void {
        this.isActive = false;
        this.isEnabled = false;
        this.value = null;
        this.product.qty = null;
      },
      onKeyup(event: Event): void {
        const val = (<HTMLInputElement>event.target).value;

        if (parseInt(val, 10) === 0) {
          this.deActivate();
        } else {
          this.isActive = true;
          this.isEnabled = true;
          this.value = parseInt(val, 10);
        }
      },
      focusIn(): void {
        this.isActive = true;
      },
      focusOut(event: Event): void {
        const value = Math.round(<number> this.value);

        if (
          !$(<HTMLElement>event.target).hasClass('ps-number')
          && (Number.isNaN(value) || value === 0)
        ) {
          this.isActive = false;
        }
        this.isEnabled = !!this.value;
      },
      sendQty(): void {
        const postUrl = this.product.edit_url;

        if (
          parseInt(this.product.qty, 10) !== 0
          && !Number.isNaN(Math.round(<number> this.value))
        ) {
          this.$store.dispatch('updateQtyByProductId', {
            url: postUrl,
            delta: this.value,
          });
          this.deActivate();
        }
      },
    },
    watch: {
      value(val: number): void {
        this.$emit('updateProductQty', {
          product: this.product,
          delta: val,
        });
      },
    },
    components: {
      PSNumber,
    },
    data() {
      return {
        value: null as null | number,
        isActive: false,
        isEnabled: false,
      };
    },
  });
</script>

<style lang="scss" type="text/scss" scoped>
  @import "~jquery-ui-dist/jquery-ui.css";
  * {
    outline: none;
  }
  .fade-enter-active,
  .fade-leave-active {
    transition: opacity 0.2s ease;
  }
  .fade-enter,
  .fade-leave-to {
    opacity: 0;
  }
</style>
