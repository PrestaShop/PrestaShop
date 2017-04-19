<template>
  <a href="#" v-on:click="sortFilter($event)"><i class="material-icons">swap_vert</i></a>
</template>

<script>
  export default {
    props: ['order','isDesc'],
    methods: {
      sortFilter: function (event) {
        let desc = this.isDesc ? ' desc' : '';

        event.preventDefault();

        this.$emit('sort');
        this.$store.dispatch('getStock', {
          order: `${this.order}${desc}`,
          page_size: this.$store.state.productsPerPage,
          page_index: this.$store.getters.pageIndex
        });
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
