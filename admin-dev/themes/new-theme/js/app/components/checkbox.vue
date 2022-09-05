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
  <div class="md-checkbox md-checkbox-inline">
    <label>
      <input
        v-if="Array.isArray(checked)"
        type="checkbox"
        :checked="checked.includes(value)"
        :class="classes"
        :disabled="disabled"
        @change="change"
      >
      <input
        v-else
        type="checkbox"
        :checked="checked"
        :class="classes"
        :disabled="disabled"
        @change="$emit('input', $event.target.checked)"
      >

      <slot>
        <!-- - Fallback content -->
        <i class="md-checkbox-control" />
      </slot>
    </label>
  </div>
</template>

<script lang="ts">
  import Vue from 'vue';

  export default Vue.extend({
    model: {
      prop: 'checked',
      event: 'input',
    },
    props: {
      classes: {
        type: Array,
        default: () => ([
          'js-tab-checkbox',
        ]),
      },
      checked: {
        required: true,
        type: [Array, Number, String],
      },
      disabled: {
        type: Boolean,
        required: false,
        default: false,
      },
      value: {
        required: true,
        type: String,
      },
    },
    methods: {
      change(): void {
        if ((<Array<string>> this.checked).includes(this.value)) {
          (<Array<string>> this.checked).splice((<Array<string>> this.checked).indexOf(this.value), 1);
        } else {
          (<Array<string>> this.checked).push(this.value);
        }

        this.$emit('change');
      },
    },
  });
</script>
