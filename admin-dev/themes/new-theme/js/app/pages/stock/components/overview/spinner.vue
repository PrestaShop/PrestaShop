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
    class="qty text-sm-right"
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
      :value="qty"
      :buttons="this.isActive"
      @change="onChange"
      @keyup="onKeyup($event)"
      @focus="focusIn"
      @blur="focusOut($event)"
    />
    <transition name="fade">
      <button v-if="isActive" class="check-button"><i class="material-icons">check</i></button>
    </transition>
  </form>
</template>

<script>
  import PSNumber from 'app/widgets/ps-number';
  export default {
    props: ['product'],
    computed: {
      qty() {
        if (!this.product.qty) {
          this.isEnabled = false;
          this.value = 0;
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
        if (!$(event.target).hasClass('ps-number') && !this.value) {
          this.isActive = false;
        }
        this.isEnabled = !!this.value;
      },
      sendQty() {
        const postUrl = this.product.edit_url;
        if (parseInt(this.product.qty, 10) !== 0 && !isNaN(parseInt(this.value, 10))) {
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

<style lang="sass" type="text/scss" scoped>
  @import "~jquery-ui-dist/jquery-ui.css";
  *{
    outline: none;
  }
  .fade-enter-active, .fade-leave-active {
    transition: opacity 0.2s ease;
  }
  .fade-enter, .fade-leave-to {
    opacity: 0
  }

</style>
