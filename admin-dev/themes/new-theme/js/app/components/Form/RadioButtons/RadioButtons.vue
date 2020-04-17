<!--**
 * 2007-2020 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *-->
<template>
  <div class="radio-buttons">
    <slot name="radios" :setter="setValue" :chosen="chosen"></slot>
  </div>
</template>

<script>
export default {
  name: "RadioButtons",
  props: {
    chosen: {
      type: String,
      default: 0
    },
    storeName: {
      type: String,
      default: "radioButtons",
      required: true
    }
  },
  data: () => ({
    active: null
  }),
  mounted() {
    this.active = this.chosen;
  },
  methods: {
    /**
     * Changing the active state of every radio button and pushing productType state to the store
     *
     * @param {Number} value of the radio button clicked
     */
    setValue(value) {
      this.active = parseInt(value);

      this.$store.dispatch("setGlobalValue", {
        globalState: { label: this.storeName, value }
      });
    }
  }
};
</script>

<style lang="scss" type="text/scss"></style>
