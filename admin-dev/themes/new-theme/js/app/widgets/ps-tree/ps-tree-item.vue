<template>
  <div :class="{className}">
    <div class="flex tree-name" :class="{active: current}" @click="clickItem">
      <div class="flex" :class="chevron">
        <i class="material-icons" v-if="open">keyboard_arrow_down</i>
        <i class="material-icons" v-else>chevron_right</i>
      </div>
      <PSCheckbox :ref="model.name" :id="id" :model="model" @checked="onCheck" v-if="hasCheckbox"/>
      <span class="tree-label" :class="{warning: isWarning}">{{model.name}}</span>
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
          :currentItem="currentItem"
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
        required: true,
      },
      className: {
        type: String,
        required: false,
      },
      hasCheckbox: {
        type: Boolean,
        required: false,
      },
      translations: {
        type: Object,
        required: false,
      },
      currentItem: {
        type: String,
        required: false,
      },
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
        return this.translations.extra ? this.translations.extra.replace('%d', this.model.extraLabel) : '';
      },
      chevron() {
        return !this.isFolder ? 'hidden' : '';
      },
      isWarning() {
        return !this.isFolder && this.model.warning;
      },
    },
    methods: {
      setCurrentEl(el) {
        if (this.$refs[el]) {
          this.clickItem();
          this.current = true;
          this.parentEl(this.$parent);
        } else {
          this.current = false;
        }
      },
      parentEl(parent) {
        if (parent.clickItem) {
          parent.clickItem();
          this.parentEl(parent.$parent);
        }
      },
      clickItem() {
        this.setCurrentEl(this.model.full_name);
        if (this.isFolder) {
          this.open = !this.open;
        } else {
          EventBus.$emit('lastTreeItemClick', {
            item: this.model,
          });
        }
      },
      onCheck(obj) {
        this.$emit('checked', obj);
      },
    },
    mounted() {
      EventBus.$on('toggleCheckbox', (tag) => {
        const checkbox = this.$refs[tag];
        if (checkbox) {
          checkbox.$data.checked = !checkbox.$data.checked;
        }
      }).$on('expand', () => {
        this.open = true;
      }).$on('reduce', () => {
        this.open = false;
      });
      this.setCurrentEl(this.currentItem);
    },
    components: {
      PSCheckbox,
    },
    data: () => ({
      open: false,
      current: false,
    }),
  };
</script>

<style lang="sass" scoped>
  @import "~PrestaKit/scss/custom/_variables.scss";
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
  .warning {
    color: $danger;
    background: none;
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
