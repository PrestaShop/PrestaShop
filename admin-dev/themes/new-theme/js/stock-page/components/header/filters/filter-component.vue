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
      <li v-for="(item, index) in items" class="flex" v-show="item.visible">
        <div v-if="item.children" class="chevron" @click="openTree">
          <i class="material-icons" v-if="isClosed">chevron_right</i>
          <i class="material-icons" v-else>keyboard_arrow_down</i>
          <ul>
            <li v-for="child in item.children">
              <Checkbox :ref="child[label]" :id="child[itemID]+index" :item="child" @checked="onCheck"/>
              <span class="m-l-1">{{child[label]}}</span>
            </li>
          </ul>
          <Checkbox :ref="item[label]" :id="itemID+index" :item="item" @checked="onCheck"/>
        </div>
        <Checkbox :ref="item[label]" :id="itemID+index" :item="item" @checked="onCheck"/>
        <span class="m-l-1">{{item[label]}}</span>
      </li>
    </ul>
  </div>
</template>

<script>
  import SearchFilter from './search-filter';
  import Checkbox from '../../utils/checkbox';
  import _ from 'lodash';

  export default {
    props: ['placeholder', 'getData', 'itemID', 'label', 'list'],
    computed: {
      items() {
        let matchList = [];
        console.log(this.list)
        this.list.filter((data)=> {
          let label = data[this.label].toLowerCase();
          data.visible = false;
          if(!!label.match(this.currentVal)) {
            data.visible = true;
            matchList.push(data);
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
      onSubmit(label) {
       let item = _.find(this.$refs[label]);
       item.$data.checked = true;
       this.currentVal = '';
      },
      onTagChanged(tag) {
        if(this.$refs[tag]) {
          this.$refs[tag][0].$data.checked = false;
          this.splice = false;
        }
      },
      filterList(tags) {
        let idList = []

        this.list.map((data)=> {
          if(tags.indexOf(data[this.label]) !== -1) {
            idList.push(data[this.itemID]);
          }
        });
        return idList;
      },
      openTree() {
        this.isClosed = ! this.isClosed;
      }
    },
    data() {
      return {
        currentVal: '',
        match: null,
        splice: true,
        isClosed: false
      }
    },
    mounted() {
      this.$store.dispatch(this.getData);
    },
    components: {
      SearchFilter,
      Checkbox
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
  .chevron {
    cursor: pointer;
    .material-icons {
      vertical-align: middle;
      font-size:20px;
    }
  }
</style>
