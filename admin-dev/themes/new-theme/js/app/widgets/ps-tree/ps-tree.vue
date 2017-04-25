<template>
  <li class="tree">
    <div :class="className">
      <div :class="chevron" @click="toggle">
        <i class="material-icons" v-if="open">keyboard_arrow_down</i>
        <i class="material-icons" v-else>chevron_right</i>
      </div>
      <PSTreeItem
        ref="item"
        :class="className"
        :model="model"
        :label="model.name"
        @checked="onCheck"
      />
    </div>
    <ul v-show="open" v-if="isFolder">
      <PSTree
        v-for="(model, index) in model.children"
        :className="className"
        :model="model"
        :key="index"
        @checked="onCheck"
      >
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
      className: String
    },
    computed: {
      isFolder() {
        return this.model.children && this.model.children.length;
      },
      chevron() {
        if(!this.isFolder) {
          return 'hidden';
        }
      }
    },
    methods: {
      toggle() {
        if (this.isFolder) {
          this.open = !this.open;
        }
      },
      onCheck(obj) {
       this.$emit('checked', obj);
      }
    },
    components: {
      PSTreeItem
    },
    data() {
      return {
        open: false
      }
    }
  }
</script>

<style lang="sass" scoped>
  .tree {
    .material-icons {
      vertical-align: middle;
      font-size: 20px;
      cursor: pointer;
    }
  }
  ul {
    margin-top: 5px;
    padding-left: 15px;
    list-style-type: none;
    cursor:pointer;
    margin-bottom: 5px;
    li {
      margin-bottom: 5px;
    }
  }
  .hidden {
    visibility: hidden;
  }
</style>