<!--**
     * 2007-2019 PrestaShop and Contributors
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
     * @copyright 2007-2019 PrestaShop SA and Contributors
     * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
     * International Registered Trademark & Property of PrestaShop SA
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
      />
      <input
        v-else
        type="checkbox"
        :checked="checked"
        :class="classes"
        :disabled="disabled"
        @change="$emit('input', $event.target.checked)"
      />

      <slot>
        <!-- - Fallback content -->
        <i class="md-checkbox-control"></i>
      </slot>
    </label>
  </div>
</template>

<script>
  export default {
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
      },
      disabled: {
        type: Boolean,
        required: false,
        default: false,
      },
      value: {
        required: true,
      },
    },
    methods: {
      change() {
        if (this.checked.includes(this.value)) {
          this.checked.splice(this.checked.indexOf(this.value), 1);
        } else {
          this.checked.push(this.value);
        }

        this.$emit('change');
      },
    },
  };
</script>
