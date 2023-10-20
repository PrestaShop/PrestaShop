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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *-->
<template>
  <div
    class="ps-tree-items"
    :class="{className}"
  >
    <div
      class="d-flex tree-name"
      :class="{active: active, disable: model.disable}"
      @click="clickElement"
    >
      <button
        class="btn btn-text"
        :class="[{hidden: isHidden}, chevronStatus]"
      >
        <span
          v-if="translations"
          class="sr-only"
        >{{ model.open ? translations.reduce : translations.expand }}</span>
      </button>
      <PSCheckbox
        :ref="model.name"
        :id="id.toString()"
        :model="model"
        @checked="onCheck"
        v-if="hasCheckbox"
      />
      <span
        class="tree-label"
        :class="{warning: isWarning}"
      >{{ model.name }}</span>
      <span
        class="tree-extra-label d-sm-none d-xl-inline-block"
        v-if="displayExtraLabel"
      >{{ getExtraLabel }}</span>
      <span
        class="tree-extra-label-mini d-xl-none"
        v-if="displayExtraLabel"
      >{{ model.extraLabel }}</span>
    </div>
    <ul
      v-show="open"
      v-if="isFolder"
      class="tree"
    >
      <li
        v-for="(element, index) in model.children"
        :key="index"
        class="tree-item"
        :class="{disable: model.disable}"
      >
        <PSTreeItem
          :ref="element.id"
          :class="className"
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
  import PSCheckbox from '@app/widgets/ps-checkbox.vue';
  import {EventEmitter} from '@components/event-emitter';
  import {defineComponent} from 'vue';

  export default defineComponent({
    name: 'PSTreeItem',
    props: {
      model: {
        type: Object,
        required: true,
      },
      className: {
        type: String,
        required: false,
        default: '',
      },
      hasCheckbox: {
        type: Boolean,
        required: false,
      },
      translations: {
        type: Object,
        required: false,
        default: () => ({}),
      },
      currentItem: {
        type: String,
        required: false,
        default: '',
      },
    },
    computed: {
      id(): number {
        return this.model.id.toString();
      },
      isFolder(): boolean {
        return this.model.children && this.model.children.length;
      },
      displayExtraLabel(): boolean {
        return this.isFolder && this.model.extraLabel;
      },
      getExtraLabel(): string {
        let extraLabel = '';

        if (this.model.extraLabel && this.model.extraLabel === 1) {
          extraLabel = this.translations.extra_singular;
        } else if (this.model.extraLabel) {
          extraLabel = this.translations.extra.replace('%d', this.model.extraLabel);
        }

        return extraLabel;
      },
      isHidden(): boolean {
        return !this.isFolder;
      },
      chevronStatus(): string {
        return this.open ? 'open' : 'closed';
      },
      isWarning(): boolean {
        return !this.isFolder && this.model.warning;
      },
      active(): boolean {
        return this.model.full_name === this.currentItem;
      },
    },
    methods: {
      setCurrentElement(el: any): void {
        if (this.$refs[el]) {
          this.openTreeItemAction();
          this.current = true;
          this.parentElement(this.$parent);
        } else {
          this.current = false;
        }
      },
      parentElement(parent: any): void {
        if (parent.clickElement) {
          parent.clickElement();
          this.parentElement(parent.$parent);
        }
      },
      clickElement(): boolean | void {
        return !this.model.disable ? this.openTreeItemAction() : false;
      },
      openTreeItemAction(): void {
        this.setCurrentElement(this.model.full_name);
        if (this.isFolder) {
          this.open = !this.open;
        } else {
          EventEmitter.emit('lastTreeItemClick', {
            item: this.model,
          });
        }
      },
      onCheck(obj: any): void {
        this.$emit('checked', obj);
      },
    },
    mounted() {
      EventEmitter.on('toggleCheckbox', (tag: any) => {
        const checkbox = this.$refs[tag];

        if (checkbox) {
          (<VCheckbox>checkbox).$data.checked = !(<VCheckbox>checkbox).$data.checked;
        }
      });
      EventEmitter.on('expand', () => {
        this.open = true;
      });
      EventEmitter.on('reduce', () => {
        this.open = false;
      });
      EventEmitter.on('setCurrentElement', (el: HTMLElement) => {
        this.setCurrentElement(el);
      });
      this.setCurrentElement(this.currentItem);
    },
    components: {
      PSCheckbox,
    },
    data: () => ({
      open: false,
      current: false,
    }),
  });
</script>
