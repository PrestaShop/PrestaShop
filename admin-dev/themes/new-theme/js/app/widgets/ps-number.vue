<!--**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
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
        const value = parseInt(this.value, 10);
        this.$emit('change', isNaN(value) ? 0 : value + 1);
      },
      decrement() {
        const value = parseInt(this.value, 10);
        this.$emit('change', isNaN(value) ? -1 : value - 1);
      },
    },
  };
</script>
