<template>
  <div class="filter-container">
    <SearchFilter :placeholder="placeholder" />
    <ul class="m-t-1">
      <li v-for="item in items" class="flex">
        <Checkbox :id="item[itemID]" />
        <span class="m-l-1">{{item[label]}}</span>
      </li>
    </ul>
  </div>
</template>

<script>
  import SearchFilter from './search-filter';
  import Checkbox from '../../utils/checkbox';

  export default {
    props: ['placeholder', 'getData', 'itemID', 'label', 'data'],
    computed: {
      items() {
        return this.data;
      }
    },
    mounted() {
      this.$store.dispatch(this.getData);
    },
    components: {
      SearchFilter,
      Checkbox
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
