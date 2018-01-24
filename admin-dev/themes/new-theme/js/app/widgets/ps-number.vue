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
  <div class="ps-number">
    <input
      type="number"
      class="ps-number form-control"
      :class="{'danger' : danger}"
      :value="value"
      @keyup="onKeyup($event)"
      @focus="focusIn"
      @blur="focusOut($event)"
    />
    <div class="ps-number-button d-flex" v-if="buttons">
      <span class="ps-number-up" @click="increment"></span>
      <span class="ps-number-down" @click="decrement"></span>
    </div>
  </div>
</template>

<script>
  export default {
    props: {
      value: 0,
      danger: false,
      buttons: false,
    },
    methods: {
      onKeyup($event) {
        this.$emit('keyup', $event);
      },
      focusIn() {
        this.$emit('focus');
      },
      focusOut($event) {
        this.$emit('blur', $event);
      },
      increment() {
        const value = parseInt(this.value, 10) + 1;
        this.$emit('change', value);
      },
      decrement() {
        const value = parseInt(this.value, 10) - 1;
        this.$emit('change', value);
      },
    },
  };
</script>

<style lang="sass" type="text/scss" scoped>
  @import "../../../scss/config/_settings.scss";
  input[type=number]::-webkit-inner-spin-button,
  input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
  }
  input[type=number] {
    -moz-appearance:textfield;
  }
  .danger {
    border: 1px solid $danger;
    background-color: #fff;
    color: $gray-dark;
    &:focus {
      outline: none;
    }
  }
  .ps-number {
    position: relative;
    width: 95px;
    .ps-number-button {
      position: absolute;
      top: 1px;
      flex-direction: column;
      right: 34px;
      cursor: pointer;
      line-height: 17px;
      transition: all 0.2s ease;
      .product-actions & {
        right: 7px;
      }
    }
    .ps-number-up::before {
      font-family: 'Material Icons';
      content: "\E5C7";
      font-size: 20px;
      color: $gray-dark;
      position: relative;
    }
    .ps-number-down::before {
      font-family: 'Material Icons';
      content: "\E5C5";
      font-size: 20px;
      color: $gray-dark;
      bottom: 6px;
      position: relative;
    }
  }
</style>
