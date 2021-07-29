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
        v-for="(item, index) in getItems()"
        :key="index"
        v-show="item.visible"
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

<script>
  import PSTags from '@app/widgets/ps-tags';
  import PSTreeItem from '@app/widgets/ps-tree/ps-tree-item';
  import PSTree from '@app/widgets/ps-tree/ps-tree';
  import {EventBus} from '@app/utils/event-bus';

  export default {
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
      },
      list: {
        type: Array,
        required: true,
      },
    },
    computed: {
      isOverview() {
        return this.$route.name === 'overview';
      },
      hasPlaceholder() {
        return !this.tags.length;
      },
      PSTreeTranslations() {
        return {
          expand: this.trans('tree_expand'),
          reduce: this.trans('tree_reduce'),
        };
      },
    },
    methods: {
      getItems() {
        const matchList = [];
        this.list.filter((data) => {
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
      onCheck(obj) {
        const itemLabel = obj.item[this.label];
        const filterType = this.hasChildren ? 'category' : 'supplier';

        if (obj.checked) {
          this.tags.push(itemLabel);
        } else {
          const index = this.tags.indexOf(itemLabel);

          if (this.splice) {
            this.tags.splice(index, 1);
          }
          this.splice = true;
        }
        if (this.tags.length) {
          this.$emit('active', this.filterList(this.tags), filterType);
        } else {
          this.$emit('active', [], filterType);
        }
      },
      onTyping(val) {
        this.currentVal = val.toLowerCase();
      },
      onTagChanged(tag) {
        let checkedTag = tag;

        if (this.tags.indexOf(this.currentVal) !== -1) {
          this.tags.pop();
        }
        this.splice = false;
        if (this.match) {
          checkedTag = this.match[this.label];
        }
        EventBus.$emit('toggleCheckbox', checkedTag);
        this.currentVal = '';
      },
      filterList(tags) {
        const idList = [];
        const {categoryList} = this.$store.state;
        const list = this.hasChildren ? categoryList : this.list;

        list.map((data) => {
          const isInIdList = idList.indexOf(Number(data[this.itemId])) === -1;

          if (tags.indexOf(data[this.label]) !== -1 && isInIdList) {
            idList.push(Number(data[this.itemId]));
          }
          return idList;
        });
        return idList;
      },
    },
    data() {
      return {
        currentVal: '',
        match: null,
        tags: [],
        splice: true,
        hasChildren: false,
      };
    },
    components: {
      PSTags,
      PSTree,
      PSTreeItem,
    },
  };
</script>
