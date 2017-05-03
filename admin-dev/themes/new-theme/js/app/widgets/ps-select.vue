<template>
  <div class="ps-select">
    <select class="form-control" v-model="selected" @change="onChange">
      <option value="default" selected>
        <slot />
      </option>
      <option
        v-for="item in items"
        :value="item[itemID]"
      >
        {{item[itemName]}}
      </option>
    </select>
  </div>
</template>

<script>
  export default {
    props: ['items', 'itemID', 'itemName'],
    methods: {
      onChange(e) {
        this.$emit('change', this.selected);
      }
    },
    data() {
      return {
        selected: 'default'
      }
    }
  }
</script>

<style lang="sass" scoped>
  @import "~PrestaKit/scss/custom/_variables.scss";
  .ps-select {
    position: relative;
    select {
      appearance: none;
      border-radius: 0;
    }
    &::after {
      content: "\E313";
      font-family: 'Material Icons';
      color: $gray-medium;
      font-size: 20px;
      position: absolute;
      right: 5px;
      top: 5px;
    }
  }
</style>