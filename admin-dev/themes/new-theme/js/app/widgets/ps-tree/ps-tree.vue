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
  <div class="ps-tree">
    <div class="mb-3 tree-header">
      <button class="btn btn-text text-uppercase pointer" @click="expand">
        <i class="material-icons">keyboard_arrow_down</i>
        <span v-if="translations">{{translations.expand}}</span>
      </button>
      <button class="btn btn-text float-right text-uppercase pointer" @click="reduce">
        <i class="material-icons">keyboard_arrow_up</i>
        <span v-if="translations">{{translations.reduce}}</span>
      </button>
    </div>
    <ul class="tree" :class="className">
      <li v-for="(element, index) in model">
        <PSTreeItem
          ref="item"
          :hasCheckbox="hasCheckbox"
          :model="element"
          :label="element.name"
          :translations="translations"
          :currentItem="currentItem"
          @checked="onCheck"
          @setCurrentElement ="setCurrentElement"
        />
      </li>
    </ul>
  </div>
</template>

<script>
  import PSTreeItem from './ps-tree-item';
  import { EventBus } from 'app/utils/event-bus';

  export default {
    name: 'PSTree',
    props: {
      model: Array,
      className: String,
      currentItem: String,
      hasCheckbox: Boolean,
      translations: {
        type: Object,
        required: false,
      },
    },
    methods: {
      onCheck(obj) {
        this.$emit('checked', obj);
      },
      expand() {
        EventBus.$emit('expand');
      },
      reduce() {
        EventBus.$emit('reduce');
      },
      setCurrentElement(id) {
        EventBus.$emit('setCurrentElement', id);
      },
    },
    components: {
      PSTreeItem,
    },
  };
</script>
