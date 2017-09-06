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
  <input
    type="number"
    class="ps-number"
    :class="{'danger' : danger}"
    :value="value"
    @keyup="onKeyup($event)"
    @focus="focusIn"
    @blur="focusOut($event)"
  />
</template>

<script>
  export default {
    props: {
      value: 0,
      danger: false,
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
    },
    mounted() {
      const self = this;
      $(this.$el).spinner({
        spin(event, ui) {
          self.$emit('change', ui.value);
        },
      });
    },
  };
</script>

<style lang="sass" scoped>
  @import "../../../scss/config/_settings.scss";
  .ps-number {
    text-indent: 5px;
    height: 33px;
    width: 100px;
    border: 1px solid $gray-light;
    margin: 3px 0;
  }
  input[type=number]::-webkit-inner-spin-button,
  input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
  }
  .danger {
    border: 1px solid $danger;
    background-color: #fff;
    color: $gray-dark;
    &:focus {
      outline: none;
    }
  }
</style>
