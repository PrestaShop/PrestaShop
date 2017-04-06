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
      <li v-for="item in items" class="flex" v-show="item.visible">
        <Checkbox :ref="item[label]" :id="item[itemID]" :item="item" @checked="onCheck"/>
        <span class="m-l-1">{{item[label]}}</span>
      </li>
    </ul>
  </div>
</template>

<script>
  import SearchFilter from './search-filter';
  import Checkbox from '../../utils/checkbox';

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
       let item = window._.find(this.$refs[label]);
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
      }
    },
    data() {
      return {
        currentVal: '',
        match: null,
        splice: true
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
</style>
