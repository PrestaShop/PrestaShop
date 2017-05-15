<template>
  <div>
    <div class="m-b-1 tree-header">
      <span class="text-uppercase pointer" @click="expand">
        <i class="material-icons">keyboard_arrow_down</i>
        <strong v-if="translations">{{translations.expand}}</strong>
      </span>
      <span class="pull-right text-uppercase pointer" @click="reduce">
        <i class="material-icons">keyboard_arrow_up</i>
        <strong v-if="translations">{{translations.reduce}}</strong>
      </span>
    </div>
    <ul class="tree" :class="className">
      <li v-for="(element, index) in model">
        <PSTreeItem
          ref="item"
          :hasCheckbox="hasCheckbox"
          :model="element"
          :label="element.name"
          :translations="translations"
          :currentItem="currentItem"
          @checked="onCheck"
          @setCurrentEl ="setCurrentEl"
        />
      </li>
    </ul>
  </div>
</template>

<script>
  import PSTreeItem from './ps-tree-item';
  import { EventBus } from 'app/utils/event-bus';

  export default {
    name: 'PSTree',
    props: {
      model: Array,
      className: String,
      currentItem: String,
      hasCheckbox: Boolean,
      translations: {
        type: Object,
        required: false
      }
    },
    methods: {
      onCheck(obj) {
       this.$emit('checked', obj);
      },
      expand() {
        EventBus.$emit('expand');
      },
      reduce() {
        EventBus.$emit('reduce');
      },
      setCurrentEl(id) {
        EventBus.$emit('setCurrentEl', id);
      }
    },
    components: {
      PSTreeItem
    }
  }
</script>

<style lang="sass" scoped>
  @import "~PrestaKit/scss/custom/_variables.scss";
  ul {
    list-style-type: none;
    cursor: pointer;
    padding: 0;
    margin: 0;
  }
  strong {
    font-weight: 600;
  }
  .tree-header {
    border-bottom: $gray-light 1px solid;
    color: $gray-medium;
  }
  .material-icons {
    vertical-align: middle;
  }
  .pointer {
    cursor: pointer;
  }
</style>
