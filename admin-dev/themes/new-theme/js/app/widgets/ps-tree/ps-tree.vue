<!--*
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *-->
<template>
  <div class="ps-tree">
    <div class="mb-3 tree-header">
      <button
        class="btn btn-text text-uppercase pointer"
        @click="expand"
        data-action="expand"
      >
        <i class="material-icons">keyboard_arrow_down</i>
        <span v-if="translations">{{ translations.expand }}</span>
      </button>
      <button
        class="btn btn-text float-right text-uppercase pointer"
        @click="reduce"
        data-action="reduce"
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
  import {defineComponent, PropType} from 'vue';
  import {EventEmitter} from '@components/event-emitter';
  import PSTreeItem from './ps-tree-item.vue';

  export default defineComponent({
    name: 'PSTree',
    props: {
      model: {
        type: Array as PropType<Array<Record<string, any>>>,
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
        EventEmitter.emit('expand');
      },
      reduce(): void {
        EventEmitter.emit('reduce');
      },
      setCurrentElement(id: string | number): void {
        EventEmitter.emit('setCurrentElement', id);
      },
    },
    components: {
      PSTreeItem,
    },
  });
</script>
