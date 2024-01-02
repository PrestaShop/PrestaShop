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
  <div
    class="ps-select"
    :id="itemId"
  >
    <select
      class="form-control"
      v-model="selected"
      @change="onChange"
    >
      <option
        value="default"
        selected
      >
        <slot />
      </option>
      <option
        v-for="(item, index) in items"
        :key="index"
        :value="item[itemId]"
      >
        {{ item[itemName] }}
      </option>
    </select>
  </div>
</template>

<script lang="ts">
  import {defineComponent, PropType} from 'vue';

  export default defineComponent({
    props: {
      items: {
        type: Array as PropType<Array<Record<string, any>>>,
        required: true,
      },
      itemId: {
        type: String,
        required: false,
        default: '',
      },
      itemName: {
        type: String,
        required: false,
        default: '',
      },
    },
    methods: {
      onChange(): void {
        this.$emit('change', {
          value: this.selected,
          itemId: this.itemId,
        });
      },
    },
    data() {
      return {
        selected: 'default',
      };
    },
  });
</script>

<style lang="scss" scoped>
  @import '~@scss/config/_settings.scss';

  .ps-select {
    position: relative;
    select {
      appearance: none;
      border-radius: 0;
    }
    &::after {
      content: "\E313";
      font-family: 'Material Icons';
      color: $gray-medium;
      font-size: 20px;
      position: absolute;
      right: 5px;
      top: 5px;
    }
  }
</style>
