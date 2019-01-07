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
  <div class="ps-tree-items" :class="{className}">
    <div class="d-flex tree-name" :class="{active: active, disable: model.disable}" @click="clickElement">
      <button class="btn btn-text" :class="[{hidden: isHidden}, chevronStatus]">
        <span v-if="translations" class="sr-only">{{this.model.open ? translations.reduce : translations.expand}}</span>
      </button>
      <PSCheckbox :ref="model.name" :id="id" :model="model" @checked="onCheck" v-if="hasCheckbox"/>
      <span class="tree-label" :class="{warning: isWarning}">{{model.name}}</span>
      <span class="tree-extra-label d-sm-none d-xl-inline-block" v-if="displayExtraLabel">{{getExtraLabel}}</span>
      <span class="tree-extra-label-mini d-xl-none" v-if="displayExtraLabel">{{this.model.extraLabel}}</span>
    </div>
    <ul v-show="open" v-if="isFolder" class="tree">
      <li v-for="(element, index) in model.children" class="tree-item" :class="{disable: model.disable}">
        <PSTreeItem
          :ref="element.id"
          :class="className"
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
  import PSCheckbox from 'app/widgets/ps-checkbox';
  import { EventBus } from 'app/utils/event-bus';

  export default {
    name: 'PSTreeItem',
    props: {
      model: { type: Object, required: true },
      className: { type: String, required: false },
      hasCheckbox: { type: Boolean, required: false },
      translations: { type: Object, required: false },
      currentItem: { type: String, required: false },
    },
    computed: {
      id() {
        return this.model.id;
      },
      isFolder() {
        return this.model.children && this.model.children.length;
      },
      displayExtraLabel() {
        return this.isFolder && this.model.extraLabel;
      },
      getExtraLabel() {
        let extraLabel = '';

        if (this.model.extraLabel && this.model.extraLabel === 1) {
          extraLabel = this.translations.extra_singular;
        } else if (this.model.extraLabel) {
          extraLabel = this.translations.extra.replace('%d', this.model.extraLabel);
        }

        return extraLabel;
      },
      isHidden() {
        return !this.isFolder;
      },
      chevronStatus() {
        return this.open? 'open' : 'closed';
      },
      isWarning() {
        return !this.isFolder && this.model.warning;
      },
      active() {
        return this.model.full_name === this.currentItem;
      },
    },
    methods: {
      setCurrentElement(el) {
        if (this.$refs[el]) {
          this.openTreeItemAction();
          this.current = true;
          this.parentElement(this.$parent);
        } else {
          this.current = false;
        }
      },
      parentElement(parent) {
        if (parent.clickElement) {
          parent.clickElement();
          this.parentElement(parent.$parent);
        }
      },
      clickElement() {
        return !this.model.disable ? this.openTreeItemAction() : false;
      },
      openTreeItemAction() {
        this.setCurrentElement(this.model.full_name);
        if (this.isFolder) {
          this.open = !this.open;
        } else {
          EventBus.$emit('lastTreeItemClick', {
            item: this.model,
          });
        }
      },
      onCheck(obj) {
        this.$emit('checked', obj);
      },
    },
    mounted() {
      EventBus.$on('toggleCheckbox', (tag) => {
        const checkbox = this.$refs[tag];
        if (checkbox) {
          checkbox.$data.checked = !checkbox.$data.checked;
        }
      }).$on('expand', () => {
        this.open = true;
      }).$on('reduce', () => {
        this.open = false;
      }).$on('setCurrentElement', (el) => {
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
  };
</script>
