<template>
  <a href="#" v-on:click="sortFilter"><i class="material-icons">swap_vert</i></a>
</template>

<script>
  export default {
    methods: {
      sortFilter: function (event) {
        event.preventDefault();
        this.$emit('sort');
      }
    }
  }
</script>
<style lang="sass?outputStyle=expanded" scoped>
  @import "~PrestaKit/scss/custom/_variables.scss";
  a .material-icons {
    vertical-align: middle;
    margin-left: 5px;
    color: $gray-medium;
    font-size: 20px;
  }
</style>
