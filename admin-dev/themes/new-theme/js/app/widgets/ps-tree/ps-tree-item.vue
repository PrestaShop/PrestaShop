<template>
  <div :class="className">
    <div class="flex tree-name" @click="clickItem">
      <div class="flex" :class="chevron">
        <i class="material-icons" v-if="open">keyboard_arrow_down</i>
        <i class="material-icons" v-else>chevron_right</i>
      </div>
      <PSCheckbox :ref="model.name" :id="id" :model="model" @checked="onCheck" v-if="hasCheckbox"/>
      <span class="tree-label">{{model.name}}</span>
      <span class="tree-extra-label" v-if="displayExtraLabel">{{getExtraLabel}}</span>
    </div>
    <ul v-show="open" v-if="isFolder">
      <li v-for="(element, index) in model.children">
        <PSTreeItem
          :ref="element.id"
          :class="className"
          :hasCheckbox="hasCheckbox"
          :model="element"
          :label="element.name"
          :translations="translations"
          @checked="onCheck"
          @setCurrentEl ="setCurrentEl"
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
    props: {
      model: {
        type: Object,
        required: true
      },
      className: {
        type: String,
        required: false
      },
      hasCheckbox: {
        type: Boolean,
        required: false
      },
      translations: {
        type: Object,
        required: false
      }
    },
    computed: {
      id() {
        return this.model.id;
      },
      isFolder() {
        return this.model.children && this.model.children.length;
      },
      displayExtraLabel() {
        return this.isFolder && this.model.extraLabel;
      },
      getExtraLabel() {
        return this.translations.extra.replace('%d', this.model.extraLabel);
      },
      chevron() {
        if(!this.isFolder) {
          return 'hidden';
        }
      }
    },
    methods: {
      setCurrentEl() {
        if(this.model.id) {
          this.$emit('setCurrentEl', this.model.id);
        }
      },
      clickItem() {
        this.setCurrentEl();
        if (this.isFolder) {
          this.open = !this.open;
        } else {
          EventBus.$emit('lastTreeItemClick', {
            item: this.model
          });
        }
      },
      onCheck(obj) {
        this.$emit('checked', obj);
      }
    },
    components: {
      PSCheckbox
    },
    data() {
      return {
        open: false,
        current: false
      }
    },
    mounted() {
      EventBus.$on('toggleCheckbox', (tag) => {
        let checkbox = this.$refs[tag];
        if(checkbox) {
          checkbox.$data.checked = !checkbox.$data.checked;
        }
      }).$on('expand', _ => {
        this.open = true;
      }).$on('reduce', _ => {
        this.open = false;
      }).$on('setCurrentEl', (el) => {
        if(this.$refs[el]) {
          this.clickItem();
          this.current = true;
          parentEl(this.$parent);
        }
        else {
          this.current = false;
        }
      });
      function parentEl(parent){
        if(parent.clickItem) {
          parent.clickItem();
          parentEl(parent.$parent);
        }
      }
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
