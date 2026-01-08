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
  <div class="filter-container">
    <PSTags
      v-if="!hasChildren"
      ref="tags"
      class="form-control search search-input mb-2"
      :tags="tags"
      :placeholder="hasPlaceholder?placeholder:''"
      :has-icon="true"
      @tagChange="onTagChanged"
      @typing="onTyping"
    />
    <div v-if="hasChildren">
      <PSTree
        v-if="isOverview"
        v-once
        ref="tree"
        :has-checkbox="true"
        :model="list"
        @checked="onCheck"
        :translations="PSTreeTranslations"
      />
      <PSTree
        v-else
        ref="tree"
        :has-checkbox="true"
        :model="list"
        @checked="onCheck"
        :translations="PSTreeTranslations"
      />
    </div>
    <ul
      class="mt-1"
      v-else
    >
      <li
        v-for="(item, index) in visibleItems"
        :key="index"
        class="item"
      >
        <PSTreeItem
          :label="item[label]"
          :model="item"
          @checked="onCheck"
          :has-checkbox="true"
        />
      </li>
    </ul>
  </div>
</template>

<script lang="ts">
  import {defineComponent} from 'vue';
  import PSTags from '@app/widgets/ps-tags.vue';
  import PSTreeItem from '@app/widgets/ps-tree/ps-tree-item.vue';
  import PSTree from '@app/widgets/ps-tree/ps-tree.vue';
  import {EventEmitter} from '@components/event-emitter';
  import translate from '@app/pages/stock/mixins/translate';

  const FilterComponent = defineComponent({
    props: {
      placeholder: {
        type: String,
        required: false,
        default: '',
      },
      itemId: {
        type: String,
        required: true,
      },
      label: {
        type: String,
        required: true,
        default: '',
      },
      list: {
        type: Array,
        required: true,
      },
    },
    mixins: [translate],
    computed: {
      isOverview(): boolean {
        return this.$route.name === 'overview';
      },
      hasPlaceholder(): boolean {
        return !this.tags.length;
      },
      PSTreeTranslations(): {expand: string, reduce: string} {
        return {
          expand: this.trans('tree_expand'),
          reduce: this.trans('tree_reduce'),
        };
      },
      visibleItems(): Array<any> {
        const items = this.getItems();

        return items.filter((item) => item.visible);
      },
    },
    methods: {
      reset(): void {
        this.tags = [];
      },
      getItems(): Array<any> {
        /* eslint-disable camelcase */
        const matchList: Array<{
          id: number,
          name: string,
          supplier_id: number,
          visible: boolean,
        }> = [];
        /* eslint-enable camelcase */
        this.list.filter((data: any) => {
          const label = data[this.label].toLowerCase();
          data.visible = false;
          if (label.match(this.currentVal)) {
            data.visible = true;
            matchList.push(data);
          }
          if (data.children) {
            this.hasChildren = true;
          }
          return data;
        });

        if (matchList.length === 1) {
          this.match = matchList[0];
        } else {
          this.match = null;
        }
        return this.list;
      },
      onCheck(obj: any): void {
        const itemId = obj.item[this.itemId];
        const filterType = this.hasChildren ? 'category' : 'supplier';
        const updateSelection = (items: Array<any>) => {
          items.forEach((item) => {
            if (item[this.itemId] === itemId) {
              item.selected = obj.checked;
            }
            if (item.children) {
              updateSelection(item.children);
            }
          });
        };
        updateSelection(this.list); 
        if (obj.checked) {
          this.tags.push(obj.item[this.label]);
        } else {
          const index = this.tags.indexOf(obj.item[this.label]);
          this.tags.splice(index, 1);
        }
        const filteredList = this.filterList(this.tags);
        this.$emit('active', filteredList, filterType);
      },
      onTyping(val: string): void {
        this.currentVal = val.toLowerCase();
      },
      onTagChanged(tag: any): void {
        let checkedTag = tag;

        if (this.tags.indexOf(this.currentVal) !== -1) {
          this.tags.pop();
        }

        if (this.match) {
          checkedTag = this.match[this.label];
        }
        EventEmitter.emit('toggleCheckbox', checkedTag);
        this.currentVal = '';
      },
      filterList(tags: Array<any>): Array<number> {
        const idList: Array<number> = [];
        const {list} = this;

        const checkCategory = (category: Record<string, any>) => {
          if (category.selected === true) {
            idList.push(Number(category[this.itemId]));
          }
          if (category.children) {
            category.children.forEach(checkCategory);
          }
        };

        list.forEach(checkCategory);
        return idList;
      },
    },
    data() {
      return {
        currentVal: '',
        match: null as null | Record<string, any>,
        tags: [] as Array<any>,
        hasChildren: false,
      };
    },
    components: {
      PSTags,
      PSTree,
      PSTreeItem,
    },
  });

  export type FilterComponentInstanceType = InstanceType<typeof FilterComponent> | undefined;

  export default FilterComponent;
</script>
