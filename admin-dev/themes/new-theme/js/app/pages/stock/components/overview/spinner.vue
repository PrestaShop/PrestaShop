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
  <form
    class="qty text-xs-right"
    :class="classObject"
    @mouseover="focusIn"
    @mouseleave="focusOut($event)"
    @submit.prevent="sendQty"
  >
    <input
      name="qty"
      class="edit-qty"
      type="number"
      placeholder="0"
      pattern= "[-+]?[0-9]*[.,]?[0-9]+"
      :id="id"
      v-model="qty"
      @keyup="onKeyup($event.target.value)"
      @focus="focusIn"
      @blur="focusOut($event)"
    >
    <transition name="fade">
      <button v-if="isActive" class="check-button"><i class="material-icons">check</i></button>
    </transition>
  </form>
</template>

<script>
  export default {
    props: ['product'],
    mounted() {
      const self = this;
      $(`#${this.id}`).spinner({
        spin(event, ui) {
          self.value = ui.value;
          self.isEnabled = !!self.value;
        },
      });
    },
    computed: {
      qty() {
        if (!this.product.qty) {
          this.isActive = false;
          this.isEnabled = false;
          this.value = 0;
          this.product.qty = 0;
        }
        return this.product.qty;
      },
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
      onKeyup(val) {
        if (!isNaN(parseInt(val, 10))) {
          this.isActive = true;
          this.isEnabled = true;
          this.value = val;
        }
      },
      focusIn() {
        this.isActive = true;
      },
      focusOut(event) {
        if (!$(event.relatedTarget).hasClass('check-button') && !this.value) {
          this.isActive = false;
        }
        this.isEnabled = !!this.value;
      },
      sendQty() {
        const postUrl = this.product.edit_url;
        if (this.product.qty && !isNaN(parseInt(this.value, 10))) {
          this.$store.dispatch('updateQtyByProductId', {
            url: postUrl,
            delta: this.value,
          });
          this.isActive = false;
          this.isEnabled = false;
          this.value = 0;
          this.product.qty = 0;
        }
      },
    },
    watch: {
      value(val) {
        this.$store.dispatch('updateProductQty', {
          product_id: this.product.product_id,
          combination_id: this.product.combination_id,
          delta: val,
        });
      },
    },
    data: () => ({
      value: null,
      isActive: false,
      isEnabled: false,
    }),
  };
</script>

<style lang="sass" scoped>
  @import "~jquery-ui/themes/base/minified/jquery.ui.spinner.min.css";
  @import "~PrestaKit/scss/custom/_variables.scss";
  *{
    outline: none;
  }
  .qty {
      position: relative;
      width: 120px;
      .check-button {
        outline:none;
        opacity: 0;
        position: absolute;
        top: 3.5px;
        right: 0;
        border: none;
        height: 31px;
        width: 40px;
        background: $brand-primary;
        z-index: 2;
        border-left: 10px solid white;
        .material-icons {
          color: white;
          vertical-align: middle;
        }
        &:hover {
          background: $primary-hover;
        }
      }
  }
  .qty.active {
    .check-button {
      opacity: 1;
    }
  }
  .qty.disabled {
    .check-button {
      background: $gray-light;
      cursor: default;
    }
  }
  .fade-enter-active, .fade-leave-active {
    transition: opacity 0.2s ease;
  }
  .fade-enter, .fade-leave-to {
    opacity: 0
  }
  .edit-qty {
    text-indent: 5px;
    height: 33px;
    width: 70px;
    border: 1px solid $gray-light;
    margin: 3px 0;
  }
  input[type=number]::-webkit-inner-spin-button,
  input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
}
</style>
