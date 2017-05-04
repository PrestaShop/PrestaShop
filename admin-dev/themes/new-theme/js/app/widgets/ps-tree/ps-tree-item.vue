<template>
  <div :class="className">
    <div class="flex">
      <div class="flex" :class="chevron" @click="toggle">
        <i class="material-icons" v-if="open">keyboard_arrow_down</i>
        <i class="material-icons" v-else>chevron_right</i>
      </div>
      <PSCheckbox :ref="model.name" :id="id" :model="model" @checked="onCheck" v-if="hasCheckbox"/>
      <span class="tree-label">{{model.name}}</span>
    </div>
    <ul v-show="open" v-if="isFolder">
      <li v-for="(element, index) in model.children">
        <PSTreeItem
          ref="item"
          :class="className"
          :hasCheckbox="hasCheckbox"
          :model="element"
          :label="element.name"
          :opened="open"
          @checked="onCheck"
        />
      </li>
    </ul>
  </div>
</template>

<script>
  import PSCheckbox from 'app/widgets/ps-checkbox';
  import { EventBus } from 'app/utils/event-bus';

  export default {
    name: 'PSTreeItem',
    props:['model','className', 'hasCheckbox', 'opened'],
    computed: {
      id() {
        return this.model.id;
      },
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
      PSCheckbox
    },
    watch: {
      opened(val) {
        this.open = val
      }
    },
    data() {
      return {
        open: false
      }
    },
    mounted() {
      EventBus.$on('toggleCheckbox', (tag) => {
        let checkbox = this.$refs[tag];
        if(checkbox) {
          checkbox.$data.checked = !checkbox.$data.checked;
        }
      });
    },
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
  .tree-label {
    margin-left: 5px;
    font-size: 12px;
  }
  .hidden {
    visibility: hidden;
  }
  ul {
    padding: 0 0 0 20px;
  }
  li {
    margin: 5px 0;
    list-style-type: none;
  }
</style>
