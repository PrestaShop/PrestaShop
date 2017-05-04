<template>
  <div class="filter-container">
    <PSTags
      v-if="!hasChildren"
      ref="tags"
      class="form-control search search-input"
      :tags="tags"
      :placeholder="hasPlaceholder?placeholder:''"
      @tagChange="onTagChanged"
      @typing="onTyping"
    />

    <PSTree
      v-if="hasChildren"
      ref="tree"
      :hasCheckbox="true"
      :model="list"
      @checked="onCheck"
      :translations="PSTreeTranslations"
    >
    </PSTree>
    <ul
      class="m-t-1"
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
      hasPlaceholder() {
        return !this.tags.length;
      },
      items() {
        let matchList = [];
        this.list.filter((data)=> {
          let label = data[this.label].toLowerCase();
          data.visible = false;
          if(!!label.match(this.currentVal)) {
            data.visible = true;
            matchList.push(data);
          }
          if(data.children) {
            this.hasChildren = true;
          }
          return data;
        });

        if(matchList.length === 1) {
          this.match = matchList[0];
        }
        else {
          this.match = null;
        }
        return this.list;
      },
      PSTreeTranslations() {
        return {
          expand: this.trans('tree_expand'),
          reduce: this.trans('tree_reduce')
        }
      }
    },
    methods: {
      onCheck(obj) {
        let itemLabel = obj.item[this.label];
        let filterType = this.hasChildren ? 'category' : 'supplier';

        if(obj.checked) {
          this.tags.push(itemLabel);
        }
        else {
          let index = this.tags.indexOf(itemLabel);
          if(this.splice) {
            this.tags.splice(index, 1);
          }
          this.splice = true;
        }
        if(this.tags.length) {
          this.$emit('active', this.filterList(this.tags), filterType);
        }
        else {
          this.$emit('active', [], filterType);
        }
      },
      onTyping(val) {
        this.currentVal = val.toLowerCase();
      },
      onTagChanged(tag) {
        if(this.tags.indexOf(this.currentVal) !== -1){
          this.tags.pop();
        }
        this.splice = false;
        if(this.match) {
          tag = this.match[this.label];
        }
        EventBus.$emit('toggleCheckbox', tag);
        this.currentVal = '';
      },
      filterList(tags) {
        let idList = [];
        let categoryList = this.$store.getters.categoryList;
        let list = this.hasChildren ? categoryList : this.list;

        list.map((data)=> {
          if(tags.indexOf(data[this.label]) !== -1 && idList.indexOf(Number(data[this.itemID])) === -1) {
            idList.push(Number(data[this.itemID]));
          }
        });
        return idList;
      }
    },
    data() {
      return {
        currentVal: '',
        match: null,
        tags: [],
        splice: true,
        hasChildren: false
      }
    },
    components: {
      PSTags,
      PSTree,
      PSTreeItem
    }
  }
</script>

<style lang="sass?outputStyle=expanded" scoped>
  @import "~PrestaKit/scss/custom/_variables.scss";
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
  }
</style>
