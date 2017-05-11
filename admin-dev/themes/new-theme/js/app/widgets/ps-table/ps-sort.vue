<template>
  <a href="#" @click.prevent="sortFilter">
    <i class="material-icons">swap_vert</i>
  </a>
</template>

<script>
  export default {
    props: ['order','isDesc'],
    methods: {
      sortFilter() {
        let desc = this.isDesc ? ' desc' : '';
        this.$emit('sort', this.order, desc);
      }
    }
  }
</script>
<style lang="sass" scoped>
  @import "~PrestaKit/scss/custom/_variables.scss";
  a .material-icons {
    vertical-align: middle;
    margin-left: 5px;
    color: $gray-medium;
    font-size: 20px;
  }
</style>
