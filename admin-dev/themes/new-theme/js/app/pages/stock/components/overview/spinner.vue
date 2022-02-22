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

<script>
  import PSNumber from '@app/widgets/ps-number';

  const {$} = window;

  export default {
    props: {
      product: {
        type: Object,
        required: true,
      },
    },
    computed: {
      id() {
        return `qty-${this.product.product_id}-${this.product.combination_id}`;
      },
      classObject() {
        return {
          active: this.isActive,
          disabled: !this.isEnabled,
        };
      },
    },
    methods: {
      getQuantity() {
        if (!this.product.qty) {
          this.isEnabled = false;
          this.value = '';
        }
        return parseInt(this.value, 10);
      },
      onChange(val) {
        this.value = val;
        this.isEnabled = !!val;
      },
      deActivate() {
        this.isActive = false;
        this.isEnabled = false;
        this.value = null;
        this.product.qty = null;
      },
      onKeyup(event) {
        const val = event.target.value;

        if (val === 0) {
          this.deActivate();
        } else {
          this.isActive = true;
          this.isEnabled = true;
          this.value = val;
        }
      },
      focusIn() {
        this.isActive = true;
      },
      focusOut(event) {
        const value = parseInt(this.value, 10);

        if (
          !$(event.target).hasClass('ps-number')
          && (Number.isNaN(value) || value === 0)
        ) {
          this.isActive = false;
        }
        this.isEnabled = !!this.value;
      },
      sendQty() {
        const postUrl = this.product.edit_url;

        if (
          parseInt(this.product.qty, 10) !== 0
          && !Number.isNaN(parseInt(this.value, 10))
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
      value(val) {
        this.$emit('updateProductQty', {
          product: this.product,
          delta: val,
        });
      },
    },
    components: {
      PSNumber,
    },
    data: () => ({
      value: null,
      isActive: false,
      isEnabled: false,
    }),
  };
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
