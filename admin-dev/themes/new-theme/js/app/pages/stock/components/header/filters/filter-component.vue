<template>
  <div class="filter-container">
<<<<<<< 44769b26d4e1a09d32b42ed2c6ce2b3deaecab80
    <SearchFilter
      ref="search"
      :placeholder="placeholder"
      :match="match"
      :label="label"
      @typing="onTyping"
      @submit="onSubmit"
      @tagChanged="onTagChanged"
      />
||||||| merged common ancestors
    <SearchFilter
      ref="search"
      :placeholder="placeholder"
      :match="match"
      :label="label"
      @typing="onTyping"
      @submit="onSubmit"
      @tagChanged="onTagChanged"
      />
=======
    <PSTags
      v-if="!hasChildren"
      ref="tags"
      class="form-control search search-input"
      :tags="tags"
      :placeholder="hasPlaceholder?placeholder:''"
      @tagChange="onTagChanged"
      @typing="onTyping"
    />
>>>>>>> BO: Improve tags component
    <ul class="m-t-1">
      <PSTree
        v-if="hasChildren"
        ref="tree"
        className="flex"
        :model="list[0]"
        :id="itemID"
        @checked="onCheck"
      >
      </PSTree>
      <li
        v-else
        v-for="(item, index) in items"
        v-show="item.visible"
        class="item"
      >
        <PSTreeItem
          :label="item[label]"
          :id="item[itemID]"
          :item="item"
          className="flex"
          @checked="onCheck"
        />
      </li>
    </ul>
  </div>
</template>

<script>
  import PSTags from 'app/widgets/ps-tags';
  import PSTreeItem from 'app/widgets/ps-tree-item';
  import PSTree from 'app/widgets/ps-tree';
  import { EventBus } from 'app/utils/event-bus';
  import _ from 'lodash';

  export default {
    props: ['placeholder', 'itemID', 'label', 'list'],
    computed: {
<<<<<<< 44769b26d4e1a09d32b42ed2c6ce2b3deaecab80
      items() {
||||||| merged common ancestors
      items() {
=======
      hasPlaceholder() {
        return !this.tags.length;
      },
<<<<<<< a8c052fd1ac5d5076d2f6d0d7b696c7453a51afc
<<<<<<< 26a1b99444f8a8767fb70a3fa06f3660f36c8ee5:admin-dev/themes/new-theme/js/stock-page/components/header/filters/filter-component.vue
      items() {
>>>>>>> BO: Improve tags component
||||||| merged common ancestors
      items() {
=======
      items() {
>>>>>>> BO: Start stock refacto:admin-dev/themes/new-theme/js/app/pages/stock/components/header/filters/filter-component.vue
||||||| merged common ancestors
      items() {
=======
      items() {
>>>>>>> BO: Improve stock movements
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
    watch: {
      tags(items) {

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
