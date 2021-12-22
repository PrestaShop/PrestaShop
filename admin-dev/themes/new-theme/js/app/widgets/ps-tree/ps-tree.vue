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
  <div class="ps-tree">
    <div class="mb-3 tree-header">
      <button
        class="btn btn-text text-uppercase pointer"
        @click="expand"
      >
        <i class="material-icons">keyboard_arrow_down</i>
        <span v-if="translations">{{ translations.expand }}</span>
      </button>
      <button
        class="btn btn-text float-right text-uppercase pointer"
        @click="reduce"
      >
        <i class="material-icons">keyboard_arrow_up</i>
        <span v-if="translations">{{ translations.reduce }}</span>
      </button>
    </div>
    <ul
      class="tree"
      :class="className"
    >
      <li
        v-for="(element, index) in model"
        :key="index"
      >
        <PSTreeItem
          ref="item"
          :has-checkbox="hasCheckbox"
          :model="element"
          :label="element.name"
          :translations="translations"
          :current-item="currentItem"
          @checked="onCheck"
          @setCurrentElement="setCurrentElement"
        />
      </li>
    </ul>
  </div>
</template>

<script lang="ts">
  import Vue from 'vue';
  import {EventBus} from '@app/utils/event-bus';
  import PSTreeItem from './ps-tree-item.vue';

  export default Vue.extend({
    name: 'PSTree',
    props: {
      model: {
        type: Array,
        default: () => ([]),
      },
      className: {
        type: String,
        default: '',
      },
      currentItem: {
        type: String,
        default: '',
      },
      hasCheckbox: {
        type: Boolean,
        default: false,
      },
      translations: {
        type: Object,
        required: false,
        default: () => ({}),
      },
    },
    methods: {
      onCheck(obj: any): void {
        this.$emit('checked', obj);
      },
      expand(): void {
        EventBus.$emit('expand');
      },
      reduce(): void {
        EventBus.$emit('reduce');
      },
      setCurrentElement(id: string | number): void {
        EventBus.$emit('setCurrentElement', id);
      },
    },
    components: {
      PSTreeItem,
    },
  });
</script>
