<template>
  <div class="tree">
    <div @click="toggle">
      <i class="material-icons" v-if="open">keyboard_arrow_down</i>
      <i class="material-icons" v-else>chevron_right</i>
    </div>
    <ul v-show="open" v-if="isFolder">
      <slot />
    </ul>
  </div>
</template>

<script>
  export default {
    props: {
      model: Object
    },
    computed: {
      isFolder: function () {
        return !!this.model.children;
      }
    },
    methods: {
      toggle() {
        if (this.isFolder) {
          this.open = !this.open
        }
      }
    },
    data() {
      return {
        open: false
      }
    }
  }
</script>

<style lang="sass?outputStyle=expanded" scoped>
  .tree {
    .material-icons {
      vertical-align: middle;
      font-size: 20px;
      cursor: pointer;
    }
  }
</style>