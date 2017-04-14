<template>
  <li class="tree">
    <div :class="className">
      <div v-if="chevron" @click="toggle">
        <i class="material-icons" v-if="open" >keyboard_arrow_down</i>
        <i class="material-icons" v-else>chevron_right</i>
      </div>
      <slot />
    </div>
    <ul v-show="open" v-if="isFolder">
      <PSTree
        v-for="(model, index) in model.children"
        :className="className"
        :model="model"
        :key="index"
        :chevron="!!model.children"
        @checked="onCheck"
      >
        <PSTreeItem :class="className" :item="model" :id="Date.now()" :label="model.name" @checked="onCheck" />
      </PSTree>
    </ul>
  </li>
</template>

<script>
  import PSTreeItem from './ps-tree-item';
  export default {
    name: 'PSTree',
    props: {
      model: Object,
      className: String,
      slotName: String,
      id: String
    },
    computed: {
      isFolder: function () {
        return !!this.model.children;
      }
    },
    methods: {
      toggle() {
        if (this.isFolder) {
          this.open = !this.open;
        }
      },
      onCheck(event,obj) {
       this.$emit('checked', obj);
      }
    },
    components: {
      PSTreeItem
    },
    data() {
      return {
        open: false,
        chevron: true
      }
    }
  }
</script>

<style lang="sass?outputStyle=expanded" scoped>
  .tree {
    font-size: 12px;
    .material-icons {
      vertical-align: middle;
      font-size: 20px;
      cursor: pointer;
    }
  }
  ul {
    padding-left: 15px;
    list-style-type: none;
    cursor:pointer;
    margin-bottom: 5px;
    li {
      margin-bottom: 5px;
    }
  }
</style>