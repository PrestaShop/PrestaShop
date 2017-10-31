<!--**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *-->
<template>
  <div class="filter-container">
    <PSTags
      v-if="!hasChildren"
      ref="tags"
      class="form-control search search-input mb-2"
      :tags="tags"
      :placeholder="hasPlaceholder?placeholder:''"
      :hasIcon="true"
      @tagChange="onTagChanged"
      @typing="onTyping"
    />
    <div v-if="hasChildren">
      <PSTree
        v-if="isOverview"
        v-once
        ref="tree"
        :hasCheckbox="true"
        :model="list"
        @checked="onCheck"
        :translations="PSTreeTranslations"
      >
      </PSTree>
      <PSTree
        v-else
        ref="tree"
        :hasCheckbox="true"
        :model="list"
        @checked="onCheck"
        :translations="PSTreeTranslations"
      >
      </PSTree>
    </div>
    <ul
      class="mt-1"
      v-else
    >
      <li
        v-for="(item, index) in items"
        v-show="item.visible"
        class="item"
      >
        <PSTreeItem
          :label="item[label]"
          :model="item"
          @checked="onCheck"
          :hasCheckbox="true"
        />
      </li>
    </ul>
  </div>
</template>

<script>
  import PSTags from 'app/widgets/ps-tags';
  import PSTreeItem from 'app/widgets/ps-tree/ps-tree-item';
  import PSTree from 'app/widgets/ps-tree/ps-tree';
  import { EventBus } from 'app/utils/event-bus';
  import _ from 'lodash';

  export default {
    props: ['placeholder', 'itemID', 'label', 'list'],
    computed: {
      isOverview() {
        return this.$route.name === 'overview';
      },
      hasPlaceholder() {
        return !this.tags.length;
      },
      items() {
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
      PSTreeTranslations() {
        return {
          expand: this.trans('tree_expand'),
          reduce: this.trans('tree_reduce'),
        };
      },
    },
    methods: {
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
        const categoryList = this.$store.state.categoryList;
        const list = this.hasChildren ? categoryList : this.list;

        list.map((data) => {
          const isInIdList = idList.indexOf(Number(data[this.itemID])) === -1;
          if (tags.indexOf(data[this.label]) !== -1 && isInIdList) {
            idList.push(Number(data[this.itemID]));
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

<style lang="sass" scoped>
  @import "../../../../../../../scss/config/_settings.scss";
  .filter-container {
    border: $gray-light 1px solid;
    padding: 10px;
  }
  .item {
    margin-bottom: 5px;
  }
  ul {
    list-style: none;
    padding-left: 0;
    margin-bottom: 0;
  }
</style>
