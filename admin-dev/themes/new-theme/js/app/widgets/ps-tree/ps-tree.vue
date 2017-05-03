<template>
  <ul class="tree">
    <li v-for="(element, index) in model">
      <PSTreeItem
        ref="item"
        :class="className"
        :hasCheckbox="hasCheckbox"
        :model="element"
        :label="element.name"
        @checked="onCheck"
      />
    </li>
  </ul>
</template>

<script>
  import PSTreeItem from './ps-tree-item';
  export default {
    name: 'PSTree',
    props: {
      model: Array,
      className: String,
      hasCheckbox: Boolean
    },
    methods: {
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
  ul {
    list-style-type: none;
    cursor: pointer;
    padding: 0;
    margin: 0;
  }
</style>
