<template>
  <div class="filter-container">
    <SearchFilter :placeholder="placeholder" />
    <ul class="m-t-1">
      <li v-for="n in 10">
        <input type="checkbox" class="m-r-1 checkbox">
      </li>
    </ul>
  </div>
</template>

<script>
  import SearchFilter from './search-filter';

  export default {
    props: ['placeholder','getData'],
    mounted() {
      this.$store.dispatch(this.getData);
    },
    components: {
      SearchFilter
    }
  }
</script>

<style lang="sass?outputStyle=expanded" scoped>
  @import "~PrestaKit/scss/custom/_variables.scss";
  .filter-container {
    border: $gray-light 1px solid;
    padding: 10px;
  }
  ul {
    list-style: none;
    padding-left: 0;
  }
</style>
