<template>
  <div class="custom-checkbox">
    <input type="checkbox" :id="id" v-model="checked">
    <label :for="id"></label>
  </div>
</template>

<script>
  export default {
    props: ['id','item'],
    data() {
      return {
        checked: false
      }
    },
    watch : {
      checked(val) {
        this.$emit('checked', {
          checked: val,
          item: this.item
        });
      }
    }
  }
</script>
<style lang="sass?outputStyle=expanded" scoped>
  @import "~PrestaKit/scss/custom/_variables.scss";
  .custom-checkbox {
    width: 15px;
    height: 15px;
    position: relative;
    label {
      width: 15px;
      height: 15px;
      cursor: pointer;
      position: absolute;
      top: 0;
      left: 0;
      border: 2px $gray-light solid;
      border-radius: 2px;
      &:after {
        content: '';
        width: 12px;
        height: 5px;
        position: absolute;
        top: 1px;
        left: 0;
        border: 2px solid white;
        border-top: none;
        border-right: none;
        background: transparent;
        opacity: 0;
        transform: rotate(-45deg);
      }
      &::before {
        content: '';
        width: 12px;
        height: 12px;
        position: absolute;
        top: 0;
        left: 0;
      }
    }
    input[type=checkbox] {
      visibility: hidden;
      &:checked + label {
        border: 2px $brand-primary solid;
      }
      &:checked + label:before {
        background: $brand-primary;
      }
      &:checked + label:after {
        opacity: 1;
      }
    }
  }
</style>