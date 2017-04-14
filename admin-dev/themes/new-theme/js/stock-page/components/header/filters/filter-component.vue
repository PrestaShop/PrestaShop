<template>
  <div class="filter-container">
    <SearchFilter
      ref="search"
      :placeholder="placeholder"
      :match="match"
      :label="label"
      :itemID="itemID"
      @typing="onTyping"
      @submit="onSubmit"
      @tagChanged="onTagChanged"
      />
    <ul class="m-t-1">
      <PSTree
        v-if="hasChildren"
        ref="tree"
        className="flex"
        :model="list[0]"
        :id="itemID"
      >
      </PSTree>
      <li
        v-else
        v-for="(item, index) in items"
        v-show="item.visible"
      >
        <FilterLine
          :ref="item[label]"
          :label="item[label]"
          :id="itemID+index"
          :item="item"
          @checked="onCheck"
        />
      </li>
    </ul>
  </div>
</template>

<script>
  import SearchFilter from './search-filter';
  import FilterLine from './filter-line';
  import PSTree from '../../utils/ps-tree';
  import Checkbox from '../../utils/checkbox';
  import { EventBus } from '../../utils/event-bus';
  import _ from 'lodash';

  export default {
    props: ['placeholder', 'getData', 'itemID', 'label', 'list'],
    computed: {
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
      categoriesId() {
        return this.$store.state.categoriesId;
      }
    },
    methods: {
      onCheck(obj) {
        let tags = this.$refs.search.$data.tags;
        let itemLabel = obj.item[this.label];
        if(obj.checked) {
          tags.push(itemLabel);
        }
        else {
          let index = tags.indexOf(itemLabel);
          if(this.splice) {
            tags.splice(index, 1);
          }
           this.splice = true;
        }
        if(tags.length) {
          this.$emit('active', true, this.filterList(tags));
        }
        else {
          this.$emit('active', false);
        }
      },
      onTyping(val) {
        this.currentVal = val.toLowerCase();
      },
      onSubmit(tag) {
       EventBus.$emit('tagChanged', tag);
       this.currentVal = '';
      },
      onTagChanged(tag) {
        EventBus.$emit('tagChanged', tag);
        this.splice = false;
      },
      filterList(tags) {
        let idList = []

        this.list.map((data)=> {
          if(tags.indexOf(data[this.label]) !== -1) {
            idList.push(data[this.itemID]);
          }
        });
        return idList;
      }
    },
    data() {
      return {
        currentVal: '',
        match: null,
        splice: true,
        hasChildren: false
      }
    },
    mounted() {
      this.$store.dispatch(this.getData);
    },
    components: {
      SearchFilter,
      Checkbox,
      PSTree,
      FilterLine
    }
  }
</script>

<style lang="sass?outputStyle=expanded" scoped>
  @import "~PrestaKit/scss/custom/_variables.scss";
  .filter-container {
    border: $gray-light 1px solid;
    padding: 10px;
  }
  ul {
    list-style: none;
    padding-left: 0;
  }
</style>
